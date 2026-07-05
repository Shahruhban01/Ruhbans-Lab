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
$currentUser = app()->session()->get(config('auth.session_key', 'auth_user'));

$getBadgeHtml = function(string $level) {
    $level = strtolower(trim($level));
    if ($level === 'public') return '';
    if ($level === 'members_only') return ' <span class="badge bg-success text-white rounded-pill ms-1 fs-8 fw-semibold px-2">Free</span>';
    if ($level === 'pro') return ' <span class="badge bg-purple text-white rounded-pill ms-1 fs-8 fw-semibold px-2">Pro</span>';
    if ($level === 'lifetime') return ' <span class="badge bg-gold text-white rounded-pill ms-1 fs-8 fw-semibold px-2">Lifetime</span>';
    return '';
};
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
                <!-- Actions / Links -->
                <div class="d-flex flex-wrap gap-2 mt-4">
                    <?php if (!empty($metaFields['live_demo'])) : ?>
                        <?php if (has_product_feature_access($post, 'live_demo', $metaFields)) : ?>
                            <a href="<?php echo e($metaFields['live_demo']); ?>" target="_blank" rel="noopener" class="btn btn-primary">Live Demo</a>
                        <?php else : ?>
                            <button type="button" class="btn btn-primary" onclick="triggerSimulatedPurchase(<?php echo e($post['id']); ?>, '<?php echo e($post['title']); ?>', <?php echo e((int)($metaFields['price_cents'] ?? 1900)); ?>)">Live Demo <?php echo $getBadgeHtml($metaFields['access_live_demo'] ?? 'public'); ?></button>
                        <?php endif; ?>
                    <?php endif; ?>
                    
                    <?php if (!empty($metaFields['github_repository'])) : ?>
                        <?php if (has_product_feature_access($post, 'github_repository', $metaFields)) : ?>
                            <a href="<?php echo e($metaFields['github_repository']); ?>" target="_blank" rel="noopener" class="btn btn-secondary">GitHub Repo</a>
                        <?php else : ?>
                            <button type="button" class="btn btn-secondary" onclick="triggerSimulatedPurchase(<?php echo e($post['id']); ?>, '<?php echo e($post['title']); ?>', <?php echo e((int)($metaFields['price_cents'] ?? 1900)); ?>)">GitHub Repo <?php echo $getBadgeHtml($metaFields['access_github_repository'] ?? 'public'); ?></button>
                        <?php endif; ?>
                    <?php endif; ?>

                    <?php if (!empty($metaFields['documentation_url'])) : ?>
                        <?php if (has_product_feature_access($post, 'documentation', $metaFields)) : ?>
                            <a href="<?php echo e($metaFields['documentation_url']); ?>" target="_blank" rel="noopener" class="btn btn-secondary">Docs</a>
                        <?php else : ?>
                            <button type="button" class="btn btn-secondary" onclick="triggerSimulatedPurchase(<?php echo e($post['id']); ?>, '<?php echo e($post['title']); ?>', <?php echo e((int)($metaFields['price_cents'] ?? 1900)); ?>)">Docs <?php echo $getBadgeHtml($metaFields['access_documentation'] ?? 'public'); ?></button>
                        <?php endif; ?>
                    <?php endif; ?>

                    <?php if (!empty($metaFields['download_url'])) : ?>
                        <?php if (has_product_feature_access($post, 'download', $metaFields)) : ?>
                            <a href="<?php echo e(url('/lab/' . $post['id'] . '/download')); ?>" class="btn btn-secondary">Download</a>
                        <?php else : ?>
                            <button type="button" class="btn btn-secondary" onclick="triggerSimulatedPurchase(<?php echo e($post['id']); ?>, '<?php echo e($post['title']); ?>', <?php echo e((int)($metaFields['price_cents'] ?? 1900)); ?>)">Download <?php echo $getBadgeHtml($metaFields['access_download'] ?? 'public'); ?></button>
                        <?php endif; ?>
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

    <!-- Demo Video Embed -->
    <?php if (!empty($metaFields['demo_video_url'])) : ?>
        <div class="card-surface p-4 mb-5">
            <h3 class="h5 fw-bold mb-3">Watch Demo Video</h3>
            <div class="ratio ratio-16x9">
                <iframe src="<?php echo e($metaFields['demo_video_url']); ?>" title="Product Demo Video" allowfullscreen></iframe>
            </div>
        </div>
    <?php endif; ?>

    <!-- Screenshots Gallery -->
    <?php if (!empty($metaFields['product_screenshots'])) : ?>
        <div class="card-surface p-4 mb-5">
            <h3 class="h5 fw-bold mb-3">Screenshots</h3>
            <div class="row g-2">
                <?php foreach (explode(',', $metaFields['product_screenshots']) as $shot) : if(trim($shot) === '') continue; ?>
                    <div class="col-md-4">
                        <img src="<?php echo e(asset(trim($shot))); ?>" alt="Screenshot" class="img-fluid rounded border shadow-sm">
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    <?php endif; ?>

    <!-- Details Content Grid -->
    <div class="row g-5">
        <div class="col-lg-8">
            <div class="card-surface p-4 mb-5 prose-content">
                <h2 class="h3 fw-bold mb-4">About <?php echo e($post['title']); ?></h2>
                <div class="product-description">
                    <?php if (has_post_access($post)) : ?>
                        <?php echo $contentHtml; ?>
                    <?php else : ?>
                        <div class="premium-teaser-container">
                            <div class="premium-preview-fade">
                                <?php 
                                $paragraphs = explode('</p>', $contentHtml);
                                echo $paragraphs[0] . '</p>';
                                if (isset($paragraphs[1])) {
                                    echo $paragraphs[1] . '</p>';
                                }
                                ?>
                            </div>
                            
                            <div class="premium-lock-card text-center p-5 my-4 rounded border card-surface shadow-sm">
                                <div class="premium-lock-icon mb-3">
                                    <svg width="48" height="48" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" class="text-primary"><rect x="3" y="11" width="18" height="11" rx="2" ry="2"></rect><path d="M7 11V7a5 5 0 0 1 10 0v4"></path></svg>
                                </div>
                                <h3 class="h4 fw-bold mb-3">Locked Premium Product</h3>
                                <p class="text-muted small max-width-500 mx-auto mb-4">
                                    This product requires a <strong><?php echo e(ucfirst(str_replace('_', ' ', $post['visibility']))); ?></strong> membership level or a one-time simulated purchase of <strong>$<?php echo e(number_format(($metaFields['price_cents'] ?? 1900)/100, 2)); ?></strong> to unlock.
                                </p>
                                <div class="d-flex justify-content-center gap-2">
                                    <button type="button" class="btn btn-primary" onclick="triggerSimulatedPurchase(<?php echo e($post['id']); ?>, '<?php echo e($post['title']); ?>', <?php echo e((int)($metaFields['price_cents'] ?? 1900)); ?>)">💳 Buy Now (Simulated)</button>
                                    <a href="<?php echo e(url('/pricing')); ?>" class="btn btn-outline-secondary">Upgrade Plan</a>
                                </div>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Features & Installation & API Docs -->
            <div class="row g-4 mb-5">
                <?php if (!empty($metaFields['product_features'])) : ?>
                    <div class="col-md-6">
                        <div class="card-surface h-100 p-4">
                            <h3 class="h5 fw-bold mb-3">Key Features</h3>
                            <ul class="list-unstyled mb-0">
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
                            <h3 class="h5 fw-bold mb-3">Installation Guide <?php echo $getBadgeHtml($metaFields['access_installation_guide'] ?? 'public'); ?></h3>
                            <?php if (has_product_feature_access($post, 'installation_guide', $metaFields)) : ?>
                                <div class="bg-light p-3 rounded">
                                    <pre class="mb-0"><code><?php echo e($metaFields['installation_guide']); ?></code></pre>
                                </div>
                            <?php else : ?>
                                <div class="premium-lock-card text-center p-4 rounded border bg-light shadow-sm">
                                    <div class="mb-2"><svg width="24" height="24" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" class="text-muted"><rect x="3" y="11" width="18" height="11" rx="2" ry="2"></rect><path d="M7 11V7a5 5 0 0 1 10 0v4"></path></svg></div>
                                    <p class="text-muted small mb-2">Installation instructions locked.</p>
                                    <button class="btn btn-outline-primary btn-sm rounded-pill px-3" onclick="triggerSimulatedPurchase(<?php echo e($post['id']); ?>, '<?php echo e($post['title']); ?>', <?php echo e((int)($metaFields['price_cents'] ?? 1900)); ?>)">Unlock Guide</button>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endif; ?>
            </div>

            <?php if (!empty($metaFields['api_documentation'])) : ?>
                <div class="card-surface p-4 mb-5">
                    <h3 class="h5 fw-bold mb-3">API Documentation <?php echo $getBadgeHtml($metaFields['access_api_documentation'] ?? 'public'); ?></h3>
                    <?php if (has_product_feature_access($post, 'api_documentation', $metaFields)) : ?>
                        <div class="bg-light p-3 rounded">
                            <pre class="mb-0"><code><?php echo e($metaFields['api_documentation']); ?></code></pre>
                        </div>
                    <?php else : ?>
                        <div class="premium-lock-card text-center p-5 rounded border bg-light shadow-sm">
                             <div class="mb-3"><svg width="32" height="32" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" class="text-muted"><rect x="3" y="11" width="18" height="11" rx="2" ry="2"></rect><path d="M7 11V7a5 5 0 0 1 10 0v4"></path></svg></div>
                             <h4 class="h5 fw-bold mb-2">API Documentation Locked</h4>
                             <p class="text-muted small max-width-400 mx-auto mb-3">Please upgrade or purchase to access full API structures.</p>
                             <button class="btn btn-primary btn-sm rounded-pill px-4" onclick="triggerSimulatedPurchase(<?php echo e($post['id']); ?>, '<?php echo e($post['title']); ?>', <?php echo e((int)($metaFields['price_cents'] ?? 1900)); ?>)">Buy / Unlock</button>
                        </div>
                    <?php endif; ?>
                </div>
            <?php endif; ?>

            <?php if (!empty($metaFields['product_changelog'])) : ?>
                <div class="card-surface p-4 mb-5" id="changelog">
                    <h3 class="h5 fw-bold mb-3">Changelog</h3>
                    <div class="prose-content">
                        <?php echo nl2br(e($metaFields['product_changelog'])); ?>
                    </div>
                </div>
            <?php endif; ?>

            <?php if (!empty($metaFields['product_version_history'])) : ?>
                <div class="card-surface p-4 mb-5" id="version-history">
                    <h3 class="h5 fw-bold mb-3">Version History</h3>
                    <div class="prose-content">
                        <?php echo nl2br(e($metaFields['product_version_history'])); ?>
                    </div>
                </div>
            <?php endif; ?>
        </div>

        <!-- Sidebar Specifications -->
        <div class="col-lg-4">
            <!-- Price Box -->
            <div class="card p-4 border border-primary border-2 shadow-sm mb-4" style="border-radius: var(--radius-lg);">
                <span class="text-muted small text-uppercase fw-semibold">Simulated Value</span>
                <div class="my-2">
                    <span class="h2 fw-bold">$<?php echo e(number_format(($metaFields['price_cents'] ?? 1900)/100, 2)); ?></span>
                </div>
                <button type="button" class="btn btn-primary w-100 py-2 mb-2" onclick="triggerSimulatedPurchase(<?php echo e($post['id']); ?>, '<?php echo e($post['title']); ?>', <?php echo e((int)($metaFields['price_cents'] ?? 1900)); ?>)">💳 Buy Product</button>
                <p class="text-muted text-center small mb-0 mt-1">One-time purchase gets lifetime downloads & updates.</p>
            </div>

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

                    <dt class="col-sm-5 text-muted small">Downloads</dt>
                    <dd class="col-sm-7 small fw-semibold"><?php echo e($metaFields['download_count'] ?? 0); ?></dd>

                    <?php if (!empty($metaFields['product_changelog'])) : ?>
                        <dt class="col-sm-5 text-muted small">Changelog</dt>
                        <dd class="col-sm-7 small"><a href="#changelog">View Changelog</a></dd>
                    <?php endif; ?>

                    <?php if (!empty($metaFields['product_version_history'])) : ?>
                        <dt class="col-sm-5 text-muted small">Revisions</dt>
                        <dd class="col-sm-7 small"><a href="#version-history">Version History</a></dd>
                    <?php endif; ?>
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

<script>
function triggerSimulatedPurchase(productId, productName, amount) {
    <?php if (!$currentUser) : ?>
        alert("Please login first to purchase or upgrade packages.");
        window.location.href = "<?php echo e(url('/login')); ?>";
        return;
    <?php endif; ?>

    var price = (amount / 100).toFixed(2);
    alert("Simulating Razorpay Payment Gateway...\n\nProduct: " + productName + "\nAmount: $" + price + "\n\nProcessing success feedback...");

    var form = document.createElement('form');
    form.method = 'POST';
    form.action = "<?php echo e(url('/lab/purchase')); ?>";

    var csrf = document.createElement('input');
    csrf.type = 'hidden';
    csrf.name = '_token';
    csrf.value = "<?php echo csrf_token(); ?>";
    form.appendChild(csrf);

    var pid = document.createElement('input');
    pid.type = 'hidden';
    pid.name = 'product_id';
    pid.value = productId;
    form.appendChild(pid);

    document.body.appendChild(form);
    form.submit();
}
</script>

