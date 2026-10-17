<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-6 col-lg-5">
            <div class="card border shadow-sm p-4 bg-white rounded-3">
                <div class="text-center mb-4">
                    <h2 class="h3 fw-bold text-success m-0">Set New Password</h2>
                    <p class="text-muted small">Choose a strong, unique password for your EveryVeda account.</p>
                </div>

                <form action="<?= url('/account/reset-password') ?>" method="POST">
                    <?= csrf_field() ?>
                    <input type="hidden" name="token" value="<?= e($token) ?>">

                    <div class="mb-3">
                        <label class="form-label small fw-bold">New Password (Min. 8 characters)</label>
                        <input type="password" name="password" class="form-control" minlength="8" required autofocus>
                    </div>

                    <div class="mb-4">
                        <label class="form-label small fw-bold">Confirm New Password</label>
                        <input type="password" name="password_confirmation" class="form-control" minlength="8" required>
                    </div>

                    <button type="submit" class="btn btn-ev-primary w-100 py-2 fs-6 fw-bold">
                        Update Password & Log In
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
