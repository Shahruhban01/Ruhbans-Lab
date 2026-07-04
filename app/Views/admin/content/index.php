<?php
$postsData = isset($posts['data']) ? $posts['data'] : array();
$pagination = isset($posts['pagination']) ? $posts['pagination'] : array('page' => 1, 'pages' => 1, 'total' => 0);
?>
<section class="page-stack">
    <div class="page-header page-header--split">
        <div>
            <p class="eyebrow">Universal content</p>
            <h2>Posts</h2>
        </div>
        <div class="content-actions">
            <a class="btn btn-primary" href="<?php echo e(url('/admin/content/create')); ?>">Create content</a>
            <a class="btn btn-secondary" href="<?php echo e(url('/admin/content/drafts')); ?>">Drafts</a>
        </div>
    </div>

    <div class="stat-grid">
        <?php echo view('admin/partials/stat-card', array('label' => 'Total Posts', 'value' => isset($stats['total']) ? $stats['total'] : 0, 'note' => 'All content items'), array('layout' => false)); ?>
        <?php echo view('admin/partials/stat-card', array('label' => 'Drafts', 'value' => isset($stats['drafts']) ? $stats['drafts'] : 0, 'note' => 'Unpublished posts'), array('layout' => false)); ?>
        <?php echo view('admin/partials/stat-card', array('label' => 'Published', 'value' => isset($stats['published']) ? $stats['published'] : 0, 'note' => 'Live posts'), array('layout' => false)); ?>
        <?php echo view('admin/partials/stat-card', array('label' => 'Scheduled', 'value' => isset($stats['scheduled']) ? $stats['scheduled'] : 0, 'note' => 'Queued for later'), array('layout' => false)); ?>
    </div>

    <section class="panel card-surface">
        <form method="get" action="<?php echo e(url('/admin/content')); ?>" class="content-filter-grid">
            <label>
                <span>Status</span>
                <select name="status">
                    <option value="all"<?php echo isset($filters['status']) && $filters['status'] === 'all' ? ' selected' : ''; ?>>All</option>
                    <option value="draft"<?php echo isset($filters['status']) && $filters['status'] === 'draft' ? ' selected' : ''; ?>>Draft</option>
                    <option value="published"<?php echo isset($filters['status']) && $filters['status'] === 'published' ? ' selected' : ''; ?>>Published</option>
                    <option value="scheduled"<?php echo isset($filters['status']) && $filters['status'] === 'scheduled' ? ' selected' : ''; ?>>Scheduled</option>
                    <option value="archived"<?php echo isset($filters['status']) && $filters['status'] === 'archived' ? ' selected' : ''; ?>>Archived</option>
                </select>
            </label>
            <label>
                <span>Content type</span>
                <select name="content_type_id">
                    <option value="0">All types</option>
                    <?php foreach ($contentTypes as $contentType) : ?>
                        <option value="<?php echo e($contentType['id']); ?>"<?php echo isset($filters['content_type_id']) && (int) $filters['content_type_id'] === (int) $contentType['id'] ? ' selected' : ''; ?>><?php echo e($contentType['name']); ?></option>
                    <?php endforeach; ?>
                </select>
            </label>
            <label>
                <span>Search</span>
                <input type="search" name="search" value="<?php echo e(isset($filters['search']) ? $filters['search'] : ''); ?>" placeholder="Search posts">
            </label>
            <div class="content-filter-grid__submit">
                <button type="submit" class="btn btn-secondary">Filter</button>
            </div>
        </form>
    </section>

    <section class="panel card-surface">
        <div class="table-wrap">
            <table class="admin-table">
                <thead>
                    <tr>
                        <th>Title</th>
                        <th>Type</th>
                        <th>Status</th>
                        <th>Author</th>
                        <th>Published</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($postsData as $post) : ?>
                        <tr>
                            <td>
                                <strong><?php echo e($post['title']); ?></strong>
                                <span><?php echo e($post['slug']); ?></span>
                            </td>
                            <td><?php echo e(isset($post['content_type_name']) ? $post['content_type_name'] : '-'); ?></td>
                            <td><span class="status-pill status-pill--active"><?php echo e($post['status']); ?></span></td>
                            <td><?php echo e(isset($post['author_name']) ? $post['author_name'] : '-'); ?></td>
                            <td><?php echo e(isset($post['published_at']) && $post['published_at'] ? $post['published_at'] : '-'); ?></td>
                            <td>
                                <div class="inline-actions">
                                    <a class="btn btn-ghost" href="<?php echo e(url('/admin/content/' . $post['id'] . '/edit')); ?>">Edit</a>
                                    <a class="btn btn-ghost" href="<?php echo e(url('/admin/content/' . $post['id'] . '/revisions')); ?>">Revisions</a>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>

        <div class="pagination-summary">
            <span>Page <?php echo e($pagination['page']); ?> of <?php echo e($pagination['pages']); ?></span>
            <span>Total posts: <?php echo e($pagination['total']); ?></span>
        </div>
    </section>
</section>
