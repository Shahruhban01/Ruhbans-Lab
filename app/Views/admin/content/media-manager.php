<?php
$mediaData = isset($media['data']) ? $media['data'] : array();
$pagination = isset($media['pagination']) ? $media['pagination'] : array('page' => 1, 'pages' => 1, 'total' => 0);
?>
<section class="page-stack">
    <div class="page-header page-header--split">
        <div>
            <p class="eyebrow">Asset administration</p>
            <h2>Media Manager Table</h2>
        </div>
        <div class="content-actions">
            <a class="btn btn-secondary" href="<?php echo e(url('/admin/content/media')); ?>">Switch to Grid view</a>
        </div>
    </div>

    <section class="panel card-surface mb-4">
        <form method="get" action="<?php echo e(url('/admin/content/media-manager')); ?>" class="search-form d-flex gap-2">
            <input type="search" name="search" class="form-control" value="<?php echo e(isset($_GET['search']) ? $_GET['search'] : ''); ?>" placeholder="Search files by name, type, or alt text...">
            <button type="submit" class="btn btn-secondary">Search</button>
        </form>
    </section>

    <div class="row">
        <div class="col-12">
            <section class="panel card-surface">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h3 class="h5 fw-bold mb-0">Library Assets</h3>
                    <span class="badge bg-primary px-3 py-2 rounded-pill">Total files: <?php echo e($pagination['total']); ?></span>
                </div>
                <div class="table-responsive">
                    <table class="table admin-table table-hover align-middle mb-0">
                        <thead>
                            <tr>
                                <th style="width: 80px;">Preview</th>
                                <th>Name & Details</th>
                                <th>Path / Location</th>
                                <th>Mime Type</th>
                                <th>File Size</th>
                                <th class="text-end" style="width: 120px;">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if ($mediaData === array()) : ?>
                                <tr>
                                    <td colspan="6" class="text-center py-5 text-muted">
                                        <p class="mb-0">No media files available in the library.</p>
                                    </td>
                                </tr>
                            <?php endif; ?>
                            <?php foreach ($mediaData as $item) : ?>
                                <tr>
                                    <td>
                                        <?php if (strpos((string)$item['mime_type'], 'image/') === 0) : ?>
                                            <img src="<?php echo e(asset($item['path'])); ?>" alt="<?php echo e($item['alt_text'] ?: 'preview'); ?>" class="rounded shadow-sm border" style="width: 48px; height: 48px; object-fit: cover;">
                                        <?php else : ?>
                                            <div class="rounded bg-light text-muted d-flex align-items-center justify-content-center border" style="width: 48px; height: 48px; font-size: 0.75rem; font-weight: 800;">PDF</div>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <span class="fw-semibold text-dark d-block text-truncate" style="max-width: 250px;" title="<?php echo e($item['original_name']); ?>"><?php echo e($item['original_name']); ?></span>
                                        <?php if (!empty($item['alt_text'])) : ?>
                                            <span class="text-muted small d-block text-truncate" style="max-width: 250px;">Alt: <?php echo e($item['alt_text']); ?></span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <code class="small text-muted"><?php echo e($item['path']); ?></code>
                                    </td>
                                    <td>
                                        <span class="badge bg-light text-secondary border"><?php echo e($item['mime_type']); ?></span>
                                    </td>
                                    <td class="text-muted small">
                                        <?php 
                                        $bytes = isset($item['file_size']) ? (int) $item['file_size'] : 0;
                                        if ($bytes >= 1048576) {
                                            echo number_format($bytes / 1048576, 2) . ' MB';
                                        } elseif ($bytes >= 1024) {
                                            echo number_format($bytes / 1024, 1) . ' KB';
                                        } else {
                                            echo $bytes . ' B';
                                        }
                                        ?>
                                    </td>
                                    <td class="text-end">
                                        <form method="post" action="<?php echo e(url('/admin/content/media/' . $item['id'] . '/delete')); ?>" class="d-inline" onsubmit="return confirm('Are you sure you want to delete this file permanently?')">
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
                    <nav aria-label="Media table pagination">
                        <ul class="pagination pagination-sm mb-0">
                            <?php for ($i = 1; $i <= $pagination['pages']; $i++) : ?>
                                <li class="page-item <?php echo $i === $pagination['page'] ? 'active' : ''; ?>">
                                    <a class="page-link" href="<?php echo e(url('/admin/content/media-manager?page=' . $i . '&search=' . urlencode(isset($_GET['search']) ? $_GET['search'] : ''))); ?>"><?php echo e($i); ?></a>
                                </li>
                            <?php endfor; ?>
                        </ul>
                    </nav>
                </div>
            </section>
        </div>
    </div>
</section>
