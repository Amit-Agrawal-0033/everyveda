<div class="mb-4">
    <a href="<?= url('/admin/coupons') ?>" class="small text-muted text-decoration-none">← Back to Coupons List</a>
    <h2 class="h3 fw-bold text-dark m-0 mt-1">Edit Coupon: <?= e($coupon['code']) ?></h2>
</div>

<div class="row justify-content-center">
    <div class="col-lg-8">
        <div class="card border shadow-sm p-4 bg-white">
            <form action="<?= url('/admin/coupons/' . $coupon['id'] . '/edit') ?>" method="POST">
                <?= csrf_field() ?>

                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label small fw-bold">Coupon Code *</label>
                        <input type="text" name="code" class="form-control text-uppercase" value="<?= e($coupon['code']) ?>" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label small fw-bold">Discount Type *</label>
                        <select name="discount_type" class="form-select">
                            <option value="percentage" <?= $coupon['discount_type'] === 'percentage' ? 'selected' : '' ?>>Percentage Discount (%)</option>
                            <option value="fixed" <?= $coupon['discount_type'] === 'fixed' ? 'selected' : '' ?>>Fixed Rupee Discount (₹)</option>
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label small fw-bold">Discount Value *</label>
                        <input type="number" step="0.01" name="discount_value" class="form-control" value="<?= e($coupon['discount_value']) ?>" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label small fw-bold">Minimum Order Amount (₹)</label>
                        <input type="number" step="0.01" name="min_order_amount" class="form-control" value="<?= e($coupon['min_order_amount']) ?>">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label small fw-bold">Max Discount Cap (₹)</label>
                        <input type="number" step="0.01" name="max_discount_amount" class="form-control" value="<?= e($coupon['max_discount_amount']) ?>">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label small fw-bold">Usage Limit Per User</label>
                        <input type="number" name="usage_limit_per_user" class="form-control" value="<?= e($coupon['usage_limit_per_user']) ?>">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label small fw-bold">Global Usage Limit</label>
                        <input type="number" name="usage_limit_global" class="form-control" value="<?= e($coupon['usage_limit_global']) ?>">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label small fw-bold">Expiry Date</label>
                        <input type="date" name="end_date" class="form-control" value="<?= $coupon['end_date'] ? date('Y-m-d', strtotime($coupon['end_date'])) : '' ?>">
                    </div>
                    <div class="col-12">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="is_active" value="1" id="activeCheck" <?= $coupon['is_active'] ? 'checked' : '' ?>>
                            <label class="form-check-label small fw-bold" for="activeCheck">Active Coupon</label>
                        </div>
                    </div>
                    <div class="col-12 mt-4">
                        <button type="submit" class="btn btn-success px-4">Save Changes</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
