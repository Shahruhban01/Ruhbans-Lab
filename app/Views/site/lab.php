<?php
$productList = isset($products) && is_array($products) ? $products : array();
$pagination = isset($pagination) && is_array($pagination) ? $pagination : array('page' => 1, 'pages' => 1, 'total' => 0);
$allCategories = isset($categories) && is_array($categories) ? $categories : array();
$allTags = isset($tags) && is_array($tags) ? $tags : array();
$activeFilters = isset($filters) && is_array($filters) ? $filters : array('search' => '', 'category' => '', 'tag' => '', 'status' => '', 'product_type' => '');
?>

<div class="container my-5">
    <!-- Hero Showcase Section -->
    <div class="row align-items-center g-5 mb-5">
        <div class="col-lg-8">
            <p class="eyebrow text-primary">Showcase & Experiments</p>
            <h1 class="display-4 fw-bold mb-3">Ruhban's Lab</h1>
            <p class="lead text-muted">Discover interactive tools, mobile apps, packages, open source software, and experimental browser extensions designed and coded in our engineering workshop.</p>
        </div>
        <div class="col-lg-4 text-lg-end">
            <span class="badge bg-primary px-3 py-2 rounded-pill fs-6"><?php echo e($pagination['total']); ?> Products Available</span>
        </div>
    </div>

    <!-- Filter bar -->
    <form class="card-surface p-4 mb-5" method="get" action="<?php echo e(url('/lab')); ?>">
        <div class="row g-3">
            <div class="col-md-3">
                <label class="form-label text-muted small fw-semibold">Search products</label>
                <input type="text" name="q" class="form-control" placeholder="Search by name, desc..." value="<?php echo e($activeFilters['search']); ?>">
            </div>
            <div class="col-md-2">
                <label class="form-label text-muted small fw-semibold">Category</label>
                <select name="category" class="form-select">
                    <option value="">All Categories</option>
                    <?php foreach ($allCategories as $cat) : ?>
                        <option value="<?php echo e($cat['slug']); ?>"<?php echo $activeFilters['category'] === $cat['slug'] ? ' selected' : ''; ?>><?php echo e($cat['name']); ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-2">
                <label class="form-label text-muted small fw-semibold">Product Type</label>
                <select name="product_type" class="form-select">
                    <option value="">All Types</option>
                    <?php foreach (array('Mobile App', 'Website', 'Web Application', 'AI Tool', 'API', 'Open Source Project', 'Desktop Application', 'Browser Extension', 'Template', 'Script', 'Package', 'Experiment') as $type) : ?>
                        <option value="<?php echo e($type); ?>"<?php echo strtolower($activeFilters['product_type']) === strtolower($type) ? ' selected' : ''; ?>><?php echo e($type); ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-2">
                <label class="form-label text-muted small fw-semibold">Status</label>
                <select name="status" class="form-select">
                    <option value="">All Statuses</option>
                    <?php foreach (array('Live', 'Beta', 'Coming Soon', 'Archived') as $status) : ?>
                        <option value="<?php echo e($status); ?>"<?php echo strtolower($activeFilters['status']) === strtolower($status) ? ' selected' : ''; ?>><?php echo e($status); ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-3 d-flex align-items-end gap-2">
                <button type="submit" class="btn btn-primary w-100">Filter</button>
                <a href="<?php echo e(url('/lab')); ?>" class="btn btn-secondary w-100">Clear</a>
            </div>
        </div>
    </form>

    <!-- Product Showcase Grid -->
    <div class="row g-4">
        <?php if ($productList === array()) : ?>
            <div class="col-12 text-center py-5">
                <div class="card-surface p-5">
                    <h3>No products found in the lab</h3>
                    <p class="text-muted">Try clearing the search or filters to see all available products.</p>
                </div>
            </div>
        <?php endif; ?>
        <?php foreach ($productList as $product) : ?>
            <div class="col-lg-4 col-md-6">
                <article class="card h-100 card-surface border-0 overflow-hidden d-flex flex-column">
                    <div class="p-4 flex-grow-1">
                        <div class="d-flex align-items-center justify-content-between mb-3">
                            <span class="badge bg-light text-dark border rounded-pill px-2 py-1 fs-7"><?php echo e($product['content_type_name']); ?></span>
                            <?php
                            $metaStatement = app()->database()->connection()->prepare("SELECT meta_key, meta_value FROM post_meta WHERE post_id = ?");
                            $metaStatement->execute(array($product['id']));
                            $metaFields = $metaStatement->fetchAll(PDO::FETCH_KEY_PAIR);
                            
                            $statusLabel = $metaFields['product_status'] ?? 'Live';
                            $badgeClass = 'bg-success';
                            if ($statusLabel === 'Beta') $badgeClass = 'bg-warning';
                            if ($statusLabel === 'Coming Soon') $badgeClass = 'bg-info';
                            if ($statusLabel === 'Archived') $badgeClass = 'bg-secondary';
                            ?>
                            <span class="badge <?php echo $badgeClass; ?> rounded-pill px-2 py-1 fs-7"><?php echo e($statusLabel); ?></span>
                        </div>
                        
                        <h3 class="h4 fw-bold mb-2"><a href="<?php echo e(url('/lab/' . $product['slug'])); ?>" class="text-dark hover-primary"><?php echo e($product['title']); ?></a></h3>
                        <p class="text-muted small mb-3"><?php echo e($product['excerpt']); ?></p>

                        <?php if (!empty($metaFields['tech_stack'])) : ?>
                            <div class="d-flex flex-wrap gap-1 mb-3">
                                <?php foreach (explode(',', $metaFields['tech_stack']) as $tech) : ?>
                                    <span class="badge bg-light text-muted border"><?php echo e(trim($tech)); ?></span>
                                <?php endforeach; ?>
                            </div>
                        <?php endif; ?>
                    </div>
                    
                    <div class="p-4 bg-light border-top mt-auto d-flex justify-content-between align-items-center">
                        <span class="text-muted small fw-semibold">Version: <?php echo e($metaFields['product_version'] ?? '1.0.0'); ?></span>
                        <a href="<?php echo e(url('/lab/' . $product['slug'])); ?>" class="btn btn-secondary btn-sm rounded-pill px-3">View Details</a>
                    </div>
                </article>
            </div>
        <?php endforeach; ?>
    </div>

    <!-- Pagination -->
    <?php if ($pagination['pages'] > 1) : ?>
        <div class="d-flex justify-content-between align-items-center mt-5 pt-3 border-top">
            <span class="text-muted">Page <?php echo e($pagination['page']); ?> of <?php echo e($pagination['pages']); ?></span>
            <div class="d-flex gap-2">
                <?php if ($pagination['page'] > 1) : ?>
                    <a href="<?php echo e(url('/lab?page=' . ($pagination['page'] - 1))); ?>" class="btn btn-secondary btn-sm">Previous</a>
                <?php endif; ?>
                <?php if ($pagination['page'] < $pagination['pages']) : ?>
                    <a href="<?php echo e(url('/lab?page=' . ($pagination['page'] + 1))); ?>" class="btn btn-secondary btn-sm">Next</a>
                <?php endif; ?>
            </div>
        </div>
    <?php endif; ?>
</div>
