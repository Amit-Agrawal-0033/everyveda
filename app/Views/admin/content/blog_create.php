<div class="mb-4">
    <a href="<?= url('/admin/content/blog') ?>" class="small text-muted text-decoration-none">← Back to Articles List</a>
    <h2 class="h3 fw-bold text-dark m-0 mt-1">Publish New Ayurvedic Article</h2>
</div>

<form action="<?= url('/admin/content/blog/create') ?>" method="POST" enctype="multipart/form-data">
    <?= csrf_field() ?>

    <div class="row g-4">
        <div class="col-lg-8">
            <div class="card border shadow-sm p-4 bg-white mb-4">
                <h5 class="fw-bold mb-3">English Article Content</h5>
                <div class="mb-3">
                    <label class="form-label small fw-bold">Title (English) *</label>
                    <input type="text" name="title_en" class="form-control" placeholder="E.g. Daily Triphala Benefits" value="<?= old('title_en') ?>" required>
                </div>
                <div class="mb-3">
                    <label class="form-label small fw-bold">Short Summary (English)</label>
                    <textarea name="summary_en" rows="2" class="form-control"><?= old('summary_en') ?></textarea>
                </div>
                <div class="mb-3">
                    <label class="form-label small fw-bold">Full Article Content (HTML Allowed) *</label>
                    <textarea name="content_en" rows="8" class="form-control" required><?= old('content_en') ?></textarea>
                </div>
            </div>

            <div class="card border shadow-sm p-4 bg-white mb-4">
                <h5 class="fw-bold mb-3">हिन्दी लेख सामग्री (Hindi Article Content)</h5>
                <div class="mb-3">
                    <label class="form-label small fw-bold">शीर्षक (Hindi Title) *</label>
                    <input type="text" name="title_hi" class="form-control" placeholder="त्रिफला के दैनिक उपयोग के लाभ" value="<?= old('title_hi') ?>" required>
                </div>
                <div class="mb-3">
                    <label class="form-label small fw-bold">संक्षिप्त विवरण (Hindi Summary)</label>
                    <textarea name="summary_hi" rows="2" class="form-control"><?= old('summary_hi') ?></textarea>
                </div>
                <div class="mb-3">
                    <label class="form-label small fw-bold">पूर्ण आलेख (Hindi Content) *</label>
                    <textarea name="content_hi" rows="8" class="form-control" required><?= old('content_hi') ?></textarea>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card border shadow-sm p-4 bg-white mb-4">
                <h5 class="fw-bold mb-3">Publish Settings</h5>
                <div class="mb-3">
                    <label class="form-label small fw-bold">URL Slug *</label>
                    <input type="text" name="slug" class="form-control form-control-sm" placeholder="triphala-health-benefits" value="<?= old('slug') ?>" required>
                </div>
                <div class="mb-3">
                    <label class="form-label small fw-bold">Author Name</label>
                    <input type="text" name="author_name" class="form-control form-control-sm" value="Vaidya Ramesh Sharma">
                </div>
                <div class="mb-3">
                    <label class="form-label small fw-bold">Featured Image</label>
                    <input type="file" name="featured_image" class="form-control form-control-sm" accept="image/*">
                </div>
                <div class="form-check mb-3">
                    <input class="form-check-input" type="checkbox" name="is_published" value="1" id="publishCheck" checked>
                    <label class="form-check-label small fw-bold" for="publishCheck">Publish Immediately</label>
                </div>

                <button type="submit" class="btn btn-success w-100 py-2">Publish Article</button>
            </div>
        </div>
    </div>
</form>
