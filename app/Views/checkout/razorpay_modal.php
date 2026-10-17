<section class="section py-5">
    <div class="container text-center" style="max-width: 650px;">
        <div class="card p-4 p-md-5 shadow-sm border-0">
            <div class="mb-4">
                <div class="spinner-border text-primary mb-3" style="width: 3.5rem; height: 3.5rem;" role="status">
                    <span class="visually-hidden">Loading...</span>
                </div>
                <h3 class="fw-bold mb-2">Connecting to Secure Payment Gateway</h3>
                <p class="text-muted">Please wait while we redirect you to Razorpay to complete your order payment.</p>
            </div>

            <div class="bg-light p-3 rounded mb-4 text-start">
                <div class="d-flex justify-content-between mb-2">
                    <span class="text-muted">Order Number:</span>
                    <strong class="font-monospace"><?= e($checkoutData['order_number'] ?? '') ?></strong>
                </div>
                <div class="d-flex justify-content-between mb-2">
                    <span class="text-muted">Customer:</span>
                    <span><?= e($checkoutData['customer_name'] ?? '') ?></span>
                </div>
                <div class="d-flex justify-content-between mb-2">
                    <span class="text-muted">Amount Payable:</span>
                    <strong class="text-primary fs-5"><?= format_inr(($checkoutData['amount_paise'] ?? 0) / 100) ?></strong>
                </div>
            </div>

            <p class="small text-muted mb-4">
                <i class="bi bi-shield-lock-fill text-success me-1"></i> 256-bit SSL encrypted. Do not refresh or close this window.
            </p>

            <button type="button" id="manual-pay-btn" class="btn btn-primary btn-lg w-100 py-3 shadow-sm">
                <i class="bi bi-credit-card me-2"></i> Click Here to Pay Now
            </button>

            <form id="rzp-verify-form" action="<?= url('/checkout/verify-razorpay') ?>" method="POST" style="display: none;">
                <?= csrf_field() ?>
                <input type="hidden" name="razorpay_order_id" id="rzp_order_id">
                <input type="hidden" name="razorpay_payment_id" id="rzp_payment_id">
                <input type="hidden" name="razorpay_signature" id="rzp_signature">
            </form>
        </div>
    </div>
</section>

<script src="https://checkout.razorpay.com/v1/checkout.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const rzpOptions = {
        key: "<?= e($checkoutData['razorpay_key'] ?? '') ?>",
        amount: <?= (int)($checkoutData['amount_paise'] ?? 0) ?>,
        currency: "<?= e($checkoutData['currency'] ?? 'INR') ?>",
        name: "EveryVeda",
        description: "Order #<?= e($checkoutData['order_number'] ?? '') ?>",
        image: "<?= url('/assets/images/logo.svg') ?>",
        order_id: "<?= e($checkoutData['razorpay_order_id'] ?? '') ?>",
        prefill: {
            name: "<?= e($checkoutData['customer_name'] ?? '') ?>",
            email: "<?= e($checkoutData['customer_email'] ?? '') ?>",
            contact: "<?= e($checkoutData['customer_phone'] ?? '') ?>"
        },
        theme: {
            color: "#214D36"
        },
        handler: function (response) {
            // Post via AJAX or submit form
            fetch("<?= url('/checkout/verify-razorpay') ?>", {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: new URLSearchParams({
                    csrf_token: "<?= csrf_token() ?>",
                    razorpay_order_id: response.razorpay_order_id,
                    razorpay_payment_id: response.razorpay_payment_id,
                    razorpay_signature: response.razorpay_signature
                })
            })
            .then(res => res.json())
            .then(data => {
                if (data.success && data.redirect_url) {
                    window.location.href = data.redirect_url;
                } else {
                    alert(data.error || 'Payment verification failed. Please contact support.');
                    window.location.href = "<?= url('/orders/track?token=' . ($checkoutData['tracking_token'] ?? '')) ?>";
                }
            })
            .catch(err => {
                // Form submit fallback
                document.getElementById('rzp_order_id').value = response.razorpay_order_id;
                document.getElementById('rzp_payment_id').value = response.razorpay_payment_id;
                document.getElementById('rzp_signature').value = response.razorpay_signature;
                document.getElementById('rzp-verify-form').submit();
            });
        },
        modal: {
            ondismiss: function() {
                // If user closed without paying
                console.log('Checkout dismissed');
            }
        }
    };

    const rzpInstance = new Razorpay(rzpOptions);
    rzpInstance.on('payment.failed', function (response) {
        alert("Payment Failed: " + (response.error.description || 'Transaction declined.'));
        window.location.href = "<?= url('/checkout') ?>";
    });

    // Auto-launch checkout
    setTimeout(function() {
        rzpInstance.open();
    }, 500);

    // Fallback button click
    document.getElementById('manual-pay-btn').addEventListener('click', function() {
        rzpInstance.open();
    });
});
</script>
