<?php
/**
 * Indian GST Tax Calculation Service
 */

declare(strict_types=1);

namespace App\Services;

class TaxService
{
    private const SELLER_STATE = 'Uttar Pradesh';

    /**
     * Compute tax breakdown for a taxable amount and destination state
     */
    public static function calculateTax(float $taxableAmount, string $destinationState, float $rate = 12.00): array
    {
        $taxableAmount = max(0, $taxableAmount);
        
        // India consumer MRP includes GST: Base = TaxableAmount / (1 + Rate/100)
        $baseAmount = round($taxableAmount / (1 + ($rate / 100)), 2);
        $totalTax = round($taxableAmount - $baseAmount, 2);

        $isSameState = strcasecmp(trim($destinationState), self::SELLER_STATE) === 0;

        if ($isSameState) {
            $halfRate = round($rate / 2, 2);
            $halfTax = round($totalTax / 2, 2);
            return [
                'type' => 'intrastate',
                'cgst_rate' => $halfRate,
                'cgst_amount' => $halfTax,
                'sgst_rate' => $halfRate,
                'sgst_amount' => round($totalTax - $halfTax, 2),
                'igst_rate' => 0.0,
                'igst_amount' => 0.0,
                'total_tax' => $totalTax,
                'base_amount' => $baseAmount
            ];
        }

        return [
            'type' => 'interstate',
            'cgst_rate' => 0.0,
            'cgst_amount' => 0.0,
            'sgst_rate' => 0.0,
            'sgst_amount' => 0.0,
            'igst_rate' => $rate,
            'igst_amount' => $totalTax,
            'total_tax' => $totalTax,
            'base_amount' => $baseAmount
        ];
    }
}
