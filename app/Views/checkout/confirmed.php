<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card border shadow-sm p-4 p-md-5 text-center bg-white rounded-3 mb-4">
                <div class="text-success display-3 mb-3">
                    <i class="bi bi-check-circle-fill"></i>
                </div>
                <h1 class="h2 fw-bold text-success mb-2"><?= __('order_confirmed') ?></h1>
                <p class="lead text-muted mb-4"><?= __('thank_you_order') ?></p>

                <div class="p-3 bg-light rounded-3 text-start mb-4">
                    <div class="row g-3">
                        <div class="col-sm-6">
                            <span class="text-muted small d-block"><?= __('order_number') ?></span>
                            <strong class="fs-5 text-dark">#<?= e($order['order_number']) ?></strong>
                        </div>
                        <div class="col-sm-6">
                            <span class="text-muted small d-block"><?= __('order_date') ?></span>
                            <strong class="text-dark"><?= date('d M Y, h:i A', strtotime($order['created_at'])) ?></strong>
                        </div>
                        <div class="col-sm-6">
                            <span class="text-muted small d-block"><?= __('payment_method') ?></span>
                            <strong class="text-uppercase"><?= e($order['payment_method']) ?></strong>
                        </div>
                        <div class="col-sm-6">
                            <span class="text-muted small d-block"><?= __('total') ?> Paid / Payable</span>
                            <strong class="text-success fs-5"><?= format_inr($order['total_amount']) ?></strong>
                        </div>
                    </div>
                </div>

                <!-- Public Tracking Code Notice -->
                <div class="alert alert-info text-start small mb-4">
                    <i class="bi bi-info-circle-fill me-1"></i> <?= __('public_tracking_info') ?>
                    <div class="mt-2">
                        <strong>Public Tracking Link:</strong>
                        <a href="<?= url('/order-tracking?ref=' . $order['tracking_token']) ?>" class="fw-bold text-decoration-underline d-block text-truncate">
                            <?= url('/order-tracking?ref=' . $order['tracking_token']) ?>
                        </a>
                    </div>
                </div>

                <div class="d-flex flex-wrap gap-2 justify-content-center">
                    <a href="<?= url('/order-tracking?ref=' . $order['tracking_token']) ?>" class="btn btn-ev-primary">
                        <i class="bi bi-truck me-1"></i> Track Order Status
                    </a>
                    <a href="<?= url('/catalog') ?>" class="btn btn-outline-secondary">
                        <i class="bi bi-shop me-1"></i> Continue Shopping
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
