<div class="container py-4">
    <div class="row g-4">
        <!-- Account Sidebar Navigation -->
        <div class="col-lg-3">
            <div class="card border shadow-sm p-3 bg-white mb-4">
                <div class="d-flex align-items-center gap-3 pb-3 border-bottom mb-3">
                    <div class="bg-success text-white rounded-circle p-3 fs-4 fw-bold" style="width:48px;height:48px;display:flex;align-items:center;justify-content:center;">
                        <?= strtoupper(substr($user['name'], 0, 1)) ?>
                    </div>
                    <div>
                        <strong class="d-block text-dark"><?= e($user['name']) ?></strong>
                        <small class="text-muted"><?= e($user['email']) ?></small>
                    </div>
                </div>

                <div class="list-group list-group-flush small">
                    <a href="<?= url('/account') ?>" class="list-group-item list-group-item-action fw-bold active bg-success border-success">
                        <i class="bi bi-speedometer2 me-2"></i> <?= __('dashboard') ?>
                    </a>
                    <a href="<?= url('/account/orders') ?>" class="list-group-item list-group-item-action">
                        <i class="bi bi-box-seam me-2"></i> <?= __('orders') ?>
                    </a>
                    <a href="<?= url('/account/addresses') ?>" class="list-group-item list-group-item-action">
                        <i class="bi bi-geo-alt me-2"></i> <?= __('addresses') ?>
                    </a>
                    <a href="<?= url('/account/profile') ?>" class="list-group-item list-group-item-action">
                        <i class="bi bi-person me-2"></i> <?= __('profile') ?>
                    </a>
                    <a href="<?= url('/account/logout') ?>" class="list-group-item list-group-item-action text-danger">
                        <i class="bi bi-box-arrow-right me-2"></i> <?= __('logout') ?>
                    </a>
                </div>
            </div>
        </div>

        <!-- Main Account Dashboard Area -->
        <div class="col-lg-9">
            <div class="card border shadow-sm p-4 bg-white mb-4">
                <h3 class="h4 fw-bold text-success mb-1">Namaste, <?= e($user['name']) ?>!</h3>
                <p class="text-muted small">From your EveryVeda account dashboard, you can view your recent orders, manage shipping addresses, and download tax invoices.</p>

                <div class="row g-3 my-2">
                    <div class="col-md-6">
                        <div class="border rounded p-3 bg-light d-flex align-items-center gap-3">
                            <i class="bi bi-box-seam fs-1 text-success"></i>
                            <div>
                                <span class="d-block small text-muted text-uppercase fw-bold">Recent Orders</span>
                                <h4 class="fw-bold m-0"><?= count($recentOrders) ?></h4>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="border rounded p-3 bg-light d-flex align-items-center gap-3">
                            <i class="bi bi-geo-alt fs-1 text-success"></i>
                            <div>
                                <span class="d-block small text-muted text-uppercase fw-bold">Saved Addresses</span>
                                <h4 class="fw-bold m-0"><?= $addressCount ?></h4>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Recent Orders Table -->
            <div class="card border shadow-sm p-4 bg-white">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h5 class="fw-bold m-0"><?= __('orders') ?></h5>
                    <a href="<?= url('/account/orders') ?>" class="small text-success fw-bold text-decoration-none">View All</a>
                </div>

                <?php if (empty($recentOrders)): ?>
                    <p class="text-muted small py-3">You have not placed any orders yet.</p>
                <?php else: ?>
                    <div class="table-responsive">
                        <table class="table align-middle small">
                            <thead class="table-light">
                                <tr>
                                    <th>Order #</th>
                                    <th>Date</th>
                                    <th>Total</th>
                                    <th>Status</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($recentOrders as $ord): ?>
                                    <tr>
                                        <td><strong>#<?= e($ord['order_number']) ?></strong></td>
                                        <td><?= date('d M Y', strtotime($ord['created_at'])) ?></td>
                                        <td><strong><?= format_inr($ord['total_amount']) ?></strong></td>
                                        <td><span class="badge bg-success-subtle text-success text-uppercase"><?= e($ord['status']) ?></span></td>
                                        <td>
                                            <a href="<?= url('/account/orders/' . $ord['id']) ?>" class="btn btn-sm btn-outline-success">View Details</a>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>
