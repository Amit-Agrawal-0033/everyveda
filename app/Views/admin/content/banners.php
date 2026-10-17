<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h2 class="h3 fw-bold text-dark m-0">Homepage Banners</h2>
        <small class="text-muted">Manage hero slider slides, headlines, and call-to-action buttons</small>
    </div>
</div>

<div class="row g-4">
    <!-- Existing Banners List -->
    <div class="col-lg-7">
        <div class="card border shadow-sm bg-white overflow-hidden">
            <div class="table-responsive">
                <table class="table align-middle mb-0 small">
                    <thead class="table-light">
                        <tr>
                            <th class="ps-3">Slide Preview</th>
                            <th>Headline (EN / HI)</th>
                            <th>Order</th>
                            <th>Status</th>
                            <th class="text-end pe-3">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($banners as $b): ?>
                            <tr>
                                <td class="ps-3">
                                    <img src="<?= asset('assets/images/banners/' . $b['image_path']) ?>" alt="Banner" style="width: 100px; height: 50px; object-fit: cover; border-radius: 4px;">
                                </td>
                                <td>
                                    <strong class="d-block text-dark"><?= e($b['title_en']) ?></strong>
                                    <small class="text-muted d-block"><?= e($b['title_hi']) ?></small>
                                    <small class="text-primary"><?= e($b['link_url']) ?></small>
                                </td>
                                <td><?= $b['sort_order'] ?></td>
                                <td>
                                    <span class="badge bg-<?= $b['is_active'] ? 'success' : 'secondary' ?>">
                                        <?= $b['is_active'] ? 'Active' : 'Hidden' ?>
                                    </span>
                                </td>
                                <td class="text-end pe-3">
                                    <form action="<?= url('/admin/content/banners/' . $b['id'] . '/delete') ?>" method="POST" onsubmit="return confirm('Delete this banner?');">
                                        <?= csrf_field() ?>
                                        <button type="submit" class="btn btn-sm btn-outline-danger"><i class="bi bi-trash"></i></button>
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Create Banner Form -->
    <div class="col-lg-5">
        <div class="card border shadow-sm p-4 bg-white">
            <h5 class="fw-bold mb-3">Add Hero Slide</h5>
            <form action="<?= url('/admin/content/banners') ?>" method="POST" enctype="multipart/form-data">
                <?= csrf_field() ?>

                <div class="mb-3">
                    <label class="form-label small fw-bold">Headline (English) *</label>
                    <input type="text" name="title_en" class="form-control form-control-sm" required>
                </div>
                <div class="mb-3">
                    <label class="form-label small fw-bold">मुख्य शीर्षक (Hindi Headline) *</label>
                    <input type="text" name="title_hi" class="form-control form-control-sm" required>
                </div>
                <div class="mb-3">
                    <label class="form-label small fw-bold">Subtitle (English)</label>
                    <textarea name="subtitle_en" rows="2" class="form-control form-control-sm"></textarea>
                </div>
                <div class="mb-3">
                    <label class="form-label small fw-bold">उपशीर्षक (Hindi Subtitle)</label>
                    <textarea name="subtitle_hi" rows="2" class="form-control form-control-sm"></textarea>
                </div>
                <div class="mb-3">
                    <label class="form-label small fw-bold">Destination URL Link</label>
                    <input type="text" name="link_url" class="form-control form-control-sm" value="/catalog">
                </div>
                <div class="mb-3">
                    <label class="form-label small fw-bold">Banner Graphic Image *</label>
                    <input type="file" name="image" class="form-control form-control-sm" accept="image/*">
                </div>
                <div class="row g-2 mb-3">
                    <div class="col-6">
                        <label class="form-label small fw-bold">Sort Order</label>
                        <input type="number" name="sort_order" class="form-control form-control-sm" value="0">
                    </div>
                    <div class="col-6 d-flex align-items-end">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="is_active" value="1" id="activeBCheck" checked>
                            <label class="form-check-label small fw-bold" for="activeBCheck">Active</label>
                        </div>
                    </div>
                </div>

                <button type="submit" class="btn btn-sm btn-success w-100">Add Slide Banner</button>
            </form>
        </div>
    </div>
</div>
