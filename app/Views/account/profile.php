<div class="container py-4">
    <div class="row g-4">
        <!-- Account Sidebar Navigation -->
        <div class="col-lg-3">
            <div class="card border shadow-sm p-3 bg-white mb-4">
                <div class="list-group list-group-flush small">
                    <a href="<?= url('/account') ?>" class="list-group-item list-group-item-action">
                        <i class="bi bi-speedometer2 me-2"></i> <?= __('dashboard') ?>
                    </a>
                    <a href="<?= url('/account/orders') ?>" class="list-group-item list-group-item-action">
                        <i class="bi bi-box-seam me-2"></i> <?= __('orders') ?>
                    </a>
                    <a href="<?= url('/account/addresses') ?>" class="list-group-item list-group-item-action">
                        <i class="bi bi-geo-alt me-2"></i> <?= __('addresses') ?>
                    </a>
                    <a href="<?= url('/account/profile') ?>" class="list-group-item list-group-item-action fw-bold active bg-success border-success">
                        <i class="bi bi-person me-2"></i> <?= __('profile') ?>
                    </a>
                    <a href="<?= url('/account/logout') ?>" class="list-group-item list-group-item-action text-danger">
                        <i class="bi bi-box-arrow-right me-2"></i> <?= __('logout') ?>
                    </a>
                </div>
            </div>
        </div>

        <div class="col-lg-9">
            <!-- Profile Details Card -->
            <div class="card border shadow-sm p-4 bg-white mb-4">
                <h4 class="fw-bold text-success mb-3"><?= __('profile') ?></h4>
                <form action="<?= url('/account/profile') ?>" method="POST">
                    <?= csrf_field() ?>
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label small fw-bold"><?= __('full_name') ?></label>
                            <input type="text" name="name" class="form-control" value="<?= e($user['name']) ?>" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-bold"><?= __('email_address') ?></label>
                            <input type="email" class="form-control" value="<?= e($user['email']) ?>" disabled>
                            <small class="text-muted">Email address cannot be changed.</small>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-bold"><?= __('phone_number') ?></label>
                            <input type="tel" name="phone" class="form-control" value="<?= e($user['phone']) ?>" pattern="[6-9][0-9]{9}" required>
                        </div>
                        <div class="col-12">
                            <button type="submit" class="btn btn-ev-primary">Save Profile Changes</button>
                        </div>
                    </div>
                </form>
            </div>

            <!-- Change Password Card -->
            <div class="card border shadow-sm p-4 bg-white">
                <h5 class="fw-bold mb-3">Change Account Password</h5>
                <form action="<?= url('/account/change-password') ?>" method="POST">
                    <?= csrf_field() ?>
                    <div class="row g-3">
                        <div class="col-md-12">
                            <label class="form-label small fw-bold">Current Password</label>
                            <input type="password" name="old_password" class="form-control" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-bold">New Password (Min. 8 characters)</label>
                            <input type="password" name="password" class="form-control" minlength="8" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-bold">Confirm New Password</label>
                            <input type="password" name="password_confirmation" class="form-control" minlength="8" required>
                        </div>
                        <div class="col-12">
                            <button type="submit" class="btn btn-outline-success">Update Password</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
