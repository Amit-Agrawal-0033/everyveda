<div class="container py-4">
    <!-- Breadcrumb -->
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb breadcrumb-ev">
            <li class="breadcrumb-item"><a href="<?= url('/') ?>"><?= __('home') ?></a></li>
            <li class="breadcrumb-item"><a href="<?= url('/catalog') ?>"><?= __('catalog') ?></a></li>
            <?php if (!empty($product['category_slug'])): ?>
                <li class="breadcrumb-item">
                    <a href="<?= url('/catalog?category=' . $product['category_slug']) ?>">
                        <?= e($currentLocale === 'hi' ? ($product['category_name_hi'] ?: $product['category_name_en']) : ($product['category_name_en'] ?: $product['category_name_hi'])) ?>
                    </a>
                </li>
            <?php endif; ?>
            <li class="breadcrumb-item active" aria-current="page"><?= e($product['name']) ?></li>
        </ol>
    </nav>

    <div class="row g-4 g-lg-5 mb-5">
        <!-- Product Image Gallery -->
        <div class="col-lg-6">
            <div class="card border p-4 bg-white rounded-3 shadow-sm text-center mb-3" style="min-height: 420px; display: flex; align-items: center; justify-content: center;">
                <img id="productMainImage" src="<?= asset('assets/images/products/' . ($product['primary_image'] ?: 'placeholder.svg')) ?>" alt="<?= e($product['name']) ?>" class="img-fluid mx-auto" style="max-height: 380px; width: 100%; object-fit: contain; aspect-ratio: 1/1;" loading="lazy">
            </div>

            <?php if (!empty($images)): ?>
                <div class="d-flex gap-2 justify-content-center">
                    <img src="<?= asset('assets/images/products/' . ($product['primary_image'] ?: 'placeholder.svg')) ?>" class="img-thumbnail product-thumb-img active-thumb bg-white" style="width: 72px; height: 72px; object-fit: contain; cursor: pointer;" data-full-src="<?= asset('assets/images/products/' . ($product['primary_image'] ?: 'placeholder.svg')) ?>" alt="Front View" loading="lazy">
                    <?php foreach ($images as $img): ?>
                        <img src="<?= asset('assets/images/products/' . $img['image_path']) ?>" class="img-thumbnail product-thumb-img bg-white" style="width: 72px; height: 72px; object-fit: contain; cursor: pointer;" data-full-src="<?= asset('assets/images/products/' . $img['image_path']) ?>" alt="<?= e($img['alt_text_en'] ?? 'Secondary View') ?>" loading="lazy">
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>

        <!-- Product Purchase Information -->
        <div class="col-lg-6">
            <span class="badge bg-success-subtle text-success border border-success-subtle px-2 py-1 mb-2 fw-bold text-uppercase">100% Classical Shastra Ayurveda</span>
            <h1 class="h2 fw-bold text-dark mb-2"><?= e($product['name']) ?></h1>
            <p class="text-muted small mb-2"><?= __('sku') ?>: <code><?= e($product['sku']) ?></code></p>

            <!-- Rating Summary -->
            <div class="d-flex align-items-center gap-2 mb-3">
                <div class="text-warning">
                    <i class="bi bi-star-fill"></i>
                    <i class="bi bi-star-fill"></i>
                    <i class="bi bi-star-fill"></i>
                    <i class="bi bi-star-fill"></i>
                    <i class="bi bi-star-fill"></i>
                </div>
                <span class="fw-bold"><?= $ratingStats['average'] ?></span>
                <span class="text-muted small">(<?= $ratingStats['count'] ?> <?= __('customer_ratings') ?>)</span>
                <a href="#reviews" class="small text-decoration-none ms-2">Read Reviews</a>
            </div>

            <!-- Price Display -->
            <div class="p-3 bg-light rounded-3 mb-4">
                <div class="d-flex align-items-baseline gap-2 mb-1">
                    <?php 
                        $hasSale = !empty($product['sale_price']) && (float)$product['sale_price'] > 0;
                        $curPrice = $hasSale ? $product['sale_price'] : $product['price'];
                    ?>
                    <span class="display-6 fw-bold text-success" id="productDisplayPrice"><?= format_inr($curPrice) ?></span>
                    <span class="text-muted text-decoration-line-through fs-5" id="productOriginalPrice" style="<?= $hasSale ? '' : 'display:none;' ?>">
                        <?= format_inr($product['price']) ?>
                    </span>
                </div>
                <small class="text-muted d-block"><i class="bi bi-info-circle"></i> <?= __('tax_inclusive') ?> (GST <?= $product['tax_rate'] ?>%)</small>
            </div>

            <!-- Short Description -->
            <div class="text-muted mb-4">
                <?= nl2br(e($product['short_description'])) ?>
            </div>

            <!-- Add to Cart Form -->
            <form action="<?= url('/cart/add') ?>" method="POST" id="productPurchaseForm">
                <?= csrf_field() ?>
                <input type="hidden" name="product_id" value="<?= $product['id'] ?>">
                <input type="hidden" name="variant_id" id="hiddenVariantId" value="<?= !empty($variants) ? $variants[0]['id'] : '' ?>">

                <!-- Pack / Variant Selector -->
                <?php if (!empty($variants)): ?>
                    <div class="mb-4">
                        <label class="form-label fw-bold small text-uppercase text-muted"><?= __('select_pack') ?>:</label>
                        <div class="d-flex flex-wrap gap-2">
                            <?php foreach ($variants as $idx => $v): ?>
                                <?php 
                                    $vName = $currentLocale === 'hi' ? ($v['variant_name_hi'] ?: $v['variant_name_en']) : ($v['variant_name_en'] ?: $v['variant_name_hi']);
                                    $vPrice = !empty($v['sale_price']) ? $v['sale_price'] : $v['price'];
                                ?>
                                <input type="radio" class="btn-check" name="variant_id_radio" id="var_<?= $v['id'] ?>" value="<?= $v['id'] ?>" data-price="<?= $v['price'] ?>" data-sale-price="<?= $v['sale_price'] ?? 0 ?>" data-stock="<?= $v['stock_quantity'] ?>" <?= $idx === 0 ? 'checked' : '' ?>>
                                <label class="btn btn-outline-secondary px-3 py-2" for="var_<?= $v['id'] ?>">
                                    <strong><?= e($vName) ?></strong>
                                    <span class="d-block small text-success fw-bold"><?= format_inr($vPrice) ?></span>
                                </label>
                            <?php endforeach; ?>
                        </div>
                    </div>
                <?php endif; ?>

                <!-- Stock Availability -->
                <div class="mb-4">
                    <span id="productStockStatus" class="<?= (int)$product['stock_quantity'] > 0 ? 'text-success fw-bold' : 'text-danger fw-bold' ?>">
                        <?= (int)$product['stock_quantity'] > 0 ? '✓ ' . __('in_stock') : '✗ ' . __('out_of_stock') ?>
                    </span>
                </div>

                <!-- Quantity & Action Buttons -->
                <div class="row g-3 align-items-center mb-4">
                    <div class="col-auto">
                        <div class="input-group" style="width: 130px;">
                            <button class="btn btn-outline-secondary btn-qty-minus" type="button">-</button>
                            <input type="number" name="quantity" class="form-control text-center qty-input" value="1" min="1" max="<?= max(1, (int)$product['stock_quantity']) ?>">
                            <button class="btn btn-outline-secondary btn-qty-plus" type="button">+</button>
                        </div>
                    </div>
                    <div class="col">
                        <button type="submit" class="btn btn-ev-primary w-100 py-2">
                            <i class="bi bi-bag-plus"></i> <?= __('add_to_cart') ?>
                        </button>
                    </div>
                    <div class="col">
                        <button type="submit" name="buy_now" value="1" class="btn btn-ev-secondary w-100 py-2">
                            <i class="bi bi-lightning-charge"></i> <?= __('buy_now') ?>
                        </button>
                    </div>
                </div>
            </form>

            <!-- PIN Code Serviceability Checker -->
            <div class="card border p-3 bg-white rounded shadow-sm mb-4">
                <label class="form-label fw-bold small text-muted text-uppercase mb-2">
                    <i class="bi bi-geo-alt-fill text-danger"></i> <?= __('check_delivery') ?>
                </label>
                <div class="input-group">
                    <input type="text" id="inputPincode" class="form-control" placeholder="<?= __('enter_pincode') ?>" maxlength="6">
                    <button class="btn btn-outline-success" type="button" id="btnCheckPincode"><?= __('check') ?></button>
                </div>
                <div id="pincodeResult" class="mt-2 small"></div>
            </div>

            <!-- Trust Highlights -->
            <div class="row g-2 border-top pt-3 small text-muted">
                <div class="col-6"><i class="bi bi-shield-check text-success"></i> 100% Authentic Product</div>
                <div class="col-6"><i class="bi bi-truck text-success"></i> Free Delivery Over ₹499</div>
                <div class="col-6"><i class="bi bi-arrow-repeat text-success"></i> 7-Day Replacement Policy</div>
                <div class="col-6"><i class="bi bi-cash-coin text-success"></i> COD Available across India</div>
            </div>
        </div>
    </div>

    <!-- Product Description, Ingredients, Directions Tabs -->
    <div class="card border shadow-sm mb-5">
        <div class="card-header bg-white p-0">
            <ul class="nav nav-tabs card-header-tabs m-0 border-0" id="productTabs" role="tablist">
                <li class="nav-item" role="presentation">
                    <button class="nav-link active px-4 py-3 fw-bold" id="desc-tab" data-bs-toggle="tab" data-bs-target="#desc-pane" type="button" role="tab">Full Description</button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link px-4 py-3 fw-bold" id="ingr-tab" data-bs-toggle="tab" data-bs-target="#ingr-pane" type="button" role="tab"><?= __('ingredients') ?></button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link px-4 py-3 fw-bold" id="dir-tab" data-bs-toggle="tab" data-bs-target="#dir-pane" type="button" role="tab"><?= __('how_to_use') ?></button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link px-4 py-3 fw-bold" id="storage-tab" data-bs-toggle="tab" data-bs-target="#storage-pane" type="button" role="tab"><?= __('storage') ?> & Warnings</button>
                </li>
            </ul>
        </div>
        <div class="card-body p-4">
            <div class="tab-content" id="productTabsContent">
                <!-- Description -->
                <div class="tab-pane fade show active" id="desc-pane" role="tabpanel">
                    <?= $product['description'] ?: '<p class="text-muted">Detailed description coming soon.</p>' ?>
                </div>

                <!-- Ingredients -->
                <div class="tab-pane fade" id="ingr-pane" role="tabpanel">
                    <h5 class="fw-bold text-success mb-3"><?= __('ingredients') ?></h5>
                    <p class="lead small"><?= nl2br(e($product['ingredients'] ?? 'Classical Ayurvedic herbs and natural extracts.')) ?></p>
                </div>

                <!-- Directions -->
                <div class="tab-pane fade" id="dir-pane" role="tabpanel">
                    <h5 class="fw-bold text-success mb-3"><?= __('how_to_use') ?></h5>
                    <p><?= nl2br(e($product['directions'] ?? 'As directed by your Ayurvedic physician.')) ?></p>
                </div>

                <!-- Storage & Warnings -->
                <div class="tab-pane fade" id="storage-pane" role="tabpanel">
                    <div class="mb-3">
                        <h6 class="fw-bold text-dark"><?= __('storage') ?></h6>
                        <p class="small text-muted"><?= nl2br(e($product['storage_info'] ?? 'Store in a cool, dry place away from sunlight.')) ?></p>
                    </div>
                    <?php if (!empty($product['warnings'])): ?>
                        <div class="alert alert-warning small">
                            <strong><i class="bi bi-exclamation-triangle"></i> <?= __('warnings') ?>:</strong><br>
                            <?= nl2br(e($product['warnings'])) ?>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <!-- Statutory Ayush & FSSAI Disclosure Card -->
    <div class="alert alert-light border p-3 rounded-3 small text-muted mb-5">
        <div class="d-flex align-items-start gap-3">
            <i class="bi bi-shield-check text-success fs-4 flex-shrink-0"></i>
            <div>
                <strong class="text-dark d-block mb-1">Authentic Classical Formulation & Statutory Advisory:</strong>
                This product is prepared strictly according to recognized classical Ayurvedic texts (Charaka Samhita / Sahasrayogam / Ayurvedic Pharmacopoeia of India) with ethically harvested botanicals. The statements made regarding this wellness product have not been evaluated as medical drug substitutes. It is intended for dietary supplementation and daily vitality balance, not for diagnosing, curing, or preventing any chronic pathological disease. If you are pregnant, lactating, taking prescription pharmaceuticals, or have an underlying clinical condition, please consult your healthcare practitioner or Ayurvedic Vaidya prior to use.
            </div>
        </div>
    </div>

    <!-- Customer Reviews Section -->
    <div class="card border shadow-sm mb-5" id="reviews">
        <div class="card-body p-4">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h4 class="fw-bold m-0"><?= __('customer_ratings') ?></h4>
                    <span class="text-muted small">Based on <?= $ratingStats['count'] ?> verified customer reviews</span>
                </div>
            </div>

            <div class="row g-4">
                <!-- Reviews List -->
                <div class="col-lg-7">
                    <?php if (empty($reviews)): ?>
                        <p class="text-muted py-3">No reviews yet for this herbal formulation. Be the first to share your experience!</p>
                    <?php else: ?>
                        <?php foreach ($reviews as $rev): ?>
                            <div class="border-bottom pb-3 mb-3">
                                <div class="d-flex justify-content-between align-items-center mb-1">
                                    <strong class="text-dark"><?= e($rev['reviewer_name']) ?></strong>
                                    <span class="text-muted small"><?= date('d M Y', strtotime($rev['created_at'])) ?></span>
                                </div>
                                <div class="text-warning small mb-2">
                                    <?php for ($i = 1; $i <= 5; $i++): ?>
                                        <i class="bi bi-star<?= $i <= (int)$rev['rating'] ? '-fill' : '' ?>"></i>
                                    <?php endfor; ?>
                                    <?php if ($rev['is_verified_purchase']): ?>
                                        <span class="badge bg-success-subtle text-success ms-2"><i class="bi bi-patch-check-fill"></i> Verified Buyer</span>
                                    <?php endif; ?>
                                </div>
                                <?php if (!empty($rev['title'])): ?>
                                    <h6 class="fw-bold mb-1"><?= e($rev['title']) ?></h6>
                                <?php endif; ?>
                                <p class="text-muted small m-0"><?= nl2br(e($rev['comment'])) ?></p>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>

                <!-- Write a Review Form -->
                <div class="col-lg-5">
                    <div class="bg-light p-4 rounded-3 border">
                        <h5 class="fw-bold mb-3"><?= __('write_a_review') ?></h5>
                        <form action="<?= url('/product/' . $product['slug'] . '/review') ?>" method="POST">
                            <?= csrf_field() ?>

                            <div class="mb-3">
                                <label class="form-label small fw-bold">Rating (1 to 5 Stars)</label>
                                <select name="rating" class="form-select form-select-sm" required>
                                    <option value="5">⭐⭐⭐⭐⭐ (5 - Excellent)</option>
                                    <option value="4">⭐⭐⭐⭐ (4 - Very Good)</option>
                                    <option value="3">⭐⭐⭐ (3 - Good)</option>
                                    <option value="2">⭐⭐ (2 - Fair)</option>
                                    <option value="1">⭐ (1 - Poor)</option>
                                </select>
                            </div>

                            <div class="mb-3">
                                <label class="form-label small fw-bold"><?= __('full_name') ?></label>
                                <input type="text" name="reviewer_name" class="form-control form-control-sm" value="<?= e($user['name'] ?? '') ?>" required>
                            </div>

                            <div class="mb-3">
                                <label class="form-label small fw-bold"><?= __('email_address') ?></label>
                                <input type="email" name="reviewer_email" class="form-control form-control-sm" value="<?= e($user['email'] ?? '') ?>" required>
                            </div>

                            <div class="mb-3">
                                <label class="form-label small fw-bold">Review Title</label>
                                <input type="text" name="title" class="form-control form-control-sm" placeholder="Summarize your review...">
                            </div>

                            <div class="mb-3">
                                <label class="form-label small fw-bold">Your Review</label>
                                <textarea name="comment" rows="3" class="form-control form-control-sm" placeholder="Tell us how this product helped your wellness routine..." required></textarea>
                            </div>

                            <button type="submit" class="btn btn-ev-primary btn-sm w-100">Submit Review</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Related Products -->
    <?php if (!empty($relatedProducts)): ?>
        <div class="mb-5">
            <h3 class="fw-bold text-success mb-4"><?= __('related_products') ?></h3>
            <div class="row g-3 g-md-4">
                <?php foreach ($relatedProducts as $p): ?>
                    <div class="col-6 col-md-3">
                        <div class="product-card">
                            <a href="<?= url('/product/' . $p['slug']) ?>" class="product-img-wrap">
                                <img src="<?= asset('assets/images/products/' . ($p['primary_image'] ?: 'placeholder.svg')) ?>" alt="<?= e($p['name']) ?>">
                            </a>
                            <div class="product-info">
                                <h4 class="product-title small">
                                    <a href="<?= url('/product/' . $p['slug']) ?>"><?= e($p['name']) ?></a>
                                </h4>
                                <div class="product-pricing">
                                    <span class="current-price fs-6"><?= format_inr($p['sale_price'] ?: $p['price']) ?></span>
                                </div>
                                <a href="<?= url('/product/' . $p['slug']) ?>" class="btn-ev-outline btn-sm w-100 mt-2 text-center">View</a>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    <?php endif; ?>
</div>
