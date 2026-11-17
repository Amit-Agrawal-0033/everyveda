<div class="container py-4">
    <h1 class="h2 fw-bold text-success mb-4"><?= __('shopping_cart') ?></h1>

    <?php if (empty($cart['items'])): ?>
        <div class="card border shadow-sm p-5 text-center my-4">
            <div class="display-3 text-muted mb-3"><i class="bi bi-cart-x"></i></div>
            <h3 class="fw-bold mb-2"><?= __('cart_empty') ?></h3>
            <p class="text-muted mb-4">Discover our handcrafted classical Rasayanas, herbal churnas, and vitality elixirs.</p>
            <div>
                <a href="<?= url('/catalog') ?>" class="btn-ev-primary btn-lg">
                    <i class="bi bi-arrow-left"></i> <?= __('start_shopping') ?>
                </a>
            </div>
        </div>
    <?php else: ?>
        <!-- Free Shipping Progress Indicator -->
        <?php if ($cart['shipping_fee'] > 0): ?>
            <div class="alert alert-warning d-flex align-items-center mb-4">
                <i class="bi bi-truck fs-4 me-3 text-warning-emphasis"></i>
                <div>
                    <strong>Free Delivery Notice:</strong> <?= __('add_more_for_free_shipping', ['amount' => format_inr($cart['free_shipping_remaining'])]) ?>
                    <div class="progress mt-2" style="height: 6px; width: 300px;">
                        <?php $pct = min(100, round(($cart['subtotal'] / $cart['shipping_threshold']) * 100)); ?>
                        <div class="progress-bar bg-success" style="width: <?= $pct ?>%;"></div>
                    </div>
                </div>
            </div>
        <?php else: ?>
            <div class="alert alert-success d-flex align-items-center mb-4">
                <i class="bi bi-check-circle-fill fs-4 me-3 text-success"></i>
                <div>
                    <strong><?= __('free_shipping_eligible') ?></strong>
                </div>
            </div>
        <?php endif; ?>

        <div class="row g-4">
            <!-- Cart Items Table -->
            <div class="col-lg-8">
                <div class="card border shadow-sm overflow-hidden mb-4">
                    <div class="table-responsive">
                        <table class="table align-middle mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th class="ps-3"><?= __('product') ?></th>
                                    <th><?= __('price') ?></th>
                                    <th style="width: 140px;"><?= __('quantity') ?></th>
                                    <th><?= __('total') ?></th>
                                    <th style="width: 50px;"></th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($cart['items'] as $item): ?>
                                    <tr>
                                        <td class="ps-3">
                                            <div class="d-flex align-items-center gap-3">
                                                <img src="<?= asset('assets/images/products/' . ($item['image'] ?: 'placeholder.svg')) ?>" alt="<?= e($item['name']) ?>" class="rounded border p-1" style="width: 60px; height: 60px; object-fit: contain;">
                                                <div>
                                                    <h6 class="mb-0 fw-bold">
                                                        <a href="<?= url('/product/' . $item['slug']) ?>" class="text-dark text-decoration-none">
                                                            <?= e($item['name']) ?>
                                                        </a>
                                                    </h6>
                                                    <?php if (!empty($item['variant_name'])): ?>
                                                        <small class="badge bg-light text-muted border"><?= e($item['variant_name']) ?></small>
                                                    <?php endif; ?>
                                                    <small class="d-block text-muted">SKU: <?= e($item['sku']) ?></small>
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <span class="fw-semibold"><?= format_inr($item['unit_price']) ?></span>
                                        </td>
                                        <td>
                                            <form action="<?= url('/cart/update') ?>" method="POST" class="d-inline">
                                                <?= csrf_field() ?>
                                                <input type="hidden" name="item_id" value="<?= $item['item_id'] ?>">
                                                <div class="input-group input-group-sm">
                                                    <button class="btn btn-outline-secondary btn-qty-minus" type="button">-</button>
                                                    <input type="number" name="quantity" class="form-control text-center qty-input" value="<?= $item['quantity'] ?>" min="1" max="<?= max(1, (int)$item['stock']) ?>" onchange="this.form.submit()">
                                                    <button class="btn btn-outline-secondary btn-qty-plus" type="button">+</button>
                                                </div>
                                            </form>
                                        </td>
                                        <td>
                                            <strong class="text-success"><?= format_inr($item['line_total']) ?></strong>
                                        </td>
                                        <td class="text-end pe-3">
                                            <form action="<?= url('/cart/remove') ?>" method="POST">
                                                <?= csrf_field() ?>
                                                <input type="hidden" name="item_id" value="<?= $item['item_id'] ?>">
                                                <button type="submit" class="btn btn-link text-danger p-0" title="<?= __('remove') ?>">
                                                    <i class="bi bi-trash3"></i>
                                                </button>
                                            </form>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="d-flex justify-content-between align-items-center">
                    <a href="<?= url('/catalog') ?>" class="btn btn-outline-secondary">
                        <i class="bi bi-arrow-left"></i> Continue Shopping
                    </a>
                </div>
            </div>

            <!-- Order Summary Sidebar -->
            <div class="col-lg-4">
                <!-- Promo Coupon Box -->
                <div class="card border shadow-sm p-3 mb-4 bg-white">
                    <h6 class="fw-bold mb-3"><i class="bi bi-ticket-perforated text-success"></i> <?= __('apply_coupon') ?></h6>
                    <?php if (!empty($cart['coupon_code'])): ?>
                        <div class="alert alert-success d-flex justify-content-between align-items-center p-2 mb-0">
                            <div>
                                <strong class="text-uppercase"><?= e($cart['coupon_code']) ?></strong>
                                <small class="d-block text-muted">Discount applied: <?= format_inr($cart['discount']) ?></small>
                            </div>
                            <form action="<?= url('/cart/coupon/remove') ?>" method="POST" class="m-0">
                                <?= csrf_field() ?>
                                <button type="submit" class="btn btn-sm btn-outline-danger"><?= __('remove') ?></button>
                            </form>
                        </div>
                    <?php else: ?>
                        <form action="<?= url('/cart/coupon/apply') ?>" method="POST">
                            <?= csrf_field() ?>
                            <div class="input-group">
                                <input type="text" name="coupon_code" class="form-control" placeholder="<?= __('enter_coupon') ?>" required>
                                <button type="submit" class="btn btn-ev-secondary"><?= __('apply') ?></button>
                            </div>
                            <small class="text-muted mt-2 d-block">Try coupon <strong>VEDA10</strong> for 10% OFF or <strong>WELCOME100</strong>.</small>
                        </form>
                    <?php endif; ?>
                </div>

                <!-- Order Totals Box -->
                <div class="card border shadow-sm p-4 bg-white">
                    <h5 class="fw-bold mb-3"><?= __('order_summary') ?></h5>

                    <div class="d-flex justify-content-between mb-2">
                        <span class="text-muted"><?= !empty($cart['prices_include_tax']) ? 'Subtotal (MRP Incl. Taxes)' : __('subtotal') ?></span>
                        <span class="fw-bold"><?= format_inr($cart['subtotal']) ?></span>
                    </div>

                    <?php if ($cart['discount'] > 0): ?>
                        <div class="d-flex justify-content-between mb-2 text-success">
                            <span><?= __('discount') ?> <?= !empty($cart['coupon_code']) ? '(' . e($cart['coupon_code']) . ')' : '' ?></span>
                            <span>-<?= format_inr($cart['discount']) ?></span>
                        </div>
                    <?php endif; ?>

                    <div class="d-flex justify-content-between mb-2">
                        <span class="text-muted"><?= __('shipping') ?></span>
                        <span><?= $cart['shipping_fee'] == 0 ? '<span class="text-success fw-bold">' . __('free_shipping') . '</span>' : format_inr($cart['shipping_fee']) ?></span>
                    </div>

                    <?php if (empty($cart['prices_include_tax'])): ?>
                        <!-- Tax-Exclusive Mode: Explicitly add tax line -->
                        <div class="d-flex justify-content-between mb-2 text-muted small">
                            <span>Applicable GST</span>
                            <span class="fw-semibold">+<?= format_inr($cart['tax_amount']) ?></span>
                        </div>
                    <?php endif; ?>

                    <hr class="my-3">

                    <div class="d-flex justify-content-between align-items-baseline mb-2">
                        <span class="h5 fw-bold text-dark"><?= __('total') ?></span>
                        <span class="h3 fw-bold text-success m-0"><?= format_inr($cart['total']) ?></span>
                    </div>

                    <?php if (!empty($cart['prices_include_tax'])): ?>
                        <!-- Tax-Inclusive Mode: Informative GST badge, no double charging! -->
                        <div class="p-2 bg-light rounded text-muted small mb-3 border">
                            <div class="d-flex justify-content-between align-items-center">
                                <span><i class="bi bi-info-circle text-success me-1"></i> Includes <?= format_inr($cart['tax_amount']) ?> GST</span>
                                <span class="badge bg-success-subtle text-success border border-success-subtle">All-Inclusive</span>
                            </div>
                            <div class="text-muted" style="font-size: 0.75rem; margin-top: 3px;">
                                Base taxable value: <?= format_inr($cart['taxable_amount']) ?> • Applicable rates: <?= implode(', ', array_keys($cart['tax_summary'] ?? [])) ?: 'GST' ?>
                            </div>
                        </div>
                    <?php endif; ?>

                    <a href="<?= url('/checkout') ?>" class="btn btn-ev-primary btn-lg w-100 py-3 fw-bold text-center">
                        <?= __('proceed_to_checkout') ?> <i class="bi bi-arrow-right"></i>
                    </a>

                    <div class="text-center mt-3 text-muted small">
                        <i class="bi bi-shield-lock-fill text-warning"></i> <?= __('secure_256bit') ?>
                    </div>
                </div>
            </div>
        </div>
    <?php endif; ?>
</div>
