<?php
/**
 * Coupon & Discount Validation Service
 */

declare(strict_types=1);

namespace App\Services;

use App\Core\Database;

class CouponService
{
    /**
     * Validate coupon code against subtotal and user
     */
    public static function validateCoupon(string $code, float $subtotal, ?int $userId = null): array
    {
        $code = strtoupper(trim($code));
        $now = date('Y-m-d H:i:s');

        $coupon = Database::fetchOne(
            "SELECT * FROM coupons WHERE code = :code AND is_active = 1",
            ['code' => $code]
        );

        if (!$coupon) {
            return ['valid' => false, 'message' => 'Invalid promotional coupon code.'];
        }

        // Date validity check
        if (!empty($coupon['start_date']) && $now < $coupon['start_date']) {
            return ['valid' => false, 'message' => 'This coupon offer has not started yet.'];
        }
        if (!empty($coupon['end_date']) && $now > $coupon['end_date']) {
            return ['valid' => false, 'message' => 'This coupon has expired.'];
        }

        // Global usage limit check
        if ($coupon['usage_limit_global'] !== null && (int)$coupon['used_count'] >= (int)$coupon['usage_limit_global']) {
            return ['valid' => false, 'message' => 'This coupon has reached its maximum total usage limit.'];
        }

        // Per user usage check
        if ($userId && $coupon['usage_limit_per_user'] > 0) {
            $userRedemptions = Database::fetchColumn(
                "SELECT COUNT(*) FROM coupon_redemptions WHERE coupon_id = :cid AND user_id = :uid",
                ['cid' => $coupon['id'], 'uid' => $userId]
            );
            if ((int)$userRedemptions >= (int)$coupon['usage_limit_per_user']) {
                return ['valid' => false, 'message' => 'You have already reached the maximum usage limit for this coupon.'];
            }
        }

        // Minimum order spend check
        $minSpend = (float)$coupon['min_order_amount'];
        if ($subtotal < $minSpend) {
            return [
                'valid' => false,
                'message' => "This coupon requires a minimum cart value of ₹" . number_format($minSpend, 2) . "."
            ];
        }

        // Calculate discount
        $discount = 0.0;
        if ($coupon['discount_type'] === 'percentage') {
            $discount = round(($subtotal * (float)$coupon['discount_value']) / 100, 2);
            if (!empty($coupon['max_discount_amount']) && (float)$coupon['max_discount_amount'] > 0) {
                $discount = min($discount, (float)$coupon['max_discount_amount']);
            }
        } else {
            // Fixed discount
            $discount = min($subtotal, (float)$coupon['discount_value']);
        }

        return [
            'valid' => true,
            'coupon' => $coupon,
            'discount' => round($discount, 2),
            'message' => 'Coupon applied successfully!'
        ];
    }

    /**
     * Record coupon redemption inside an active transaction
     */
    public static function recordRedemption(int $couponId, ?int $userId, int $orderId, float $discountAmount): void
    {
        Database::insert('coupon_redemptions', [
            'coupon_id' => $couponId,
            'user_id' => $userId,
            'order_id' => $orderId,
            'discount_amount' => $discountAmount,
            'redeemed_at' => date('Y-m-d H:i:s')
        ]);

        Database::query(
            "UPDATE coupons SET used_count = used_count + 1 WHERE id = :id",
            ['id' => $couponId]
        );
    }
}
