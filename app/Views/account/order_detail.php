<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <a href="<?= url('/account/orders') ?>" class="small text-muted text-decoration-none">← Back to Orders</a>
            <h2 class="h3 fw-bold text-success mb-0 mt-1">Order #<?= e($order['order_number']) ?></h2>
            <small class="text-muted">Placed on <?= date('d M Y, h:i A', strtotime($order['created_at'])) ?></small>
        </div>
        <div>
            <a href="<?= url('/account/orders/' . $order['id'] . '/invoice') ?>" target="_blank" class="btn btn-outline-success">
                <i class="bi bi-printer me-1"></i> <?= __('download_invoice') ?>
            </a>
        </div>
    </div>

    <div class="row g-4">
        <!-- Order Items -->
        <div class="col-lg-8">
            <div class="card border shadow-sm p-4 bg-white mb-4">
                <h5 class="fw-bold mb-3">Order Items</h5>
                <div class="table-responsive">
                    <table class="table align-middle">
                        <thead class="table-light small">
                            <tr>
                                <th>Item</th>
                                <th class="text-center">Qty</th>
                                <th class="text-end">Price</th>
                                <th class="text-end">Total</th>
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
                                        <small class="text-muted">SKU: <?= e($item['sku']) ?></small>
                                    </td>
                                    <td class="text-center"><?= $item['quantity'] ?></td>
                                    <td class="text-end"><?= format_inr($item['unit_price']) ?></td>
                                    <td class="text-end fw-bold"><?= format_inr($item['total_price']) ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                        <tfoot class="border-top">
                            <tr>
                                <td colspan="3" class="text-end">Subtotal:</td>
                                <td class="text-end fw-bold"><?= format_inr($order['subtotal']) ?></td>
                            </tr>
                            <?php if ((float)$order['discount_amount'] > 0): ?>
                                <tr class="text-success">
                                    <td colspan="3" class="text-end">Discount (<?= e($order['coupon_code']) ?>):</td>
                                    <td class="text-end">-<?= format_inr($order['discount_amount']) ?></td>
                                </tr>
                            <?php endif; ?>
                            <tr>
                                <td colspan="3" class="text-end">Shipping:</td>
                                <td class="text-end"><?= (float)$order['shipping_fee'] == 0 ? 'FREE' : format_inr($order['shipping_fee']) ?></td>
                            </tr>
                            <tr>
                                <td colspan="3" class="text-end">GST Included:</td>
                                <td class="text-end text-muted"><?= format_inr($order['tax_amount']) ?></td>
                            </tr>
                            <tr class="fs-5 text-success">
                                <td colspan="3" class="text-end fw-bold">Grand Total:</td>
                                <td class="text-end fw-bold"><?= format_inr($order['total_amount']) ?></td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>

            <!-- Shipment Courier Box if available -->
            <?php if ($shipment): ?>
                <div class="card border shadow-sm p-4 bg-white mb-4">
                    <h5 class="fw-bold text-success mb-3"><i class="bi bi-truck me-1"></i> Courier Tracking Information</h5>
                    <div class="row g-2">
                        <div class="col-sm-6">
                            <strong>Courier:</strong> <?= e($shipment['courier_name']) ?>
                        </div>
                        <div class="col-sm-6">
                            <strong>Tracking No (AWB):</strong> <code><?= e($shipment['tracking_number']) ?></code>
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
        </div>

        <!-- Sidebar Details -->
        <div class="col-lg-4">
            <div class="card border shadow-sm p-4 bg-white mb-4">
                <h6 class="fw-bold mb-3">Order Status</h6>
                <span class="badge bg-success-subtle text-success fs-6 text-uppercase mb-3"><?= e($order['status']) ?></span>

                <div class="small mb-3">
                    <strong>Payment Method:</strong> <?= strtoupper($order['payment_method']) ?><br>
                    <strong>Payment Status:</strong> <span class="badge bg-light text-dark border"><?= e($order['payment_status']) ?></span>
                </div>

                <hr>

                <h6 class="fw-bold mb-2">Delivery Address</h6>
                <p class="small text-muted mb-0">
                    <strong><?= e($order['customer_name']) ?></strong><br>
                    <?= e($order['shipping_address_line1']) ?><br>
                    <?php if (!empty($order['shipping_address_line2'])): ?>
                        <?= e($order['shipping_address_line2']) ?><br>
                    <?php endif; ?>
                    <?= e($order['shipping_city']) ?>, <?= e($order['shipping_state']) ?> - <?= e($order['shipping_pincode']) ?><br>
                    Phone: <?= e($order['customer_phone']) ?>
                </p>
            </div>
        </div>
    </div>
</div>
