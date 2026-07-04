<?php
$mediaData = isset($media['data']) ? $media['data'] : array();
$pagination = isset($media['pagination']) ? $media['pagination'] : array('page' => 1, 'pages' => 1, 'total' => 0);
?>
<section class="page-stack">
    <div class="page-header page-header--split">
        <div>
            <p class="eyebrow">Asset library</p>
            <h2>Media Manager</h2>
        </div>
        <div class="content-actions">
            <a class="btn btn-secondary" href="<?php echo e(url('/admin/content/media-manager')); ?>">Switch to Library view</a>
        </div>
    </div>

    <section class="panel card-surface mb-4">
        <form method="get" action="<?php echo e(url('/admin/content/media')); ?>" class="search-form d-flex gap-2">
            <input type="search" name="search" class="form-control" value="<?php echo e(isset($_GET['search']) ? $_GET['search'] : ''); ?>" placeholder="Search media...">
            <button type="submit" class="btn btn-secondary">Search</button>
        </form>
    </section>

    <div class="row g-4">
        <div class="col-lg-4">
            <section class="panel card-surface">
                <h3 class="h5 fw-bold mb-4">Upload media</h3>
                <form method="post" action="<?php echo e(url('/admin/content/media')); ?>" enctype="multipart/form-data" class="d-flex flex-column gap-3">
                    <?php echo csrf_field(); ?>
                    <div class="form-group">
                        <label class="form-label text-muted small fw-semibold">File</label>
                        <input type="file" name="file" class="form-control" accept="image/*,application/pdf" required>
                    </div>
                    <div class="form-group">
                        <label class="form-label text-muted small fw-semibold">ALT text</label>
                        <input type="text" name="alt_text" class="form-control" value="">
                    </div>
                    <button type="submit" class="btn btn-primary w-100 mt-2">Upload</button>
                </form>
            </section>
        </div>

        <div class="col-lg-8">
            <section class="panel card-surface">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h3 class="h5 fw-bold mb-0">Library</h3>
                    <span class="badge bg-primary px-3 py-2 rounded-pill">Total files: <?php echo e($pagination['total']); ?></span>
                </div>
                <div class="table-responsive">
                    <table class="table admin-table table-hover align-middle mb-0">
                        <thead>
                            <tr>
                                <th style="width: 70px;">Preview</th>
                                <th>Name & Details</th>
                                <th>Path / Location</th>
                                <th class="text-end" style="width: 100px;">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if ($mediaData === array()) : ?>
                                <tr>
                                    <td colspan="4" class="text-center py-4 text-muted">No media files uploaded yet.</td>
                                </tr>
                            <?php endif; ?>
                            <?php foreach ($mediaData as $item) : ?>
                                <tr>
                                    <td>
                                        <?php if (strpos((string)$item['mime_type'], 'image/') === 0) : ?>
                                            <img src="<?php echo e(asset($item['path'])); ?>" alt="<?php echo e($item['alt_text'] ?: 'preview'); ?>" class="rounded shadow-sm border" style="width: 40px; height: 40px; object-fit: cover;">
                                        <?php else : ?>
                                            <div class="rounded bg-light text-muted d-flex align-items-center justify-content-center border" style="width: 40px; height: 40px; font-size: 0.65rem; font-weight: 800;">PDF</div>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <span class="fw-semibold text-dark small d-block text-truncate" style="max-width: 180px;" title="<?php echo e($item['original_name']); ?>"><?php echo e($item['original_name']); ?></span>
                                        <span class="text-muted small fs-7"><?php echo e($item['mime_type']); ?></span>
                                    </td>
                                    <td>
                                        <code class="small text-muted"><?php echo e($item['path']); ?></code>
                                    </td>
                                    <td class="text-end">
                                        <form method="post" action="<?php echo e(url('/admin/content/media/' . $item['id'] . '/delete')); ?>" class="d-inline" onsubmit="return confirm('Are you sure you want to delete this file?')">
                                            <?php echo csrf_field(); ?>
                                            <button type="submit" class="btn btn-outline-danger btn-sm px-3 rounded-pill">Delete</button>
                                        </form>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
                <div class="d-flex justify-content-between align-items-center mt-4">
                    <span class="text-muted small">Showing Page <?php echo e($pagination['page']); ?> of <?php echo e($pagination['pages']); ?></span>
                    <nav aria-label="Media pagination">
                        <ul class="pagination pagination-sm mb-0">
                            <?php for ($i = 1; $i <= $pagination['pages']; $i++) : ?>
                                <li class="page-item <?php echo $i === $pagination['page'] ? 'active' : ''; ?>">
                                    <a class="page-link" href="<?php echo e(url('/admin/content/media?page=' . $i . '&search=' . urlencode(isset($_GET['search']) ? $_GET['search'] : ''))); ?>"><?php echo e($i); ?></a>
                                </li>
                            <?php endfor; ?>
                        </ul>
                    </nav>
                </div>
            </section>
        </div>
    </div>
</section>
