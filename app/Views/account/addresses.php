<div class="container py-4">
    <div class="row g-4">
        <!-- Account Sidebar Navigation -->
        <div class="col-lg-3">
            <div class="card border shadow-sm p-3 bg-white mb-4">
                <div class="list-group list-group-flush small">
                    <a href="<?= url('/account') ?>" class="list-group-item list-group-item-action">
                        <i class="bi bi-speedometer2 me-2"></i> <?= __('dashboard') ?>
                    </a>
                    <a href="<?= url('/account/orders') ?>" class="list-group-item list-group-item-action">
                        <i class="bi bi-box-seam me-2"></i> <?= __('orders') ?>
                    </a>
                    <a href="<?= url('/account/addresses') ?>" class="list-group-item list-group-item-action fw-bold active bg-success border-success">
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
            <div class="card border shadow-sm p-4 bg-white mb-4">
                <h4 class="fw-bold text-success mb-3"><?= __('addresses') ?></h4>

                <div class="row g-3">
                    <?php foreach ($addresses as $addr): ?>
                        <div class="col-md-6">
                            <div class="card border h-100 p-3">
                                <?php if ($addr['is_default_shipping']): ?>
                                    <span class="badge bg-success align-self-start mb-2">Default Delivery Address</span>
                                <?php endif; ?>
                                <strong class="d-block text-dark"><?= e($addr['recipient_name']) ?></strong>
                                <p class="small text-muted mb-3">
                                    <?= e($addr['address_line1']) ?><br>
                                    <?php if (!empty($addr['address_line2'])): ?>
                                        <?= e($addr['address_line2']) ?><br>
                                    <?php endif; ?>
                                    <?= e($addr['city']) ?>, <?= e($addr['state']) ?> - <?= e($addr['pincode']) ?><br>
                                    Phone: <?= e($addr['phone']) ?>
                                </p>
                                <form action="<?= url('/account/addresses/' . $addr['id'] . '/delete') ?>" method="POST" class="mt-auto text-end" onsubmit="return confirm('Delete this address?');">
                                    <?= csrf_field() ?>
                                    <button type="submit" class="btn btn-sm btn-outline-danger">Delete</button>
                                </form>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>

            <!-- Add Address Card -->
            <div class="card border shadow-sm p-4 bg-white">
                <h5 class="fw-bold mb-3">Add New Address</h5>
                <form action="<?= url('/account/addresses') ?>" method="POST">
                    <?= csrf_field() ?>
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label small fw-bold">Recipient Name *</label>
                            <input type="text" name="recipient_name" class="form-control" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-bold">Phone Number *</label>
                            <input type="tel" name="phone" class="form-control" placeholder="10-digit mobile" required>
                        </div>
                        <div class="col-12">
                            <label class="form-label small fw-bold">Address Line 1 *</label>
                            <input type="text" name="address_line1" class="form-control" placeholder="Flat, house no, street" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-bold">Address Line 2 (Optional)</label>
                            <input type="text" name="address_line2" class="form-control" placeholder="Area, landmark">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-bold">PIN Code *</label>
                            <input type="text" name="pincode" class="form-control" maxlength="6" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-bold">City *</label>
                            <input type="text" name="city" class="form-control" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-bold">State *</label>
                            <input type="text" name="state" class="form-control" required>
                        </div>
                        <div class="col-12">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="is_default_shipping" value="1" id="isDefaultCheck">
                                <label class="form-check-label small" for="isDefaultCheck">Make default delivery address</label>
                            </div>
                        </div>
                        <div class="col-12">
                            <button type="submit" class="btn btn-ev-primary">Save Address</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
