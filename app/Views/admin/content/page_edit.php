<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h1 class="h3 mb-0">Edit Page: <?= e($page['slug']) ?></h1>
        <p class="text-muted small mb-0">Maintain authentic, bilingual English and Hindi page content with full SEO meta tags.</p>
    </div>
    <div>
        <a href="<?= url('/page/' . $page['slug']) ?>" target="_blank" class="btn btn-outline-secondary me-2">
            <i class="bi bi-box-arrow-up-right me-1"></i> Preview Live
        </a>
        <a href="<?= url('/admin/content/pages') ?>" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left me-1"></i> Back to Pages
        </a>
    </div>
</div>

<form action="<?= url('/admin/content/pages/' . $page['id']) ?>" method="POST">
    <?= csrf_field() ?>

    <div class="row g-4">
        <!-- English Content -->
        <div class="col-lg-6">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-white py-3 border-bottom">
                    <h5 class="card-title mb-0 d-flex align-items-center">
                        <span class="badge bg-primary me-2">EN</span> English Version
                    </h5>
                </div>
                <div class="card-body p-4">
                    <div class="mb-3">
                        <label for="title_en" class="form-label fw-semibold">Page Title (English) <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="title_en" name="title_en" value="<?= e($transEn['title'] ?? '') ?>" required>
                    </div>

                    <div class="mb-3">
                        <label for="content_en" class="form-label fw-semibold">Page Content (HTML allowed) <span class="text-danger">*</span></label>
                        <textarea class="form-control font-monospace" id="content_en" name="content_en" rows="16" required><?= e($transEn['content'] ?? '') ?></textarea>
                        <div class="form-text">Supports HTML markup for paragraphs, headings, lists, and tables.</div>
                    </div>

                    <hr class="my-4">
                    <h6 class="fw-bold mb-3 text-muted text-uppercase small">SEO Settings (English)</h6>

                    <div class="mb-3">
                        <label for="meta_title_en" class="form-label fw-semibold">Meta Title</label>
                        <input type="text" class="form-control" id="meta_title_en" name="meta_title_en" value="<?= e($transEn['meta_title'] ?? '') ?>" placeholder="Optimal length: 50-60 characters">
                    </div>

                    <div class="mb-3">
                        <label for="meta_description_en" class="form-label fw-semibold">Meta Description</label>
                        <textarea class="form-control" id="meta_description_en" name="meta_description_en" rows="3" placeholder="Optimal length: 150-160 characters"><?= e($transEn['meta_description'] ?? '') ?></textarea>
                    </div>
                </div>
            </div>
        </div>

        <!-- Hindi Content -->
        <div class="col-lg-6">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-white py-3 border-bottom">
                    <h5 class="card-title mb-0 d-flex align-items-center">
                        <span class="badge bg-warning text-dark me-2">HI</span> हिंदी संस्करण (Hindi Version)
                    </h5>
                </div>
                <div class="card-body p-4">
                    <div class="mb-3">
                        <label for="title_hi" class="form-label fw-semibold">पृष्ठ शीर्षक (Hindi Title) <span class="text-danger">*</span></label>
                        <input type="text" class="form-control font-hindi" id="title_hi" name="title_hi" value="<?= e($transHi['title'] ?? '') ?>" required>
                    </div>

                    <div class="mb-3">
                        <label for="content_hi" class="form-label fw-semibold">सामग्री (Hindi Content - HTML Allowed) <span class="text-danger">*</span></label>
                        <textarea class="form-control font-hindi font-monospace" id="content_hi" name="content_hi" rows="16" required><?= e($transHi['content'] ?? '') ?></textarea>
                        <div class="form-text">Supports HTML markup for paragraphs, headings, lists, and tables.</div>
                    </div>

                    <hr class="my-4">
                    <h6 class="fw-bold mb-3 text-muted text-uppercase small">एसईओ सेटिंग्स (SEO Settings Hindi)</h6>

                    <div class="mb-3">
                        <label for="meta_title_hi" class="form-label fw-semibold">मेटा शीर्षक (Meta Title)</label>
                        <input type="text" class="form-control font-hindi" id="meta_title_hi" name="meta_title_hi" value="<?= e($transHi['meta_title'] ?? '') ?>">
                    </div>

                    <div class="mb-3">
                        <label for="meta_description_hi" class="form-label fw-semibold">मेटा विवरण (Meta Description)</label>
                        <textarea class="form-control font-hindi" id="meta_description_hi" name="meta_description_hi" rows="3"><?= e($transHi['meta_description'] ?? '') ?></textarea>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="d-flex justify-content-end gap-2 mt-4">
        <a href="<?= url('/admin/content/pages') ?>" class="btn btn-outline-secondary px-4">Cancel</a>
        <button type="submit" class="btn btn-primary px-5 shadow-sm">
            <i class="bi bi-save me-1"></i> Save Changes
        </button>
    </div>
</form>
