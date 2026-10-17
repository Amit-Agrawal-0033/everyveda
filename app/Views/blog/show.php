<div class="container py-4">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb breadcrumb-ev">
            <li class="breadcrumb-item"><a href="<?= url('/') ?>"><?= __('home') ?></a></li>
            <li class="breadcrumb-item"><a href="<?= url('/blog') ?>"><?= __('blog') ?></a></li>
            <li class="breadcrumb-item active text-truncate" style="max-width: 300px;"><?= e($post['title']) ?></li>
        </ol>
    </nav>

    <div class="row g-5">
        <div class="col-lg-8">
            <article class="bg-white p-4 p-md-5 border rounded-3 shadow-sm mb-5">
                <span class="badge bg-success-subtle text-success border px-2 py-1 mb-2 fw-bold text-uppercase">Ayurvedic Science</span>
                <h1 class="display-6 fw-bold text-dark mb-3"><?= e($post['title']) ?></h1>

                <div class="d-flex align-items-center gap-3 text-muted small pb-3 mb-4 border-bottom">
                    <span><i class="bi bi-person-fill"></i> By <?= e($post['author_name']) ?></span>
                    <span><i class="bi bi-calendar3"></i> <?= date('d M Y', strtotime($post['published_at'])) ?></span>
                </div>

                <div class="text-center mb-4">
                    <img src="<?= asset('assets/images/' . ($post['featured_image'] ?: 'blog-triphala.svg')) ?>" alt="<?= e($post['title']) ?>" class="img-fluid rounded border">
                </div>

                <div class="article-content fs-6 lh-lg">
                    <?= $post['content'] ?>
                </div>

                <div class="border-top pt-4 mt-5">
                    <a href="<?= url('/blog') ?>" class="btn btn-outline-secondary btn-sm">
                        ← Back to All Articles
                    </a>
                </div>
            </article>
        </div>

        <!-- Sidebar Recent Articles -->
        <div class="col-lg-4">
            <div class="card border shadow-sm p-4 bg-white mb-4">
                <h5 class="fw-bold mb-3">Recent Wellness Articles</h5>
                <?php foreach ($recentPosts as $rp): ?>
                    <div class="d-flex gap-3 mb-3 pb-3 border-bottom">
                        <img src="<?= asset('assets/images/' . ($rp['featured_image'] ?: 'blog-triphala.svg')) ?>" alt="<?= e($rp['title']) ?>" style="width: 70px; height: 50px; object-fit: cover; border-radius: 4px;">
                        <div>
                            <h6 class="small fw-bold mb-1">
                                <a href="<?= url('/blog/' . $rp['slug']) ?>" class="text-dark text-decoration-none">
                                    <?= e($rp['title']) ?>
                                </a>
                            </h6>
                            <small class="text-muted"><?= date('d M Y', strtotime($rp['published_at'])) ?></small>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>

            <!-- Banner promo card -->
            <div class="card border p-4 text-white rounded-3 shadow-sm" style="background: linear-gradient(135deg, #183827, #214D36);">
                <h5 class="fw-bold text-warning">Ayurvedic Immunity</h5>
                <p class="small text-white-50">Handcrafted Royal Gold Chyawanprash with fresh forest amla & saffron.</p>
                <a href="<?= url('/product/everyveda-royal-chyawanprash-gold') ?>" class="btn btn-ev-secondary btn-sm align-self-start">Explore Chyawanprash</a>
            </div>
        </div>
    </div>
</div>
