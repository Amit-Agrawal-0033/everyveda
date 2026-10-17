<?php
/**
 * Checkout & Order Placement Engine
 * Transaction-safe order creation, Razorpay order provisioning, and post-payment reconciliation
 */

declare(strict_types=1);

namespace App\Services;

use App\Core\Database;
use RuntimeException;

class CheckoutService
{
    /**
     * Process checkout form submission and create pending/confirmed order
     */
    public static function processCheckout(array $data): array
    {
        $cart = CartService::getCartDetails();
        if (empty($cart['items'])) {
            throw new RuntimeException("Your shopping cart is empty.");
        }

        $userId = auth_user()['id'] ?? null;
        $paymentMethod = $data['payment_method'] ?? 'razorpay';

        // Check if COD is allowed
        if ($paymentMethod === 'cod' && !(bool)env('ENABLE_COD', true)) {
            throw new RuntimeException("Cash on delivery is currently unavailable. Please choose online payment.");
        }

        // Generate unique, non-sequential order number and tracking token
        $today = date('Ymd');
        $randomSeq = str_pad((string)random_int(1000, 9999), 4, '0', STR_PAD_LEFT);
        $orderNumber = "EV-{$today}-{$randomSeq}";
        $trackingToken = bin2hex(random_bytes(32));

        $shippingState = trim($data['shipping_state']);
        $taxBreakdown = TaxService::calculateTax($cart['subtotal'] - $cart['discount'], $shippingState, (float)$cart['tax_rate']);

        // Persist Order Inside ACID Transaction
        return Database::transaction(function () use (
            $data, $cart, $userId, $paymentMethod, $orderNumber, $trackingToken, $taxBreakdown
        ) {
            $isSameAddress = !empty($data['same_as_shipping']);

            $orderId = Database::insert('orders', [
                'order_number' => $orderNumber,
                'tracking_token' => $trackingToken,
                'user_id' => $userId,
                'customer_name' => trim($data['name']),
                'customer_email' => strtolower(trim($data['email'])),
                'customer_phone' => preg_replace('/[^\d]/', '', $data['phone']),
                
                // Shipping
                'shipping_address_line1' => trim($data['shipping_address_line1']),
                'shipping_address_line2' => !empty($data['shipping_address_line2']) ? trim($data['shipping_address_line2']) : null,
                'shipping_city' => trim($data['shipping_city']),
                'shipping_state' => trim($data['shipping_state']),
                'shipping_pincode' => trim($data['shipping_pincode']),

                // Billing
                'billing_address_line1' => $isSameAddress ? trim($data['shipping_address_line1']) : trim($data['billing_address_line1'] ?? $data['shipping_address_line1']),
                'billing_address_line2' => $isSameAddress ? (!empty($data['shipping_address_line2']) ? trim($data['shipping_address_line2']) : null) : (!empty($data['billing_address_line2']) ? trim($data['billing_address_line2']) : null),
                'billing_city' => $isSameAddress ? trim($data['shipping_city']) : trim($data['billing_city'] ?? $data['shipping_city']),
                'billing_state' => $isSameAddress ? trim($data['shipping_state']) : trim($data['billing_state'] ?? $data['shipping_state']),
                'billing_pincode' => $isSameAddress ? trim($data['shipping_pincode']) : trim($data['billing_pincode'] ?? $data['shipping_pincode']),

                // Amounts
                'subtotal' => $cart['subtotal'],
                'discount_amount' => $cart['discount'],
                'coupon_code' => $cart['coupon_code'],
                'shipping_fee' => $cart['shipping_fee'],
                'tax_amount' => $taxBreakdown['total_tax'],
                'total_amount' => $cart['total'],

                // Status
                'status' => $paymentMethod === 'cod' ? 'confirmed' : 'pending',
                'payment_method' => $paymentMethod,
                'payment_status' => 'pending',
                'order_notes' => !empty($data['order_notes']) ? trim($data['order_notes']) : null,
                'created_at' => date('Y-m-d H:i:s')
            ]);

            // Save order items snapshot
            foreach ($cart['items'] as $item) {
                Database::insert('order_items', [
                    'order_id' => $orderId,
                    'product_id' => $item['product_id'],
                    'variant_id' => $item['variant_id'],
                    'sku' => $item['sku'],
                    'product_name_en' => $item['name'],
                    'product_name_hi' => $item['name'],
                    'variant_name_en' => $item['variant_name'],
                    'variant_name_hi' => $item['variant_name'],
                    'unit_price' => $item['unit_price'],
                    'quantity' => $item['quantity'],
                    'tax_rate' => $item['tax_rate'],
                    'tax_amount' => round(($item['line_total'] * $item['tax_rate']) / (100 + $item['tax_rate']), 2),
                    'discount_amount' => 0.00,
                    'total_price' => $item['line_total'],
                    'created_at' => date('Y-m-d H:i:s')
                ]);
            }

            // Record Coupon Usage if applied
            if (!empty($cart['coupon']) && $cart['discount'] > 0) {
                CouponService::recordRedemption((int)$cart['coupon']['id'], $userId, $orderId, (float)$cart['discount']);
            }

            // Save Customer Address if user is logged in
            if ($userId && !empty($data['save_address'])) {
                Database::insert('customer_addresses', [
                    'user_id' => $userId,
                    'recipient_name' => trim($data['name']),
                    'phone' => preg_replace('/[^\d]/', '', $data['phone']),
                    'address_line1' => trim($data['shipping_address_line1']),
                    'address_line2' => !empty($data['shipping_address_line2']) ? trim($data['shipping_address_line2']) : null,
                    'city' => trim($data['shipping_city']),
                    'state' => trim($data['shipping_state']),
                    'pincode' => trim($data['shipping_pincode']),
                    'is_default_shipping' => 1
                ]);
            }

            // Record initial status history
            Database::insert('order_status_history', [
                'order_id' => $orderId,
                'status' => $paymentMethod === 'cod' ? 'confirmed' : 'pending',
                'comment' => $paymentMethod === 'cod' ? 'Order placed via Cash on Delivery' : 'Order placed, awaiting Razorpay payment',
                'created_at' => date('Y-m-d H:i:s')
            ]);

            // Handling based on Payment Method
            if ($paymentMethod === 'cod') {
                // For COD, immediately decrement stock
                InventoryService::deductStockForOrder($orderId);
                CartService::clearCart();

                // Send email notification
                MailerService::sendOrderConfirmation($orderId);

                return [
                    'status' => 'success',
                    'order_id' => $orderId,
                    'order_number' => $orderNumber,
                    'tracking_token' => $trackingToken,
                    'redirect_url' => url("/order-confirmed?token={$trackingToken}")
                ];
            }

            // Razorpay Workflow: Create Razorpay Order
            $razorpay = new RazorpayService();
            $rzpOrder = $razorpay->createOrder($cart['total'], $orderNumber, [
                'order_id' => (string)$orderId,
                'order_number' => $orderNumber
            ]);

            // Record Payment Entity
            Database::insert('payments', [
                'order_id' => $orderId,
                'provider' => 'razorpay',
                'razorpay_order_id' => $rzpOrder['id'],
                'amount' => $cart['total'],
                'currency' => 'INR',
                'status' => 'created',
                'created_at' => date('Y-m-d H:i:s')
            ]);

            return [
                'status' => 'razorpay_checkout',
                'order_id' => $orderId,
                'order_number' => $orderNumber,
                'tracking_token' => $trackingToken,
                'razorpay_order_id' => $rzpOrder['id'],
                'razorpay_key' => $razorpay->getKeyId(),
                'amount_paise' => (int)round($cart['total'] * 100),
                'currency' => 'INR',
                'customer_name' => trim($data['name']),
                'customer_email' => strtolower(trim($data['email'])),
                'customer_phone' => preg_replace('/[^\d]/', '', $data['phone'])
            ];
        });
    }

    /**
     * Reconcile successful Razorpay payment (Signature Verified)
     */
    public static function reconcilePayment(
        string $razorpayOrderId,
        string $razorpayPaymentId,
        string $signature
    ): array {
        $razorpay = new RazorpayService();
        $isValid = $razorpay->verifyPaymentSignature($razorpayOrderId, $razorpayPaymentId, $signature);

        if (!$isValid) {
            throw new RuntimeException("Cryptographic payment signature verification failed.");
        }

        $payment = Database::fetchOne(
            "SELECT * FROM payments WHERE razorpay_order_id = :roid",
            ['roid' => $razorpayOrderId]
        );

        if (!$payment) {
            throw new RuntimeException("Payment record not found for Razorpay Order: {$razorpayOrderId}");
        }

        $orderId = (int)$payment['order_id'];
        $order = Database::fetchOne("SELECT * FROM orders WHERE id = :id", ['id' => $orderId]);

        if (!$order) {
            throw new RuntimeException("Associated order not found.");
        }

        // Idempotency: If already confirmed/captured, return success immediately
        if ($order['status'] !== 'pending' && $order['payment_status'] === 'captured') {
            return [
                'status' => 'success',
                'order_id' => $order['id'],
                'order_number' => $order['order_number'],
                'tracking_token' => $order['tracking_token']
            ];
        }

        // Execute payment capture and stock deduction inside transaction
        Database::transaction(function () use ($payment, $order, $razorpayPaymentId, $signature) {
            Database::update('payments', [
                'razorpay_payment_id' => $razorpayPaymentId,
                'razorpay_signature' => $signature,
                'status' => 'captured',
                'updated_at' => date('Y-m-d H:i:s')
            ], "id = :id", ['id' => $payment['id']]);

            Database::update('orders', [
                'status' => 'confirmed',
                'payment_status' => 'captured',
                'updated_at' => date('Y-m-d H:i:s')
            ], "id = :id", ['id' => $order['id']]);

            // Decrement Stock
            InventoryService::deductStockForOrder((int)$order['id']);

            // Record status history
            Database::insert('order_status_history', [
                'order_id' => $order['id'],
                'status' => 'confirmed',
                'comment' => "Payment confirmed via Razorpay (Payment ID: {$razorpayPaymentId})",
                'created_at' => date('Y-m-d H:i:s')
            ]);

            // Clear Cart
            CartService::clearCart();

            // Send Email
            MailerService::sendOrderConfirmation((int)$order['id']);
        });

        return [
            'status' => 'success',
            'order_id' => $order['id'],
            'order_number' => $order['order_number'],
            'tracking_token' => $order['tracking_token']
        ];
    }
}
