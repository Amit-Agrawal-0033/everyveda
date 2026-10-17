<div class="mb-4">
    <a href="<?= url('/admin/categories') ?>" class="small text-muted text-decoration-none">← Back to Categories List</a>
    <h2 class="h3 fw-bold text-dark m-0 mt-1">Edit Category: <?= e($category['name_en']) ?></h2>
</div>

<div class="row justify-content-center">
    <div class="col-lg-8">
        <div class="card border shadow-sm p-4 bg-white">
            <form action="<?= url('/admin/categories/' . $category['id'] . '/edit') ?>" method="POST" enctype="multipart/form-data">
                <?= csrf_field() ?>

                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label small fw-bold">Category Title (English) *</label>
                        <input type="text" name="name_en" class="form-control" value="<?= e($category['name_en']) ?>" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label small fw-bold">श्रेणी का नाम (Hindi Title) *</label>
                        <input type="text" name="name_hi" class="form-control" value="<?= e($category['name_hi']) ?>" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label small fw-bold">URL Slug *</label>
                        <input type="text" name="slug" class="form-control" value="<?= e($category['slug']) ?>" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label small fw-bold">Sort Order</label>
                        <input type="number" name="sort_order" class="form-control" value="<?= e($category['sort_order']) ?>">
                    </div>
                    <div class="col-12">
                        <label class="form-label small fw-bold">English Description</label>
                        <textarea name="description_en" rows="2" class="form-control"><?= e($category['description_en']) ?></textarea>
                    </div>
                    <div class="col-12">
                        <label class="form-label small fw-bold">Hindi Description</label>
                        <textarea name="description_hi" rows="2" class="form-control"><?= e($category['description_hi']) ?></textarea>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label small fw-bold">Update Icon / Image</label>
                        <input type="file" name="image" class="form-control" accept="image/*">
                    </div>
                    <div class="col-md-6 d-flex align-items-center">
                        <div class="form-check mt-3">
                            <input class="form-check-input" type="checkbox" name="is_active" value="1" id="activeCheck" <?= $category['is_active'] ? 'checked' : '' ?>>
                            <label class="form-check-label small fw-bold" for="activeCheck">Active Category</label>
                        </div>
                    </div>
                    <div class="col-12 mt-4">
                        <button type="submit" class="btn btn-success px-4">Save Changes</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
