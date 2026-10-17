<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h2 class="h3 fw-bold text-dark m-0">Products Management</h2>
        <small class="text-muted">Manage Ayurvedic SKUs, pricing, stock levels, variants, and translations</small>
    </div>
    <div>
        <a href="<?= url('/admin/products/create') ?>" class="btn btn-sm btn-success">
            <i class="bi bi-plus-lg me-1"></i> Add New Product
        </a>
    </div>
</div>

<!-- Filters & Search Bar -->
<div class="card border shadow-sm p-3 bg-white mb-4">
    <form action="<?= url('/admin/products') ?>" method="GET" class="row g-2 align-items-center">
        <div class="col-md-5">
            <input type="text" name="q" class="form-control form-control-sm" placeholder="Search by SKU, English or Hindi name..." value="<?= e($filters['q']) ?>">
        </div>
        <div class="col-md-3">
            <select name="category" class="form-select form-select-sm">
                <option value="all">All Categories</option>
                <?php foreach ($categories as $cat): ?>
                    <option value="<?= $cat['id'] ?>" <?= ($filters['category'] == $cat['id']) ? 'selected' : '' ?>><?= e($cat['name_en']) ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="col-md-2">
            <select name="status" class="form-select form-select-sm">
                <option value="all">All Statuses</option>
                <option value="published" <?= ($filters['status'] === 'published') ? 'selected' : '' ?>>Published</option>
                <option value="draft" <?= ($filters['status'] === 'draft') ? 'selected' : '' ?>>Draft</option>
                <option value="archived" <?= ($filters['status'] === 'archived') ? 'selected' : '' ?>>Archived</option>
            </select>
        </div>
        <div class="col-md-2">
            <button type="submit" class="btn btn-sm btn-outline-success w-100">Filter</button>
        </div>
    </form>
</div>

<!-- Products Table -->
<div class="card border shadow-sm bg-white overflow-hidden">
    <div class="table-responsive">
        <table class="table align-middle mb-0 small">
            <thead class="table-light">
                <tr>
                    <th class="ps-3">Product</th>
                    <th>Category</th>
                    <th>Price</th>
                    <th>Stock</th>
                    <th>Variants</th>
                    <th>Status</th>
                    <th class="text-end pe-3">Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($products)): ?>
                    <tr>
                        <td colspan="7" class="text-center py-4 text-muted">No products found matching filters.</td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($products as $p): ?>
                        <tr>
                            <td class="ps-3">
                                <div class="d-flex align-items-center gap-2">
                                    <img src="<?= asset('assets/images/products/' . ($p['primary_image'] ?: 'placeholder.svg')) ?>" alt="<?= e($p['name_en']) ?>" style="width: 42px; height: 42px; object-fit: contain;" class="border rounded p-1">
                                    <div>
                                        <strong class="d-block text-dark"><?= e($p['name_en']) ?></strong>
                                        <small class="text-muted d-block"><?= e($p['name_hi']) ?></small>
                                        <code class="small"><?= e($p['sku']) ?></code>
                                    </div>
                                </div>
                            </td>
                            <td><?= e($p['category_name'] ?: 'Uncategorized') ?></td>
                            <td>
                                <strong><?= format_inr($p['price']) ?></strong>
                                <?php if ($p['sale_price']): ?>
                                    <small class="d-block text-danger">Sale: <?= format_inr($p['sale_price']) ?></small>
                                <?php endif; ?>
                            </td>
                            <td>
                                <?php if ($p['stock_quantity'] <= $p['low_stock_threshold']): ?>
                                    <span class="badge bg-danger"><?= $p['stock_quantity'] ?> Low</span>
                                <?php else: ?>
                                    <span class="badge bg-success-subtle text-success"><?= $p['stock_quantity'] ?> Units</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <span class="badge bg-light text-dark border"><?= $p['variant_count'] ?> Pack(s)</span>
                            </td>
                            <td>
                                <span class="badge bg-<?= $p['status'] === 'published' ? 'success' : ($p['status'] === 'draft' ? 'warning' : 'secondary') ?>">
                                    <?= ucfirst($p['status']) ?>
                                </span>
                            </td>
                            <td class="text-end pe-3">
                                <div class="btn-group btn-group-sm">
                                    <a href="<?= url('/product/' . $p['slug']) ?>" target="_blank" class="btn btn-outline-secondary" title="View in Store">
                                        <i class="bi bi-box-arrow-up-right"></i>
                                    </a>
                                    <a href="<?= url('/admin/products/' . $p['id'] . '/edit') ?>" class="btn btn-outline-success" title="Edit">
                                        <i class="bi bi-pencil-square"></i>
                                    </a>
                                    <form action="<?= url('/admin/products/' . $p['id'] . '/delete') ?>" method="POST" class="d-inline" onsubmit="return confirm('Archive this product?');">
                                        <?= csrf_field() ?>
                                        <button type="submit" class="btn btn-outline-danger" title="Archive">
                                            <i class="bi bi-archive"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- Pagination -->
<?php if ($totalPages > 1): ?>
    <nav class="mt-4">
        <ul class="pagination pagination-sm justify-content-center">
            <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                <li class="page-item <?= $page === $i ? 'active' : '' ?>">
                    <a class="page-link" href="<?= url('/admin/products?' . http_build_query(array_merge($filters, ['page' => $i]))) ?>"><?= $i ?></a>
                </li>
            <?php endfor; ?>
        </ul>
    </nav>
<?php endif; ?>
