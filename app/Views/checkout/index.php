<div class="container py-4">
    <h1 class="h2 fw-bold text-success mb-4"><?= __('checkout') ?></h1>

    <div class="row g-4">
        <!-- Checkout Form Column -->
        <div class="col-lg-8">
            <form action="<?= url('/checkout') ?>" method="POST" id="checkoutForm">
                <?= csrf_field() ?>

                <!-- Customer Details -->
                <div class="card border shadow-sm p-4 mb-4 bg-white">
                    <h5 class="fw-bold mb-3"><i class="bi bi-person text-success"></i> <?= __('customer_info') ?></h5>
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label small fw-bold"><?= __('full_name') ?> *</label>
                            <input type="text" name="name" class="form-control" value="<?= old('name', $user['name'] ?? '') ?>" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-bold"><?= __('phone_number') ?> *</label>
                            <input type="tel" name="phone" class="form-control" placeholder="10-digit mobile number" value="<?= old('phone', $user['phone'] ?? '') ?>" pattern="[6-9][0-9]{9}" required>
                        </div>
                        <div class="col-12">
                            <label class="form-label small fw-bold"><?= __('email_address') ?> *</label>
                            <input type="email" name="email" class="form-control" placeholder="For order confirmation & invoice" value="<?= old('email', $user['email'] ?? '') ?>" required>
                        </div>
                    </div>
                </div>

                <!-- Saved Addresses for Registered User -->
                <?php if (!empty($savedAddresses)): ?>
                    <div class="card border shadow-sm p-4 mb-4 bg-white">
                        <h5 class="fw-bold mb-3"><i class="bi bi-bookmark text-success"></i> Select from Saved Addresses</h5>
                        <div class="row g-2">
                            <?php foreach ($savedAddresses as $addr): ?>
                                <div class="col-md-6">
                                    <div class="border rounded p-3 h-100 position-relative address-pick-card" style="cursor: pointer;" onclick="populateAddress(<?= htmlspecialchars(json_encode($addr), ENT_QUOTES, 'UTF-8') ?>)">
                                        <strong class="d-block text-dark"><?= e($addr['recipient_name']) ?> (<?= e($addr['phone']) ?>)</strong>
                                        <p class="small text-muted mb-0">
                                            <?= e($addr['address_line1']) ?>, <?= e($addr['city']) ?>, <?= e($addr['state']) ?> - <?= e($addr['pincode']) ?>
                                        </p>
                                        <button type="button" class="btn btn-sm btn-outline-success mt-2">Use This Address</button>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                <?php endif; ?>

                <!-- Shipping Address -->
                <div class="card border shadow-sm p-4 mb-4 bg-white">
                    <h5 class="fw-bold mb-3"><i class="bi bi-geo-alt text-success"></i> <?= __('shipping_address') ?></h5>
                    <div class="row g-3">
                        <div class="col-12">
                            <label class="form-label small fw-bold"><?= __('address_line1') ?> *</label>
                            <input type="text" name="shipping_address_line1" id="shipping_address_line1" class="form-control" placeholder="House/Flat No, Apartment, Street" value="<?= old('shipping_address_line1') ?>" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-bold"><?= __('address_line2') ?></label>
                            <input type="text" name="shipping_address_line2" id="shipping_address_line2" class="form-control" placeholder="Area, Landmark" value="<?= old('shipping_address_line2') ?>">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-bold"><?= __('pincode') ?> *</label>
                            <input type="text" name="shipping_pincode" id="shipping_pincode" class="form-control" placeholder="6-digit PIN code" maxlength="6" value="<?= old('shipping_pincode') ?>" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-bold"><?= __('city') ?> *</label>
                            <input type="text" name="shipping_city" id="shipping_city" class="form-control" value="<?= old('shipping_city') ?>" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-bold"><?= __('state') ?> *</label>
                            <select name="shipping_state" id="shipping_state" class="form-select" required>
                                <option value="">Select State</option>
                                <?php
                                $states = [
                                    "Andhra Pradesh", "Arunachal Pradesh", "Assam", "Bihar", "Chhattisgarh", "Goa", "Gujarat", "Haryana",
                                    "Himachal Pradesh", "Jharkhand", "Karnataka", "Kerala", "Madhya Pradesh", "Maharashtra", "Manipur",
                                    "Meghalaya", "Mizoram", "Nagaland", "Odisha", "Punjab", "Rajasthan", "Sikkim", "Tamil Nadu",
                                    "Telangana", "Tripura", "Uttar Pradesh", "Uttarakhand", "West Bengal", "Delhi", "Jammu and Kashmir"
                                ];
                                foreach ($states as $st):
                                ?>
                                    <option value="<?= $st ?>" <?= old('shipping_state') === $st ? 'selected' : '' ?>><?= $st ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <?php if ($user): ?>
                            <div class="col-12">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="save_address" value="1" id="saveAddressCheck" checked>
                                    <label class="form-check-label small" for="saveAddressCheck">Save this address to my profile</label>
                                </div>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Billing Address Toggle -->
                <div class="card border shadow-sm p-4 mb-4 bg-white">
                    <div class="form-check mb-0">
                        <input class="form-check-input" type="checkbox" name="same_as_shipping" id="sameAsShippingCheck" value="1" checked onchange="toggleBillingAddress(this)">
                        <label class="form-check-label fw-bold" for="sameAsShippingCheck">
                            <?= __('same_as_shipping') ?>
                        </label>
                    </div>

                    <!-- Separate Billing Address Fields -->
                    <div id="billingAddressFields" class="row g-3 mt-2" style="display: none;">
                        <h6 class="fw-bold text-success border-top pt-3"><?= __('billing_address') ?></h6>
                        <div class="col-12">
                            <label class="form-label small fw-bold"><?= __('address_line1') ?> *</label>
                            <input type="text" name="billing_address_line1" class="form-control" placeholder="House/Flat No, Apartment, Street">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-bold"><?= __('pincode') ?> *</label>
                            <input type="text" name="billing_pincode" class="form-control" maxlength="6">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-bold"><?= __('city') ?> *</label>
                            <input type="text" name="billing_city" class="form-control">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-bold"><?= __('state') ?> *</label>
                            <select name="billing_state" class="form-select">
                                <option value="">Select State</option>
                                <?php foreach ($states as $st): ?>
                                    <option value="<?= $st ?>"><?= $st ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                </div>

                <!-- Payment Method Selector -->
                <div class="card border shadow-sm p-4 mb-4 bg-white">
                    <h5 class="fw-bold mb-3"><i class="bi bi-credit-card text-success"></i> <?= __('payment_method') ?></h5>

                    <div class="form-check p-3 border rounded mb-3 bg-light">
                        <input class="form-check-input" type="radio" name="payment_method" id="payRazorpay" value="razorpay" checked>
                        <label class="form-check-label fw-bold d-block" for="payRazorpay">
                            <?= __('pay_online') ?>
                            <small class="d-block text-muted fw-normal">Google Pay, PhonePe, Paytm, UPI, Cards, NetBanking, EMI & Wallets</small>
                        </label>
                    </div>

                    <?php if ($enableCod): ?>
                        <div class="form-check p-3 border rounded mb-3">
                            <input class="form-check-input" type="radio" name="payment_method" id="payCod" value="cod">
                            <label class="form-check-label fw-bold d-block" for="payCod">
                                <?= __('pay_cod') ?>
                                <small class="d-block text-muted fw-normal">Pay cash upon delivery at your doorstep</small>
                            </label>
                        </div>
                    <?php endif; ?>
                </div>

                <!-- Order Notes -->
                <div class="card border shadow-sm p-4 mb-4 bg-white">
                    <label class="form-label fw-bold small text-muted text-uppercase"><?= __('order_notes') ?></label>
                    <textarea name="order_notes" rows="2" class="form-control" placeholder="E.g. landmark details, deliver during daytime..."></textarea>
                </div>

                <!-- Submit Button -->
                <button type="submit" id="btnPlaceOrder" class="btn btn-ev-primary btn-lg w-100 py-3 fw-bold fs-5 shadow">
                    <i class="bi bi-shield-check"></i> <?= __('place_order') ?> (<?= format_inr($cart['total']) ?>)
                </button>
            </form>
        </div>

        <!-- Order Summary Column -->
        <div class="col-lg-4">
            <div class="card border shadow-sm p-4 bg-white sticky-top" style="top: 100px;">
                <h5 class="fw-bold mb-3"><?= __('order_summary') ?> (<?= $cart['count'] ?> Items)</h5>

                <!-- Mini Items List -->
                <div class="mb-3 overflow-auto" style="max-height: 250px;">
                    <?php foreach ($cart['items'] as $item): ?>
                        <div class="d-flex align-items-center gap-2 mb-2 pb-2 border-bottom">
                            <img src="<?= asset('assets/images/products/' . ($item['image'] ?: 'placeholder.svg')) ?>" alt="<?= e($item['name']) ?>" style="width: 45px; height: 45px; object-fit: contain;">
                            <div class="flex-grow-1 small">
                                <strong class="d-block text-truncate" style="max-width: 170px;"><?= e($item['name']) ?></strong>
                                <span class="text-muted"><?= $item['quantity'] ?> × <?= format_inr($item['unit_price']) ?></span>
                            </div>
                            <span class="fw-bold small"><?= format_inr($item['line_total']) ?></span>
                        </div>
                    <?php endforeach; ?>
                </div>

                <div class="d-flex justify-content-between mb-2">
                    <span class="text-muted"><?= !empty($cart['prices_include_tax']) ? 'Subtotal (MRP Incl. Taxes)' : __('subtotal') ?></span>
                    <span class="fw-bold"><?= format_inr($cart['subtotal']) ?></span>
                </div>

                <?php if ($cart['discount'] > 0): ?>
                    <div class="d-flex justify-content-between mb-2 text-success">
                        <span><?= __('discount') ?> (<?= e($cart['coupon_code']) ?>)</span>
                        <span>-<?= format_inr($cart['discount']) ?></span>
                    </div>
                <?php endif; ?>

                <div class="d-flex justify-content-between mb-2">
                    <span class="text-muted"><?= __('shipping') ?></span>
                    <span><?= $cart['shipping_fee'] == 0 ? '<span class="text-success fw-bold">' . __('free_shipping') . '</span>' : format_inr($cart['shipping_fee']) ?></span>
                </div>

                <?php if (empty($cart['prices_include_tax'])): ?>
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
                    <div class="p-2 bg-light rounded text-muted small mb-3 border">
                        <div class="d-flex justify-content-between align-items-center">
                            <span><i class="bi bi-info-circle text-success me-1"></i> Includes <?= format_inr($cart['tax_amount']) ?> GST</span>
                            <span class="badge bg-success-subtle text-success border border-success-subtle" id="gstBadgeType"><?= $cart['tax_type'] === 'intrastate' ? 'CGST + SGST' : 'IGST' ?></span>
                        </div>
                        <div class="text-muted" style="font-size: 0.75rem; margin-top: 3px;" id="gstBreakdownDetails">
                            <?php if ($cart['tax_type'] === 'intrastate'): ?>
                                CGST: <?= format_inr($cart['cgst_amount']) ?> • SGST: <?= format_inr($cart['sgst_amount']) ?>
                            <?php else: ?>
                                IGST: <?= format_inr($cart['igst_amount']) ?>
                            <?php endif; ?>
                            • Base Taxable: <?= format_inr($cart['taxable_amount']) ?>
                        </div>
                    </div>
                <?php endif; ?>

                <div class="alert alert-light border small text-muted text-center m-0">
                    <i class="bi bi-shield-check text-success"></i> 100% Secure Checkout Guaranteed
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Razorpay Official Checkout SDK -->
<script src="https://checkout.razorpay.com/v1/checkout.js"></script>

<script>
function toggleBillingAddress(check) {
    document.getElementById('billingAddressFields').style.display = check.checked ? 'none' : 'flex';
}

function populateAddress(addr) {
    document.getElementById('shipping_address_line1').value = addr.address_line1;
    document.getElementById('shipping_address_line2').value = addr.address_line2 || '';
    document.getElementById('shipping_pincode').value = addr.pincode;
    document.getElementById('shipping_city').value = addr.city;
    document.getElementById('shipping_state').value = addr.state;
    document.getElementById('shipping_state').dispatchEvent(new Event('change'));
}

// Dynamic Intrastate (CGST/SGST) vs Interstate (IGST) live label switcher
const stateSelect = document.getElementById('shipping_state');
if (stateSelect) {
    stateSelect.addEventListener('change', function () {
        const val = this.value.trim().toLowerCase();
        const badge = document.getElementById('gstBadgeType');
        const details = document.getElementById('gstBreakdownDetails');
        if (badge && details) {
            const totalTax = <?= json_encode(format_inr($cart['tax_amount'])) ?>;
            const cgst = <?= json_encode(format_inr($cart['cgst_amount'] > 0 ? $cart['cgst_amount'] : round($cart['tax_amount'] / 2, 2))) ?>;
            const sgst = <?= json_encode(format_inr($cart['sgst_amount'] > 0 ? $cart['sgst_amount'] : round($cart['tax_amount'] / 2, 2))) ?>;
            const baseTaxable = <?= json_encode(format_inr($cart['taxable_amount'])) ?>;

            if (val === 'uttar pradesh' || val === '') {
                badge.innerText = 'CGST + SGST';
                details.innerText = 'CGST: ' + cgst + ' • SGST: ' + sgst + ' • Base Taxable: ' + baseTaxable;
            } else {
                badge.innerText = 'IGST';
                details.innerText = 'IGST: ' + totalTax + ' • Base Taxable: ' + baseTaxable;
            }
        }
    });
}

// Intercept checkout form to handle Razorpay Modal securely
document.getElementById('checkoutForm').addEventListener('submit', function (e) {
    const paymentMethod = document.querySelector('input[name="payment_method"]:checked').value;
    if (paymentMethod !== 'razorpay') {
        return; // Standard submit for COD
    }

    e.preventDefault();
    const btn = document.getElementById('btnPlaceOrder');
    btn.disabled = true;
    btn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span> Initializing Secure Payment...';

    const formData = new FormData(this);

    fetch('<?= url("/checkout") ?>', {
        method: 'POST',
        headers: {
            'X-Requested-With': 'XMLHttpRequest',
            'Accept': 'application/json'
        },
        body: formData
    })
    .then(res => res.json())
    .then(data => {
        if (!data.success) {
            btn.disabled = false;
            btn.innerHTML = '<i class="bi bi-shield-check"></i> <?= __("place_order") ?>';
            alert(data.error || data.message || 'Validation error. Please verify your address.');
            return;
        }

        // Launch Razorpay Checkout Modal
        const options = {
            key: data.razorpay_key,
            amount: data.amount_paise,
            currency: data.currency,
            name: "EveryVeda Organics",
            description: "Ayurvedic Order #" + data.order_number,
            image: "<?= asset('assets/images/logo.svg') ?>",
            order_id: data.razorpay_order_id,
            prefill: {
                name: data.customer_name,
                email: data.customer_email,
                contact: data.customer_phone
            },
            theme: {
                color: "#214D36"
            },
            handler: function (response) {
                btn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span> Verifying Payment Signature...';
                
                // Verify signature server-side
                const verifyData = new FormData();
                verifyData.append('_csrf_token', '<?= csrf_token() ?>');
                verifyData.append('razorpay_order_id', response.razorpay_order_id);
                verifyData.append('razorpay_payment_id', response.razorpay_payment_id);
                verifyData.append('razorpay_signature', response.razorpay_signature);

                fetch('<?= url("/checkout/razorpay/verify") ?>', {
                    method: 'POST',
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    body: verifyData
                })
                .then(vRes => vRes.json())
                .then(vData => {
                    if (vData.success && vData.redirect_url) {
                        window.location.href = vData.redirect_url;
                    } else {
                        alert(vData.error || 'Signature verification failed.');
                        btn.disabled = false;
                        btn.innerHTML = '<i class="bi bi-shield-check"></i> Try Payment Again';
                    }
                })
                .catch(() => {
                    alert('Communication error verifying payment.');
                    btn.disabled = false;
                });
            },
            modal: {
                ondismiss: function () {
                    btn.disabled = false;
                    btn.innerHTML = '<i class="bi bi-shield-check"></i> <?= __("place_order") ?>';
                }
            }
        };

        const rzp = new Razorpay(options);
        rzp.open();
    })
    .catch(err => {
        btn.disabled = false;
        btn.innerHTML = '<i class="bi bi-shield-check"></i> <?= __("place_order") ?>';
        alert('Could not initialize checkout. Please check internet connection.');
    });
});
</script>
