<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-9">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb breadcrumb-ev mb-3">
                    <li class="breadcrumb-item"><a href="<?= url('/') ?>"><?= __('home') ?></a></li>
                    <li class="breadcrumb-item active"><?= e($page['title']) ?></li>
                </ol>
            </nav>

            <div class="card border shadow-sm p-4 p-md-5 bg-white rounded-3">
                <h1 class="h2 fw-bold text-success mb-4"><?= e($page['title']) ?></h1>
                <div class="cms-page-content fs-6 lh-lg">
                    <?= $page['content'] ?>
                </div>
            </div>
        </div>
    </div>
</div>
