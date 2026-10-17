<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h2 class="h3 fw-bold text-dark m-0">Categories Management</h2>
        <small class="text-muted">Manage bilingual category taxonomy and sorting</small>
    </div>
    <div>
        <a href="<?= url('/admin/categories/create') ?>" class="btn btn-sm btn-success">
            <i class="bi bi-plus-lg me-1"></i> Add Category
        </a>
    </div>
</div>

<div class="card border shadow-sm bg-white overflow-hidden">
    <div class="table-responsive">
        <table class="table align-middle mb-0 small">
            <thead class="table-light">
                <tr>
                    <th class="ps-3">Category</th>
                    <th>Hindi Title</th>
                    <th>Slug</th>
                    <th>Sort Order</th>
                    <th>Products</th>
                    <th>Status</th>
                    <th class="text-end pe-3">Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($categories as $cat): ?>
                    <tr>
                        <td class="ps-3">
                            <div class="d-flex align-items-center gap-2">
                                <img src="<?= asset('assets/images/categories/' . ($cat['image'] ?: 'placeholder.svg')) ?>" style="width: 32px; height: 32px; object-fit: contain;">
                                <strong class="text-dark"><?= e($cat['name_en']) ?></strong>
                            </div>
                        </td>
                        <td><?= e($cat['name_hi']) ?></td>
                        <td><code><?= e($cat['slug']) ?></code></td>
                        <td><?= $cat['sort_order'] ?></td>
                        <td><span class="badge bg-light text-dark border"><?= $cat['product_count'] ?> Products</span></td>
                        <td>
                            <span class="badge bg-<?= $cat['is_active'] ? 'success' : 'secondary' ?>">
                                <?= $cat['is_active'] ? 'Active' : 'Disabled' ?>
                            </span>
                        </td>
                        <td class="text-end pe-3">
                            <div class="btn-group btn-group-sm">
                                <a href="<?= url('/admin/categories/' . $cat['id'] . '/edit') ?>" class="btn btn-outline-success">
                                    <i class="bi bi-pencil-square"></i>
                                </a>
                                <form action="<?= url('/admin/categories/' . $cat['id'] . '/delete') ?>" method="POST" class="d-inline" onsubmit="return confirm('Delete this category?');">
                                    <?= csrf_field() ?>
                                    <button type="submit" class="btn btn-outline-danger">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>
