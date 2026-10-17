<?php
/**
 * Cart & Order Financial Calculation Unit Tests
 */

declare(strict_types=1);

namespace Tests\Unit;

use App\Services\TaxService;
use App\Services\ShippingService;
use App\Services\CouponService;

class CartTest
{
    public function testShippingFeeCalculation(): void
    {
        // Default threshold is 499.00
        $feeBelow = ShippingService::calculateShipping(299.00);
        assert($feeBelow > 0, "Subtotal below free threshold must incur shipping fee, got {$feeBelow}");

        $feeAtThreshold = ShippingService::calculateShipping(499.00);
        assert($feeAtThreshold === 0.0, "Subtotal equal to free threshold must have free shipping, got {$feeAtThreshold}");

        $feeAbove = ShippingService::calculateShipping(999.00);
        assert($feeAbove === 0.0, "Subtotal above free threshold must have free shipping, got {$feeAbove}");
    }

    public function testPincodeValidationAndServiceability(): void
    {
        // Valid 6-digit Indian PIN codes
        $validPincodes = ['110001', '400001', '560001', '700001', '201301'];
        foreach ($validPincodes as $pin) {
            $result = ShippingService::checkPincode($pin);
            assert($result['serviceable'] === true, "PIN code {$pin} must be serviceable");
            assert(!empty($result['zone']), "PIN code {$pin} must have a determined delivery zone");
            assert(!empty($result['estimated_days']), "PIN code {$pin} must have estimated delivery days");
        }

        // Invalid PIN codes
        $invalidPincodes = ['012345', '12345', '1234567', 'ABCDEF', '11000A'];
        foreach ($invalidPincodes as $badPin) {
            $result = ShippingService::checkPincode($badPin);
            assert($result['serviceable'] === false, "Invalid PIN code {$badPin} must not be serviceable");
        }
    }

    public function testIntrastateGstBreakdown(): void
    {
        // For seller state Uttar Pradesh (12% tax inclusive)
        $taxable = 1120.00;
        $result = TaxService::calculateTax($taxable, 'Uttar Pradesh', 12.00);

        assert($result['type'] === 'intrastate', "Seller state match must produce intrastate tax");
        assert($result['cgst_rate'] === 6.0, "Intrastate CGST rate must be 6.0%");
        assert($result['sgst_rate'] === 6.0, "Intrastate SGST rate must be 6.0%");
        assert($result['igst_rate'] === 0.0, "Intrastate IGST rate must be 0%");
        assert($result['igst_amount'] === 0.0, "Intrastate IGST amount must be 0");

        // Tax inclusive math: Base = 1120 / 1.12 = 1000. Total Tax = 120 (CGST 60 + SGST 60)
        assert($result['base_amount'] === 1000.00, "Base amount for 1120 at 12% must be 1000.00, got {$result['base_amount']}");
        assert($result['total_tax'] === 120.00, "Total tax for 1120 at 12% must be 120.00, got {$result['total_tax']}");
        assert($result['cgst_amount'] === 60.00, "CGST must be 60.00");
        assert($result['sgst_amount'] === 60.00, "SGST must be 60.00");
    }

    public function testInterstateGstBreakdown(): void
    {
        // For interstate buyers (e.g. Maharashtra)
        $taxable = 1120.00;
        $result = TaxService::calculateTax($taxable, 'Maharashtra', 12.00);

        assert($result['type'] === 'interstate', "Interstate buyer must produce interstate tax");
        assert($result['cgst_rate'] === 0.0, "Interstate CGST rate must be 0%");
        assert($result['sgst_rate'] === 0.0, "Interstate SGST rate must be 0%");
        assert($result['igst_rate'] === 12.0, "Interstate IGST rate must be 12.0%");
        assert($result['cgst_amount'] === 0.0, "Interstate CGST amount must be 0");
        assert($result['sgst_amount'] === 0.0, "Interstate SGST amount must be 0");
        assert($result['igst_amount'] === 120.00, "Interstate IGST amount must be 120.00");
        assert($result['total_tax'] === 120.00, "Total tax must be 120.00");
    }

    public function testCouponValidationRules(): void
    {
        // Non-existent coupon
        $invalidRes = CouponService::validateCoupon('NONEXISTENT_CODE', 1000.00);
        assert($invalidRes['valid'] === false, "Invalid code must fail validation");

        // Active seeded percentage coupon VEDA10 (10% off, min order 499)
        $validRes = CouponService::validateCoupon('VEDA10', 1000.00);
        assert($validRes['valid'] === true, "VEDA10 on 1000 order must be valid");
        assert($validRes['discount'] === 100.00, "10% of 1000 must be 100.00 discount, got {$validRes['discount']}");

        // Subtotal below minimum order requirement for VEDA10 (min 499)
        $belowMinRes = CouponService::validateCoupon('VEDA10', 250.00);
        assert($belowMinRes['valid'] === false, "Order below min spend threshold must be rejected");

        // Active seeded fixed coupon WELCOME100 (₹100 off, min order 999)
        $fixedRes = CouponService::validateCoupon('WELCOME100', 1200.00);
        assert($fixedRes['valid'] === true, "WELCOME100 on 1200 order must be valid");
        assert($fixedRes['discount'] === 100.00, "WELCOME100 discount must be 100.00, got {$fixedRes['discount']}");
    }
}
