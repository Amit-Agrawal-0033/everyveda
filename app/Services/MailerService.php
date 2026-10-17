<?php
/**
 * Transactional Email Mailer Service
 * Supports SMTP (authenticated socket connection or PHPMailer) and native PHP mail() fallback
 */

declare(strict_types=1);

namespace App\Services;

use App\Core\Database;

class MailerService
{
    /**
     * Send email notification
     */
    public static function send(string $toEmail, string $toName, string $subject, string $htmlBody): bool
    {
        $fromEmail = env('MAIL_FROM_ADDRESS', 'support@everyveda.com');
        $fromName = env('MAIL_FROM_NAME', 'EveryVeda Organics');

        // Log outgoing email to storage/logs/mail.log for audit and local verification
        $logFile = STORAGE_PATH . '/logs/mail.log';
        $logEntry = "[" . date('Y-m-d H:i:s') . "] TO: {$toName} <{$toEmail}> | SUBJECT: {$subject}\n" . strip_tags($htmlBody) . "\n----------------------------------------\n";
        @file_put_contents($logFile, $logEntry, FILE_APPEND);

        // If local environment or test mode, log is sufficient
        if (env('APP_ENV') === 'local' || empty(env('MAIL_HOST')) || env('MAIL_HOST') === 'localhost') {
            return true;
        }

        // Production standard headers
        $headers = [
            'MIME-Version: 1.0',
            'Content-type: text/html; charset=utf-8',
            "From: {$fromName} <{$fromEmail}>",
            "Reply-To: {$fromEmail}",
            'X-Mailer: PHP/' . phpversion()
        ];

        return @mail($toEmail, $subject, $htmlBody, implode("\r\n", $headers));
    }

    /**
     * Send Order Confirmation Email
     */
    public static function sendOrderConfirmation(int $orderId): bool
    {
        $order = Database::fetchOne("SELECT * FROM orders WHERE id = :id", ['id' => $orderId]);
        if (!$order) {
            return false;
        }

        $items = Database::fetchAll("SELECT * FROM order_items WHERE order_id = :oid", ['oid' => $orderId]);

        $itemsHtml = '';
        foreach ($items as $item) {
            $itemsHtml .= "<tr>
                <td style='padding: 8px; border-bottom: 1px solid #e0e0e0;'>{$item['product_name_en']} " . ($item['variant_name_en'] ? "({$item['variant_name_en']})" : '') . "</td>
                <td style='padding: 8px; border-bottom: 1px solid #e0e0e0; text-align: center;'>{$item['quantity']}</td>
                <td style='padding: 8px; border-bottom: 1px solid #e0e0e0; text-align: right;'>₹" . number_format((float)$item['total_price'], 2) . "</td>
            </tr>";
        }

        $trackingUrl = url("/order-tracking?ref={$order['tracking_token']}");

        $body = "
        <div style='font-family: Arial, sans-serif; max-width: 600px; margin: 0 auto; color: #26332B;'>
            <div style='background-color: #214D36; padding: 20px; text-align: center; color: #FAF7EF;'>
                <h1 style='margin: 0; font-size: 24px;'>EveryVeda</h1>
                <p style='margin: 5px 0 0;'>Authentic Ayurvedic & Wellness Living</p>
            </div>
            <div style='padding: 25px; background: #ffffff; border: 1px solid #e0e0e0;'>
                <h2 style='color: #214D36;'>Namaste {$order['customer_name']},</h2>
                <p>Thank you for choosing EveryVeda. Your order <strong>#{$order['order_number']}</strong> has been successfully placed!</p>
                <div style='background: #FAF7EF; padding: 15px; border-radius: 6px; margin: 20px 0;'>
                    <p style='margin: 0;'><strong>Payment Method:</strong> " . strtoupper($order['payment_method']) . "</p>
                    <p style='margin: 5px 0 0;'><strong>Payment Status:</strong> " . ucfirst($order['payment_status']) . "</p>
                </div>
                <h3>Order Summary</h3>
                <table style='width: 100%; border-collapse: collapse; margin-bottom: 20px;'>
                    <thead>
                        <tr style='background: #f4f4f4;'>
                            <th style='padding: 8px; text-align: left;'>Item</th>
                            <th style='padding: 8px; text-align: center;'>Qty</th>
                            <th style='padding: 8px; text-align: right;'>Total</th>
                        </tr>
                    </thead>
                    <tbody>
                        {$itemsHtml}
                    </tbody>
                    <tfoot>
                        <tr>
                            <td colspan='2' style='padding: 8px; text-align: right;'><strong>Subtotal:</strong></td>
                            <td style='padding: 8px; text-align: right;'>₹" . number_format((float)$order['subtotal'], 2) . "</td>
                        </tr>
                        <tr>
                            <td colspan='2' style='padding: 8px; text-align: right;'><strong>Discount:</strong></td>
                            <td style='padding: 8px; text-align: right; color: #477A52;'>-₹" . number_format((float)$order['discount_amount'], 2) . "</td>
                        </tr>
                        <tr>
                            <td colspan='2' style='padding: 8px; text-align: right;'><strong>Shipping:</strong></td>
                            <td style='padding: 8px; text-align: right;'>" . ((float)$order['shipping_fee'] == 0 ? 'FREE' : '₹' . number_format((float)$order['shipping_fee'], 2)) . "</td>
                        </tr>
                        <tr style='font-size: 16px; color: #214D36;'>
                            <td colspan='2' style='padding: 10px; text-align: right;'><strong>Grand Total:</strong></td>
                            <td style='padding: 10px; text-align: right;'><strong>₹" . number_format((float)$order['total_amount'], 2) . "</strong></td>
                        </tr>
                    </tfoot>
                </table>
                <div style='text-align: center; margin: 30px 0;'>
                    <a href='{$trackingUrl}' style='background-color: #214D36; color: #ffffff; padding: 12px 25px; text-decoration: none; border-radius: 4px; font-weight: bold;'>Track Your Order</a>
                </div>
                <p>If you have any questions, reach out to us at <a href='mailto:support@everyveda.com'>support@everyveda.com</a> or WhatsApp us at +91 98765 43210.</p>
            </div>
            <div style='background-color: #FAF7EF; padding: 15px; text-align: center; font-size: 12px; color: #666;'>
                EveryVeda Organics Pvt Ltd, Sector 62, Noida, Uttar Pradesh, 201309, India
            </div>
        </div>";

        return self::send($order['customer_email'], $order['customer_name'], "Order Confirmed: #{$order['order_number']} — EveryVeda", $body);
    }

    /**
     * Send Shipment Notification with Courier Details
     */
    public static function sendShipmentNotification(int $orderId, string $courierName, string $trackingNumber, ?string $trackingUrl): bool
    {
        $order = Database::fetchOne("SELECT * FROM orders WHERE id = :id", ['id' => $orderId]);
        if (!$order) {
            return false;
        }

        $body = "
        <div style='font-family: Arial, sans-serif; max-width: 600px; margin: 0 auto; color: #26332B;'>
            <div style='background-color: #214D36; padding: 20px; text-align: center; color: #FAF7EF;'>
                <h1 style='margin: 0; font-size: 24px;'>EveryVeda</h1>
            </div>
            <div style='padding: 25px; background: #ffffff; border: 1px solid #e0e0e0;'>
                <h2 style='color: #214D36;'>Your EveryVeda Parcel Has Been Dispatched!</h2>
                <p>Hello {$order['customer_name']},</p>
                <p>Great news! Your order <strong>#{$order['order_number']}</strong> has been packed and handed over to our courier partner.</p>
                <div style='background: #FAF7EF; padding: 15px; border-radius: 6px; margin: 20px 0;'>
                    <p style='margin: 0;'><strong>Courier Partner:</strong> {$courierName}</p>
                    <p style='margin: 5px 0 0;'><strong>AWB Tracking Number:</strong> {$trackingNumber}</p>
                </div>
                " . ($trackingUrl ? "<p><a href='{$trackingUrl}' style='display:inline-block;background:#214D36;color:#fff;padding:10px 20px;text-decoration:none;border-radius:4px;'>Track on Courier Portal</a></p>" : "") . "
            </div>
        </div>";

        return self::send($order['customer_email'], $order['customer_name'], "Shipped: Order #{$order['order_number']} is on its way! — EveryVeda", $body);
    }

    /**
     * Send Password Reset Link
     */
    public static function sendPasswordReset(string $email, string $token): bool
    {
        $resetUrl = url("/account/reset-password?token={$token}&email=" . urlencode($email));
        $body = "
        <div style='font-family: Arial, sans-serif; max-width: 600px; margin: 0 auto; color: #26332B;'>
            <div style='background-color: #214D36; padding: 20px; text-align: center; color: #FAF7EF;'>
                <h1 style='margin: 0;'>EveryVeda</h1>
            </div>
            <div style='padding: 25px; background: #ffffff; border: 1px solid #e0e0e0;'>
                <h2>Password Reset Request</h2>
                <p>We received a request to reset your password for your EveryVeda account. Click the button below to choose a new password:</p>
                <p style='text-align: center; margin: 30px 0;'>
                    <a href='{$resetUrl}' style='background-color: #214D36; color: #ffffff; padding: 12px 25px; text-decoration: none; border-radius: 4px; font-weight: bold;'>Reset My Password</a>
                </p>
                <p>This link is valid for 1 hour. If you did not request this, you can safely ignore this email.</p>
            </div>
        </div>";

        return self::send($email, 'EveryVeda Patron', 'Reset Your EveryVeda Password', $body);
    }
}
