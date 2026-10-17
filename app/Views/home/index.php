<!-- Homepage Hero Slider -->
<section class="hero-slider-wrap">
    <div id="homeHeroCarousel" class="carousel slide carousel-fade" data-bs-ride="carousel" data-bs-interval="5000">
        <div class="carousel-indicators">
            <?php foreach ($banners as $idx => $banner): ?>
                <button type="button" data-bs-target="#homeHeroCarousel" data-bs-slide-to="<?= $idx ?>" class="<?= $idx === 0 ? 'active' : '' ?>" aria-label="Slide <?= $idx + 1 ?>"></button>
            <?php endforeach; ?>
        </div>
        <div class="carousel-inner">
            <?php foreach ($banners as $idx => $banner): ?>
                <div class="carousel-item <?= $idx === 0 ? 'active' : '' ?>">
                    <div class="hero-slide-item" style="background: linear-gradient(rgba(24, 56, 39, 0.85), rgba(33, 77, 54, 0.9)), url('<?= asset('assets/images/banners/' . $banner['image_path']) ?>') center/cover;">
                        <div class="container text-center text-lg-start">
                            <div class="row align-items-center">
                                <div class="col-lg-8">
                                    <span class="hero-badge">🌿 100% Classical Shastra Ayurveda</span>
                                    <h1 class="hero-title text-white"><?= e($banner['title']) ?></h1>
                                    <p class="hero-subtitle text-white-50"><?= e($banner['subtitle']) ?></p>
                                    <div class="d-flex flex-wrap gap-3 justify-content-center justify-content-lg-start">
                                        <a href="<?= url($banner['link_url'] ?: '/catalog') ?>" class="btn-ev-secondary btn-lg">
                                            <?= __('shop_now') ?> <i class="bi bi-arrow-right"></i>
                                        </a>
                                        <a href="<?= url('/about-us') ?>" class="btn btn-outline-light btn-lg px-4">
                                            <?= __('about_us') ?>
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
        <button class="carousel-control-prev" type="button" data-bs-target="#homeHeroCarousel" data-bs-slide="prev">
            <span class="carousel-control-prev-icon"></span>
        </button>
        <button class="carousel-control-next" type="button" data-bs-target="#homeHeroCarousel" data-bs-slide="next">
            <span class="carousel-control-next-icon"></span>
        </button>
    </div>
</section>

<!-- Trust Badges Strip -->
<section class="trust-strip">
    <div class="container">
        <div class="row g-3">
            <div class="col-6 col-lg-3">
                <div class="trust-card">
                    <div class="trust-icon"><i class="bi bi-patch-check"></i></div>
                    <div>
                        <h6 class="trust-title"><?= __('badge_ayurvedic') ?></h6>
                        <p class="trust-desc"><?= __('badge_ayurvedic_sub') ?></p>
                    </div>
                </div>
            </div>
            <div class="col-6 col-lg-3">
                <div class="trust-card">
                    <div class="trust-icon"><i class="bi bi-flower1"></i></div>
                    <div>
                        <h6 class="trust-title"><?= __('badge_pure') ?></h6>
                        <p class="trust-desc"><?= __('badge_pure_sub') ?></p>
                    </div>
                </div>
            </div>
            <div class="col-6 col-lg-3">
                <div class="trust-card">
                    <div class="trust-icon"><i class="bi bi-truck"></i></div>
                    <div>
                        <h6 class="trust-title"><?= __('badge_shipping') ?></h6>
                        <p class="trust-desc"><?= __('badge_shipping_sub') ?></p>
                    </div>
                </div>
            </div>
            <div class="col-6 col-lg-3">
                <div class="trust-card">
                    <div class="trust-icon"><i class="bi bi-shield-lock"></i></div>
                    <div>
                        <h6 class="trust-title"><?= __('badge_payment') ?></h6>
                        <p class="trust-desc"><?= __('badge_payment_sub') ?></p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Featured Categories Section -->
<section class="py-5 bg-white">
    <div class="container">
        <div class="d-flex justify-content-between align-items-end mb-4">
            <div>
                <span class="text-uppercase small fw-bold text-success">Ancient Formulations</span>
                <h2 class="m-0"><?= __('featured_categories') ?></h2>
            </div>
            <a href="<?= url('/catalog') ?>" class="btn-ev-outline btn-sm d-none d-sm-inline-flex">
                <?= __('view_all') ?> <i class="bi bi-chevron-right"></i>
            </a>
        </div>

        <div class="row g-3 g-md-4">
            <?php foreach ($categories as $cat): ?>
                <div class="col-6 col-md-4 col-lg-2">
                    <a href="<?= url('/catalog?category=' . $cat['slug']) ?>" class="category-card">
                        <div class="category-icon-box">
                            <img src="<?= asset('assets/images/categories/' . $cat['image']) ?>" alt="<?= e($cat['name_en']) ?>">
                        </div>
                        <h6 class="category-card-title"><?= e($currentLocale === 'hi' ? $cat['name_hi'] : $cat['name_en']) ?></h6>
                        <span class="category-card-count"><?= $cat['product_count'] ?> Products</span>
                    </a>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<!-- Best Sellers Section -->
<section class="py-5" style="background-color: #faf9f6;">
    <div class="container">
        <div class="d-flex justify-content-between align-items-end mb-4">
            <div>
                <span class="text-uppercase small fw-bold text-success">Customer Favorites</span>
                <h2 class="m-0"><?= __('best_sellers') ?></h2>
            </div>
            <a href="<?= url('/catalog') ?>" class="btn-ev-outline btn-sm">
                <?= __('view_all') ?> <i class="bi bi-chevron-right"></i>
            </a>
        </div>

        <div class="row g-3 g-md-4">
            <?php foreach ($bestSellers as $p): ?>
                <div class="col-6 col-md-4 col-lg-3">
                    <div class="product-card">
                        <?php if ($p['sale_price']): ?>
                            <span class="product-card-badge sale">Sale</span>
                        <?php elseif ($p['is_best_seller']): ?>
                            <span class="product-card-badge">Top Seller</span>
                        <?php endif; ?>

                        <a href="<?= url('/product/' . $p['slug']) ?>" class="product-img-wrap">
                            <img src="<?= asset('assets/images/products/' . ($p['primary_image'] ?: 'placeholder.svg')) ?>" alt="<?= e($p['name']) ?>" loading="lazy">
                        </a>

                        <div class="product-info">
                            <span class="product-category-tag"><?= e($p['category_slug'] ?? 'Ayurveda') ?></span>
                            <h3 class="product-title">
                                <a href="<?= url('/product/' . $p['slug']) ?>"><?= e($p['name']) ?></a>
                            </h3>

                            <div class="product-rating">
                                <i class="bi bi-star-fill"></i>
                                <i class="bi bi-star-fill"></i>
                                <i class="bi bi-star-fill"></i>
                                <i class="bi bi-star-fill"></i>
                                <i class="bi bi-star-fill"></i>
                                <span class="ms-1 text-muted small">(4.9)</span>
                            </div>

                            <div class="product-pricing">
                                <?php if ($p['sale_price']): ?>
                                    <span class="current-price"><?= format_inr($p['sale_price']) ?></span>
                                    <span class="original-price"><?= format_inr($p['price']) ?></span>
                                    <span class="discount-badge">Save <?= round((($p['price'] - $p['sale_price']) / $p['price']) * 100) ?>%</span>
                                <?php else: ?>
                                    <span class="current-price"><?= format_inr($p['price']) ?></span>
                                <?php endif; ?>
                            </div>

                            <form action="<?= url('/cart/add') ?>" method="POST" class="mt-2">
                                <?= csrf_field() ?>
                                <input type="hidden" name="product_id" value="<?= $p['id'] ?>">
                                <input type="hidden" name="quantity" value="1">
                                <button type="submit" class="btn btn-ev-primary w-100 py-2">
                                    <i class="bi bi-bag-plus"></i> <?= __('add_to_cart') ?>
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<!-- Brand Heritage & Trust Section -->
<section class="py-5 bg-white border-top border-bottom">
    <div class="container">
        <div class="row align-items-center g-4">
            <div class="col-lg-6">
                <span class="text-uppercase small fw-bold text-success">Tradition & Science</span>
                <h2 class="display-6 fw-bold mb-3">Authentic Ayurveda for Modern Daily Life</h2>
                <p class="lead text-muted">
                    At EveryVeda, we believe nature holds the ultimate restorative balance. Every rasayana, churnam, and herbal elixir is prepared according to classical Ayurvedic scriptures like Charaka Samhita.
                </p>
                <div class="row g-3 mt-2">
                    <div class="col-sm-6">
                        <div class="d-flex gap-3">
                            <i class="bi bi-check2-circle fs-3 text-success"></i>
                            <div>
                                <h6 class="fw-bold mb-1">Ethical Wildcrafting</h6>
                                <p class="small text-muted mb-0">Harvested at peak botanical potency from pristine Indian forest reserves.</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <div class="d-flex gap-3">
                            <i class="bi bi-shield-check fs-3 text-success"></i>
                            <div>
                                <h6 class="fw-bold mb-1">Heavy Metal Tested</h6>
                                <p class="small text-muted mb-0">Lab tested for lead, arsenic, and mercury to ensure maximum safety.</p>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="mt-4">
                    <a href="<?= url('/about-us') ?>" class="btn-ev-primary">
                        Read Our Full Story <i class="bi bi-arrow-right"></i>
                    </a>
                </div>
            </div>
            <div class="col-lg-6">
                <div class="p-4 bg-light rounded-4 text-center border">
                    <img src="<?= asset('assets/images/products/product-chyawanprash.svg') ?>" alt="EveryVeda Heritage" class="img-fluid" style="max-height: 340px;">
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Featured Products (Recommended by Vaidyas) -->
<section class="py-5" style="background-color: #faf9f6;">
    <div class="container">
        <div class="d-flex justify-content-between align-items-end mb-4">
            <div>
                <span class="text-uppercase small fw-bold text-success">Vaidya Selected</span>
                <h2 class="m-0"><?= __('vaidya_recommended') ?></h2>
            </div>
            <a href="<?= url('/catalog') ?>" class="btn-ev-outline btn-sm">
                <?= __('view_all') ?> <i class="bi bi-chevron-right"></i>
            </a>
        </div>

        <div class="row g-3 g-md-4">
            <?php foreach ($featuredProducts as $p): ?>
                <div class="col-6 col-md-4 col-lg-3">
                    <div class="product-card">
                        <a href="<?= url('/product/' . $p['slug']) ?>" class="product-img-wrap">
                            <img src="<?= asset('assets/images/products/' . ($p['primary_image'] ?: 'placeholder.svg')) ?>" alt="<?= e($p['name']) ?>" loading="lazy">
                        </a>
                        <div class="product-info">
                            <span class="product-category-tag"><?= e($p['category_slug'] ?? 'Wellness') ?></span>
                            <h3 class="product-title">
                                <a href="<?= url('/product/' . $p['slug']) ?>"><?= e($p['name']) ?></a>
                            </h3>
                            <div class="product-pricing">
                                <?php if ($p['sale_price']): ?>
                                    <span class="current-price"><?= format_inr($p['sale_price']) ?></span>
                                    <span class="original-price"><?= format_inr($p['price']) ?></span>
                                <?php else: ?>
                                    <span class="current-price"><?= format_inr($p['price']) ?></span>
                                <?php endif; ?>
                            </div>
                            <a href="<?= url('/product/' . $p['slug']) ?>" class="btn-ev-outline w-100 text-center py-2 mt-2">
                                <?= __('shop_now') ?>
                            </a>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<!-- Customer Testimonials -->
<section class="py-5 bg-white">
    <div class="container">
        <div class="text-center mb-5">
            <span class="text-uppercase small fw-bold text-success">Real Experiences</span>
            <h2 class="m-0"><?= __('customer_reviews') ?></h2>
        </div>

        <div class="row g-4">
            <div class="col-md-4">
                <div class="card h-100 border p-4 shadow-sm">
                    <div class="text-warning mb-2 fs-5">
                        <i class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i>
                    </div>
                    <p class="text-muted fst-italic">"EveryVeda Royal Gold Chyawanprash has transformed our winter immunity. The taste of wild amla and Desi cow ghee is genuine and traditional, completely unlike mass-market sugary pastes."</p>
                    <div class="mt-auto d-flex align-items-center gap-2">
                        <div class="bg-success text-white rounded-circle p-2 fw-bold" style="width:36px;height:36px;display:flex;align-items:center;justify-content:center;">RP</div>
                        <div>
                            <strong class="d-block">Dr. Rajesh Patel</strong>
                            <small class="text-muted"><i class="bi bi-patch-check-fill text-success"></i> Verified Buyer, Mumbai</small>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-4">
                <div class="card h-100 border p-4 shadow-sm">
                    <div class="text-warning mb-2 fs-5">
                        <i class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i>
                    </div>
                    <p class="text-muted fst-italic">"The pure Himalayan Shilajit resin dissolves cleanly in warm water and gives remarkable natural stamina during my morning yoga sessions. Fast 3-day delivery to Bengaluru."</p>
                    <div class="mt-auto d-flex align-items-center gap-2">
                        <div class="bg-success text-white rounded-circle p-2 fw-bold" style="width:36px;height:36px;display:flex;align-items:center;justify-content:center;">AK</div>
                        <div>
                            <strong class="d-block">Ananya Kulkarni</strong>
                            <small class="text-muted"><i class="bi bi-patch-check-fill text-success"></i> Verified Buyer, Bengaluru</small>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-4">
                <div class="card h-100 border p-4 shadow-sm">
                    <div class="text-warning mb-2 fs-5">
                        <i class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i>
                    </div>
                    <p class="text-muted fst-italic">"Maha Narayan oil worked wonders for my mother's knee joint stiffness. The scent of sesame and camphor is pure. So glad I discovered EveryVeda!"</p>
                    <div class="mt-auto d-flex align-items-center gap-2">
                        <div class="bg-success text-white rounded-circle p-2 fw-bold" style="width:36px;height:36px;display:flex;align-items:center;justify-content:center;">SG</div>
                        <div>
                            <strong class="d-block">Suresh Gupta</strong>
                            <small class="text-muted"><i class="bi bi-patch-check-fill text-success"></i> Verified Buyer, New Delhi</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Wellness Blog & Articles -->
<section class="py-5" style="background-color: #faf9f6;">
    <div class="container">
        <div class="d-flex justify-content-between align-items-end mb-4">
            <div>
                <span class="text-uppercase small fw-bold text-success">Ayurvedic Journal</span>
                <h2 class="m-0"><?= __('from_our_blog') ?></h2>
            </div>
            <a href="<?= url('/blog') ?>" class="btn-ev-outline btn-sm">
                <?= __('view_all') ?> <i class="bi bi-chevron-right"></i>
            </a>
        </div>

        <div class="row g-4">
            <?php foreach ($blogPosts as $post): ?>
                <div class="col-md-4">
                    <div class="card h-100 border overflow-hidden shadow-sm">
                        <img src="<?= asset('assets/images/' . ($post['featured_image'] ?: 'blog-triphala.svg')) ?>" class="card-img-top" alt="<?= e($post['title']) ?>">
                        <div class="card-body d-flex flex-direction-column">
                            <small class="text-muted mb-2 d-block"><i class="bi bi-calendar3 me-1"></i> <?= date('d M Y', strtotime($post['published_at'])) ?> • <?= e($post['author_name']) ?></small>
                            <h5 class="card-title fw-bold">
                                <a href="<?= url('/blog/' . $post['slug']) ?>"><?= e($post['title']) ?></a>
                            </h5>
                            <p class="card-text text-muted small">
                                <?= e(substr(strip_tags((string)$post['summary']), 0, 120)) ?>...
                            </p>
                            <a href="<?= url('/blog/' . $post['slug']) ?>" class="mt-auto text-success fw-bold text-decoration-none small">
                                <?= __('read_more') ?> →
                            </a>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<!-- Newsletter Subscription -->
<section class="py-5 text-white" style="background: linear-gradient(135deg, #183827, #214D36);">
    <div class="container text-center">
        <div class="row justify-content-center">
            <div class="col-md-8 col-lg-6">
                <span class="badge bg-warning text-dark mb-2 fw-bold">EXCLUSIVE COMMUNITY</span>
                <h2 class="text-white fw-bold"><?= __('subscribe_newsletter') ?></h2>
                <p class="text-white-50 mb-4"><?= __('newsletter_subtitle') ?></p>
                <form action="<?= url('/newsletter/subscribe') ?>" method="POST" class="d-flex gap-2">
                    <?= csrf_field() ?>
                    <input type="email" name="email" class="form-control form-control-lg rounded-pill" placeholder="Enter your email address..." required>
                    <button type="submit" class="btn btn-ev-secondary btn-lg rounded-pill px-4">
                        <?= __('subscribe') ?>
                    </button>
                </form>
            </div>
        </div>
    </div>
</section>
