<?php
/**
 * Admin Application Settings Controller
 */

declare(strict_types=1);

namespace App\Controllers\Admin;

use App\Core\Controller;
use App\Core\Database;
use App\Core\Request;
use App\Services\AdminAuthService;

class SettingController extends Controller
{
    public function index(Request $request): void
    {
        $rows = Database::fetchAll("SELECT * FROM settings ORDER BY group_name ASC, `key` ASC");
        $settings = [];
        foreach ($rows as $r) {
            $settings[$r['key']] = $r['value'];
        }

        $this->adminView('admin/settings/index', [
            'meta_title' => 'System Settings — EveryVeda Admin',
            'settings' => $settings
        ]);
    }

    public function update(Request $request): void
    {
        $this->validateCsrf($request);

        $allowedKeys = [
            'site_name', 'site_tagline', 'contact_email', 'contact_phone', 'whatsapp_number',
            'support_hours', 'tax_cgst_rate', 'tax_sgst_rate', 'tax_igst_rate',
            'shipping_fee_standard', 'shipping_free_threshold', 'enable_cod', 'cod_extra_charge',
            'razorpay_mode', 'razorpay_key_id', 'razorpay_key_secret', 'razorpay_webhook_secret',
            'company_name', 'company_address', 'gstin', 'fssai_license', 'ayush_license',
            'google_analytics_id', 'low_stock_notification_email'
        ];

        Database::transaction(function () use ($request, $allowedKeys) {
            foreach ($allowedKeys as $key) {
                if ($request->post($key) !== null) {
                    $val = trim((string)$request->post($key));
                    Database::query(
                        "INSERT INTO settings (`key`, `value`) VALUES (:k, :v)
                         ON DUPLICATE KEY UPDATE `value` = VALUES(`value`), updated_at = NOW()",
                        ['k' => $key, 'v' => $val]
                    );
                }
            }

            AdminAuthService::logAudit((int)admin_user()['id'], 'update_settings', 'settings', null, "Updated store settings");
        });

        $this->redirect(url('/admin/settings'), 'success', 'Settings updated successfully.');
    }
}
