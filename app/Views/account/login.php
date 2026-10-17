<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-6 col-lg-5">
            <div class="card border shadow-sm p-4 bg-white rounded-3">
                <div class="text-center mb-4">
                    <div class="brand-emblem mx-auto mb-2" style="width: 48px; height: 48px; font-size: 24px;">ॐ</div>
                    <h2 class="h3 fw-bold text-success m-0"><?= __('login') ?></h2>
                    <p class="text-muted small">Sign in to access your orders, saved addresses & wellness points.</p>
                </div>

                <form action="<?= url('/account/login') ?>" method="POST">
                    <?= csrf_field() ?>

                    <div class="mb-3">
                        <label class="form-label small fw-bold"><?= __('email_address') ?></label>
                        <input type="email" name="email" class="form-control" value="<?= old('email') ?>" required autofocus>
                    </div>

                    <div class="mb-3">
                        <div class="d-flex justify-content-between align-items-center">
                            <label class="form-label small fw-bold">Password</label>
                            <a href="<?= url('/account/forgot-password') ?>" class="small text-decoration-none">Forgot Password?</a>
                        </div>
                        <input type="password" name="password" class="form-control" required>
                    </div>

                    <button type="submit" class="btn btn-ev-primary w-100 py-2 fs-6 fw-bold mb-3">
                        <?= __('login') ?>
                    </button>

                    <div class="text-center small text-muted">
                        Don't have an EveryVeda account?
                        <a href="<?= url('/account/register') ?>" class="fw-bold text-success text-decoration-none">Register here</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
