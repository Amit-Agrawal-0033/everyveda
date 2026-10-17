<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= e($meta_title ?? 'Admin Login') ?></title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <style>
        body { background-color: #183827; font-family: 'Inter', sans-serif; display: flex; align-items: center; justify-content: center; min-height: 100vh; margin: 0; }
        .login-box { width: 100%; max-width: 420px; padding: 15px; }
        .card { border: none; border-radius: 12px; box-shadow: 0 10px 30px rgba(0,0,0,0.3); }
        .btn-ev-admin { background-color: #214D36; color: #fff; font-weight: 600; border: none; }
        .btn-ev-admin:hover { background-color: #173827; color: #fff; }
    </style>
</head>
<body>

<div class="login-box">
    <div class="card p-4 p-md-5">
        <div class="text-center mb-4">
            <span class="fs-1 text-warning">ॐ</span>
            <h3 class="fw-bold text-dark mb-0">Every<span style="color:#C6A45C;">Veda</span></h3>
            <span class="badge bg-secondary text-uppercase mt-1">Control Center</span>
        </div>

        <?php if (!empty($flashes['error'])): ?>
            <?php foreach ($flashes['error'] as $err): ?>
                <div class="alert alert-danger small p-2 mb-3">
                    <i class="bi bi-exclamation-octagon-fill me-1"></i> <?= e($err) ?>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>

        <?php if (!empty($flashes['warning'])): ?>
            <?php foreach ($flashes['warning'] as $w): ?>
                <div class="alert alert-warning small p-2 mb-3">
                    <i class="bi bi-exclamation-triangle-fill me-1"></i> <?= e($w) ?>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>

        <form action="<?= url('/admin/login') ?>" method="POST">
            <?= csrf_field() ?>

            <div class="mb-3">
                <label class="form-label small fw-bold">Admin Email</label>
                <input type="email" name="email" class="form-control" value="<?= old('email') ?>" placeholder="admin@everyveda.com" required autofocus>
            </div>

            <div class="mb-4">
                <label class="form-label small fw-bold">Password</label>
                <input type="password" name="password" class="form-control" placeholder="••••••••" required>
            </div>

            <button type="submit" class="btn btn-ev-admin w-100 py-2 fs-6">
                <i class="bi bi-lock-fill me-1"></i> Authenticate
            </button>
        </form>

        <div class="text-center mt-4">
            <small class="text-muted">Default Login: <code>admin@everyveda.com</code></small>
        </div>
    </div>
</div>

</body>
</html>
