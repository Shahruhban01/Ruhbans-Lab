<?php
$post = isset($post) && is_array($post) ? $post : array();
$requiredAccess = isset($requiredAccess) ? (string) $requiredAccess : 'pro';
$relatedFree = isset($relatedFree) && is_array($relatedFree) ? $relatedFree : array();

$badgeClass = 'bg-primary';
if ($requiredAccess === 'pro')      $badgeClass = 'bg-purple text-white';
if ($requiredAccess === 'lifetime') $badgeClass = 'bg-gold text-white';
if ($requiredAccess === 'members_only') $badgeClass = 'bg-success text-white';

?>

<div class="teaser-upgrade-page container my-5">
    <div class="row g-5">
        <!-- Main Teaser Column -->
        <div class="col-lg-8">
            <article class="teaser-preview-container card-surface p-4 rounded-4 border position-relative overflow-hidden mb-4">
                <div class="d-flex align-items-center gap-2 mb-3">
                    <span class="badge <?php echo $badgeClass; ?> rounded-pill px-3 py-2 text-uppercase font-monospace" style="font-size:0.75rem;">
                        ⚡ <?php echo e($requiredAccess); ?> Content
                    </span>
                    <span class="text-muted small">Required membership access to read further.</span>
                </div>
                
                <h1 class="fw-bold mb-3"><?php echo e($post['title'] ?? 'Locked Premium Content'); ?></h1>
                <p class="lead text-muted"><?php echo e($post['excerpt'] ?? 'Configure your plan subscription to unlock this premium guide.'); ?></p>
                
                <!-- Blur effect mockup -->
                <div class="teaser-blur-wrap position-relative my-4" style="user-select: none; pointer-events: none;">
                    <div class="teaser-blur-overlay d-flex flex-column align-items-center justify-content-center text-center p-4">
                        <div class="teaser-lock-icon bg-white p-3 rounded-circle shadow mb-3 border">
                            🔒
                        </div>
                        <h3 class="fw-bold">Unlock This Article</h3>
                        <p class="text-muted small max-width-xs">This resource is reserved for <?php echo e(ucfirst($requiredAccess)); ?> members. Gain full access to libraries, code updates, and repositories.</p>
                        <div class="d-flex gap-2 mt-2">
                            <a href="<?php echo e(url('/pricing')); ?>" class="btn btn-primary" style="pointer-events: auto;">Upgrade Workspace</a>
                            <a href="<?php echo e(url('/login')); ?>" class="btn btn-outline-secondary" style="pointer-events: auto;">Sign In</a>
                        </div>
                    </div>
                    <div class="teaser-blur-text text-muted" style="filter: blur(5px); opacity: 0.35;">
                        <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Proin non porta ligula. Phasellus scelerisque tempor urna, vel mollis libero mollis at. Maecenas ac erat varius, dapibus quam sit amet, hendrerit ipsum. Curabitur vitae sem dictum, interdum magna tincidunt, eleifend est. Nullam in lectus vitae felis tempor imperdiet.</p>
                        <p>Morbi finibus egestas lectus, vel hendrerit risus condimentum non. Phasellus cursus diam ut tellus dictum aliquet. Sed eu libero pulvinar, accumsan lorem sed, luctus diam. Quisque cursus facilisis ipsum sit amet porta. Curabitur et justo ac libero consequat lacinia. Curabitur lacinia congue elit, vel efficitur ex dictum ut.</p>
                    </div>
                </div>
            </article>

            <!-- Related Free Content -->
            <?php if (!empty($relatedFree)) : ?>
                <div class="related-free-section mt-5">
                    <h3 class="fw-bold mb-4">Recommended Free Content</h3>
                    <div class="row g-4">
                        <?php foreach ($relatedFree as $freePost) : ?>
                            <div class="col-md-6">
                                <a href="<?php echo e(url('/content/' . ($freePost['slug'] ?? ''))); ?>" class="card h-100 p-4 border card-surface text-decoration-none text-dark" style="border-radius: var(--radius-lg); transition: all 0.2s ease;">
                                    <h5 class="fw-bold mb-2"><?php echo e($freePost['title']); ?></h5>
                                    <p class="text-muted small mb-0"><?php echo e(mb_substr($freePost['excerpt'] ?? '', 0, 100)); ?>…</p>
                                </a>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            <?php endif; ?>
        </div>

        <!-- Sidebar Conversion Card -->
        <div class="col-lg-4">
            <div class="card p-4 border border-primary border-2 shadow" style="border-radius: var(--radius-lg); position: sticky; top: 7rem;">
                <h4 class="fw-bold mb-3">Upgrade Membership</h4>
                <p class="text-muted small mb-4">Join developers accessing complete codebase templates, configuration boilerplate scripts, and priority developer review tickets.</p>
                
                <div class="p-3 bg-light rounded-3 mb-4">
                    <div class="d-flex justify-content-between mb-2">
                        <span class="small text-muted">Feature Level</span>
                        <strong class="small text-primary text-uppercase"><?php echo e($requiredAccess); ?> Plan</strong>
                    </div>
                    <div class="d-flex justify-content-between">
                        <span class="small text-muted">Pro Access Starts</span>
                        <strong class="small text-success">$9.00 / mo</strong>
                    </div>
                </div>

                <a href="<?php echo e(url('/pricing')); ?>" class="btn btn-primary w-100 py-2 mb-2">⚡ View Pricing Plans</a>
                <a href="<?php echo e(url('/login')); ?>" class="btn btn-outline-secondary w-100 py-2">Sign In to Account</a>
            </div>
        </div>
    </div>
</div>
