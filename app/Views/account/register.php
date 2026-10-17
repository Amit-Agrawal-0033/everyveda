<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-6 col-lg-5">
            <div class="card border shadow-sm p-4 bg-white rounded-3">
                <div class="text-center mb-4">
                    <div class="brand-emblem mx-auto mb-2" style="width: 48px; height: 48px; font-size: 24px;">ॐ</div>
                    <h2 class="h3 fw-bold text-success m-0"><?= __('register') ?></h2>
                    <p class="text-muted small">Create an account to track orders and enjoy personalized Ayurvedic recommendations.</p>
                </div>

                <form action="<?= url('/account/register') ?>" method="POST">
                    <?= csrf_field() ?>

                    <div class="mb-3">
                        <label class="form-label small fw-bold"><?= __('full_name') ?> *</label>
                        <input type="text" name="name" class="form-control" value="<?= old('name') ?>" required autofocus>
                    </div>

                    <div class="mb-3">
                        <label class="form-label small fw-bold"><?= __('email_address') ?> *</label>
                        <input type="email" name="email" class="form-control" value="<?= old('email') ?>" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label small fw-bold"><?= __('phone_number') ?> *</label>
                        <input type="tel" name="phone" class="form-control" placeholder="10-digit mobile number" value="<?= old('phone') ?>" pattern="[6-9][0-9]{9}" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label small fw-bold">Password * (Min. 8 characters)</label>
                        <input type="password" name="password" class="form-control" minlength="8" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label small fw-bold">Confirm Password *</label>
                        <input type="password" name="password_confirmation" class="form-control" minlength="8" required>
                    </div>

                    <button type="submit" class="btn btn-ev-primary w-100 py-2 fs-6 fw-bold mb-3">
                        <?= __('register') ?>
                    </button>

                    <div class="text-center small text-muted">
                        Already have an account?
                        <a href="<?= url('/account/login') ?>" class="fw-bold text-success text-decoration-none">Sign In</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
