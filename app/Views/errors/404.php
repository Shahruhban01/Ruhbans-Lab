<section class="container error-page">
    <div class="page-hero page-hero--split card-surface">
        <div>
            <p class="eyebrow">404</p>
            <h1>Page not found</h1>
            <p class="lead">The page you requested no longer exists or the URL is incorrect. Use search, browse the archive, or return home.</p>
            <div class="hero__actions">
                <a class="btn btn-primary" href="<?php echo e(url('/')); ?>">Go home</a>
                <a class="btn btn-secondary" href="<?php echo e(url('/archive')); ?>">Browse archive</a>
                <a class="btn btn-secondary" href="<?php echo e(url('/search')); ?>">Search site</a>
            </div>
        </div>
        <aside class="panel card-surface">
            <p class="eyebrow">Quick recovery</p>
            <h2>Helpful places</h2>
            <div class="post-list-mini">
                <a href="<?php echo e(url('/search')); ?>"><strong>Search content</strong><span>Find tutorials, projects, and tools</span></a>
                <a href="<?php echo e(url('/archive')); ?>"><strong>Browse archive</strong><span>Review all published posts</span></a>
                <a href="<?php echo e(url('/about')); ?>"><strong>About this site</strong><span>Learn what the platform offers</span></a>
            </div>
        </aside>
    </div>
</section>
