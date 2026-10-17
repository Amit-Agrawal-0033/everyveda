<div class="mb-4 d-flex justify-content-between align-items-center">
    <div>
        <h2 class="h3 fw-bold text-dark m-0">Dashboard Overview</h2>
        <small class="text-muted">Real-time e-commerce performance metrics & inventory status</small>
    </div>
    <div>
        <a href="<?= url('/admin/reports') ?>" class="btn btn-sm btn-outline-success">
            <i class="bi bi-file-earmark-spreadsheet me-1"></i> Download Reports
        </a>
    </div>
</div>

<!-- 1. Key Metric Cards -->
<div class="row g-3 mb-4">
    <!-- Total Net Sales -->
    <div class="col-sm-6 col-xl-3">
        <div class="metric-card">
            <div class="metric-title"><i class="bi bi-currency-rupee text-success"></i> Net Sales (Confirmed)</div>
            <div class="metric-value"><?= format_inr($stats['total_sales']) ?></div>
            <div class="metric-sub text-success">Excludes pending & cancelled</div>
        </div>
    </div>

    <!-- Total Orders -->
    <div class="col-sm-6 col-xl-3">
        <div class="metric-card">
            <div class="metric-title"><i class="bi bi-cart-check text-primary"></i> Total Orders</div>
            <div class="metric-value"><?= $stats['total_orders'] ?></div>
            <div class="metric-sub"><?= $stats['pending_orders'] ?> pending confirmation</div>
        </div>
    </div>

    <!-- Active Products -->
    <div class="col-sm-6 col-xl-3">
        <div class="metric-card">
            <div class="metric-title"><i class="bi bi-box-seam text-warning"></i> Active Formulations</div>
            <div class="metric-value"><?= $stats['total_products'] ?></div>
            <div class="metric-sub"><?= count($lowStockItems) ?> low-stock alerts</div>
        </div>
    </div>

    <!-- Registered Customers -->
    <div class="col-sm-6 col-xl-3">
        <div class="metric-card">
            <div class="metric-title"><i class="bi bi-people text-info"></i> Customers</div>
            <div class="metric-value"><?= $stats['total_customers'] ?></div>
            <div class="metric-sub"><?= $stats['captured_payments'] ?> successful online payments</div>
        </div>
    </div>
</div>

<!-- 2. Status Breakdown Strip -->
<div class="row g-3 mb-4">
    <div class="col-md-2 col-4">
        <div class="p-3 bg-white border rounded text-center">
            <small class="text-muted d-block fw-bold">PENDING</small>
            <h4 class="fw-bold text-warning mb-0"><?= $stats['pending_orders'] ?></h4>
        </div>
    </div>
    <div class="col-md-2 col-4">
        <div class="p-3 bg-white border rounded text-center">
            <small class="text-muted d-block fw-bold">PROCESSING</small>
            <h4 class="fw-bold text-secondary mb-0"><?= $stats['processing_orders'] ?></h4>
        </div>
    </div>
    <div class="col-md-2 col-4">
        <div class="p-3 bg-white border rounded text-center">
            <small class="text-muted d-block fw-bold">SHIPPED</small>
            <h4 class="fw-bold text-primary mb-0"><?= $stats['shipped_orders'] ?></h4>
        </div>
    </div>
    <div class="col-md-2 col-4">
        <div class="p-3 bg-white border rounded text-center">
            <small class="text-muted d-block fw-bold">CANCELLED</small>
            <h4 class="fw-bold text-danger mb-0"><?= $stats['cancelled_orders'] ?></h4>
        </div>
    </div>
    <div class="col-md-2 col-4">
        <div class="p-3 bg-white border rounded text-center">
            <small class="text-muted d-block fw-bold">REFUNDED</small>
            <h4 class="fw-bold text-dark mb-0"><?= $stats['refunded_orders'] ?></h4>
        </div>
    </div>
    <div class="col-md-2 col-4">
        <div class="p-3 bg-white border rounded text-center">
            <small class="text-muted d-block fw-bold">FAILED PAY</small>
            <h4 class="fw-bold text-danger mb-0"><?= $stats['failed_payments'] ?></h4>
        </div>
    </div>
</div>

<div class="row g-4">
    <!-- Recent Orders Table -->
    <div class="col-lg-8">
        <div class="card border shadow-sm p-4 bg-white mb-4">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h5 class="fw-bold m-0"><i class="bi bi-clock-history me-1"></i> Recent Orders</h5>
                <a href="<?= url('/admin/orders') ?>" class="small text-success fw-bold text-decoration-none">View All Orders</a>
            </div>

            <div class="table-responsive">
                <table class="table align-middle small">
                    <thead class="table-light">
                        <tr>
                            <th>Order #</th>
                            <th>Customer</th>
                            <th>Amount</th>
                            <th>Payment</th>
                            <th>Status</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($recentOrders)): ?>
                            <tr>
                                <td colspan="6" class="text-center py-4 text-muted">No orders recorded yet.</td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($recentOrders as $ro): ?>
                                <tr>
                                    <td><strong>#<?= e($ro['order_number']) ?></strong></td>
                                    <td><?= e($ro['customer_name']) ?></td>
                                    <td><strong><?= format_inr($ro['total_amount']) ?></strong></td>
                                    <td>
                                        <span class="badge bg-light text-dark border text-uppercase"><?= e($ro['payment_method']) ?></span>
                                    </td>
                                    <td>
                                        <span class="badge-status badge-<?= e($ro['status']) ?>"><?= e($ro['status']) ?></span>
                                    </td>
                                    <td>
                                        <a href="<?= url('/admin/orders/' . $ro['id']) ?>" class="btn btn-sm btn-outline-success">Manage</a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Low Stock Alerts & Best Sellers Sidebar -->
    <div class="col-lg-4">
        <!-- Low Stock Alerts -->
        <div class="card border shadow-sm p-3 bg-white mb-4">
            <h6 class="fw-bold text-danger mb-3"><i class="bi bi-exclamation-triangle-fill"></i> Low Stock Threshold Alerts</h6>
            <?php if (empty($lowStockItems)): ?>
                <p class="small text-muted m-0">✓ All active formulations are well stocked.</p>
            <?php else: ?>
                <div class="list-group list-group-flush small">
                    <?php foreach ($lowStockItems as $lsi): ?>
                        <div class="list-group-item px-0 d-flex justify-content-between align-items-center">
                            <div>
                                <strong class="d-block"><?= e($lsi['name']) ?></strong>
                                <small class="text-muted">SKU: <?= e($lsi['sku']) ?></small>
                            </div>
                            <span class="badge bg-danger rounded-pill"><?= $lsi['stock_quantity'] ?> left</span>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>

        <!-- Top Selling Formulations -->
        <div class="card border shadow-sm p-3 bg-white">
            <h6 class="fw-bold text-success mb-3"><i class="bi bi-trophy-fill text-warning"></i> Best Selling Formulations</h6>
            <?php if (empty($bestSellers)): ?>
                <p class="small text-muted m-0">No sales data yet.</p>
            <?php else: ?>
                <div class="list-group list-group-flush small">
                    <?php foreach ($bestSellers as $bs): ?>
                        <div class="list-group-item px-0 d-flex justify-content-between align-items-center">
                            <div>
                                <strong class="d-block text-truncate" style="max-width: 180px;"><?= e($bs['name']) ?></strong>
                                <small class="text-muted">SKU: <?= e($bs['sku']) ?></small>
                            </div>
                            <span class="badge bg-light text-dark border"><?= $bs['total_sold'] ?> sold</span>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>
