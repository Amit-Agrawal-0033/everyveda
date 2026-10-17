<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h2 class="h3 fw-bold text-dark m-0">Orders & Fulfillment</h2>
        <small class="text-muted">Process customer orders, update statuses, dispatch shipments, and export CSV</small>
    </div>
    <div>
        <a href="<?= url('/admin/orders/export/csv?' . http_build_query($_GET)) ?>" class="btn btn-sm btn-outline-success">
            <i class="bi bi-file-earmark-spreadsheet me-1"></i> Export Filtered CSV
        </a>
    </div>
</div>

<!-- Filters Card -->
<div class="card border shadow-sm p-3 bg-white mb-4">
    <form action="<?= url('/admin/orders') ?>" method="GET" class="row g-2 align-items-center">
        <div class="col-md-3">
            <input type="text" name="q" class="form-control form-control-sm" placeholder="Order ID, name, phone, email..." value="<?= e($filters['q']) ?>">
        </div>
        <div class="col-md-2">
            <select name="status" class="form-select form-select-sm">
                <option value="all">All Statuses</option>
                <option value="pending" <?= ($filters['status'] === 'pending') ? 'selected' : '' ?>>Pending</option>
                <option value="confirmed" <?= ($filters['status'] === 'confirmed') ? 'selected' : '' ?>>Confirmed</option>
                <option value="processing" <?= ($filters['status'] === 'processing') ? 'selected' : '' ?>>Processing</option>
                <option value="shipped" <?= ($filters['status'] === 'shipped') ? 'selected' : '' ?>>Shipped</option>
                <option value="delivered" <?= ($filters['status'] === 'delivered') ? 'selected' : '' ?>>Delivered</option>
                <option value="cancelled" <?= ($filters['status'] === 'cancelled') ? 'selected' : '' ?>>Cancelled</option>
                <option value="refunded" <?= ($filters['status'] === 'refunded') ? 'selected' : '' ?>>Refunded</option>
            </select>
        </div>
        <div class="col-md-2">
            <select name="payment_status" class="form-select form-select-sm">
                <option value="all">All Payment Statuses</option>
                <option value="captured" <?= ($filters['payment_status'] === 'captured') ? 'selected' : '' ?>>Captured (Paid)</option>
                <option value="pending" <?= ($filters['payment_status'] === 'pending') ? 'selected' : '' ?>>Pending</option>
                <option value="failed" <?= ($filters['payment_status'] === 'failed') ? 'selected' : '' ?>>Failed</option>
            </select>
        </div>
        <div class="col-md-2">
            <input type="date" name="start_date" class="form-control form-control-sm" value="<?= e($filters['start_date']) ?>">
        </div>
        <div class="col-md-2">
            <input type="date" name="end_date" class="form-control form-control-sm" value="<?= e($filters['end_date']) ?>">
        </div>
        <div class="col-md-1">
            <button type="submit" class="btn btn-sm btn-outline-success w-100">Go</button>
        </div>
    </form>
</div>

<!-- Orders Table -->
<div class="card border shadow-sm bg-white overflow-hidden">
    <div class="table-responsive">
        <table class="table align-middle mb-0 small">
            <thead class="table-light">
                <tr>
                    <th class="ps-3">Order #</th>
                    <th>Date</th>
                    <th>Customer</th>
                    <th>Destination</th>
                    <th>Amount</th>
                    <th>Payment</th>
                    <th>Order Status</th>
                    <th class="text-end pe-3">Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($orders)): ?>
                    <tr>
                        <td colspan="8" class="text-center py-4 text-muted">No orders found matching your filter criteria.</td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($orders as $o): ?>
                        <tr>
                            <td class="ps-3">
                                <strong class="d-block text-dark">#<?= e($o['order_number']) ?></strong>
                            </td>
                            <td><?= date('d M Y, h:i A', strtotime($o['created_at'])) ?></td>
                            <td>
                                <strong class="d-block"><?= e($o['customer_name']) ?></strong>
                                <small class="text-muted"><?= e($o['customer_phone']) ?></small>
                            </td>
                            <td><?= e($o['shipping_city']) ?>, <?= e($o['shipping_state']) ?></td>
                            <td><strong><?= format_inr($o['total_amount']) ?></strong></td>
                            <td>
                                <span class="badge bg-light text-dark border text-uppercase"><?= e($o['payment_method']) ?></span>
                                <small class="d-block text-muted"><?= e($o['payment_status']) ?></small>
                            </td>
                            <td>
                                <span class="badge-status badge-<?= e($o['status']) ?>"><?= e($o['status']) ?></span>
                            </td>
                            <td class="text-end pe-3">
                                <div class="btn-group btn-group-sm">
                                    <a href="<?= url('/admin/orders/' . $o['id']) ?>" class="btn btn-outline-success" title="View & Process">
                                        <i class="bi bi-eye"></i> View
                                    </a>
                                    <a href="<?= url('/admin/orders/' . $o['id'] . '/invoice') ?>" target="_blank" class="btn btn-outline-secondary" title="Invoice">
                                        <i class="bi bi-printer"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- Pagination -->
<?php if ($totalPages > 1): ?>
    <nav class="mt-4">
        <ul class="pagination pagination-sm justify-content-center">
            <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                <li class="page-item <?= $page === $i ? 'active' : '' ?>">
                    <a class="page-link" href="<?= url('/admin/orders?' . http_build_query(array_merge($filters, ['page' => $i]))) ?>"><?= $i ?></a>
                </li>
            <?php endfor; ?>
        </ul>
    </nav>
<?php endif; ?>
