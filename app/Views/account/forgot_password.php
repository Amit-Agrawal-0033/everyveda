<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-6 col-lg-5">
            <div class="card border shadow-sm p-4 bg-white rounded-3">
                <div class="text-center mb-4">
                    <h2 class="h3 fw-bold text-success m-0">Reset Password</h2>
                    <p class="text-muted small">Enter your account email to receive a secure, single-use password reset link.</p>
                </div>

                <form action="<?= url('/account/forgot-password') ?>" method="POST">
                    <?= csrf_field() ?>

                    <div class="mb-4">
                        <label class="form-label small fw-bold"><?= __('email_address') ?></label>
                        <input type="email" name="email" class="form-control" placeholder="name@example.com" required autofocus>
                    </div>

                    <button type="submit" class="btn btn-ev-primary w-100 py-2 fs-6 fw-bold mb-3">
                        Send Password Reset Link
                    </button>

                    <div class="text-center small">
                        <a href="<?= url('/account/login') ?>" class="text-muted text-decoration-none">← Return to Login</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
