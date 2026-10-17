<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h2 class="h3 fw-bold text-dark m-0">Promo Coupons</h2>
        <small class="text-muted">Manage percentage and fixed promotional discount codes</small>
    </div>
    <div>
        <a href="<?= url('/admin/coupons/create') ?>" class="btn btn-sm btn-success">
            <i class="bi bi-plus-lg me-1"></i> Create Coupon
        </a>
    </div>
</div>

<div class="card border shadow-sm bg-white overflow-hidden">
    <div class="table-responsive">
        <table class="table align-middle mb-0 small">
            <thead class="table-light">
                <tr>
                    <th class="ps-3">Code</th>
                    <th>Type</th>
                    <th>Discount</th>
                    <th>Min Spend</th>
                    <th>Max Cap</th>
                    <th>Usage Count</th>
                    <th>Validity</th>
                    <th>Status</th>
                    <th class="text-end pe-3">Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($coupons as $c): ?>
                    <tr>
                        <td class="ps-3">
                            <strong class="text-dark fs-6"><?= e($c['code']) ?></strong>
                        </td>
                        <td><span class="badge bg-light text-dark border text-uppercase"><?= e($c['discount_type']) ?></span></td>
                        <td>
                            <strong><?= $c['discount_type'] === 'percentage' ? $c['discount_value'] . '%' : format_inr($c['discount_value']) ?></strong>
                        </td>
                        <td><?= format_inr($c['min_order_amount']) ?></td>
                        <td><?= $c['max_discount_amount'] ? format_inr($c['max_discount_amount']) : 'No Limit' ?></td>
                        <td><?= $c['used_count'] ?><?= $c['usage_limit_global'] ? ' / ' . $c['usage_limit_global'] : '' ?></td>
                        <td>
                            <?php if ($c['end_date']): ?>
                                <?= date('d M Y', strtotime($c['end_date'])) ?>
                            <?php else: ?>
                                Permanent
                            <?php endif; ?>
                        </td>
                        <td>
                            <span class="badge bg-<?= $c['is_active'] ? 'success' : 'secondary' ?>">
                                <?= $c['is_active'] ? 'Active' : 'Disabled' ?>
                            </span>
                        </td>
                        <td class="text-end pe-3">
                            <div class="btn-group btn-group-sm">
                                <a href="<?= url('/admin/coupons/' . $c['id'] . '/edit') ?>" class="btn btn-outline-success">
                                    <i class="bi bi-pencil-square"></i>
                                </a>
                                <form action="<?= url('/admin/coupons/' . $c['id'] . '/delete') ?>" method="POST" class="d-inline" onsubmit="return confirm('Delete this coupon?');">
                                    <?= csrf_field() ?>
                                    <button type="submit" class="btn btn-outline-danger">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>
