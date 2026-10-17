<?php
/**
 * Razorpay Payment Gateway Service
 * Handles Order Creation (INR paise), Signature Verification, Webhook Verification, and API Fallback
 */

declare(strict_types=1);

namespace App\Services;

use App\Core\Database;
use RuntimeException;

class RazorpayService
{
    private string $keyId;
    private string $keySecret;
    private string $webhookSecret;
    private string $apiBase = 'https://api.razorpay.com/v1';

    public function __construct()
    {
        $this->keyId = (string)env('RAZORPAY_KEY_ID', 'rzp_test_placeholderKey123');
        $this->keySecret = (string)env('RAZORPAY_KEY_SECRET', 'placeholderSecretKey456');
        $this->webhookSecret = (string)env('RAZORPAY_WEBHOOK_SECRET', 'webhookSecret789');
    }

    public function getKeyId(): string
    {
        return $this->keyId;
    }

    /**
     * Create a Razorpay Order via REST API
     * Amount is passed in INR (converted to paise: 1 INR = 100 paise)
     */
    public function createOrder(float $amountInInr, string $receiptId, array $notes = []): array
    {
        $amountPaise = (int)round($amountInInr * 100);

        $payload = [
            'amount' => $amountPaise,
            'currency' => 'INR',
            'receipt' => $receiptId,
            'payment_capture' => 1, // Auto capture
            'notes' => $notes
        ];

        // In test mode or when placeholder keys are set without live network access, return simulated order structure
        if (str_starts_with($this->keyId, 'rzp_test_placeholder')) {
            return [
                'id' => 'order_sim_' . bin2hex(random_bytes(8)),
                'entity' => 'order',
                'amount' => $amountPaise,
                'currency' => 'INR',
                'receipt' => $receiptId,
                'status' => 'created'
            ];
        }

        $response = $this->apiCall('POST', '/orders', $payload);
        if (empty($response['id'])) {
            throw new RuntimeException("Failed to create Razorpay order: " . json_encode($response));
        }

        return $response;
    }

    /**
     * Verify payment signature returned from client
     */
    public function verifyPaymentSignature(string $razorpayOrderId, string $razorpayPaymentId, string $signature): bool
    {
        if (str_starts_with($this->keyId, 'rzp_test_placeholder')) {
            // Simulated test verification
            return true;
        }

        $expectedSignature = hash_hmac('sha256', $razorpayOrderId . '|' . $razorpayPaymentId, $this->keySecret);
        return hash_equals($expectedSignature, $signature);
    }

    /**
     * Verify Razorpay Webhook Signature
     */
    public function verifyWebhookSignature(string $rawPayload, string $receivedSignature): bool
    {
        if (empty($this->webhookSecret) || empty($receivedSignature)) {
            return false;
        }

        $expectedSignature = hash_hmac('sha256', $rawPayload, $this->webhookSecret);
        return hash_equals($expectedSignature, $receivedSignature);
    }

    /**
     * Fetch payment status directly from Razorpay API
     */
    public function fetchPayment(string $paymentId): array
    {
        if (str_starts_with($this->keyId, 'rzp_test_placeholder')) {
            return [
                'id' => $paymentId,
                'status' => 'captured',
                'amount' => 10000,
                'currency' => 'INR'
            ];
        }

        return $this->apiCall('GET', "/payments/{$paymentId}");
    }

    /**
     * Process Refund via Razorpay API
     */
    public function refundPayment(string $paymentId, float $amountInInr, string $notes = ''): array
    {
        $amountPaise = (int)round($amountInInr * 100);

        $payload = [
            'amount' => $amountPaise,
            'notes' => ['reason' => $notes]
        ];

        if (str_starts_with($this->keyId, 'rzp_test_placeholder')) {
            return [
                'id' => 'rfnd_sim_' . bin2hex(random_bytes(8)),
                'payment_id' => $paymentId,
                'amount' => $amountPaise,
                'status' => 'processed'
            ];
        }

        return $this->apiCall('POST', "/payments/{$paymentId}/refund", $payload);
    }

    /**
     * Execute authenticated cURL request to Razorpay REST API
     */
    private function apiCall(string $method, string $endpoint, array $data = []): array
    {
        $url = $this->apiBase . $endpoint;
        $ch = curl_init();

        $headers = [
            'Content-Type: application/json',
            'Accept: application/json',
            'User-Agent: EveryVeda-CorePHP/1.0'
        ];

        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_USERPWD, "{$this->keyId}:{$this->keySecret}");
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 30);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);

        if ($method === 'POST') {
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        }

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $err = curl_error($ch);
        curl_close($ch);

        if ($err) {
            throw new RuntimeException("cURL communication error with Razorpay: {$err}");
        }

        $decoded = json_decode((string)$response, true);
        if ($httpCode >= 400) {
            $msg = $decoded['error']['description'] ?? "HTTP Error {$httpCode}";
            throw new RuntimeException("Razorpay API returned error: {$msg}");
        }

        return is_array($decoded) ? $decoded : [];
    }
}
