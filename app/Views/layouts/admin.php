<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= e($meta_title ?? 'EveryVeda Control Center') ?></title>

    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    
    <!-- Bootstrap 5.3 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    
    <!-- Admin CSS -->
    <link rel="stylesheet" href="<?= asset('assets/css/admin.css') ?>">
</head>
<body class="admin-body">

<div class="admin-wrapper">
    <!-- Admin Sidebar -->
    <aside class="admin-sidebar">
        <div class="sidebar-header d-flex align-items-center gap-2">
            <span class="fs-4 text-warning">ॐ</span>
            <div>
                <h5 class="m-0 text-white fw-bold">Every<span style="color:#C6A45C;">Veda</span></h5>
                <small class="text-white-50" style="font-size:0.75rem;">ADMIN CONTROL CENTER</small>
            </div>
        </div>

        <ul class="sidebar-nav">
            <li class="nav-item">
                <a class="nav-link <?= str_ends_with($_SERVER['REQUEST_URI'] ?? '', '/dashboard') || str_ends_with($_SERVER['REQUEST_URI'] ?? '', '/admin') ? 'active' : '' ?>" href="<?= url('/admin/dashboard') ?>">
                    <i class="bi bi-speedometer2"></i> Dashboard
                </a>
            </li>

            <li class="nav-heading">Store Catalog</li>
            <li class="nav-item">
                <a class="nav-link <?= str_contains($_SERVER['REQUEST_URI'] ?? '', '/admin/products') ? 'active' : '' ?>" href="<?= url('/admin/products') ?>">
                    <i class="bi bi-box-seam"></i> Products & Stock
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link <?= str_contains($_SERVER['REQUEST_URI'] ?? '', '/admin/categories') ? 'active' : '' ?>" href="<?= url('/admin/categories') ?>">
                    <i class="bi bi-tags"></i> Categories
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link <?= str_contains($_SERVER['REQUEST_URI'] ?? '', '/admin/coupons') ? 'active' : '' ?>" href="<?= url('/admin/coupons') ?>">
                    <i class="bi bi-ticket-perforated"></i> Promo Coupons
                </a>
            </li>

            <li class="nav-heading">Sales & Customers</li>
            <li class="nav-item">
                <a class="nav-link <?= str_contains($_SERVER['REQUEST_URI'] ?? '', '/admin/orders') ? 'active' : '' ?>" href="<?= url('/admin/orders') ?>">
                    <i class="bi bi-cart-check"></i> Orders & Shipments
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link <?= str_contains($_SERVER['REQUEST_URI'] ?? '', '/admin/customers') ? 'active' : '' ?>" href="<?= url('/admin/customers') ?>">
                    <i class="bi bi-people"></i> Customers
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link <?= str_contains($_SERVER['REQUEST_URI'] ?? '', '/admin/reviews') ? 'active' : '' ?>" href="<?= url('/admin/reviews') ?>">
                    <i class="bi bi-star-half"></i> Review Moderation
                </a>
            </li>

            <li class="nav-heading">Content & CMS</li>
            <li class="nav-item">
                <a class="nav-link <?= str_contains($_SERVER['REQUEST_URI'] ?? '', '/admin/content/banners') ? 'active' : '' ?>" href="<?= url('/admin/content/banners') ?>">
                    <i class="bi bi-images"></i> Homepage Banners
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link <?= str_contains($_SERVER['REQUEST_URI'] ?? '', '/admin/content/blog') ? 'active' : '' ?>" href="<?= url('/admin/content/blog') ?>">
                    <i class="bi bi-journal-richtext"></i> Wellness Blog
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link <?= str_contains($_SERVER['REQUEST_URI'] ?? '', '/admin/content/pages') ? 'active' : '' ?>" href="<?= url('/admin/content/pages') ?>">
                    <i class="bi bi-file-earmark-text"></i> CMS Policy Pages
                </a>
            </li>

            <li class="nav-heading">Operations & Reports</li>
            <li class="nav-item">
                <a class="nav-link <?= str_contains($_SERVER['REQUEST_URI'] ?? '', '/admin/reports') ? 'active' : '' ?>" href="<?= url('/admin/reports') ?>">
                    <i class="bi bi-file-earmark-spreadsheet"></i> CSV Reports
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link <?= str_contains($_SERVER['REQUEST_URI'] ?? '', '/admin/settings') ? 'active' : '' ?>" href="<?= url('/admin/settings') ?>">
                    <i class="bi bi-sliders"></i> Store Settings
                </a>
            </li>
        </ul>
    </aside>

    <!-- Main Content -->
    <div class="admin-main">
        <!-- Topbar -->
        <header class="admin-topbar">
            <div>
                <a href="<?= url('/') ?>" target="_blank" class="btn btn-sm btn-outline-success">
                    <i class="bi bi-box-arrow-up-right me-1"></i> View Public Store
                </a>
            </div>

            <div class="d-flex align-items-center gap-3">
                <div class="text-end d-none d-sm-block">
                    <strong class="d-block text-dark"><?= e($admin['name'] ?? 'Administrator') ?></strong>
                    <span class="badge bg-secondary"><?= e($admin['role_name'] ?? 'Super Admin') ?></span>
                </div>
                <a href="<?= url('/admin/logout') ?>" class="btn btn-sm btn-outline-danger">
                    <i class="bi bi-box-arrow-right me-1"></i> Sign Out
                </a>
            </div>
        </header>

        <!-- Flash Messages -->
        <div class="px-4 pt-3">
            <?php foreach ($flashes as $type => $messages): ?>
                <?php 
                    $bsClass = match($type) {
                        'error' => 'danger',
                        'success' => 'success',
                        'warning' => 'warning',
                        default => 'info'
                    };
                ?>
                <?php foreach ($messages as $msg): ?>
                    <div class="alert alert-<?= $bsClass ?> alert-dismissible fade show shadow-sm" role="alert">
                        <i class="bi bi-info-circle-fill me-2"></i> <?= e($msg) ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                <?php endforeach; ?>
            <?php endforeach; ?>
        </div>

        <!-- Injected View Content -->
        <div class="admin-content">
            <?= $content ?>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
