<div class="container py-4">
    <!-- Breadcrumb -->
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb breadcrumb-ev">
            <li class="breadcrumb-item"><a href="<?= url('/') ?>"><?= __('home') ?></a></li>
            <li class="breadcrumb-item active" aria-current="page"><?= __('catalog') ?></li>
            <?php if ($selectedCategory): ?>
                <li class="breadcrumb-item active" aria-current="page"><?= e($currentLocale === 'hi' ? $selectedCategory['name_hi'] : $selectedCategory['name_en']) ?></li>
            <?php endif; ?>
        </ol>
    </nav>

    <!-- Header Banner if category is chosen -->
    <?php if ($selectedCategory): ?>
        <div class="p-4 mb-4 rounded-3 text-white" style="background: linear-gradient(135deg, #183827, #214D36);">
            <h1 class="h2 text-white fw-bold mb-2"><?= e($currentLocale === 'hi' ? $selectedCategory['name_hi'] : $selectedCategory['name_en']) ?></h1>
            <p class="m-0 text-white-50"><?= e($currentLocale === 'hi' ? $selectedCategory['description_hi'] : $selectedCategory['description_en']) ?></p>
        </div>
    <?php else: ?>
        <div class="mb-4">
            <h1 class="h2 fw-bold text-success mb-1"><?= __('catalog') ?></h1>
            <p class="text-muted"><?= __('showing_results', ['count' => $total]) ?></p>
        </div>
    <?php endif; ?>

    <div class="row g-4">
        <!-- Sidebar Filters -->
        <div class="col-lg-3">
            <div class="card border shadow-sm p-3 mb-4">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h5 class="m-0 fw-bold"><i class="bi bi-funnel me-1"></i> <?= __('filters') ?></h5>
                    <a href="<?= url('/catalog') ?>" class="text-danger small text-decoration-none"><?= __('reset_filters') ?></a>
                </div>

                <form action="<?= url('/catalog') ?>" method="GET" id="catalogFilterForm">
                    <?php if (!empty($filters['q'])): ?>
                        <input type="hidden" name="q" value="<?= e($filters['q']) ?>">
                    <?php endif; ?>

                    <!-- Categories Filter -->
                    <div class="mb-4">
                        <label class="form-label fw-bold small text-uppercase text-muted"><?= __('filter_by_category') ?></label>
                        <div class="list-group list-group-flush small">
                            <a href="<?= url('/catalog' . (!empty($filters['q']) ? '?q=' . urlencode($filters['q']) : '')) ?>" class="list-group-item list-group-item-action border-0 px-0 <?= empty($filters['category']) ? 'fw-bold text-success' : '' ?>">
                                All Categories
                            </a>
                            <?php foreach ($categories as $cat): ?>
                                <a href="<?= url('/catalog?category=' . $cat['slug'] . (!empty($filters['q']) ? '&q=' . urlencode($filters['q']) : '')) ?>" class="list-group-item list-group-item-action border-0 px-0 d-flex justify-content-between align-items-center <?= ($filters['category'] ?? '') === $cat['slug'] ? 'fw-bold text-success' : '' ?>">
                                    <span><?= e($currentLocale === 'hi' ? $cat['name_hi'] : $cat['name_en']) ?></span>
                                    <span class="badge bg-light text-dark rounded-pill"><?= $cat['product_count'] ?></span>
                                </a>
                            <?php endforeach; ?>
                        </div>
                    </div>

                    <!-- In Stock Checkbox -->
                    <div class="mb-4 form-check">
                        <input type="checkbox" class="form-check-input" id="inStockCheck" name="in_stock" value="1" <?= !empty($filters['in_stock']) ? 'checked' : '' ?> onchange="this.form.submit()">
                        <label class="form-check-label small fw-semibold" for="inStockCheck"><?= __('filter_in_stock') ?></label>
                    </div>

                    <!-- Price Filter -->
                    <div class="mb-4">
                        <label class="form-label fw-bold small text-uppercase text-muted"><?= __('filter_by_price') ?></label>
                        <div class="d-flex align-items-center gap-2 mb-2">
                            <input type="number" name="min_price" class="form-control form-control-sm" placeholder="Min ₹" value="<?= e($filters['min_price'] ?? '') ?>">
                            <span>-</span>
                            <input type="number" name="max_price" class="form-control form-control-sm" placeholder="Max ₹" value="<?= e($filters['max_price'] ?? '') ?>">
                        </div>
                        <button type="submit" class="btn btn-sm btn-outline-success w-100">Apply Price</button>
                    </div>

                    <!-- Hidden Category if active -->
                    <?php if (!empty($filters['category'])): ?>
                        <input type="hidden" name="category" value="<?= e($filters['category']) ?>">
                    <?php endif; ?>
                </form>
            </div>
        </div>

        <!-- Products Listing Grid -->
        <div class="col-lg-9">
            <!-- Sorting Bar -->
            <div class="d-flex flex-wrap justify-content-between align-items-center mb-3 p-3 bg-white border rounded shadow-sm">
                <span class="small text-muted mb-2 mb-sm-0"><?= __('showing_results', ['count' => count($products)]) ?> of <?= $total ?></span>

                <div class="d-flex align-items-center gap-2">
                    <label for="sortSelector" class="small text-nowrap m-0"><?= __('sort_by') ?>:</label>
                    <select id="sortSelector" class="form-select form-select-sm" style="width: auto;" onchange="location = this.value;">
                        <?php
                            $baseParams = $filters;
                            unset($baseParams['sort']);
                            $queryStr = http_build_query(array_filter($baseParams));
                            $glue = $queryStr ? '&' : '';
                        ?>
                        <option value="<?= url('/catalog?' . $queryStr . $glue . 'sort=latest') ?>" <?= ($filters['sort'] ?? '') === 'latest' ? 'selected' : '' ?>><?= __('sort_latest') ?></option>
                        <option value="<?= url('/catalog?' . $queryStr . $glue . 'sort=price_asc') ?>" <?= ($filters['sort'] ?? '') === 'price_asc' ? 'selected' : '' ?>><?= __('sort_price_low_high') ?></option>
                        <option value="<?= url('/catalog?' . $queryStr . $glue . 'sort=price_desc') ?>" <?= ($filters['sort'] ?? '') === 'price_desc' ? 'selected' : '' ?>><?= __('sort_price_high_low') ?></option>
                        <option value="<?= url('/catalog?' . $queryStr . $glue . 'sort=popular') ?>" <?= ($filters['sort'] ?? '') === 'popular' ? 'selected' : '' ?>><?= __('sort_popular') ?></option>
                    </select>
                </div>
            </div>

            <!-- Product Grid -->
            <?php if (empty($products)): ?>
                <div class="text-center py-5 bg-white border rounded">
                    <div class="fs-1 text-muted mb-3"><i class="bi bi-search"></i></div>
                    <h4><?= __('no_products_found') ?></h4>
                    <p class="text-muted">Try clearing your filters or searching for alternative herbal keywords.</p>
                    <a href="<?= url('/catalog') ?>" class="btn-ev-primary btn-sm"><?= __('reset_filters') ?></a>
                </div>
            <?php else: ?>
                <div class="row g-3 g-md-4">
                    <?php foreach ($products as $p): ?>
                        <div class="col-6 col-md-4">
                            <div class="product-card">
                                <?php if ($p['sale_price']): ?>
                                    <span class="product-card-badge sale">Sale</span>
                                <?php endif; ?>

                                <a href="<?= url('/product/' . $p['slug']) ?>" class="product-img-wrap">
                                    <img src="<?= asset('assets/images/products/' . ($p['primary_image'] ?: 'placeholder.svg')) ?>" alt="<?= e($p['name']) ?>" loading="lazy">
                                </a>

                                <div class="product-info">
                                    <span class="product-category-tag"><?= e($p['category_slug'] ?? 'Ayurveda') ?></span>
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

                                    <form action="<?= url('/cart/add') ?>" method="POST" class="mt-auto">
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

                <!-- Pagination -->
                <?php if ($totalPages > 1): ?>
                    <nav class="mt-5" aria-label="Catalog Pagination">
                        <ul class="pagination justify-content-center">
                            <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                                <?php 
                                    $pageParams = array_merge($filters, ['page' => $i]);
                                    $link = url('/catalog?' . http_build_query(array_filter($pageParams)));
                                ?>
                                <li class="page-item <?= $page === $i ? 'active' : '' ?>">
                                    <a class="page-link" href="<?= $link ?>"><?= $i ?></a>
                                </li>
                            <?php endfor; ?>
                        </ul>
                    </nav>
                <?php endif; ?>
            <?php endif; ?>
        </div>
    </div>
</div>
