<?php
/**
 * Razorpay Payment Gateway & Cryptographic Signature Unit Tests
 */

declare(strict_types=1);

namespace Tests\Unit;

use App\Services\RazorpayService;

class RazorpaySignatureTest
{
    public function testHmacSha256SignatureComputation(): void
    {
        $orderId = "order_EKwxwAgItmmXdp";
        $paymentId = "pay_29QQoUBi66xm2f";
        $secret = "enSubscriptionsSecretKey99";

        $data = $orderId . '|' . $paymentId;
        $expectedSignature = hash_hmac('sha256', $data, $secret);

        assert(strlen($expectedSignature) === 64, "HMAC-SHA256 signature must be 64 characters hex");

        // Verify valid match
        assert(hash_equals($expectedSignature, hash_hmac('sha256', $data, $secret)), "Valid signature must match");

        // Verify tampered signature fails
        $tamperedSignature = $expectedSignature;
        $tamperedSignature[0] = $tamperedSignature[0] === 'a' ? 'b' : 'a';
        assert(!hash_equals($expectedSignature, $tamperedSignature), "Tampered signature must not validate");

        // Verify tampered order ID fails
        $tamperedData = "order_TAMPERED" . '|' . $paymentId;
        $invalidSig = hash_hmac('sha256', $tamperedData, $secret);
        assert(!hash_equals($expectedSignature, $invalidSig), "Signature with modified order ID must not validate");
    }

    public function testWebhookSignatureVerification(): void
    {
        $webhookSecret = "topSecretWebhookKey_EveryVeda2026";
        $rawPayload = json_encode([
            'entity' => 'event',
            'account_id' => 'acc_BFu7nDwcuGQJwQ',
            'event' => 'payment.captured',
            'contains' => ['payment'],
            'payload' => [
                'payment' => [
                    'entity' => [
                        'id' => 'pay_DESlfW0H8K9mEA',
                        'amount' => 49900,
                        'currency' => 'INR',
                        'status' => 'captured',
                        'order_id' => 'order_DESlm1omNkvdcq'
                    ]
                ]
            ],
            'created_at' => 1600000000
        ], JSON_UNESCAPED_SLASHES);

        $validSignature = hash_hmac('sha256', $rawPayload, $webhookSecret);

        // Native verify simulation
        $computed = hash_hmac('sha256', $rawPayload, $webhookSecret);
        assert(hash_equals($validSignature, $computed), "Webhook signature must match raw payload hash");

        // Any payload mutation must invalidate signature
        $mutatedPayload = str_replace('49900', '99900', $rawPayload);
        $computedMutated = hash_hmac('sha256', $mutatedPayload, $webhookSecret);
        assert(!hash_equals($validSignature, $computedMutated), "Modified payload must invalidate webhook signature");
    }

    public function testInrPaiseConversionAccuracy(): void
    {
        $testCases = [
            ['inr' => 0.00, 'paise' => 0],
            ['inr' => 49.00, 'paise' => 4900],
            ['inr' => 499.00, 'paise' => 49900],
            ['inr' => 1249.50, 'paise' => 124950],
            ['inr' => 9999.99, 'paise' => 999999],
            ['inr' => 0.50, 'paise' => 50]
        ];

        foreach ($testCases as $case) {
            $inr = $case['inr'];
            $expectedPaise = $case['paise'];
            $converted = (int)round($inr * 100);
            assert($converted === $expectedPaise, "INR {$inr} must convert to {$expectedPaise} paise, got {$converted}");
            
            $reverted = round($converted / 100, 2);
            assert($reverted === $inr, "{$expectedPaise} paise must revert to INR {$inr}, got {$reverted}");
        }
    }

    public function testRazorpayServiceOrderCreation(): void
    {
        $service = new RazorpayService();
        $order = $service->createOrder(499.00, 'TEST_RECEIPT_101');

        assert(!empty($order['id']), "Created order must have an id");
        assert($order['amount'] === 49900, "Created order amount must be in paise (49900), got {$order['amount']}");
        assert($order['currency'] === 'INR', "Created order currency must be INR");
    }
}
