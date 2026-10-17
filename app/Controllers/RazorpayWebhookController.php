<?php
/**
 * Razorpay Webhook Controller (Cryptographically Verified & Idempotent)
 */

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Database;
use App\Core\Request;
use App\Core\Response;
use App\Services\CheckoutService;
use App\Services\InventoryService;
use App\Services\RazorpayService;

class RazorpayWebhookController extends Controller
{
    public function handle(Request $request): void
    {
        $rawPayload = $request->rawBody();
        $signature = (string)$request->header('X-Razorpay-Signature');
        $eventId = (string)$request->header('X-Razorpay-Event-Id');

        $razorpay = new RazorpayService();

        // Verify HMAC-SHA256 signature against raw request body
        if (!$razorpay->verifyWebhookSignature($rawPayload, $signature)) {
            http_response_code(400);
            Response::json(['error' => 'Invalid webhook cryptographic signature.'], 400);
            return;
        }

        $data = json_decode($rawPayload, true);
        if (!is_array($data)) {
            Response::json(['error' => 'Invalid JSON payload.'], 400);
            return;
        }

        $eventType = $data['event'] ?? 'unknown';
        $eventId = $eventId ?: ($data['event_id'] ?? ('evt_' . md5($rawPayload)));

        // Idempotency Check: Prevent duplicate webhook processing
        $existing = Database::fetchOne(
            "SELECT id, processed_status FROM payment_events WHERE provider_event_id = :eid",
            ['eid' => $eventId]
        );

        if ($existing && $existing['processed_status'] === 'processed') {
            // Already processed earlier; return 200 OK to acknowledge
            Response::json(['status' => 'already_processed', 'event_id' => $eventId]);
            return;
        }

        $eventDbId = 0;
        if (!$existing) {
            $eventDbId = Database::insert('payment_events', [
                'provider_event_id' => $eventId,
                'event_type' => $eventType,
                'processed_status' => 'received',
                'event_payload' => $rawPayload,
                'created_at' => date('Y-m-d H:i:s')
            ]);
        } else {
            $eventDbId = (int)$existing['id'];
        }

        // Process Event
        try {
            switch ($eventType) {
                case 'payment.captured':
                    $paymentEntity = $data['payload']['payment']['entity'] ?? [];
                    $rzpOrderId = $paymentEntity['order_id'] ?? null;
                    $rzpPaymentId = $paymentEntity['id'] ?? null;

                    if ($rzpOrderId && $rzpPaymentId) {
                        $payment = Database::fetchOne(
                            "SELECT * FROM payments WHERE razorpay_order_id = :roid",
                            ['roid' => $rzpOrderId]
                        );

                        if ($payment) {
                            $order = Database::fetchOne("SELECT * FROM orders WHERE id = :id", ['id' => $payment['order_id']]);
                            if ($order && $order['status'] === 'pending') {
                                Database::transaction(function () use ($payment, $order, $rzpPaymentId) {
                                    Database::update('payments', [
                                        'razorpay_payment_id' => $rzpPaymentId,
                                        'status' => 'captured',
                                        'updated_at' => date('Y-m-d H:i:s')
                                    ], "id = :id", ['id' => $payment['id']]);

                                    Database::update('orders', [
                                        'status' => 'confirmed',
                                        'payment_status' => 'captured',
                                        'updated_at' => date('Y-m-d H:i:s')
                                    ], "id = :id", ['id' => $order['id']]);

                                    InventoryService::deductStockForOrder((int)$order['id']);

                                    Database::insert('order_status_history', [
                                        'order_id' => $order['id'],
                                        'status' => 'confirmed',
                                        'comment' => "Payment captured via Razorpay Webhook (ID: {$rzpPaymentId})",
                                        'created_at' => date('Y-m-d H:i:s')
                                    ]);
                                });
                            }
                        }
                    }
                    break;

                case 'payment.failed':
                    $paymentEntity = $data['payload']['payment']['entity'] ?? [];
                    $rzpOrderId = $paymentEntity['order_id'] ?? null;
                    $errDesc = $paymentEntity['error_description'] ?? 'Payment failed';

                    if ($rzpOrderId) {
                        Database::update('payments', [
                            'status' => 'failed',
                            'error_description' => $errDesc,
                            'updated_at' => date('Y-m-d H:i:s')
                        ], "razorpay_order_id = :roid", ['roid' => $rzpOrderId]);
                    }
                    break;
            }

            // Mark event as processed
            Database::update('payment_events', [
                'processed_status' => 'processed'
            ], "id = :id", ['id' => $eventDbId]);

            Response::json(['status' => 'success', 'event_id' => $eventId]);
        } catch (\Throwable $e) {
            Database::update('payment_events', [
                'processed_status' => 'failed',
                'error_message' => $e->getMessage()
            ], "id = :id", ['id' => $eventDbId]);

            error_log("Razorpay Webhook Error: " . $e->getMessage());
            Response::json(['error' => 'Webhook processing failed.'], 500);
        }
    }
}
