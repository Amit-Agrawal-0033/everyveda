<div class="container py-4">
    <div class="row g-4">
        <!-- Account Sidebar Navigation -->
        <div class="col-lg-3">
            <div class="card border shadow-sm p-3 bg-white mb-4">
                <div class="list-group list-group-flush small">
                    <a href="<?= url('/account') ?>" class="list-group-item list-group-item-action">
                        <i class="bi bi-speedometer2 me-2"></i> <?= __('dashboard') ?>
                    </a>
                    <a href="<?= url('/account/orders') ?>" class="list-group-item list-group-item-action fw-bold active bg-success border-success">
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

        <div class="col-lg-9">
            <div class="card border shadow-sm p-4 bg-white">
                <h4 class="fw-bold text-success mb-3"><?= __('orders') ?></h4>

                <?php if (empty($orders)): ?>
                    <div class="text-center py-5">
                        <p class="text-muted">You haven't placed any orders yet.</p>
                        <a href="<?= url('/catalog') ?>" class="btn btn-ev-primary btn-sm">Explore Products</a>
                    </div>
                <?php else: ?>
                    <div class="table-responsive">
                        <table class="table align-middle">
                            <thead class="table-light small">
                                <tr>
                                    <th>Order #</th>
                                    <th>Date</th>
                                    <th>Payment</th>
                                    <th>Total</th>
                                    <th>Status</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($orders as $ord): ?>
                                    <tr>
                                        <td><strong>#<?= e($ord['order_number']) ?></strong></td>
                                        <td><?= date('d M Y', strtotime($ord['created_at'])) ?></td>
                                        <td>
                                            <span class="badge bg-light text-dark border text-uppercase"><?= e($ord['payment_method']) ?></span>
                                            <small class="d-block text-muted"><?= e($ord['payment_status']) ?></small>
                                        </td>
                                        <td><strong><?= format_inr($ord['total_amount']) ?></strong></td>
                                        <td>
                                            <span class="badge bg-success-subtle text-success text-uppercase"><?= e($ord['status']) ?></span>
                                        </td>
                                        <td>
                                            <div class="btn-group btn-group-sm">
                                                <a href="<?= url('/account/orders/' . $ord['id']) ?>" class="btn btn-outline-success">View</a>
                                                <a href="<?= url('/account/orders/' . $ord['id'] . '/invoice') ?>" target="_blank" class="btn btn-outline-secondary" title="Invoice">
                                                    <i class="bi bi-file-earmark-pdf"></i>
                                                </a>
                                            </div>
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
