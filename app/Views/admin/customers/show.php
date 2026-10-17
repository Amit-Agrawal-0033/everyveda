<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h1 class="h3 mb-0"><?= e($customer['name']) ?></h1>
        <p class="text-muted small mb-0">Customer ID #<?= (int)$customer['id'] ?> &bull; Joined <?= date('d M Y', strtotime($customer['created_at'])) ?></p>
    </div>
    <div>
        <a href="<?= url('/admin/customers') ?>" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left me-1"></i> Back to Customers
        </a>
    </div>
</div>

<div class="row g-4 mb-4">
    <!-- Contact Info Card -->
    <div class="col-md-4">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-header bg-white py-3 border-bottom">
                <h5 class="card-title mb-0">Customer Profile</h5>
            </div>
            <div class="card-body">
                <div class="mb-3">
                    <label class="text-muted small text-uppercase fw-semibold d-block">Full Name</label>
                    <div class="fw-bold fs-5"><?= e($customer['name']) ?></div>
                </div>
                <div class="mb-3">
                    <label class="text-muted small text-uppercase fw-semibold d-block">Email Address</label>
                    <div><a href="mailto:<?= e($customer['email']) ?>"><?= e($customer['email']) ?></a></div>
                </div>
                <div class="mb-3">
                    <label class="text-muted small text-uppercase fw-semibold d-block">Mobile Phone</label>
                    <div><?= !empty($customer['phone']) ? e($customer['phone']) : '<span class="text-muted">Not provided</span>' ?></div>
                </div>
                <div class="mb-3">
                    <label class="text-muted small text-uppercase fw-semibold d-block">Account Status</label>
                    <div>
                        <?php if (!empty($customer['is_active'])): ?>
                            <span class="badge bg-success">Active Customer</span>
                        <?php else: ?>
                            <span class="badge bg-danger">Suspended</span>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Saved Addresses Card -->
    <div class="col-md-8">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-header bg-white py-3 border-bottom">
                <h5 class="card-title mb-0">Saved Addresses</h5>
            </div>
            <div class="card-body">
                <?php if (empty($addresses)): ?>
                    <p class="text-muted mb-0">No saved addresses on file for this customer.</p>
                <?php else: ?>
                    <div class="row g-3">
                        <?php foreach ($addresses as $addr): ?>
                            <div class="col-sm-6">
                                <div class="border rounded p-3 position-relative h-100 bg-light">
                                    <?php if (!empty($addr['is_default_shipping'])): ?>
                                        <span class="badge bg-primary position-absolute top-0 end-0 m-2">Default</span>
                                    <?php endif; ?>
                                    <strong><?= e($addr['recipient_name']) ?></strong>
                                    <div class="small text-muted mb-2"><i class="bi bi-telephone me-1"></i><?= e($addr['phone']) ?></div>
                                    <div class="small">
                                        <?= e($addr['address_line1']) ?><br>
                                        <?php if (!empty($addr['address_line2'])): ?>
                                            <?= e($addr['address_line2']) ?><br>
                                        <?php endif; ?>
                                        <?= e($addr['city']) ?>, <?= e($addr['state']) ?> - <?= e($addr['pincode']) ?>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<!-- Orders History Card -->
<div class="card border-0 shadow-sm">
    <div class="card-header bg-white py-3 border-bottom d-flex justify-content-between align-items-center">
        <h5 class="card-title mb-0">Order History (<?= count($orders) ?>)</h5>
    </div>
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-light">
                <tr>
                    <th>Order #</th>
                    <th>Date Placed</th>
                    <th>Order Status</th>
                    <th>Payment</th>
                    <th>Total</th>
                    <th class="text-end">Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($orders)): ?>
                    <tr>
                        <td colspan="6" class="text-center py-4 text-muted">This customer has not placed any orders yet.</td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($orders as $ord): ?>
                        <tr>
                            <td>
                                <a href="<?= url('/admin/orders/' . $ord['id']) ?>" class="fw-bold font-monospace">
                                    <?= e($ord['order_number']) ?>
                                </a>
                            </td>
                            <td class="small text-muted">
                                <?= date('d M Y, h:i A', strtotime($ord['created_at'])) ?>
                            </td>
                            <td>
                                <span class="badge status-<?= e($ord['status']) ?>">
                                    <?= ucfirst(e($ord['status'])) ?>
                                </span>
                            </td>
                            <td>
                                <span class="badge bg-secondary-subtle text-secondary text-uppercase small">
                                    <?= e($ord['payment_method']) ?>
                                </span>
                                <?php if ($ord['payment_status'] === 'paid'): ?>
                                    <span class="badge bg-success-subtle text-success small ms-1">Paid</span>
                                <?php else: ?>
                                    <span class="badge bg-warning-subtle text-dark small ms-1"><?= ucfirst(e($ord['payment_status'])) ?></span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <strong><?= format_inr((float)$ord['total_amount']) ?></strong>
                            </td>
                            <td class="text-end">
                                <a href="<?= url('/admin/orders/' . $ord['id']) ?>" class="btn btn-sm btn-outline-primary">
                                    <i class="bi bi-eye me-1"></i> View Order
                                </a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
