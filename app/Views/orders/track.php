<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <h1 class="h2 fw-bold text-success text-center mb-2"><?= __('track_order') ?></h1>
            <p class="text-muted text-center mb-4">Enter your EveryVeda Order Number (e.g. EV-20261016-1001) or Secure Tracking Reference.</p>

            <!-- Search Form -->
            <div class="card border shadow-sm p-4 bg-white mb-4">
                <form action="<?= url('/track-order') ?>" method="GET" class="row g-3">
                    <div class="col-md-9">
                        <input type="text" name="order_number" class="form-control form-control-lg" placeholder="Order ID or Tracking Reference..." value="<?= e($_GET['order_number'] ?? $_GET['ref'] ?? '') ?>" required>
                    </div>
                    <div class="col-md-3">
                        <button type="submit" class="btn btn-ev-primary btn-lg w-100">
                            <i class="bi bi-search"></i> <?= __('check') ?>
                        </button>
                    </div>
                </form>
            </div>

            <!-- Tracking Timeline Results -->
            <?php if ($searched && !$trackingData): ?>
                <div class="alert alert-warning text-center">
                    <i class="bi bi-exclamation-circle-fill me-1"></i> No matching order found. Please verify your Order ID.
                </div>
            <?php elseif ($trackingData): ?>
                <?php 
                    $order = $trackingData['order'];
                    $items = $trackingData['items'];
                    $shipment = $trackingData['shipment'];
                    $timeline = $trackingData['timeline'];

                    $steps = [
                        'pending' => 'Pending',
                        'confirmed' => 'Confirmed',
                        'processing' => 'Packed',
                        'shipped' => 'Dispatched',
                        'delivered' => 'Delivered'
                    ];

                    $statusOrder = ['pending' => 1, 'confirmed' => 2, 'processing' => 3, 'shipped' => 4, 'delivered' => 5, 'cancelled' => 0, 'refunded' => 0];
                    $currentRank = $statusOrder[$order['status']] ?? 1;
                ?>

                <div class="card border shadow-sm p-4 bg-white mb-4">
                    <div class="d-flex flex-wrap justify-content-between align-items-center pb-3 border-bottom mb-4">
                        <div>
                            <span class="text-muted small d-block"><?= __('order_number') ?></span>
                            <strong class="fs-4 text-dark">#<?= e($order['order_number']) ?></strong>
                        </div>
                        <div class="text-end">
                            <span class="badge bg-success-subtle text-success fs-6 px-3 py-2 text-uppercase">
                                Status: <?= e($order['status']) ?>
                            </span>
                        </div>
                    </div>

                    <!-- Progress Stepper -->
                    <?php if ($order['status'] !== 'cancelled' && $order['status'] !== 'refunded'): ?>
                        <div class="position-relative m-4">
                            <div class="progress" style="height: 4px;">
                                <?php $stepPct = (($currentRank - 1) / 4) * 100; ?>
                                <div class="progress-bar bg-success" role="progressbar" style="width: <?= $stepPct ?>%;"></div>
                            </div>
                            <div class="d-flex justify-content-between position-absolute top-50 start-0 w-100 translate-middle-y">
                                <?php foreach ($steps as $key => $label): ?>
                                    <?php $isDone = ($statusOrder[$key] <= $currentRank); ?>
                                    <div class="text-center" style="width: 20px;">
                                        <div class="rounded-circle <?= $isDone ? 'bg-success text-white' : 'bg-light border text-muted' ?> d-flex align-items-center justify-content-center mx-auto" style="width: 28px; height: 28px; font-size: 12px; font-weight: bold;">
                                            <?= $isDone ? '✓' : '' ?>
                                        </div>
                                        <span class="d-block small mt-2 fw-semibold <?= $isDone ? 'text-success' : 'text-muted' ?>" style="font-size: 0.75rem; white-space: nowrap; margin-left: -20px;">
                                            <?= $label ?>
                                        </span>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                        <div style="height: 35px;"></div>
                    <?php else: ?>
                        <div class="alert alert-danger">
                            <strong>Notice:</strong> This order is marked as <strong><?= strtoupper($order['status']) ?></strong>.
                        </div>
                    <?php endif; ?>

                    <!-- Courier Shipment Box if available -->
                    <?php if ($shipment): ?>
                        <div class="p-3 bg-light rounded border mb-4">
                            <h6 class="fw-bold text-success mb-2"><i class="bi bi-box-seam me-1"></i> Courier Dispatch Details</h6>
                            <div class="row g-2 small">
                                <div class="col-sm-6">
                                    <strong>Courier Partner:</strong> <?= e($shipment['courier_name']) ?>
                                </div>
                                <div class="col-sm-6">
                                    <strong>AWB Tracking Number:</strong> <code><?= e($shipment['tracking_number']) ?></code>
                                </div>
                                <?php if (!empty($shipment['tracking_url'])): ?>
                                    <div class="col-12 mt-2">
                                        <a href="<?= e($shipment['tracking_url']) ?>" target="_blank" rel="noopener noreferrer" class="btn btn-sm btn-outline-success">
                                            Track on Courier Portal <i class="bi bi-box-arrow-up-right"></i>
                                        </a>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    <?php endif; ?>

                    <!-- Ordered Items (No Customer PII Exposed) -->
                    <h6 class="fw-bold mb-2">Package Contents (<?= count($items) ?> items)</h6>
                    <div class="list-group list-group-flush mb-4">
                        <?php foreach ($items as $item): ?>
                            <div class="list-group-item px-0 d-flex justify-content-between align-items-center">
                                <div>
                                    <span class="fw-semibold"><?= e($item['product_name_en']) ?></span>
                                    <?php if (!empty($item['variant_name_en'])): ?>
                                        <small class="text-muted d-block"><?= e($item['variant_name_en']) ?></small>
                                    <?php endif; ?>
                                </div>
                                <span class="badge bg-light text-dark border">Qty: <?= $item['quantity'] ?></span>
                            </div>
                        <?php endforeach; ?>
                    </div>

                    <!-- Destination City (City & PIN only) -->
                    <div class="small text-muted border-top pt-3">
                        <i class="bi bi-geo-alt"></i> Destination: <?= e($order['shipping_city']) ?>, <?= e($order['shipping_state']) ?> - <?= e($order['shipping_pincode']) ?>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>
