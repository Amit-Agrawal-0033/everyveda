<div class="mb-4">
    <a href="<?= url('/admin/coupons') ?>" class="small text-muted text-decoration-none">← Back to Coupons List</a>
    <h2 class="h3 fw-bold text-dark m-0 mt-1">Create Promotional Coupon</h2>
</div>

<div class="row justify-content-center">
    <div class="col-lg-8">
        <div class="card border shadow-sm p-4 bg-white">
            <form action="<?= url('/admin/coupons/create') ?>" method="POST">
                <?= csrf_field() ?>

                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label small fw-bold">Coupon Code *</label>
                        <input type="text" name="code" class="form-control text-uppercase" placeholder="e.g. AYUSH15" value="<?= old('code') ?>" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label small fw-bold">Discount Type *</label>
                        <select name="discount_type" class="form-select">
                            <option value="percentage">Percentage Discount (%)</option>
                            <option value="fixed">Fixed Rupee Discount (₹)</option>
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label small fw-bold">Discount Value *</label>
                        <input type="number" step="0.01" name="discount_value" class="form-control" placeholder="e.g. 15 for 15% or 100 for ₹100" value="<?= old('discount_value') ?>" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label small fw-bold">Minimum Order Amount (₹)</label>
                        <input type="number" step="0.01" name="min_order_amount" class="form-control" value="<?= old('min_order_amount', '499.00') ?>">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label small fw-bold">Max Discount Cap (₹ Optional for %)</label>
                        <input type="number" step="0.01" name="max_discount_amount" class="form-control" value="<?= old('max_discount_amount') ?>">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label small fw-bold">Usage Limit Per User</label>
                        <input type="number" name="usage_limit_per_user" class="form-control" value="<?= old('usage_limit_per_user', 1) ?>">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label small fw-bold">Global Usage Limit</label>
                        <input type="number" name="usage_limit_global" class="form-control" placeholder="Leave empty for unlimited" value="<?= old('usage_limit_global') ?>">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label small fw-bold">Expiry Date</label>
                        <input type="date" name="end_date" class="form-control" value="<?= old('end_date') ?>">
                    </div>
                    <div class="col-12">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="is_active" value="1" id="activeCheck" checked>
                            <label class="form-check-label small fw-bold" for="activeCheck">Active Coupon</label>
                        </div>
                    </div>
                    <div class="col-12 mt-4">
                        <button type="submit" class="btn btn-success px-4">Create Coupon</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
