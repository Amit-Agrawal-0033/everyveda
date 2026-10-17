<?php
/**
 * Tax Invoice Generation Service (GST Compliant)
 */

declare(strict_types=1);

namespace App\Services;

use App\Core\Database;

class InvoiceService
{
    /**
     * Generate HTML Tax Invoice printable format
     */
    public static function generateHtml(int $orderId): string
    {
        $order = Database::fetchOne("SELECT * FROM orders WHERE id = :id", ['id' => $orderId]);
        if (!$order) {
            throw new \RuntimeException("Order not found.");
        }

        $items = Database::fetchAll("SELECT * FROM order_items WHERE order_id = :oid", ['oid' => $orderId]);

        $settingsRows = Database::fetchAll("SELECT `key`, `value` FROM settings");
        $settings = array_column($settingsRows, 'value', 'key');

        $taxBreakdown = TaxService::calculateTax(
            (float)$order['subtotal'] - (float)$order['discount_amount'],
            $order['shipping_state'],
            12.00
        );

        ob_start();
        ?>
        <!DOCTYPE html>
        <html lang="en">
        <head>
            <meta charset="UTF-8">
            <title>Tax Invoice - <?= e($order['order_number']) ?> - EveryVeda</title>
            <style>
                body { font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif; color: #26332B; margin: 0; padding: 20px; font-size: 13px; line-height: 1.5; }
                .invoice-box { max-width: 800px; margin: auto; padding: 30px; border: 1px solid #eee; background: #fff; }
                .header-table { width: 100%; border-bottom: 2px solid #214D36; padding-bottom: 20px; margin-bottom: 20px; }
                .brand-title { color: #214D36; font-size: 26px; font-weight: bold; margin: 0; }
                .brand-tagline { color: #477A52; font-size: 12px; margin: 2px 0 0; }
                .info-table { width: 100%; margin-bottom: 20px; }
                .info-table td { vertical-align: top; width: 50%; }
                .items-table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
                .items-table th { background: #214D36; color: #fff; padding: 8px 10px; text-align: left; font-size: 12px; }
                .items-table td { padding: 8px 10px; border-bottom: 1px solid #ddd; }
                .totals-table { width: 350px; margin-left: auto; border-collapse: collapse; }
                .totals-table td { padding: 5px 8px; }
                .totals-table .grand-total { font-size: 15px; font-weight: bold; color: #214D36; border-top: 2px solid #214D36; }
                .text-right { text-align: right; }
                .text-center { text-align: center; }
                .print-btn-bar { text-align: center; margin-bottom: 20px; }
                .btn-print { background: #214D36; color: #fff; border: none; padding: 8px 20px; border-radius: 4px; cursor: pointer; font-size: 14px; }
                @media print {
                    .print-btn-bar { display: none; }
                    .invoice-box { border: none; padding: 0; }
                }
            </style>
        </head>
        <body>
            <div class="print-btn-bar">
                <button class="btn-print" onclick="window.print()">🖨️ Print / Save as PDF</button>
            </div>
            <div class="invoice-box">
                <table class="header-table">
                    <tr>
                        <td>
                            <h1 class="brand-title">EveryVeda</h1>
                            <p class="brand-tagline">Pure Ayurvedic & Wellness Living</p>
                            <p style="margin: 5px 0 0; font-size: 11px; color: #666;">
                                <?= e($settings['company_name'] ?? 'EveryVeda Organics Pvt Ltd') ?><br>
                                <?= e($settings['company_address'] ?? 'Sector 62, Noida, Uttar Pradesh, 201309') ?><br>
                                <strong>GSTIN:</strong> <?= e($settings['gstin'] ?? '07AAAAE1234P1Z5') ?> | <strong>FSSAI:</strong> <?= e($settings['fssai_license'] ?? '12723999000123') ?>
                            </p>
                        </td>
                        <td class="text-right" style="vertical-align: top;">
                            <h2 style="margin: 0; color: #214D36;">TAX INVOICE</h2>
                            <p style="margin: 5px 0 0;">
                                <strong>Invoice No:</strong> INV-<?= e($order['order_number']) ?><br>
                                <strong>Order Date:</strong> <?= date('d M Y, h:i A', strtotime($order['created_at'])) ?><br>
                                <strong>Payment Method:</strong> <?= strtoupper($order['payment_method']) ?><br>
                                <strong>Payment Status:</strong> <?= strtoupper($order['payment_status']) ?>
                            </p>
                        </td>
                    </tr>
                </table>

                <table class="info-table">
                    <tr>
                        <td>
                            <strong>Bill To:</strong><br>
                            <?= e($order['customer_name']) ?><br>
                            <?= e($order['billing_address_line1']) ?><br>
                            <?php if (!empty($order['billing_address_line2'])): ?>
                                <?= e($order['billing_address_line2']) ?><br>
                            <?php endif; ?>
                            <?= e($order['billing_city']) ?>, <?= e($order['billing_state']) ?> - <?= e($order['billing_pincode']) ?><br>
                            Phone: <?= e($order['customer_phone']) ?><br>
                            Email: <?= e($order['customer_email']) ?>
                        </td>
                        <td>
                            <strong>Ship To:</strong><br>
                            <?= e($order['customer_name']) ?><br>
                            <?= e($order['shipping_address_line1']) ?><br>
                            <?php if (!empty($order['shipping_address_line2'])): ?>
                                <?= e($order['shipping_address_line2']) ?><br>
                            <?php endif; ?>
                            <?= e($order['shipping_city']) ?>, <?= e($order['shipping_state']) ?> - <?= e($order['shipping_pincode']) ?><br>
                            Phone: <?= e($order['customer_phone']) ?>
                        </td>
                    </tr>
                </table>

                <table class="items-table">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Description of Ayurvedic Goods</th>
                            <th>SKU</th>
                            <th class="text-center">Qty</th>
                            <th class="text-right">Unit Price</th>
                            <th class="text-right">GST %</th>
                            <th class="text-right">Total Amount</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($items as $idx => $item): ?>
                            <tr>
                                <td><?= $idx + 1 ?></td>
                                <td>
                                    <strong><?= e($item['product_name_en']) ?></strong>
                                    <?php if (!empty($item['variant_name_en'])): ?>
                                        <br><small style="color: #666;"><?= e($item['variant_name_en']) ?></small>
                                    <?php endif; ?>
                                </td>
                                <td><code><?= e($item['sku']) ?></code></td>
                                <td class="text-center"><?= $item['quantity'] ?></td>
                                <td class="text-right">₹<?= number_format((float)$item['unit_price'], 2) ?></td>
                                <td class="text-right"><?= number_format((float)$item['tax_rate'], 1) ?>%</td>
                                <td class="text-right">₹<?= number_format((float)$item['total_price'], 2) ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>

                <table class="totals-table">
                    <tr>
                        <td>Subtotal:</td>
                        <td class="text-right">₹<?= number_format((float)$order['subtotal'], 2) ?></td>
                    </tr>
                    <?php if ((float)$order['discount_amount'] > 0): ?>
                        <tr>
                            <td>Discount (<?= e($order['coupon_code'] ?? 'Coupon') ?>):</td>
                            <td class="text-right" style="color: #477A52;">-₹<?= number_format((float)$order['discount_amount'], 2) ?></td>
                        </tr>
                    <?php endif; ?>
                    <tr>
                        <td>Shipping Charges:</td>
                        <td class="text-right"><?= (float)$order['shipping_fee'] == 0 ? 'FREE' : '₹' . number_format((float)$order['shipping_fee'], 2) ?></td>
                    </tr>
                    <?php if ($taxBreakdown['type'] === 'intrastate'): ?>
                        <tr>
                            <td>CGST (<?= $taxBreakdown['cgst_rate'] ?>%):</td>
                            <td class="text-right">₹<?= number_format($taxBreakdown['cgst_amount'], 2) ?></td>
                        </tr>
                        <tr>
                            <td>SGST (<?= $taxBreakdown['sgst_rate'] ?>%):</td>
                            <td class="text-right">₹<?= number_format($taxBreakdown['sgst_amount'], 2) ?></td>
                        </tr>
                    <?php else: ?>
                        <tr>
                            <td>IGST (<?= $taxBreakdown['igst_rate'] ?>%):</td>
                            <td class="text-right">₹<?= number_format($taxBreakdown['igst_amount'], 2) ?></td>
                        </tr>
                    <?php endif; ?>
                    <tr class="grand-total">
                        <td>Grand Total:</td>
                        <td class="text-right">₹<?= number_format((float)$order['total_amount'], 2) ?></td>
                    </tr>
                </table>

                <div style="margin-top: 40px; padding-top: 15px; border-top: 1px solid #eee; font-size: 11px; color: #777;">
                    <p><strong>Declaration:</strong> This is a computer generated invoice and does not require a physical signature. Goods once sold are covered under our 7-day replacement policy for manufacturing transit defects.</p>
                </div>
            </div>
        </body>
        </html>
        <?php
        return ob_get_clean() ?: '';
    }
}
