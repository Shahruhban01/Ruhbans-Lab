<?php
$categories = isset($categories) && is_array($categories) ? $categories : array();
$tags = isset($tags) && is_array($tags) ? $tags : array();
$relatedProducts = isset($relatedProducts) && is_array($relatedProducts) ? $relatedProducts : array();
$author = isset($author) && is_array($author) ? $author : array();
$seo = isset($seo) && is_array($seo) ? $seo : array();
$comments = isset($comments) && is_array($comments) ? $comments : array();
$interactionCounts = isset($interactionCounts) && is_array($interactionCounts) ? $interactionCounts : array('likes' => 0, 'bookmarks' => 0, 'favorites' => 0, 'comments' => 0);
$interactionState = isset($interactionState) && is_array($interactionState) ? $interactionState : array('like' => false, 'bookmark' => false, 'favorite' => false);
$contentHtml = isset($contentHtml) ? (string) $contentHtml : (string) $post['content'];
$metaFields = isset($metaFields) && is_array($metaFields) ? $metaFields : array();

$publishedAt = !empty($post['published_at']) ? $post['published_at'] : $post['created_at'];
?>

<div class="container my-5">
    <!-- Breadcrumb -->
    <nav class="mb-4" aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="<?php echo e(url('/')); ?>">Home</a></li>
            <li class="breadcrumb-item"><a href="<?php echo e(url('/lab')); ?>">Lab</a></li>
            <li class="breadcrumb-item active" aria-current="page"><?php echo e($post['title']); ?></li>
        </ol>
    </nav>

    <!-- Product Title Header -->
    <div class="card-surface p-5 mb-5">
        <div class="row align-items-center g-4">
            <div class="col-lg-8">
                <div class="d-flex flex-wrap align-items-center gap-2 mb-3">
                    <span class="badge bg-primary rounded-pill"><?php echo e($metaFields['product_type'] ?? 'Software'); ?></span>
                    <span class="badge bg-success rounded-pill"><?php echo e($metaFields['product_status'] ?? 'Live'); ?></span>
                    <span class="text-muted small">v<?php echo e($metaFields['product_version'] ?? '1.0.0'); ?></span>
                </div>
                <h1 class="display-4 fw-bold mb-3"><?php echo e($post['title']); ?></h1>
                <p class="lead text-muted"><?php echo e($post['excerpt']); ?></p>

                <!-- Actions / Links -->
                <div class="d-flex flex-wrap gap-2 mt-4">
                    <?php if (!empty($metaFields['live_demo'])) : ?>
                        <a href="<?php echo e($metaFields['live_demo']); ?>" target="_blank" rel="noopener" class="btn btn-primary">Live Demo</a>
                    <?php endif; ?>
                    <?php if (!empty($metaFields['github_repository'])) : ?>
                        <a href="<?php echo e($metaFields['github_repository']); ?>" target="_blank" rel="noopener" class="btn btn-secondary">GitHub Repo</a>
                    <?php endif; ?>
                    <?php if (!empty($metaFields['documentation_url'])) : ?>
                        <a href="<?php echo e($metaFields['documentation_url']); ?>" target="_blank" rel="noopener" class="btn btn-secondary">Docs</a>
                    <?php endif; ?>
                    <?php if (!empty($metaFields['download_url'])) : ?>
                        <a href="<?php echo e($metaFields['download_url']); ?>" target="_blank" rel="noopener" class="btn btn-secondary">Download</a>
                    <?php endif; ?>
                </div>
            </div>
            
            <div class="col-lg-4 text-center">
                <?php if (!empty($post['featured_image'])) : ?>
                    <img class="img-fluid rounded shadow-lg max-height-300" src="<?php echo e(asset($post['featured_image'])); ?>" alt="<?php echo e($post['title']); ?>">
                <?php else : ?>
                    <div class="p-5 border rounded bg-light text-muted">No Image Available</div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Details Content Grid -->
    <div class="row g-5">
        <div class="col-lg-8">
            <div class="card-surface p-4 mb-5 prose-content">
                <h2 class="h3 fw-bold mb-4">About <?php echo e($post['title']); ?></h2>
                <div class="product-description">
                    <?php echo $contentHtml; ?>
                </div>
            </div>

            <!-- Features & Installation -->
            <?php if (!empty($metaFields['product_features']) || !empty($metaFields['installation_guide'])) : ?>
                <div class="row g-4 mb-5">
                    <?php if (!empty($metaFields['product_features'])) : ?>
                        <div class="col-md-6">
                            <div class="card-surface h-100 p-4">
                                <h3 class="h5 fw-bold mb-3">Key Features</h3>
                                <ul class="list-unstyled">
                                    <?php foreach (explode("\n", $metaFields['product_features']) as $feature) : ?>
                                        <?php if (trim($feature) !== '') : ?>
                                            <li class="mb-2"><svg class="text-success me-2" width="16" height="16" fill="currentColor" viewBox="0 0 16 16"><path d="M12.736 3.97a.733.733 0 0 1 1.047 0c.286.289.29.756.01 1.05L7.88 12.01a.733.733 0 0 1-1.065.02L3.217 8.384a.757.757 0 0 1 0-1.06.733.733 0 0 1 1.047 0l3.052 3.093 5.4-6.425a.247.247 0 0 1 .02-.022z"/></svg><?php echo e(trim($feature)); ?></li>
                                        <?php endif; ?>
                                    <?php endforeach; ?>
                                </ul>
                            </div>
                        </div>
                    <?php endif; ?>

                    <?php if (!empty($metaFields['installation_guide'])) : ?>
                        <div class="col-md-6">
                            <div class="card-surface h-100 p-4">
                                <h3 class="h5 fw-bold mb-3">Installation Guide</h3>
                                <div class="bg-light p-3 rounded">
                                    <pre class="mb-0"><code><?php echo e($metaFields['installation_guide']); ?></code></pre>
                                </div>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>
            <?php endif; ?>
        </div>

        <!-- Sidebar Specifications -->
        <div class="col-lg-4">
            <div class="card-surface p-4 mb-4">
                <h3 class="h5 fw-bold mb-4">Product Specifications</h3>
                
                <dl class="row mb-0">
                    <dt class="col-sm-5 text-muted small">License</dt>
                    <dd class="col-sm-7 small fw-semibold"><?php echo e($metaFields['product_license'] ?? 'MIT'); ?></dd>
                    
                    <dt class="col-sm-5 text-muted small">Tech Stack</dt>
                    <dd class="col-sm-7 small fw-semibold"><?php echo e($metaFields['tech_stack'] ?? 'Not Specified'); ?></dd>

                    <?php if (!empty($metaFields['requirements'])) : ?>
                        <dt class="col-sm-5 text-muted small">Requirements</dt>
                        <dd class="col-sm-7 small"><?php echo e($metaFields['requirements']); ?></dd>
                    <?php endif; ?>

                    <dt class="col-sm-5 text-muted small">Changelog</dt>
                    <dd class="col-sm-7 small"><a href="#changelog">View Changelog</a></dd>
                </dl>
            </div>

            <!-- Stores links -->
            <?php if (!empty($metaFields['play_store_url']) || !empty($metaFields['app_store_url'])) : ?>
                <div class="card-surface p-4 mb-4">
                    <h3 class="h5 fw-bold mb-3">Get the App</h3>
                    <div class="d-grid gap-2">
                        <?php if (!empty($metaFields['play_store_url'])) : ?>
                            <a href="<?php echo e($metaFields['play_store_url']); ?>" target="_blank" rel="noopener" class="btn btn-dark btn-sm text-start"><svg width="16" height="16" fill="currentColor" class="me-2" viewBox="0 0 16 16"><path d="M10.222 6.222a.5.5 0 0 0-.707.707l1.38 1.38-1.38 1.38a.5.5 0 1 0 .707.707l1.733-1.733a.5.5 0 0 0 0-.707L10.22 6.222z"/><path d="M6 3a3 3 0 1 1-6 0 3 3 0 0 1 6 0z"/></svg> Google Play</a>
                        <?php endif; ?>
                        <?php if (!empty($metaFields['app_store_url'])) : ?>
                            <a href="<?php echo e($metaFields['app_store_url']); ?>" target="_blank" rel="noopener" class="btn btn-dark btn-sm text-start"><svg width="16" height="16" fill="currentColor" class="me-2" viewBox="0 0 16 16"><path d="M11.182.008C11.148-.03 10.5-.023 9.688.574c-.813.599-1.124 1.767-1.124 1.767s1.13.064 1.94-.536c.813-.6 1.077-1.4 1.077-1.4s-.368-.358-.4-.397z"/><path d="M12.181 3.518c-.461-.263-1.042-.405-1.597-.405-1.384 0-2.072.846-2.825.846-.745 0-1.428-.809-2.604-.809-1.282 0-2.482.909-3.087 1.996C1.455 6.303.958 8.818 1.838 11.238c.414 1.144 1.258 2.378 2.399 2.404.992.022 1.32-.596 2.476-.596 1.157 0 1.45.587 2.477.587 1.033 0 1.833-1.126 2.383-2.146.638-.934.898-1.854.912-1.9a.1.1 0 0 0-.057-.123c-.092-.036-1.748-.684-1.748-2.697 0-1.688 1.353-2.513 1.41-2.551a.105.105 0 0 0 .043-.097c-.015-.054-.374-1.173-.99-1.699z"/></svg> App Store</a>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>
