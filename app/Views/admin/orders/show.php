<div class="mb-4 d-flex justify-content-between align-items-center">
    <div>
        <a href="<?= url('/admin/orders') ?>" class="small text-muted text-decoration-none">← Back to Orders List</a>
        <h2 class="h3 fw-bold text-dark m-0 mt-1">Order #<?= e($order['order_number']) ?></h2>
        <small class="text-muted">Placed on <?= date('d M Y, h:i A', strtotime($order['created_at'])) ?></small>
    </div>
    <div class="d-flex gap-2">
        <a href="<?= url('/admin/orders/' . $order['id'] . '/invoice') ?>" target="_blank" class="btn btn-sm btn-outline-success">
            <i class="bi bi-printer me-1"></i> Print Tax Invoice
        </a>
    </div>
</div>

<div class="row g-4">
    <!-- Left Column: Items, Payments, Shipments -->
    <div class="col-lg-8">
        <!-- Order Items -->
        <div class="card border shadow-sm p-4 bg-white mb-4">
            <h5 class="fw-bold mb-3">Order Items Snapshot</h5>
            <div class="table-responsive">
                <table class="table align-middle small mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Item Details</th>
                            <th>SKU / HSN</th>
                            <th class="text-center">Qty</th>
                            <th class="text-end">Unit Price</th>
                            <th class="text-end">Taxable Base</th>
                            <th class="text-end">GST %</th>
                            <th class="text-end">Total Price</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($items as $item): ?>
                            <tr>
                                <td>
                                    <strong><?= e($item['product_name_en']) ?></strong>
                                    <?php if (!empty($item['variant_name_en'])): ?>
                                        <small class="d-block text-muted"><?= e($item['variant_name_en']) ?></small>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <code><?= e($item['sku']) ?></code><br>
                                    <small class="text-muted">HSN: <?= e($item['hsn_code'] ?? '30049011') ?></small>
                                </td>
                                <td class="text-center"><?= $item['quantity'] ?></td>
                                <td class="text-end"><?= format_inr($item['unit_price']) ?></td>
                                <td class="text-end"><?= format_inr($item['taxable_amount'] ?? ($item['total_price'] / (1 + ($item['tax_rate']/100)))) ?></td>
                                <td class="text-end">
                                    <span class="badge bg-light text-dark border"><?= $item['tax_rate'] ?>%</span>
                                </td>
                                <td class="text-end fw-bold"><?= format_inr($item['total_price']) ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                    <tfoot class="border-top">
                        <tr>
                            <td colspan="6" class="text-end">Subtotal (MRP Incl. GST):</td>
                            <td class="text-end fw-bold"><?= format_inr($order['subtotal']) ?></td>
                        </tr>
                        <?php if ((float)$order['discount_amount'] > 0): ?>
                            <tr class="text-success">
                                <td colspan="6" class="text-end">Coupon Discount (<?= e($order['coupon_code']) ?>):</td>
                                <td class="text-end">-<?= format_inr($order['discount_amount']) ?></td>
                            </tr>
                        <?php endif; ?>
                        <tr>
                            <td colspan="6" class="text-end">Shipping Fee:</td>
                            <td class="text-end"><?= (float)$order['shipping_fee'] == 0 ? 'FREE' : format_inr($order['shipping_fee']) ?></td>
                        </tr>
                        <tr>
                            <td colspan="6" class="text-end">
                                Tax Breakdown:
                                <?php if (!empty($order['cgst_amount']) && (float)$order['cgst_amount'] > 0): ?>
                                    <small class="text-muted d-block">Intrastate CGST (<?= format_inr($order['cgst_amount']) ?>) + SGST (<?= format_inr($order['sgst_amount']) ?>)</small>
                                <?php elseif (!empty($order['igst_amount']) && (float)$order['igst_amount'] > 0): ?>
                                    <small class="text-muted d-block">Interstate IGST (<?= format_inr($order['igst_amount']) ?>)</small>
                                <?php else: ?>
                                    <small class="text-muted d-block">Total GST Included in MRP</small>
                                <?php endif; ?>
                            </td>
                            <td class="text-end text-muted fw-semibold"><?= format_inr($order['tax_amount']) ?></td>
                        </tr>
                        <tr class="fs-5 text-success">
                            <td colspan="6" class="text-end fw-bold">Grand Total:</td>
                            <td class="text-end fw-bold"><?= format_inr($order['total_amount']) ?></td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>

        <!-- Payments Log -->
        <div class="card border shadow-sm p-4 bg-white mb-4">
            <h5 class="fw-bold mb-3"><i class="bi bi-credit-card me-1"></i> Payment Records</h5>
            <?php if (empty($payments)): ?>
                <p class="text-muted small m-0">Order placed via Cash on Delivery (payment pending at delivery).</p>
            <?php else: ?>
                <div class="table-responsive">
                    <table class="table align-middle small mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Provider</th>
                                <th>Razorpay Order ID</th>
                                <th>Razorpay Payment ID</th>
                                <th>Amount</th>
                                <th>Status</th>
                                <th>Timestamp</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($payments as $pay): ?>
                                <tr>
                                    <td><strong class="text-uppercase"><?= e($pay['provider']) ?></strong></td>
                                    <td><code><?= e($pay['razorpay_order_id'] ?: '-') ?></code></td>
                                    <td><code><?= e($pay['razorpay_payment_id'] ?: '-') ?></code></td>
                                    <td><?= format_inr($pay['amount']) ?></td>
                                    <td><span class="badge bg-<?= $pay['status'] === 'captured' ? 'success' : ($pay['status'] === 'failed' ? 'danger' : 'warning') ?>"><?= e($pay['status']) ?></span></td>
                                    <td><?= date('d M Y, h:i A', strtotime($pay['created_at'])) ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        </div>

        <!-- Shipments & Tracking -->
        <div class="card border shadow-sm p-4 bg-white mb-4">
            <h5 class="fw-bold mb-3"><i class="bi bi-truck me-1"></i> Courier Dispatches</h5>
            <?php if (empty($shipments)): ?>
                <p class="text-muted small">No courier parcel dispatched yet.</p>
            <?php else: ?>
                <div class="table-responsive mb-3">
                    <table class="table align-middle small mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Courier Partner</th>
                                <th>Tracking AWB Number</th>
                                <th>Tracking URL</th>
                                <th>Dispatched At</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($shipments as $sh): ?>
                                <tr>
                                    <td><strong><?= e($sh['courier_name']) ?></strong></td>
                                    <td><code><?= e($sh['tracking_number']) ?></code></td>
                                    <td>
                                        <?php if (!empty($sh['tracking_url'])): ?>
                                            <a href="<?= e($sh['tracking_url']) ?>" target="_blank" class="small">Open Portal</a>
                                        <?php else: ?>
                                            -
                                        <?php endif; ?>
                                    </td>
                                    <td><?= date('d M Y, h:i A', strtotime($sh['shipped_at'])) ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>

            <!-- Record New Shipment Form -->
            <div class="p-3 bg-light rounded border">
                <h6 class="fw-bold mb-2">Record Courier Shipment</h6>
                <form action="<?= url('/admin/orders/' . $order['id'] . '/shipment') ?>" method="POST">
                    <?= csrf_field() ?>
                    <div class="row g-2">
                        <div class="col-md-4">
                            <label class="form-label small fw-bold">Courier Partner *</label>
                            <input type="text" name="courier_name" class="form-control form-control-sm" placeholder="e.g. Blue Dart, Delhivery" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label small fw-bold">Tracking Number (AWB) *</label>
                            <input type="text" name="tracking_number" class="form-control form-control-sm" placeholder="e.g. 1234567890" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label small fw-bold">Courier Tracking URL (Optional)</label>
                            <input type="url" name="tracking_url" class="form-control form-control-sm" placeholder="https://...">
                        </div>
                        <div class="col-12 mt-2">
                            <button type="submit" class="btn btn-sm btn-success">Save & Notify Customer</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Right Column: Status Update, Customer Info, Audit Timeline -->
    <div class="col-lg-4">
        <!-- Order Status State Machine Card -->
        <div class="card border shadow-sm p-4 bg-white mb-4">
            <h5 class="fw-bold mb-3">Update Order Status</h5>
            <div class="mb-3">
                <span class="text-muted small d-block">Current Status:</span>
                <span class="badge-status badge-<?= e($order['status']) ?> fs-6 mt-1"><?= e($order['status']) ?></span>
            </div>

            <form action="<?= url('/admin/orders/' . $order['id'] . '/status') ?>" method="POST">
                <?= csrf_field() ?>
                <div class="mb-3">
                    <label class="form-label small fw-bold">New Status</label>
                    <select name="status" class="form-select form-select-sm" required>
                        <option value="">Choose permitted transition...</option>
                        <?php if ($order['status'] === 'pending'): ?>
                            <option value="confirmed">Confirmed</option>
                            <option value="cancelled">Cancelled</option>
                        <?php elseif ($order['status'] === 'confirmed'): ?>
                            <option value="processing">Processing (Packing)</option>
                            <option value="cancelled">Cancelled</option>
                        <?php elseif ($order['status'] === 'processing'): ?>
                            <option value="shipped">Shipped</option>
                            <option value="cancelled">Cancelled</option>
                        <?php elseif ($order['status'] === 'shipped'): ?>
                            <option value="delivered">Delivered</option>
                            <option value="cancelled">Cancelled</option>
                        <?php elseif ($order['status'] === 'delivered'): ?>
                            <option value="refunded">Refunded</option>
                        <?php else: ?>
                            <option value="" disabled>Terminal State (No further transitions)</option>
                        <?php endif; ?>
                    </select>
                </div>

                <div class="mb-3">
                    <label class="form-label small fw-bold">Status Comment / Reason</label>
                    <textarea name="comment" rows="2" class="form-control form-control-sm" placeholder="Internal or customer note..."></textarea>
                </div>

                <button type="submit" class="btn btn-sm btn-success w-100 py-2">
                    Update Order Status
                </button>
            </form>
        </div>

        <!-- Customer & Delivery Address Card -->
        <div class="card border shadow-sm p-4 bg-white mb-4">
            <h5 class="fw-bold mb-3"><i class="bi bi-person me-1"></i> Customer & Shipping</h5>
            <strong class="d-block text-dark"><?= e($order['customer_name']) ?></strong>
            <div class="small text-muted mb-3">
                Email: <a href="mailto:<?= e($order['customer_email']) ?>"><?= e($order['customer_email']) ?></a><br>
                Phone: <a href="tel:<?= e($order['customer_phone']) ?>"><?= e($order['customer_phone']) ?></a>
            </div>

            <h6 class="fw-bold small text-uppercase text-muted">Shipping Address</h6>
            <p class="small text-muted mb-0">
                <?= e($order['shipping_address_line1']) ?><br>
                <?php if (!empty($order['shipping_address_line2'])): ?>
                    <?= e($order['shipping_address_line2']) ?><br>
                <?php endif; ?>
                <?= e($order['shipping_city']) ?>, <?= e($order['shipping_state']) ?> - <?= e($order['shipping_pincode']) ?>
            </p>

            <?php if (!empty($order['order_notes'])): ?>
                <div class="alert alert-light border small mt-3 mb-0">
                    <strong>Delivery Notes:</strong><br>
                    <?= nl2br(e($order['order_notes'])) ?>
                </div>
            <?php endif; ?>
        </div>

        <!-- Status History Timeline -->
        <div class="card border shadow-sm p-4 bg-white">
            <h5 class="fw-bold mb-3"><i class="bi bi-clock-history me-1"></i> Status History</h5>
            <div class="list-group list-group-flush small">
                <?php foreach ($timeline as $tl): ?>
                    <div class="list-group-item px-0">
                        <div class="d-flex justify-content-between align-items-baseline">
                            <span class="badge bg-secondary text-uppercase"><?= e($tl['status']) ?></span>
                            <small class="text-muted"><?= date('d M, h:i A', strtotime($tl['created_at'])) ?></small>
                        </div>
                        <?php if (!empty($tl['comment'])): ?>
                            <p class="text-muted small mt-1 mb-0"><?= e($tl['comment']) ?></p>
                        <?php endif; ?>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
</div>
