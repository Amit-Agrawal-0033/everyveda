<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h1 class="h3 mb-0">System Settings & Store Configuration</h1>
        <p class="text-muted small mb-0">Configure store identity, taxes, shipping, Razorpay credentials, and regulatory licenses.</p>
    </div>
</div>

<form action="<?= url('/admin/settings') ?>" method="POST">
    <?= csrf_field() ?>

    <div class="row g-4">
        <!-- 1. General & Contact Settings -->
        <div class="col-lg-6">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-white py-3 border-bottom d-flex align-items-center">
                    <i class="bi bi-shop text-primary fs-5 me-2"></i>
                    <h5 class="card-title mb-0">Store Brand & Contact</h5>
                </div>
                <div class="card-body p-4">
                    <div class="mb-3">
                        <label for="site_name" class="form-label fw-semibold">Store Brand Name</label>
                        <input type="text" class="form-control" id="site_name" name="site_name" value="<?= e($settings['site_name'] ?? 'EveryVeda') ?>" required>
                    </div>

                    <div class="mb-3">
                        <label for="site_tagline" class="form-label fw-semibold">Tagline</label>
                        <input type="text" class="form-control" id="site_tagline" name="site_tagline" value="<?= e($settings['site_tagline'] ?? '') ?>" placeholder="Pure Ayurvedic Wellness, Rooted in Nature">
                    </div>

                    <div class="row g-3 mb-3">
                        <div class="col-sm-6">
                            <label for="contact_email" class="form-label fw-semibold">Support Email</label>
                            <input type="email" class="form-control" id="contact_email" name="contact_email" value="<?= e($settings['contact_email'] ?? '') ?>" required>
                        </div>
                        <div class="col-sm-6">
                            <label for="contact_phone" class="form-label fw-semibold">Support Phone</label>
                            <input type="text" class="form-control" id="contact_phone" name="contact_phone" value="<?= e($settings['contact_phone'] ?? '') ?>">
                        </div>
                    </div>

                    <div class="row g-3 mb-3">
                        <div class="col-sm-6">
                            <label for="whatsapp_number" class="form-label fw-semibold">WhatsApp Support Number</label>
                            <input type="text" class="form-control" id="whatsapp_number" name="whatsapp_number" value="<?= e($settings['whatsapp_number'] ?? '') ?>" placeholder="919876543210">
                            <div class="form-text">Include country code without + or spaces.</div>
                        </div>
                        <div class="col-sm-6">
                            <label for="support_hours" class="form-label fw-semibold">Support Operating Hours</label>
                            <input type="text" class="form-control" id="support_hours" name="support_hours" value="<?= e($settings['support_hours'] ?? 'Mon - Sat: 9:00 AM - 7:00 PM IST') ?>">
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- 2. Legal & Regulatory Compliance -->
        <div class="col-lg-6">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-white py-3 border-bottom d-flex align-items-center">
                    <i class="bi bi-file-earmark-check text-success fs-5 me-2"></i>
                    <h5 class="card-title mb-0">Legal, GST & Ayurvedic Licensing</h5>
                </div>
                <div class="card-body p-4">
                    <div class="mb-3">
                        <label for="company_name" class="form-label fw-semibold">Registered Entity / Company Name</label>
                        <input type="text" class="form-control" id="company_name" name="company_name" value="<?= e($settings['company_name'] ?? 'EveryVeda Wellness Private Limited') ?>" required>
                    </div>

                    <div class="mb-3">
                        <label for="company_address" class="form-label fw-semibold">Registered Office Address</label>
                        <textarea class="form-control" id="company_address" name="company_address" rows="2"><?= e($settings['company_address'] ?? '') ?></textarea>
                    </div>

                    <div class="row g-3 mb-3">
                        <div class="col-sm-6">
                            <label for="gstin" class="form-label fw-semibold">GSTIN (15 Digits)</label>
                            <input type="text" class="form-control font-monospace" id="gstin" name="gstin" value="<?= e($settings['gstin'] ?? '') ?>" placeholder="07AAAAA0000A1Z5">
                        </div>
                        <div class="col-sm-6">
                            <label for="ayush_license" class="form-label fw-semibold">AYUSH Manufacturing License #</label>
                            <input type="text" class="form-control font-monospace" id="ayush_license" name="ayush_license" value="<?= e($settings['ayush_license'] ?? '') ?>" placeholder="DL-1234-AYU">
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="fssai_license" class="form-label fw-semibold">FSSAI License # (14 Digits)</label>
                        <input type="text" class="form-control font-monospace" id="fssai_license" name="fssai_license" value="<?= e($settings['fssai_license'] ?? '') ?>" placeholder="10020011000000">
                    </div>
                </div>
            </div>
        </div>

        <!-- 3. GST Tax & Shipping Configuration -->
        <div class="col-lg-6">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-white py-3 border-bottom d-flex align-items-center">
                    <i class="bi bi-truck text-warning fs-5 me-2"></i>
                    <h5 class="card-title mb-0">GST Tax Rates & Delivery Rules</h5>
                </div>
                <div class="card-body p-4">
                    <h6 class="fw-bold small text-muted text-uppercase mb-3">Goods & Services Tax (GST)</h6>
                    <div class="row g-3 mb-4">
                        <div class="col-4">
                            <label for="tax_cgst_rate" class="form-label small fw-semibold">CGST (%)</label>
                            <input type="number" step="0.01" class="form-control" id="tax_cgst_rate" name="tax_cgst_rate" value="<?= e($settings['tax_cgst_rate'] ?? '6.00') ?>" required>
                        </div>
                        <div class="col-4">
                            <label for="tax_sgst_rate" class="form-label small fw-semibold">SGST (%)</label>
                            <input type="number" step="0.01" class="form-control" id="tax_sgst_rate" name="tax_sgst_rate" value="<?= e($settings['tax_sgst_rate'] ?? '6.00') ?>" required>
                        </div>
                        <div class="col-4">
                            <label for="tax_igst_rate" class="form-label small fw-semibold">IGST (%)</label>
                            <input type="number" step="0.01" class="form-control" id="tax_igst_rate" name="tax_igst_rate" value="<?= e($settings['tax_igst_rate'] ?? '12.00') ?>" required>
                        </div>
                    </div>

                    <h6 class="fw-bold small text-muted text-uppercase mb-3">Shipping & Cash On Delivery</h6>
                    <div class="row g-3 mb-3">
                        <div class="col-sm-6">
                            <label for="shipping_fee_standard" class="form-label small fw-semibold">Standard Shipping (₹)</label>
                            <input type="number" step="0.01" class="form-control" id="shipping_fee_standard" name="shipping_fee_standard" value="<?= e($settings['shipping_fee_standard'] ?? '49.00') ?>" required>
                        </div>
                        <div class="col-sm-6">
                            <label for="shipping_free_threshold" class="form-label small fw-semibold">Free Shipping Min Order (₹)</label>
                            <input type="number" step="0.01" class="form-control" id="shipping_free_threshold" name="shipping_free_threshold" value="<?= e($settings['shipping_free_threshold'] ?? '499.00') ?>" required>
                        </div>
                    </div>

                    <div class="row g-3">
                        <div class="col-sm-6">
                            <label for="enable_cod" class="form-label small fw-semibold">Cash On Delivery (COD)</label>
                            <select class="form-select" id="enable_cod" name="enable_cod">
                                <option value="1" <?= ($settings['enable_cod'] ?? '1') == '1' ? 'selected' : '' ?>>Enabled</option>
                                <option value="0" <?= ($settings['enable_cod'] ?? '1') == '0' ? 'selected' : '' ?>>Disabled</option>
                            </select>
                        </div>
                        <div class="col-sm-6">
                            <label for="cod_extra_charge" class="form-label small fw-semibold">COD Handling Fee (₹)</label>
                            <input type="number" step="0.01" class="form-control" id="cod_extra_charge" name="cod_extra_charge" value="<?= e($settings['cod_extra_charge'] ?? '0.00') ?>">
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- 4. Razorpay Payment Gateway -->
        <div class="col-lg-6">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-white py-3 border-bottom d-flex align-items-center">
                    <i class="bi bi-credit-card text-danger fs-5 me-2"></i>
                    <h5 class="card-title mb-0">Razorpay Payment Gateway</h5>
                </div>
                <div class="card-body p-4">
                    <div class="mb-3">
                        <label for="razorpay_mode" class="form-label fw-semibold">Operation Environment</label>
                        <select class="form-select" id="razorpay_mode" name="razorpay_mode">
                            <option value="test" <?= ($settings['razorpay_mode'] ?? 'test') === 'test' ? 'selected' : '' ?>>Sandbox / Test Mode (rzp_test_...)</option>
                            <option value="live" <?= ($settings['razorpay_mode'] ?? '') === 'live' ? 'selected' : '' ?>>Production / Live Mode (rzp_live_...)</option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label for="razorpay_key_id" class="form-label fw-semibold">Razorpay Key ID</label>
                        <input type="text" class="form-control font-monospace" id="razorpay_key_id" name="razorpay_key_id" value="<?= e($settings['razorpay_key_id'] ?? '') ?>" placeholder="rzp_test_XXXXXXXXXXXXXXXX">
                    </div>

                    <div class="mb-3">
                        <label for="razorpay_key_secret" class="form-label fw-semibold">Razorpay Key Secret</label>
                        <input type="password" class="form-control font-monospace" id="razorpay_key_secret" name="razorpay_key_secret" value="<?= e($settings['razorpay_key_secret'] ?? '') ?>" placeholder="••••••••••••••••••••••••">
                        <div class="form-text">Cryptographically guarded. Never committed to git repositories.</div>
                    </div>

                    <div class="mb-3">
                        <label for="razorpay_webhook_secret" class="form-label fw-semibold">Razorpay Webhook Secret</label>
                        <input type="password" class="form-control font-monospace" id="razorpay_webhook_secret" name="razorpay_webhook_secret" value="<?= e($settings['razorpay_webhook_secret'] ?? '') ?>">
                        <div class="form-text">Configure this secret in your Razorpay Dashboard webhook settings pointing to <code><?= url('/webhook/razorpay') ?></code>.</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- 5. Notifications & Analytics -->
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white py-3 border-bottom d-flex align-items-center">
                    <i class="bi bi-bell text-secondary fs-5 me-2"></i>
                    <h5 class="card-title mb-0">Automated Alerts & Tracking</h5>
                </div>
                <div class="card-body p-4">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label for="low_stock_notification_email" class="form-label fw-semibold">Low Stock Alert Email</label>
                            <input type="email" class="form-control" id="low_stock_notification_email" name="low_stock_notification_email" value="<?= e($settings['low_stock_notification_email'] ?? 'admin@everyveda.com') ?>">
                            <div class="form-text">Receives daily notifications when inventory units fall below threshold.</div>
                        </div>
                        <div class="col-md-6">
                            <label for="google_analytics_id" class="form-label fw-semibold">Google Analytics / Tag Manager ID</label>
                            <input type="text" class="form-control font-monospace" id="google_analytics_id" name="google_analytics_id" value="<?= e($settings['google_analytics_id'] ?? '') ?>" placeholder="G-XXXXXXXXXX">
                            <div class="form-text">Embedded safely in the public header for web analytics.</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="d-flex justify-content-end gap-2 mt-4">
        <button type="submit" class="btn btn-primary px-5 py-2 shadow-sm">
            <i class="bi bi-check2-circle me-1"></i> Save Store Settings
        </button>
    </div>
</form>
