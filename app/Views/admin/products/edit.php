<div class="mb-4">
    <a href="<?= url('/admin/products') ?>" class="small text-muted text-decoration-none">← Back to Products List</a>
    <div class="d-flex justify-content-between align-items-center mt-1">
        <h2 class="h3 fw-bold text-dark m-0">Edit Product: <?= e($transEn['name'] ?? $product['sku']) ?></h2>
        <a href="<?= url('/product/' . $product['slug']) ?>" target="_blank" class="btn btn-sm btn-outline-success">
            <i class="bi bi-box-arrow-up-right me-1"></i> Preview Live
        </a>
    </div>
</div>

<div class="row g-4">
    <div class="col-lg-8">
        <!-- Edit Form -->
        <form action="<?= url('/admin/products/' . $product['id'] . '/edit') ?>" method="POST" enctype="multipart/form-data">
            <?= csrf_field() ?>

            <!-- Basic SKU & Slug -->
            <div class="card border shadow-sm p-4 bg-white mb-4">
                <h5 class="fw-bold mb-3">Product Identifiers</h5>
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label small fw-bold">Base SKU *</label>
                        <input type="text" name="sku" class="form-control" value="<?= e($product['sku']) ?>" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label small fw-bold">URL Slug *</label>
                        <input type="text" name="slug" class="form-control" value="<?= e($product['slug']) ?>" required>
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
                        <input type="text" name="name_en" class="form-control" value="<?= e($transEn['name'] ?? '') ?>" required>
                    </div>
                    <div class="col-12">
                        <label class="form-label small fw-bold">Short Description</label>
                        <textarea name="short_description_en" rows="2" class="form-control"><?= e($transEn['short_description'] ?? '') ?></textarea>
                    </div>
                    <div class="col-12">
                        <label class="form-label small fw-bold">Full Ayurvedic Description (HTML allowed)</label>
                        <textarea name="description_en" rows="5" class="form-control"><?= e($transEn['description'] ?? '') ?></textarea>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label small fw-bold">Classical Ingredients</label>
                        <textarea name="ingredients_en" rows="3" class="form-control"><?= e($transEn['ingredients'] ?? '') ?></textarea>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label small fw-bold">Directions for Use</label>
                        <textarea name="directions_en" rows="3" class="form-control"><?= e($transEn['directions'] ?? '') ?></textarea>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label small fw-bold">Storage Info</label>
                        <input type="text" name="storage_info_en" class="form-control" value="<?= e($transEn['storage_info'] ?? '') ?>">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label small fw-bold">Safety Warnings</label>
                        <input type="text" name="warnings_en" class="form-control" value="<?= e($transEn['warnings'] ?? '') ?>">
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
                        <input type="text" name="name_hi" class="form-control" value="<?= e($transHi['name'] ?? '') ?>" required>
                    </div>
                    <div class="col-12">
                        <label class="form-label small fw-bold">संक्षिप्त विवरण (Short Summary)</label>
                        <textarea name="short_description_hi" rows="2" class="form-control"><?= e($transHi['short_description'] ?? '') ?></textarea>
                    </div>
                    <div class="col-12">
                        <label class="form-label small fw-bold">विस्तृत विवरण (Full Description)</label>
                        <textarea name="description_hi" rows="5" class="form-control"><?= e($transHi['description'] ?? '') ?></textarea>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label small fw-bold">घटक एवं जड़ी-बूटियाँ (Ingredients)</label>
                        <textarea name="ingredients_hi" rows="3" class="form-control"><?= e($transHi['ingredients'] ?? '') ?></textarea>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label small fw-bold">सेवन विधि (Directions)</label>
                        <textarea name="directions_hi" rows="3" class="form-control"><?= e($transHi['directions'] ?? '') ?></textarea>
                    </div>
                </div>
            </div>

            <button type="submit" class="btn btn-success py-2 px-4 fw-bold">
                <i class="bi bi-save me-1"></i> Update Product Information
            </button>
        </form>

        <!-- Product Variants / Pack Sizes Management -->
        <div class="card border shadow-sm p-4 bg-white mt-5" id="variants">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <div>
                    <h5 class="fw-bold m-0">Pack Sizes & Variants</h5>
                    <small class="text-muted">Configure multiple sizes (e.g. 250g, 500g, 1kg or multi-packs) with specific pricing and inventory</small>
                </div>
            </div>

            <?php if (!empty($variants)): ?>
                <div class="table-responsive mb-4">
                    <table class="table align-middle small">
                        <thead class="table-light">
                            <tr>
                                <th>Pack Size (EN)</th>
                                <th>Pack Size (HI)</th>
                                <th>Variant SKU</th>
                                <th>Price</th>
                                <th>Sale Price</th>
                                <th>Stock</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($variants as $var): ?>
                                <tr>
                                    <td><strong><?= e($var['variant_name_en']) ?></strong></td>
                                    <td><?= e($var['variant_name_hi']) ?></td>
                                    <td><code><?= e($var['sku']) ?></code></td>
                                    <td><?= format_inr($var['price']) ?></td>
                                    <td><?= $var['sale_price'] ? format_inr($var['sale_price']) : '-' ?></td>
                                    <td><?= $var['stock_quantity'] ?></td>
                                    <td>
                                        <form action="<?= url('/admin/products/' . $product['id'] . '/variants/' . $var['id'] . '/delete') ?>" method="POST" onsubmit="return confirm('Delete this variant?');">
                                            <?= csrf_field() ?>
                                            <button type="submit" class="btn btn-sm btn-outline-danger"><i class="bi bi-trash"></i></button>
                                        </form>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php else: ?>
                <p class="text-muted small">No variant pack sizes created yet.</p>
            <?php endif; ?>

            <!-- Add Variant Form -->
            <div class="p-3 bg-light rounded border">
                <h6 class="fw-bold mb-3">Add New Pack Size Variant</h6>
                <form action="<?= url('/admin/products/' . $product['id'] . '/variants') ?>" method="POST">
                    <?= csrf_field() ?>
                    <div class="row g-2">
                        <div class="col-md-3">
                            <label class="form-label small fw-bold">Name (EN) *</label>
                            <input type="text" name="variant_name_en" class="form-control form-control-sm" placeholder="e.g. 500g Jar" required>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label small fw-bold">Name (HI) *</label>
                            <input type="text" name="variant_name_hi" class="form-control form-control-sm" placeholder="500 ग्राम जार" required>
                        </div>
                        <div class="col-md-2">
                            <label class="form-label small fw-bold">Variant SKU *</label>
                            <input type="text" name="sku" class="form-control form-control-sm" placeholder="EV-VAR-01" required>
                        </div>
                        <div class="col-md-2">
                            <label class="form-label small fw-bold">Price (₹) *</label>
                            <input type="number" step="0.01" name="price" class="form-control form-control-sm" required>
                        </div>
                        <div class="col-md-2">
                            <label class="form-label small fw-bold">Sale Price (₹)</label>
                            <input type="number" step="0.01" name="sale_price" class="form-control form-control-sm">
                        </div>
                        <div class="col-md-2">
                            <label class="form-label small fw-bold">Stock *</label>
                            <input type="number" name="stock_quantity" class="form-control form-control-sm" value="50" required>
                        </div>
                        <div class="col-md-2">
                            <label class="form-label small fw-bold">Weight (g)</label>
                            <input type="number" name="weight_grams" class="form-control form-control-sm" value="500">
                        </div>
                        <div class="col-md-4 d-flex align-items-end">
                            <button type="submit" class="btn btn-sm btn-primary w-100">Add Variant Pack</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Sidebar Info Column -->
    <div class="col-lg-4">
        <!-- Status & Settings -->
        <div class="card border shadow-sm p-4 bg-white mb-4">
            <h5 class="fw-bold mb-3">Status & Taxonomy</h5>
            <div class="mb-3">
                <label class="form-label small fw-bold">Status</label>
                <select name="status" class="form-select" form="productEditForm">
                    <option value="published" <?= $product['status'] === 'published' ? 'selected' : '' ?>>Published</option>
                    <option value="draft" <?= $product['status'] === 'draft' ? 'selected' : '' ?>>Draft</option>
                    <option value="archived" <?= $product['status'] === 'archived' ? 'selected' : '' ?>>Archived</option>
                </select>
            </div>
            <div class="mb-3">
                <label class="form-label small fw-bold">Category</label>
                <select name="category_id" class="form-select" form="productEditForm">
                    <option value="">None (Uncategorized)</option>
                    <?php foreach ($categories as $cat): ?>
                        <option value="<?= $cat['id'] ?>" <?= $product['category_id'] == $cat['id'] ? 'selected' : '' ?>><?= e($cat['name_en']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
        </div>

        <!-- Image Preview -->
        <div class="card border shadow-sm p-4 bg-white mb-4 text-center">
            <h5 class="fw-bold mb-3">Primary Photo</h5>
            <img src="<?= asset('assets/images/products/' . ($product['primary_image'] ?: 'placeholder.svg')) ?>" class="img-fluid border rounded p-2 mb-3" style="max-height: 200px;">
        </div>
    </div>
</div>
