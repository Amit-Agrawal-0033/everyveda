<div class="d-flex flex-wrap justify-content-between align-items-center mb-4 gap-2">
    <div>
        <h1 class="h3 mb-0">Product Reviews Moderation</h1>
        <p class="text-muted small mb-0">Review, approve, or reject customer feedback and Ayurvedic ratings before publication.</p>
    </div>
    <div class="btn-group shadow-sm">
        <a href="<?= url('/admin/reviews?status=pending') ?>" class="btn btn-outline-secondary <?= ($currentStatus ?? 'pending') === 'pending' ? 'active' : '' ?>">
            <i class="bi bi-clock-history me-1"></i> Pending
        </a>
        <a href="<?= url('/admin/reviews?status=approved') ?>" class="btn btn-outline-secondary <?= ($currentStatus ?? '') === 'approved' ? 'active' : '' ?>">
            <i class="bi bi-check2-circle me-1"></i> Approved
        </a>
        <a href="<?= url('/admin/reviews?status=rejected') ?>" class="btn btn-outline-secondary <?= ($currentStatus ?? '') === 'rejected' ? 'active' : '' ?>">
            <i class="bi bi-x-circle me-1"></i> Rejected
        </a>
        <a href="<?= url('/admin/reviews?status=all') ?>" class="btn btn-outline-secondary <?= ($currentStatus ?? '') === 'all' ? 'active' : '' ?>">
            All
        </a>
    </div>
</div>

<div class="card border-0 shadow-sm">
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-light">
                <tr>
                    <th style="width: 50px;">#</th>
                    <th style="width: 200px;">Product</th>
                    <th style="width: 160px;">Reviewer</th>
                    <th style="width: 120px;">Rating</th>
                    <th>Review Title & Comment</th>
                    <th style="width: 100px;">Status</th>
                    <th style="width: 110px;">Date</th>
                    <th class="text-end" style="width: 170px;">Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($reviews)): ?>
                    <tr>
                        <td colspan="8" class="text-center py-5 text-muted">
                            <i class="bi bi-chat-square-quote display-6 d-block mb-2 text-secondary"></i>
                            No reviews found in this moderation queue.
                        </td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($reviews as $r): ?>
                        <tr>
                            <td class="text-muted">#<?= (int)$r['id'] ?></td>
                            <td>
                                <a href="<?= url('/product/' . $r['product_slug']) ?>" target="_blank" class="fw-semibold text-decoration-none">
                                    <?= e($r['product_name'] ?? $r['product_slug']) ?>
                                    <i class="bi bi-box-arrow-up-right small text-muted ms-1"></i>
                                </a>
                            </td>
                            <td>
                                <strong><?= e($r['reviewer_name']) ?></strong>
                                <?php if (!empty($r['reviewer_email'])): ?>
                                    <div class="small text-muted"><?= e($r['reviewer_email']) ?></div>
                                <?php endif; ?>
                            </td>
                            <td>
                                <div class="text-warning">
                                    <?php for ($i = 1; $i <= 5; $i++): ?>
                                        <i class="bi <?= $i <= (int)$r['rating'] ? 'bi-star-fill' : 'bi-star' ?>"></i>
                                    <?php endfor; ?>
                                </div>
                                <span class="small text-muted"><?= (int)$r['rating'] ?> of 5 stars</span>
                            </td>
                            <td>
                                <?php if (!empty($r['title'])): ?>
                                    <div class="fw-semibold"><?= e($r['title']) ?></div>
                                <?php endif; ?>
                                <p class="mb-0 small text-secondary" style="max-width: 450px;"><?= nl2br(e($r['comment'])) ?></p>
                            </td>
                            <td>
                                <?php if ($r['status'] === 'approved'): ?>
                                    <span class="badge bg-success-subtle text-success border border-success-subtle">Approved</span>
                                <?php elseif ($r['status'] === 'rejected'): ?>
                                    <span class="badge bg-danger-subtle text-danger border border-danger-subtle">Rejected</span>
                                <?php else: ?>
                                    <span class="badge bg-warning-subtle text-dark border border-warning-subtle">Pending</span>
                                <?php endif; ?>
                            </td>
                            <td class="small text-muted">
                                <?= date('d M Y', strtotime($r['created_at'])) ?>
                            </td>
                            <td class="text-end">
                                <div class="d-inline-flex gap-1">
                                    <?php if ($r['status'] !== 'approved'): ?>
                                        <form action="<?= url('/admin/reviews/' . $r['id'] . '/status') ?>" method="POST" class="d-inline">
                                            <?= csrf_field() ?>
                                            <input type="hidden" name="status" value="approved">
                                            <button type="submit" class="btn btn-sm btn-success" title="Approve Review">
                                                <i class="bi bi-check-lg"></i>
                                            </button>
                                        </form>
                                    <?php endif; ?>

                                    <?php if ($r['status'] !== 'rejected'): ?>
                                        <form action="<?= url('/admin/reviews/' . $r['id'] . '/status') ?>" method="POST" class="d-inline">
                                            <?= csrf_field() ?>
                                            <input type="hidden" name="status" value="rejected">
                                            <button type="submit" class="btn btn-sm btn-outline-warning text-dark" title="Reject Review">
                                                <i class="bi bi-slash-circle"></i>
                                            </button>
                                        </form>
                                    <?php endif; ?>

                                    <form action="<?= url('/admin/reviews/' . $r['id'] . '/delete') ?>" method="POST" class="d-inline" onsubmit="return confirm('Permanently delete this customer review?');">
                                        <?= csrf_field() ?>
                                        <button type="submit" class="btn btn-sm btn-outline-danger" title="Delete Review">
                                            <i class="bi bi-trash"></i>
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
