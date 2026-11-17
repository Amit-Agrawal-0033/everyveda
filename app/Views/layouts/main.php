<!DOCTYPE html>
<html lang="<?= e($currentLocale) ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= e($meta_title ?? __('site_title')) ?></title>
    <meta name="description" content="<?= e($meta_description ?? 'Pure Ayurvedic & Wellness Living') ?>">
    <link rel="canonical" href="<?= e(url($_SERVER['REQUEST_URI'] ?? '/')) ?>">

    <!-- Google Fonts: Inter & Noto Sans Devanagari -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&family=Noto+Sans+Devanagari:wght@400;500;600;700&display=swap" rel="stylesheet">

    <!-- Bootstrap 5.3 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">

    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

    <!-- Custom EveryVeda CSS Design System -->
    <link rel="stylesheet" href="<?= asset('assets/css/style.css') ?>">

    <?php if (!empty($settings['google_analytics_id'])): ?>
        <!-- Google Analytics 4 -->
        <script async src="https://www.googletagmanager.com/gtag/js?id=<?= e($settings['google_analytics_id']) ?>"></script>
        <script>
            window.dataLayer = window.dataLayer || [];
            function gtag(){dataLayer.push(arguments);}
            gtag('js', new Date());
            gtag('config', '<?= e($settings['google_analytics_id']) ?>');
        </script>
    <?php endif; ?>

    <?php if (!empty($schemaJson)): ?>
        <script type="application/ld+json">
            <?= $schemaJson ?>
        </script>
    <?php endif; ?>
</head>
<body class="lang-<?= e($currentLocale) ?>">

    <!-- 1. Top Announcement Bar -->
    <div class="announcement-bar text-center">
        <div class="container d-flex justify-content-between align-items-center">
            <span class="d-none d-md-inline"><i class="bi bi-shield-check text-warning"></i> GMP Certified & Classical Shastra Formulated</span>
            <span><?= __('announcement') ?></span>
            <div class="d-none d-md-flex align-items-center gap-3">
                <a href="tel:<?= e($settings['contact_phone'] ?? '+919876543210') ?>" class="text-white-50"><i class="bi bi-telephone"></i> <?= e($settings['contact_phone'] ?? '+91 98765 43210') ?></a>
                <!-- Language Switcher -->
                <div class="dropdown">
                    <button class="btn btn-sm btn-link text-white text-decoration-none dropdown-toggle p-0" type="button" data-bs-toggle="dropdown">
                        <i class="bi bi-translate"></i> <?= $currentLocale === 'hi' ? 'हिन्दी' : 'English' ?>
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end shadow-sm">
                        <li><a class="dropdown-item <?= $currentLocale === 'en' ? 'active' : '' ?>" href="<?= url('/language/en') ?>">English</a></li>
                        <li><a class="dropdown-item <?= $currentLocale === 'hi' ? 'active' : '' ?>" href="<?= url('/language/hi') ?>">हिन्दी (Hindi)</a></li>
                    </ul>
                </div>
            </div>
        </div>
    </div>

    <!-- 2. Main Header -->
    <header class="main-header">
        <div class="container py-3">
            <div class="row align-items-center">
                <!-- Logo -->
                <div class="col-6 col-lg-3">
                    <a href="<?= url('/') ?>" class="brand-logo d-flex align-items-center gap-2 text-decoration-none">
                        <img src="<?= asset('assets/images/logo.png') ?>" alt="EveryVeda - Ayurveda for Every Life" class="img-fluid" style="height: 54px; width: auto; object-fit: contain;">
                    </a>
                </div>

                <!-- Search Bar -->
                <div class="col-12 col-lg-6 order-3 order-lg-2 mt-3 mt-lg-0">
                    <form action="<?= url('/catalog') ?>" method="GET" class="header-search mx-auto">
                        <input type="text" name="q" placeholder="<?= __('search_placeholder') ?>" value="<?= e($_GET['q'] ?? '') ?>" autocomplete="off">
                        <button type="submit" aria-label="Search"><i class="bi bi-search"></i></button>
                    </form>
                </div>

                <!-- Right Action Icons (Account, Wishlist, Cart) -->
                <div class="col-6 col-lg-3 order-2 order-lg-3 text-end d-flex justify-content-end align-items-center gap-3">
                    <!-- Mobile Language Switcher (Visible on small screens) -->
                    <div class="dropdown d-inline-block d-md-none">
                        <button class="btn btn-sm btn-outline-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown">
                            <?= strtoupper($currentLocale) ?>
                        </button>
                        <ul class="dropdown-menu dropdown-menu-end">
                            <li><a class="dropdown-item" href="<?= url('/language/en') ?>">English</a></li>
                            <li><a class="dropdown-item" href="<?= url('/language/hi') ?>">हिन्दी</a></li>
                        </ul>
                    </div>

                    <!-- Track Order Link -->
                    <a href="<?= url('/track-order') ?>" class="text-secondary d-none d-xl-inline-block text-decoration-none small fw-semibold">
                        <i class="bi bi-truck text-success"></i> <?= __('track_order') ?>
                    </a>

                    <!-- Account Dropdown -->
                    <div class="dropdown">
                        <button class="btn btn-outline-light text-dark p-2 rounded-circle" type="button" data-bs-toggle="dropdown" aria-label="My Account">
                            <i class="bi bi-person fs-5"></i>
                        </button>
                        <ul class="dropdown-menu dropdown-menu-end shadow">
                            <?php if ($user): ?>
                                <li class="px-3 py-2 border-bottom">
                                    <strong class="d-block text-truncate" style="max-width: 180px;"><?= e($user['name']) ?></strong>
                                    <small class="text-muted d-block text-truncate" style="max-width: 180px;"><?= e($user['email']) ?></small>
                                </li>
                                <li><a class="dropdown-item py-2" href="<?= url('/account') ?>"><i class="bi bi-speedometer2 me-2"></i> <?= __('dashboard') ?></a></li>
                                <li><a class="dropdown-item py-2" href="<?= url('/account/orders') ?>"><i class="bi bi-box-seam me-2"></i> <?= __('orders') ?></a></li>
                                <li><a class="dropdown-item py-2" href="<?= url('/account/addresses') ?>"><i class="bi bi-geo-alt me-2"></i> <?= __('addresses') ?></a></li>
                                <li><a class="dropdown-item py-2" href="<?= url('/account/profile') ?>"><i class="bi bi-gear me-2"></i> <?= __('profile') ?></a></li>
                                <li><hr class="dropdown-divider"></li>
                                <li><a class="dropdown-item py-2 text-danger" href="<?= url('/account/logout') ?>"><i class="bi bi-box-arrow-right me-2"></i> <?= __('logout') ?></a></li>
                            <?php else: ?>
                                <li><a class="dropdown-item py-2" href="<?= url('/account/login') ?>"><i class="bi bi-box-arrow-in-right me-2"></i> <?= __('login') ?></a></li>
                                <li><a class="dropdown-item py-2" href="<?= url('/account/register') ?>"><i class="bi bi-person-plus me-2"></i> <?= __('register') ?></a></li>
                                <li><hr class="dropdown-divider"></li>
                                <li><a class="dropdown-item py-2" href="<?= url('/track-order') ?>"><i class="bi bi-truck me-2"></i> <?= __('track_order') ?></a></li>
                            <?php endif; ?>
                        </ul>
                    </div>

                    <!-- Cart Icon with Live Count -->
                    <a href="<?= url('/cart') ?>" class="btn btn-outline-light text-dark p-2 rounded-circle position-relative" aria-label="Shopping Cart">
                        <i class="bi bi-bag fs-5"></i>
                        <span class="cart-badge" id="headerCartCount"><?= (int)$cartCount ?></span>
                    </a>
                </div>
            </div>
        </div>

        <!-- 3. Categories Navigation Bar -->
        <nav class="nav-categories-bar">
            <div class="container d-flex align-items-center justify-content-start justify-content-lg-center overflow-x-auto text-nowrap py-1">
                <a href="<?= url('/catalog') ?>" class="category-nav-link <?= empty($_GET['category']) && str_ends_with($_SERVER['REQUEST_URI'] ?? '', '/catalog') ? 'active' : '' ?>">
                    <i class="bi bi-grid-fill me-1"></i> <?= __('catalog') ?>
                </a>
                <?php foreach ($navCategories as $cat): ?>
                    <a href="<?= url('/catalog?category=' . $cat['slug']) ?>" class="category-nav-link <?= ($_GET['category'] ?? '') === $cat['slug'] ? 'active' : '' ?>">
                        <?= e($currentLocale === 'hi' ? $cat['name_hi'] : $cat['name_en']) ?>
                    </a>
                <?php endforeach; ?>
                <a href="<?= url('/blog') ?>" class="category-nav-link">
                    <i class="bi bi-journal-text me-1"></i> <?= __('blog') ?>
                </a>
            </div>
        </nav>
    </header>

    <!-- 4. Flash Alerts Notification Center -->
    <div class="container mt-3">
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

    <!-- 5. Main Content View Body -->
    <main>
        <?= $content ?>
    </main>

    <!-- 6. Floating WhatsApp Support Button -->
    <a href="https://wa.me/<?= e($settings['whatsapp_number'] ?? '919876543210') ?>?text=Namaste%20EveryVeda%2C%20I%20have%20an%20inquiry%20regarding%20wellness%20products." class="whatsapp-float" target="_blank" rel="noopener noreferrer" aria-label="Chat on WhatsApp">
        <i class="bi bi-whatsapp"></i>
    </a>

    <!-- 7. Footer -->
    <footer class="site-footer">
        <div class="container">
            <div class="row g-4">
                <!-- Col 1: Brand Info -->
                <div class="col-12 col-md-4">
                    <div class="d-flex align-items-center gap-2 mb-3">
                        <img src="<?= asset('assets/images/logo.png') ?>" alt="EveryVeda" style="height: 48px; width: auto; object-fit: contain; background:#FAF7EF; padding: 4px 10px; border-radius: 6px;">
                    </div>
                    <p class="small opacity-75">
                        <?= __('footer_about') ?>
                    </p>
                    <p class="small text-white-50">
                        <strong>EVERYVEDA WELLNESS PVT. LTD.</strong><br>
                        B-32, Sector-63, Noida, Uttar Pradesh - 201301<br>
                        <strong>Phone:</strong> <a href="tel:9580251422" class="text-white-50 text-decoration-none">+91 95802 51422</a><br>
                        <strong>Email:</strong> <a href="mailto:care@everyveda.com" class="text-white-50 text-decoration-none">care@everyveda.com</a>
                    </p>
                </div>

                <!-- Col 2: Categories -->
                <div class="col-6 col-md-2">
                    <h5><?= __('categories') ?></h5>
                    <ul class="list-unstyled small">
                        <?php foreach (array_slice($navCategories, 0, 5) as $cat): ?>
                            <li class="mb-2">
                                <a href="<?= url('/catalog?category=' . $cat['slug']) ?>">
                                    <?= e($currentLocale === 'hi' ? $cat['name_hi'] : $cat['name_en']) ?>
                                </a>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                </div>

                <!-- Col 3: Quick Links & Support -->
                <div class="col-6 col-md-3">
                    <h5><?= __('quick_links') ?></h5>
                    <ul class="list-unstyled small">
                        <li class="mb-2"><a href="<?= url('/about-us') ?>"><?= __('about_us') ?></a></li>
                        <li class="mb-2"><a href="<?= url('/catalog') ?>"><?= __('catalog') ?></a></li>
                        <li class="mb-2"><a href="<?= url('/blog') ?>"><?= __('blog') ?></a></li>
                        <li class="mb-2"><a href="<?= url('/track-order') ?>"><?= __('track_order') ?></a></li>
                        <li class="mb-2"><a href="<?= url('/contact-us') ?>"><?= __('contact_us') ?></a></li>
                    </ul>
                </div>

                <!-- Col 4: Legal & Policies -->
                <div class="col-12 col-md-3">
                    <h5><?= __('policies') ?></h5>
                    <ul class="list-unstyled small">
                        <li class="mb-2"><a href="<?= url('/pricing') ?>">Pricing & GST Transparency</a></li>
                        <li class="mb-2"><a href="<?= url('/shipping-policy') ?>"><?= __('shipping_policy') ?></a></li>
                        <li class="mb-2"><a href="<?= url('/return-refund-policy') ?>"><?= __('return_policy') ?></a></li>
                        <li class="mb-2"><a href="<?= url('/privacy-policy') ?>"><?= __('privacy_policy') ?></a></li>
                        <li class="mb-2"><a href="<?= url('/terms-conditions') ?>"><?= __('terms_conditions') ?></a></li>
                        <li class="mb-2"><a href="<?= url('/medical-disclaimer') ?>"><?= __('medical_disclaimer') ?></a></li>
                    </ul>
                </div>
            </div>

            <!-- Footer Bottom Strip -->
            <div class="footer-bottom text-center">
                <div class="d-flex flex-column flex-md-row justify-content-between align-items-center gap-2">
                    <p class="m-0 text-white-50">
                        <?= __('copyright', ['year' => date('Y')]) ?>
                    </p>
                    <div class="d-flex gap-3 align-items-center text-white-50 small">
                        <span><i class="bi bi-shield-lock-fill text-warning"></i> 256-Bit SSL Encrypted</span>
                        <span><i class="bi bi-credit-card-2-front"></i> Razorpay & Cash on Delivery</span>
                    </div>
                </div>
            </div>
        </div>
    </footer>

    <!-- Bootstrap 5.3 Bundle with Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>

    <!-- Custom Frontend App JS -->
    <script src="<?= asset('assets/js/app.js') ?>"></script>
</body>
</html>
