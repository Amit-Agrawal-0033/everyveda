<?php
/**
 * Admin Coupon Management Controller
 */

declare(strict_types=1);

namespace App\Controllers\Admin;

use App\Core\Controller;
use App\Core\Database;
use App\Core\Request;
use App\Services\AdminAuthService;

class CouponController extends Controller
{
    public function index(Request $request): void
    {
        $coupons = Database::fetchAll("SELECT * FROM coupons ORDER BY id DESC");

        $this->adminView('admin/coupons/index', [
            'meta_title' => 'Coupons & Promos — EveryVeda Admin',
            'coupons' => $coupons
        ]);
    }

    public function create(Request $request): void
    {
        $this->adminView('admin/coupons/create', [
            'meta_title' => 'Create Promo Coupon — EveryVeda Admin'
        ]);
    }

    public function store(Request $request): void
    {
        $this->validateCsrf($request);

        $errors = $this->validate($request->all(), [
            'code' => 'required|min:3|max:50',
            'discount_type' => 'required',
            'discount_value' => 'required|numeric'
        ]);

        if (!empty($errors)) {
            flash_input($request->all());
            $this->redirect(url('/admin/coupons/create'), 'error', reset($errors));
            return;
        }

        $code = strtoupper(trim((string)$request->post('code')));
        $existing = Database::fetchOne("SELECT id FROM coupons WHERE code = :code", ['code' => $code]);
        if ($existing) {
            flash_input($request->all());
            $this->redirect(url('/admin/coupons/create'), 'error', 'A coupon with this code already exists.');
            return;
        }

        $cid = Database::insert('coupons', [
            'code' => $code,
            'discount_type' => (string)$request->post('discount_type', 'percentage'),
            'discount_value' => (float)$request->post('discount_value'),
            'min_order_amount' => (float)$request->post('min_order_amount', 0),
            'max_discount_amount' => $request->post('max_discount_amount') ? (float)$request->post('max_discount_amount') : null,
            'usage_limit_global' => $request->post('usage_limit_global') ? (int)$request->post('usage_limit_global') : null,
            'usage_limit_per_user' => (int)$request->post('usage_limit_per_user', 1),
            'start_date' => $request->post('start_date') ? $request->post('start_date') . ' 00:00:00' : null,
            'end_date' => $request->post('end_date') ? $request->post('end_date') . ' 23:59:59' : null,
            'is_active' => !empty($request->post('is_active')) ? 1 : 0,
            'created_at' => date('Y-m-d H:i:s')
        ]);

        AdminAuthService::logAudit((int)admin_user()['id'], 'create_coupon', 'coupons', $cid, "Created coupon {$code}");

        $this->redirect(url('/admin/coupons'), 'success', "Coupon {$code} created successfully.");
    }

    public function edit(Request $request, string $id): void
    {
        $coupon = Database::fetchOne("SELECT * FROM coupons WHERE id = :id", ['id' => (int)$id]);
        if (!$coupon) {
            $this->redirect(url('/admin/coupons'), 'error', 'Coupon not found.');
            return;
        }

        $this->adminView('admin/coupons/edit', [
            'meta_title' => 'Edit Coupon — EveryVeda Admin',
            'coupon' => $coupon
        ]);
    }

    public function update(Request $request, string $id): void
    {
        $this->validateCsrf($request);
        $cid = (int)$id;

        $errors = $this->validate($request->all(), [
            'code' => 'required|min:3|max:50',
            'discount_type' => 'required',
            'discount_value' => 'required|numeric'
        ]);

        if (!empty($errors)) {
            $this->redirect(url('/admin/coupons/' . $cid . '/edit'), 'error', reset($errors));
            return;
        }

        $code = strtoupper(trim((string)$request->post('code')));

        Database::update('coupons', [
            'code' => $code,
            'discount_type' => (string)$request->post('discount_type', 'percentage'),
            'discount_value' => (float)$request->post('discount_value'),
            'min_order_amount' => (float)$request->post('min_order_amount', 0),
            'max_discount_amount' => $request->post('max_discount_amount') ? (float)$request->post('max_discount_amount') : null,
            'usage_limit_global' => $request->post('usage_limit_global') ? (int)$request->post('usage_limit_global') : null,
            'usage_limit_per_user' => (int)$request->post('usage_limit_per_user', 1),
            'start_date' => $request->post('start_date') ? $request->post('start_date') . ' 00:00:00' : null,
            'end_date' => $request->post('end_date') ? $request->post('end_date') . ' 23:59:59' : null,
            'is_active' => !empty($request->post('is_active')) ? 1 : 0,
            'updated_at' => date('Y-m-d H:i:s')
        ], "id = :id", ['id' => $cid]);

        AdminAuthService::logAudit((int)admin_user()['id'], 'update_coupon', 'coupons', $cid, "Updated coupon {$code}");

        $this->redirect(url('/admin/coupons'), 'success', 'Coupon updated successfully.');
    }

    public function delete(Request $request, string $id): void
    {
        $this->validateCsrf($request);
        $cid = (int)$id;

        Database::delete('coupons', "id = :id", ['id' => $cid]);
        AdminAuthService::logAudit((int)admin_user()['id'], 'delete_coupon', 'coupons', $cid, "Deleted coupon #{$cid}");

        $this->redirect(url('/admin/coupons'), 'info', 'Coupon deleted.');
    }
}
