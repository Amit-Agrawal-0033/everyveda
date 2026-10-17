<div class="d-flex flex-wrap justify-content-between align-items-center mb-4 gap-2">
    <div>
        <h1 class="h3 mb-0">Registered Customers</h1>
        <p class="text-muted small mb-0">View customer profiles, order history, lifetime spend, and export data.</p>
    </div>
    <div class="d-flex gap-2">
        <a href="<?= url('/admin/customers/export') ?>" class="btn btn-outline-success">
            <i class="bi bi-file-earmark-excel me-1"></i> Export Customers CSV
        </a>
    </div>
</div>

<!-- Search & Filter Card -->
<div class="card border-0 shadow-sm mb-4">
    <div class="card-body p-3">
        <form action="<?= url('/admin/customers') ?>" method="GET" class="row g-2 align-items-center">
            <div class="col-md-9 col-lg-10">
                <div class="input-group">
                    <span class="input-group-text bg-white"><i class="bi bi-search text-muted"></i></span>
                    <input type="text" name="q" class="form-control border-start-0" placeholder="Search by customer name, email address, or mobile number..." value="<?= e($q ?? '') ?>">
                </div>
            </div>
            <div class="col-md-3 col-lg-2 d-flex gap-2">
                <button type="submit" class="btn btn-primary w-100">Search</button>
                <?php if (!empty($q)): ?>
                    <a href="<?= url('/admin/customers') ?>" class="btn btn-outline-secondary" title="Clear filter"><i class="bi bi-x-lg"></i></a>
                <?php endif; ?>
            </div>
        </form>
    </div>
</div>

<!-- Customers Table -->
<div class="card border-0 shadow-sm">
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-light">
                <tr>
                    <th style="width: 70px;">ID</th>
                    <th>Customer Name</th>
                    <th>Contact Email</th>
                    <th>Mobile Phone</th>
                    <th>Total Orders</th>
                    <th>Lifetime Spend</th>
                    <th>Registered On</th>
                    <th class="text-end" style="width: 120px;">Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($customers)): ?>
                    <tr>
                        <td colspan="8" class="text-center py-5 text-muted">
                            <i class="bi bi-people display-6 d-block mb-2 text-secondary"></i>
                            No registered customers found matching your criteria.
                        </td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($customers as $c): ?>
                        <tr>
                            <td class="text-muted">#<?= (int)$c['id'] ?></td>
                            <td>
                                <div class="fw-bold"><?= e($c['name']) ?></div>
                                <?php if (!empty($c['is_active'])): ?>
                                    <span class="badge bg-success-subtle text-success small">Active</span>
                                <?php else: ?>
                                    <span class="badge bg-danger-subtle text-danger small">Suspended</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <a href="mailto:<?= e($c['email']) ?>" class="text-decoration-none"><?= e($c['email']) ?></a>
                            </td>
                            <td>
                                <?= !empty($c['phone']) ? e($c['phone']) : '<span class="text-muted">—</span>' ?>
                            </td>
                            <td>
                                <span class="badge bg-secondary-subtle text-secondary fs-6"><?= (int)$c['order_count'] ?></span>
                            </td>
                            <td>
                                <strong class="text-success"><?= format_inr((float)$c['total_spend']) ?></strong>
                            </td>
                            <td class="small text-muted">
                                <?= date('d M Y', strtotime($c['created_at'])) ?>
                            </td>
                            <td class="text-end">
                                <a href="<?= url('/admin/customers/' . $c['id']) ?>" class="btn btn-sm btn-outline-primary" title="View Customer Details">
                                    <i class="bi bi-eye me-1"></i> Details
                                </a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
