<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h2 class="h3 fw-bold text-dark m-0">Wellness Blog & Articles</h2>
        <small class="text-muted">Publish classical Ayurvedic education, herb monographs, and lifestyle articles</small>
    </div>
    <div>
        <a href="<?= url('/admin/content/blog/create') ?>" class="btn btn-sm btn-success">
            <i class="bi bi-pencil-square me-1"></i> Write New Article
        </a>
    </div>
</div>

<div class="card border shadow-sm bg-white overflow-hidden">
    <div class="table-responsive">
        <table class="table align-middle mb-0 small">
            <thead class="table-light">
                <tr>
                    <th class="ps-3">Article Title (EN / HI)</th>
                    <th>Author</th>
                    <th>Published Date</th>
                    <th>Status</th>
                    <th class="text-end pe-3">Action</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($posts as $post): ?>
                    <tr>
                        <td class="ps-3">
                            <strong class="d-block text-dark"><?= e($post['title_en']) ?></strong>
                            <small class="text-muted d-block"><?= e($post['title_hi']) ?></small>
                            <code class="small"><?= e($post['slug']) ?></code>
                        </td>
                        <td><?= e($post['author_name']) ?></td>
                        <td><?= date('d M Y', strtotime($post['published_at'])) ?></td>
                        <td>
                            <span class="badge bg-<?= $post['is_published'] ? 'success' : 'secondary' ?>">
                                <?= $post['is_published'] ? 'Published' : 'Draft' ?>
                            </span>
                        </td>
                        <td class="text-end pe-3">
                            <div class="btn-group btn-group-sm">
                                <a href="<?= url('/blog/' . $post['slug']) ?>" target="_blank" class="btn btn-outline-secondary" title="View Article">
                                    <i class="bi bi-box-arrow-up-right"></i>
                                </a>
                                <form action="<?= url('/admin/content/blog/' . $post['id'] . '/delete') ?>" method="POST" class="d-inline" onsubmit="return confirm('Delete this article?');">
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
