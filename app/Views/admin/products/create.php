<div class="mb-4">
    <a href="<?= url('/admin/products') ?>" class="small text-muted text-decoration-none">← Back to Products List</a>
    <h2 class="h3 fw-bold text-dark m-0 mt-1">Create New Product</h2>
    <small class="text-muted">Enter product specifications, inventory, and bilingual English & Hindi descriptions</small>
</div>

<form action="<?= url('/admin/products/create') ?>" method="POST" enctype="multipart/form-data">
    <?= csrf_field() ?>

    <div class="row g-4">
        <!-- Main Form Column -->
        <div class="col-lg-8">
            <!-- Basic SKU & Identifiers -->
            <div class="card border shadow-sm p-4 bg-white mb-4">
                <h5 class="fw-bold mb-3">Product Identifiers</h5>
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label small fw-bold">Base SKU *</label>
                        <input type="text" name="sku" class="form-control" placeholder="e.g. EV-CHY-002" value="<?= old('sku') ?>" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label small fw-bold">URL Slug *</label>
                        <input type="text" name="slug" class="form-control" placeholder="e.g. organic-chyawanprash" value="<?= old('slug') ?>" required>
                    </div>
                </div>
            </div>

            <!-- English Content -->
            <div class="card border shadow-sm p-4 bg-white mb-4">
                <div class="d-flex align-items-center gap-2 mb-3">
                    <span class="badge bg-primary">EN</span>
                    <h5 class="fw-bold m-0">English Product Details</h5>
                </div>
                <div class="row g-3">
                    <div class="col-12">
                        <label class="form-label small fw-bold">Product Title (English) *</label>
                        <input type="text" name="name_en" class="form-control" placeholder="EveryVeda Royal Gold Chyawanprash" value="<?= old('name_en') ?>" required>
                    </div>
                    <div class="col-12">
                        <label class="form-label small fw-bold">Short Description</label>
                        <textarea name="short_description_en" rows="2" class="form-control"><?= old('short_description_en') ?></textarea>
                    </div>
                    <div class="col-12">
                        <label class="form-label small fw-bold">Full Ayurvedic Description (HTML allowed)</label>
                        <textarea name="description_en" rows="5" class="form-control"><?= old('description_en') ?></textarea>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label small fw-bold">Classical Ingredients</label>
                        <textarea name="ingredients_en" rows="3" class="form-control"><?= old('ingredients_en') ?></textarea>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label small fw-bold">Directions for Use</label>
                        <textarea name="directions_en" rows="3" class="form-control"><?= old('directions_en') ?></textarea>
                    </div>
                </div>
            </div>

            <!-- Hindi Content -->
            <div class="card border shadow-sm p-4 bg-white mb-4">
                <div class="d-flex align-items-center gap-2 mb-3">
                    <span class="badge bg-warning text-dark">HI</span>
                    <h5 class="fw-bold m-0">हिन्दी विवरण (Hindi Product Details)</h5>
                </div>
                <div class="row g-3">
                    <div class="col-12">
                        <label class="form-label small fw-bold">उत्पाद का नाम (Hindi Title) *</label>
                        <input type="text" name="name_hi" class="form-control" placeholder="एवरीवेदा रॉयल गोल्ड च्यवनप्राश" value="<?= old('name_hi') ?>" required>
                    </div>
                    <div class="col-12">
                        <label class="form-label small fw-bold">संक्षिप्त विवरण (Short Summary)</label>
                        <textarea name="short_description_hi" rows="2" class="form-control"><?= old('short_description_hi') ?></textarea>
                    </div>
                    <div class="col-12">
                        <label class="form-label small fw-bold">विस्तृत विवरण (Full Description)</label>
                        <textarea name="description_hi" rows="5" class="form-control"><?= old('description_hi') ?></textarea>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label small fw-bold">घटक एवं जड़ी-बूटियाँ (Ingredients)</label>
                        <textarea name="ingredients_hi" rows="3" class="form-control"><?= old('ingredients_hi') ?></textarea>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label small fw-bold">सेवन विधि (Directions)</label>
                        <textarea name="directions_hi" rows="3" class="form-control"><?= old('directions_hi') ?></textarea>
                    </div>
                </div>
            </div>
        </div>

        <!-- Sidebar Options Column -->
        <div class="col-lg-4">
            <!-- Publish Status Card -->
            <div class="card border shadow-sm p-4 bg-white mb-4">
                <h5 class="fw-bold mb-3">Publish Settings</h5>
                <div class="mb-3">
                    <label class="form-label small fw-bold">Status</label>
                    <select name="status" class="form-select">
                        <option value="published">Published</option>
                        <option value="draft">Draft</option>
                    </select>
                </div>
                <div class="mb-3">
                    <label class="form-label small fw-bold">Category</label>
                    <select name="category_id" class="form-select">
                        <option value="">Select Category</option>
                        <?php foreach ($categories as $cat): ?>
                            <option value="<?= $cat['id'] ?>"><?= e($cat['name_en']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="form-check mb-2">
                    <input class="form-check-input" type="checkbox" name="is_featured" value="1" id="checkFeatured">
                    <label class="form-check-label small" for="checkFeatured">Featured on Homepage</label>
                </div>
                <div class="form-check mb-2">
                    <input class="form-check-input" type="checkbox" name="is_best_seller" value="1" id="checkBestSeller">
                    <label class="form-check-label small" for="checkBestSeller">Best Seller Badge</label>
                </div>
                <div class="form-check mb-3">
                    <input class="form-check-input" type="checkbox" name="is_new_arrival" value="1" id="checkNewArrival">
                    <label class="form-check-label small" for="checkNewArrival">New Arrival Badge</label>
                </div>

                <button type="submit" class="btn btn-success w-100 py-2 fw-bold">
                    <i class="bi bi-check2"></i> Save & Continue
                </button>
            </div>

            <!-- Pricing & Stock Card -->
            <div class="card border shadow-sm p-4 bg-white mb-4">
                <h5 class="fw-bold mb-3">Pricing & Inventory</h5>
                <div class="mb-3">
                    <label class="form-label small fw-bold">Base Price (₹) *</label>
                    <input type="number" step="0.01" name="price" class="form-control" value="<?= old('price') ?>" required>
                </div>
                <div class="mb-3">
                    <label class="form-label small fw-bold">Sale Price (₹ Optional)</label>
                    <input type="number" step="0.01" name="sale_price" class="form-control" value="<?= old('sale_price') ?>">
                </div>
                <div class="mb-3">
                    <label class="form-label small fw-bold">Stock Quantity *</label>
                    <input type="number" name="stock_quantity" class="form-control" value="<?= old('stock_quantity', 100) ?>" required>
                </div>
                <div class="mb-3">
                    <label class="form-label small fw-bold">Low Stock Threshold</label>
                    <input type="number" name="low_stock_threshold" class="form-control" value="<?= old('low_stock_threshold', 5) ?>">
                </div>
                <div class="mb-3">
                    <label class="form-label small fw-bold">GST Tax Rate (%)</label>
                    <input type="number" step="0.01" name="tax_rate" class="form-control" value="12.00">
                </div>
                <div class="mb-3">
                    <label class="form-label small fw-bold">Weight (grams)</label>
                    <input type="number" name="weight_grams" class="form-control" value="250">
                </div>
            </div>

            <!-- Image Upload Card -->
            <div class="card border shadow-sm p-4 bg-white">
                <h5 class="fw-bold mb-3">Primary Photo</h5>
                <input type="file" name="primary_image" class="form-control" accept="image/*">
                <small class="text-muted d-block mt-2">Accepted formats: JPG, PNG, WebP, SVG. Recommended 600×600 px.</small>
            </div>
        </div>
    </div>
</form>
