<div class="container py-4">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb breadcrumb-ev">
            <li class="breadcrumb-item"><a href="<?= url('/') ?>"><?= __('home') ?></a></li>
            <li class="breadcrumb-item active"><?= __('blog') ?></li>
        </ol>
    </nav>

    <div class="text-center mb-5">
        <span class="text-uppercase small fw-bold text-success">Ancient Wisdom • Modern Science</span>
        <h1 class="display-6 fw-bold text-success mb-2"><?= __('blog') ?></h1>
        <p class="text-muted max-w-600 mx-auto">Explore authentic classical Ayurvedic literature, herbal preparation rituals, and seasonal health balance.</p>
    </div>

    <div class="row g-4 mb-5">
        <?php foreach ($posts as $post): ?>
            <div class="col-md-6 col-lg-4">
                <div class="card h-100 border overflow-hidden shadow-sm">
                    <img src="<?= asset('assets/images/' . ($post['featured_image'] ?: 'blog-triphala.svg')) ?>" class="card-img-top" alt="<?= e($post['title']) ?>">
                    <div class="card-body d-flex flex-column">
                        <small class="text-muted mb-2 d-block">
                            <i class="bi bi-calendar3 me-1"></i> <?= date('d M Y', strtotime($post['published_at'])) ?> • <?= e($post['author_name']) ?>
                        </small>
                        <h4 class="card-title fw-bold fs-5">
                            <a href="<?= url('/blog/' . $post['slug']) ?>" class="text-dark text-decoration-none">
                                <?= e($post['title']) ?>
                            </a>
                        </h4>
                        <p class="card-text text-muted small">
                            <?= e(substr(strip_tags((string)$post['summary']), 0, 140)) ?>...
                        </p>
                        <a href="<?= url('/blog/' . $post['slug']) ?>" class="mt-auto text-success fw-bold text-decoration-none small">
                            <?= __('read_more') ?> →
                        </a>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</div>
