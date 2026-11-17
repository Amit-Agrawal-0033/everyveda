<?php
/**
 * EveryVeda Master Route Registry
 */

declare(strict_types=1);

use App\Core\Router;
use App\Middleware\AuthMiddleware;
use App\Middleware\AdminAuthMiddleware;
use App\Middleware\CsrfMiddleware;

// Public Website Routes
Router::get('/', [App\Controllers\HomeController::class, 'index']);
Router::get('/catalog', [App\Controllers\CatalogController::class, 'index']);
Router::get('/product/{slug}', [App\Controllers\ProductController::class, 'show']);
Router::post('/product/{slug}/review', [App\Controllers\ProductController::class, 'submitReview']);
Router::post('/api/check-pincode', [App\Controllers\ProductController::class, 'checkPincode']);

// Cart Routes
Router::get('/cart', [App\Controllers\CartController::class, 'index']);
Router::post('/cart/add', [App\Controllers\CartController::class, 'add']);
Router::post('/cart/update', [App\Controllers\CartController::class, 'update']);
Router::post('/cart/remove', [App\Controllers\CartController::class, 'remove']);
Router::post('/cart/coupon/apply', [App\Controllers\CartController::class, 'applyCoupon']);
Router::post('/cart/coupon/remove', [App\Controllers\CartController::class, 'removeCoupon']);
Router::get('/api/cart/count', [App\Controllers\CartController::class, 'count']);

// Checkout & Payments
Router::get('/checkout', [App\Controllers\CheckoutController::class, 'index']);
Router::post('/checkout', [App\Controllers\CheckoutController::class, 'submit']);
Router::post('/checkout/razorpay/verify', [App\Controllers\CheckoutController::class, 'verifyRazorpay']);
Router::get('/order-confirmed', [App\Controllers\CheckoutController::class, 'confirmed']);
Router::get('/track-order', [App\Controllers\OrderTrackingController::class, 'index']);
Router::get('/order-tracking', [App\Controllers\OrderTrackingController::class, 'index']);

// Razorpay Webhook (Exempt from CSRF)
Router::post('/webhook/razorpay', [App\Controllers\RazorpayWebhookController::class, 'handle']);

// Localization
Router::get('/language/{locale}', [App\Controllers\LanguageController::class, 'switch']);

// CMS Pages & Policies
Router::get('/about-us', fn($req) => (new App\Controllers\PageController())->show($req, 'about-us'));
Router::get('/shipping-policy', fn($req) => (new App\Controllers\PageController())->show($req, 'shipping-policy'));
Router::get('/return-refund-policy', fn($req) => (new App\Controllers\PageController())->show($req, 'return-refund-policy'));
Router::get('/privacy-policy', fn($req) => (new App\Controllers\PageController())->show($req, 'privacy-policy'));
Router::get('/terms-conditions', fn($req) => (new App\Controllers\PageController())->show($req, 'terms-conditions'));
Router::get('/medical-disclaimer', fn($req) => (new App\Controllers\PageController())->show($req, 'medical-disclaimer'));
Router::get('/pricing', [App\Controllers\PageController::class, 'pricing']);
Router::get('/sample-invoice', [App\Controllers\PageController::class, 'sampleInvoice']);

// Contact & Newsletter
Router::get('/contact-us', [App\Controllers\PageController::class, 'contact']);
Router::post('/contact-us', [App\Controllers\PageController::class, 'submitContact']);
Router::post('/newsletter/subscribe', [App\Controllers\PageController::class, 'subscribeNewsletter']);

// Blog
Router::get('/blog', [App\Controllers\BlogController::class, 'index']);
Router::get('/blog/{slug}', [App\Controllers\BlogController::class, 'show']);

// XML Sitemap for Search Engines
Router::get('/sitemap.xml', [App\Controllers\SitemapController::class, 'index']);

// Customer Authentication
Router::get('/account/login', [App\Controllers\AccountController::class, 'showLogin']);
Router::post('/account/login', [App\Controllers\AccountController::class, 'login']);
Router::get('/account/register', [App\Controllers\AccountController::class, 'showRegister']);
Router::post('/account/register', [App\Controllers\AccountController::class, 'register']);
Router::get('/account/logout', [App\Controllers\AccountController::class, 'logout']);
Router::get('/account/forgot-password', [App\Controllers\AccountController::class, 'showForgotPassword']);
Router::post('/account/forgot-password', [App\Controllers\AccountController::class, 'sendResetLink']);
Router::get('/account/reset-password', [App\Controllers\AccountController::class, 'showResetPassword']);
Router::post('/account/reset-password', [App\Controllers\AccountController::class, 'resetPassword']);

// Protected Customer Account Routes
Router::group(['middleware' => [AuthMiddleware::class]], function () {
    Router::get('/account', [App\Controllers\AccountController::class, 'dashboard']);
    Router::get('/account/orders', [App\Controllers\AccountController::class, 'orders']);
    Router::get('/account/orders/{id}', [App\Controllers\AccountController::class, 'orderDetail']);
    Router::get('/account/orders/{id}/invoice', [App\Controllers\AccountController::class, 'downloadInvoice']);
    Router::get('/account/addresses', [App\Controllers\AccountController::class, 'addresses']);
    Router::post('/account/addresses', [App\Controllers\AccountController::class, 'saveAddress']);
    Router::post('/account/addresses/{id}/delete', [App\Controllers\AccountController::class, 'deleteAddress']);
    Router::get('/account/profile', [App\Controllers\AccountController::class, 'profile']);
    Router::post('/account/profile', [App\Controllers\AccountController::class, 'updateProfile']);
    Router::post('/account/change-password', [App\Controllers\AccountController::class, 'changePassword']);
});

// Admin Authentication (Public Login)
Router::get('/admin/login', [App\Controllers\Admin\AuthController::class, 'showLogin']);
Router::post('/admin/login', [App\Controllers\Admin\AuthController::class, 'login']);
Router::get('/admin/logout', [App\Controllers\Admin\AuthController::class, 'logout']);

// Protected Admin Control Panel Routes
Router::group(['prefix' => '/admin', 'middleware' => [AdminAuthMiddleware::class]], function () {
    Router::get('/', [App\Controllers\Admin\DashboardController::class, 'index']);
    Router::get('/dashboard', [App\Controllers\Admin\DashboardController::class, 'index']);

    // Products
    Router::get('/products', [App\Controllers\Admin\ProductController::class, 'index']);
    Router::get('/products/create', [App\Controllers\Admin\ProductController::class, 'create']);
    Router::post('/products/create', [App\Controllers\Admin\ProductController::class, 'store']);
    Router::get('/products/{id}/edit', [App\Controllers\Admin\ProductController::class, 'edit']);
    Router::post('/products/{id}/edit', [App\Controllers\Admin\ProductController::class, 'update']);
    Router::post('/products/{id}/variants', [App\Controllers\Admin\ProductController::class, 'saveVariant']);
    Router::post('/products/{id}/variants/{variantId}/delete', [App\Controllers\Admin\ProductController::class, 'deleteVariant']);
    Router::post('/products/{id}/delete', [App\Controllers\Admin\ProductController::class, 'delete']);

    // Categories
    Router::get('/categories', [App\Controllers\Admin\CategoryController::class, 'index']);
    Router::get('/categories/create', [App\Controllers\Admin\CategoryController::class, 'create']);
    Router::post('/categories/create', [App\Controllers\Admin\CategoryController::class, 'store']);
    Router::get('/categories/{id}/edit', [App\Controllers\Admin\CategoryController::class, 'edit']);
    Router::post('/categories/{id}/edit', [App\Controllers\Admin\CategoryController::class, 'update']);
    Router::post('/categories/{id}/delete', [App\Controllers\Admin\CategoryController::class, 'delete']);

    // Orders
    Router::get('/orders', [App\Controllers\Admin\OrderController::class, 'index']);
    Router::get('/orders/export/csv', [App\Controllers\Admin\OrderController::class, 'export']);
    Router::get('/orders/{id}', [App\Controllers\Admin\OrderController::class, 'show']);
    Router::post('/orders/{id}/status', [App\Controllers\Admin\OrderController::class, 'updateStatus']);
    Router::post('/orders/{id}/shipment', [App\Controllers\Admin\OrderController::class, 'recordShipment']);
    Router::get('/orders/{id}/invoice', [App\Controllers\Admin\OrderController::class, 'invoice']);

    // Coupons
    Router::get('/coupons', [App\Controllers\Admin\CouponController::class, 'index']);
    Router::get('/coupons/create', [App\Controllers\Admin\CouponController::class, 'create']);
    Router::post('/coupons/create', [App\Controllers\Admin\CouponController::class, 'store']);
    Router::get('/coupons/{id}/edit', [App\Controllers\Admin\CouponController::class, 'edit']);
    Router::post('/coupons/{id}/edit', [App\Controllers\Admin\CouponController::class, 'update']);
    Router::post('/coupons/{id}/delete', [App\Controllers\Admin\CouponController::class, 'delete']);

    // Content: Banners, Blog & CMS Pages
    Router::get('/content/banners', [App\Controllers\Admin\ContentController::class, 'banners']);
    Router::post('/content/banners', [App\Controllers\Admin\ContentController::class, 'storeBanner']);
    Router::post('/content/banners/{id}/delete', [App\Controllers\Admin\ContentController::class, 'deleteBanner']);

    Router::get('/content/blog', [App\Controllers\Admin\ContentController::class, 'blog']);
    Router::get('/content/blog/create', [App\Controllers\Admin\ContentController::class, 'createBlogPost']);
    Router::post('/content/blog/create', [App\Controllers\Admin\ContentController::class, 'storeBlogPost']);
    Router::post('/content/blog/{id}/delete', [App\Controllers\Admin\ContentController::class, 'deleteBlogPost']);

    Router::get('/content/pages', [App\Controllers\Admin\ContentController::class, 'pages']);
    Router::get('/content/pages/{id}/edit', [App\Controllers\Admin\ContentController::class, 'editPage']);
    Router::post('/content/pages/{id}/edit', [App\Controllers\Admin\ContentController::class, 'updatePage']);

    // Customers
    Router::get('/customers', [App\Controllers\Admin\CustomerController::class, 'index']);
    Router::get('/customers/export/csv', [App\Controllers\Admin\CustomerController::class, 'export']);
    Router::get('/customers/{id}', [App\Controllers\Admin\CustomerController::class, 'show']);

    // Reviews Moderation
    Router::get('/reviews', [App\Controllers\Admin\ReviewController::class, 'index']);
    Router::post('/reviews/{id}/status', [App\Controllers\Admin\ReviewController::class, 'updateStatus']);
    Router::post('/reviews/{id}/delete', [App\Controllers\Admin\ReviewController::class, 'delete']);

    // Reports
    Router::get('/reports', [App\Controllers\Admin\ReportController::class, 'index']);
    Router::get('/reports/orders.csv', [App\Controllers\Admin\ReportController::class, 'ordersCsv']);
    Router::get('/reports/inventory.csv', [App\Controllers\Admin\ReportController::class, 'inventoryCsv']);
    Router::get('/reports/customers.csv', [App\Controllers\Admin\ReportController::class, 'customersCsv']);

    // Settings
    Router::get('/settings', [App\Controllers\Admin\SettingController::class, 'index']);
    Router::post('/settings', [App\Controllers\Admin\SettingController::class, 'update']);
});
