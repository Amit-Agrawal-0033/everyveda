<?php
/**
 * EveryVeda Centralized Server-Side Pricing & GST Calculation Engine
 * 
 * Compliant with:
 * - Indian GST Act (CGST / SGST Intrastate vs IGST Interstate)
 * - Legal Metrology (Packaged Commodities) Rules for Consumer MRP (Tax-Inclusive)
 * - Configurable Tax-Inclusive / Tax-Exclusive Mode
 * - Granular per-item GST rates (5%, 12%, 18%) and HSN codes
 * - Proportionate coupon discount distribution across taxable line items
 */

declare(strict_types=1);

namespace App\Services;

use App\Core\Database;

class PricingEngine
{
    public const SELLER_STATE = 'Uttar Pradesh';

    /**
     * Compute complete pricing breakdown
     *
     * @param array $items Line items: [['unit_price' => float, 'quantity' => int, 'tax_rate' => float, ...]]
     * @param string|null $couponCode Optional promo coupon code
     * @param string|null $destinationState Destination shipping state (e.g. 'Uttar Pradesh', 'Maharashtra')
     * @param int|null $userId Optional authenticated customer ID for per-user limits
     * @return array Complete pricing breakdown
     */
    public static function calculate(
        array $items,
        ?string $couponCode = null,
        ?string $destinationState = null,
        ?int $userId = null
    ): array {
        // 1. Fetch Tax and Shipping Configurations
        $settings = self::getSettings();
        $pricesIncludeTax = (bool)($settings['prices_include_tax'] ?? true);
        $shippingThreshold = (float)($settings['shipping_free_threshold'] ?? 499.00);
        $standardShippingFee = (float)($settings['shipping_fee_standard'] ?? 50.00);
        $sellerState = (string)($settings['seller_state'] ?? self::SELLER_STATE);

        // 2. Compute Gross Subtotal
        $subtotal = 0.0;
        $totalQuantity = 0;
        $totalWeightGrams = 0;

        foreach ($items as $idx => $item) {
            $unitPrice = round((float)$item['unit_price'], 2);
            $qty = max(1, (int)$item['quantity']);
            $lineGross = round($unitPrice * $qty, 2);

            $items[$idx]['unit_price'] = $unitPrice;
            $items[$idx]['quantity'] = $qty;
            $items[$idx]['line_gross'] = $lineGross;
            $items[$idx]['tax_rate'] = isset($item['tax_rate']) ? (float)$item['tax_rate'] : 12.00;
            $items[$idx]['hsn_code'] = $item['hsn_code'] ?? '30049011';

            $subtotal += $lineGross;
            $totalQuantity += $qty;
            $totalWeightGrams += ((int)($item['weight_grams'] ?? 250)) * $qty;
        }

        $subtotal = round($subtotal, 2);

        // 3. Coupon Discount Validation & Calculation
        $discountAmount = 0.0;
        $couponData = null;
        $validatedCouponCode = null;

        if (!empty($couponCode) && $subtotal > 0) {
            $couponResult = CouponService::validateCoupon($couponCode, $subtotal, $userId);
            if ($couponResult['valid']) {
                $discountAmount = min($subtotal, round((float)$couponResult['discount'], 2));
                $couponData = $couponResult['coupon'];
                $validatedCouponCode = strtoupper(trim($couponCode));
            }
        }

        // 4. Distribute Discount Proportionately across Items
        $remainingDiscount = $discountAmount;
        $itemCount = count($items);

        foreach ($items as $idx => $item) {
            if ($subtotal > 0 && $discountAmount > 0) {
                if ($idx === $itemCount - 1) {
                    // Last item absorbs remaining rounding difference
                    $itemDiscount = $remainingDiscount;
                } else {
                    $itemDiscount = round(($item['line_gross'] / $subtotal) * $discountAmount, 2);
                    $itemDiscount = min($remainingDiscount, $itemDiscount);
                    $remainingDiscount -= $itemDiscount;
                }
            } else {
                $itemDiscount = 0.0;
            }

            $lineNet = max(0.0, round($item['line_gross'] - $itemDiscount, 2));
            $items[$idx]['discount_amount'] = $itemDiscount;
            $items[$idx]['line_net'] = $lineNet;
        }

        // 5. Determine Transaction Tax Nature (Intrastate vs Interstate)
        $stateToCompare = !empty($destinationState) ? trim($destinationState) : $sellerState;
        $isIntrastate = strcasecmp($stateToCompare, $sellerState) === 0;
        $taxType = $isIntrastate ? 'intrastate' : 'interstate';

        // 6. Compute Granular GST (per item according to tax_rate)
        $totalTaxableAmount = 0.0;
        $totalTaxAmount = 0.0;
        $totalCgstAmount = 0.0;
        $totalSgstAmount = 0.0;
        $totalIgstAmount = 0.0;
        $taxSummaryByRate = [];

        foreach ($items as $idx => $item) {
            $rate = (float)$item['tax_rate'];
            $lineNet = (float)$item['line_net'];

            if ($pricesIncludeTax) {
                // MRP includes GST: Base = Net / (1 + Rate / 100)
                $itemTaxable = round($lineNet / (1 + ($rate / 100)), 2);
                $itemTax = round($lineNet - $itemTaxable, 2);
            } else {
                // Tax-Exclusive: Base = Net, Tax = Base * (Rate / 100)
                $itemTaxable = $lineNet;
                $itemTax = round($itemTaxable * ($rate / 100), 2);
            }

            if ($isIntrastate) {
                $cgstRate = round($rate / 2, 2);
                $sgstRate = round($rate / 2, 2);
                $cgstAmount = round($itemTax / 2, 2);
                $sgstAmount = round($itemTax - $cgstAmount, 2);
                $igstRate = 0.0;
                $igstAmount = 0.0;
            } else {
                $cgstRate = 0.0;
                $sgstRate = 0.0;
                $cgstAmount = 0.0;
                $sgstAmount = 0.0;
                $igstRate = $rate;
                $igstAmount = $itemTax;
            }

            $items[$idx]['taxable_amount'] = $itemTaxable;
            $items[$idx]['tax_amount'] = $itemTax;
            $items[$idx]['cgst_rate'] = $cgstRate;
            $items[$idx]['cgst_amount'] = $cgstAmount;
            $items[$idx]['sgst_rate'] = $sgstRate;
            $items[$idx]['sgst_amount'] = $sgstAmount;
            $items[$idx]['igst_rate'] = $igstRate;
            $items[$idx]['igst_amount'] = $igstAmount;

            $totalTaxableAmount += $itemTaxable;
            $totalTaxAmount += $itemTax;
            $totalCgstAmount += $cgstAmount;
            $totalSgstAmount += $sgstAmount;
            $totalIgstAmount += $igstAmount;

            // Group summary by tax rate
            $rateKey = number_format($rate, 1) . '%';
            if (!isset($taxSummaryByRate[$rateKey])) {
                $taxSummaryByRate[$rateKey] = [
                    'rate' => $rate,
                    'taxable_amount' => 0.0,
                    'tax_amount' => 0.0,
                    'cgst_amount' => 0.0,
                    'sgst_amount' => 0.0,
                    'igst_amount' => 0.0,
                ];
            }
            $taxSummaryByRate[$rateKey]['taxable_amount'] += $itemTaxable;
            $taxSummaryByRate[$rateKey]['tax_amount'] += $itemTax;
            $taxSummaryByRate[$rateKey]['cgst_amount'] += $cgstAmount;
            $taxSummaryByRate[$rateKey]['sgst_amount'] += $sgstAmount;
            $taxSummaryByRate[$rateKey]['igst_amount'] += $igstAmount;
        }

        $totalTaxableAmount = round($totalTaxableAmount, 2);
        $totalTaxAmount = round($totalTaxAmount, 2);
        $totalCgstAmount = round($totalCgstAmount, 2);
        $totalSgstAmount = round($totalSgstAmount, 2);
        $totalIgstAmount = round($totalIgstAmount, 2);

        // 7. Calculate Delivery / Shipping Fee
        if ($totalQuantity === 0 || $subtotal >= $shippingThreshold) {
            $shippingFee = 0.00;
        } else {
            $shippingFee = $standardShippingFee;
        }

        // 8. Calculate Final Payable Amount
        if ($pricesIncludeTax) {
            // Net product amount already contains GST; add shipping only
            $finalTotal = round(max(0, $subtotal - $discountAmount) + $shippingFee, 2);
        } else {
            // Tax-Exclusive: Base Net + Tax + Shipping
            $finalTotal = round($totalTaxableAmount + $totalTaxAmount + $shippingFee, 2);
        }

        return [
            'items' => $items,
            'count' => $totalQuantity,
            'weight_grams' => $totalWeightGrams,
            'subtotal' => $subtotal,
            'discount' => $discountAmount,
            'coupon_code' => $validatedCouponCode,
            'coupon' => $couponData,
            'prices_include_tax' => $pricesIncludeTax,
            'tax_type' => $taxType,
            'destination_state' => $stateToCompare,
            'seller_state' => $sellerState,
            'taxable_amount' => $totalTaxableAmount,
            'tax_amount' => $totalTaxAmount,
            'cgst_amount' => $totalCgstAmount,
            'sgst_amount' => $totalSgstAmount,
            'igst_amount' => $totalIgstAmount,
            'tax_summary' => $taxSummaryByRate,
            'shipping_fee' => $shippingFee,
            'shipping_threshold' => $shippingThreshold,
            'free_shipping_remaining' => max(0.0, round($shippingThreshold - $subtotal, 2)),
            'total' => $finalTotal
        ];
    }

    /**
     * Cached settings helper
     */
    private static function getSettings(): array
    {
        static $settings = null;
        if ($settings === null) {
            try {
                $rows = Database::fetchAll("SELECT `key`, `value` FROM settings");
                $settings = array_column($rows, 'value', 'key');
            } catch (\Throwable $e) {
                $settings = [];
            }
        }
        return $settings;
    }
}
