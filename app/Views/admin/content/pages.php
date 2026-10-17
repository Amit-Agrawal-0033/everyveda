<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h1 class="h3 mb-0">Policy & CMS Pages</h1>
        <p class="text-muted small mb-0">Manage bilingual policy documents, about us, terms, and regulatory disclosures.</p>
    </div>
</div>

<div class="card border-0 shadow-sm">
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-light">
                <tr>
                    <th style="width: 60px;">#</th>
                    <th>Page Title (English)</th>
                    <th>Page Title (Hindi)</th>
                    <th>URL Slug</th>
                    <th>Status</th>
                    <th>Updated At</th>
                    <th class="text-end" style="width: 140px;">Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($pages)): ?>
                    <tr>
                        <td colspan="7" class="text-center py-4 text-muted">No CMS pages found.</td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($pages as $p): ?>
                        <tr>
                            <td class="text-muted">#<?= (int)$p['id'] ?></td>
                            <td>
                                <strong><?= e($p['title_en'] ?? $p['slug']) ?></strong>
                            </td>
                            <td>
                                <span class="text-muted"><?= e($p['title_hi'] ?? '—') ?></span>
                            </td>
                            <td>
                                <code class="small text-primary">/page/<?= e($p['slug']) ?></code>
                            </td>
                            <td>
                                <?php if (!empty($p['is_active'])): ?>
                                    <span class="badge bg-success-subtle text-success border border-success-subtle">Published</span>
                                <?php else: ?>
                                    <span class="badge bg-secondary-subtle text-secondary border border-secondary-subtle">Draft</span>
                                <?php endif; ?>
                            </td>
                            <td class="small text-muted">
                                <?= date('d M Y, h:i A', strtotime($p['updated_at'] ?? $p['created_at'])) ?>
                            </td>
                            <td class="text-end">
                                <a href="<?= url('/page/' . $p['slug']) ?>" target="_blank" class="btn btn-sm btn-outline-secondary me-1" title="View live page">
                                    <i class="bi bi-box-arrow-up-right"></i>
                                </a>
                                <a href="<?= url('/admin/content/pages/' . $p['id'] . '/edit') ?>" class="btn btn-sm btn-primary">
                                    <i class="bi bi-pencil-square me-1"></i> Edit
                                </a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
