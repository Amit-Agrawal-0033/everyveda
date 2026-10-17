<?php
/**
 * Shipping & PIN Code Delivery Service
 */

declare(strict_types=1);

namespace App\Services;

class ShippingService
{
    /**
     * Check if a given 6-digit Indian PIN code is serviceable
     */
    public static function checkPincode(string $pincode): array
    {
        $pincode = trim($pincode);
        if (!preg_match('/^[1-9]\d{5}$/', $pincode)) {
            return [
                'serviceable' => false,
                'message' => 'Please enter a valid 6-digit Indian PIN code.'
            ];
        }

        // Region prefix checks (India PIN prefixes 1 to 9)
        $firstDigit = (int)$pincode[0];
        $zone = 'Standard Delivery Zone';
        $estDays = '3 - 5 Business Days';

        if (in_array($firstDigit, [1, 2], true)) {
            $zone = 'North India';
            $estDays = '2 - 4 Business Days';
        } elseif (in_array($firstDigit, [3, 4], true)) {
            $zone = 'West India';
            $estDays = '3 - 5 Business Days';
        } elseif (in_array($firstDigit, [5, 6], true)) {
            $zone = 'South India';
            $estDays = '3 - 5 Business Days';
        } elseif (in_array($firstDigit, [7, 8], true)) {
            $zone = 'East & North-East India';
            $estDays = '4 - 7 Business Days';
        }

        return [
            'serviceable' => true,
            'pincode' => $pincode,
            'zone' => $zone,
            'estimated_days' => $estDays,
            'cod_available' => (bool)env('ENABLE_COD', true),
            'message' => "EveryVeda delivers to PIN code {$pincode} via Express Courier ({$estDays})."
        ];
    }

    /**
     * Calculate shipping charge for order subtotal
     */
    public static function calculateShipping(float $subtotal): float
    {
        $threshold = (float)env('SHIPPING_FREE_THRESHOLD', 499.00);
        $standard = (float)env('SHIPPING_STANDARD_FEE', 50.00);

        return $subtotal >= $threshold ? 0.00 : $standard;
    }
}
