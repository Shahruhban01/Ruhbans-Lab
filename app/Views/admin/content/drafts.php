<?php
$drafts = isset($posts['data']) ? $posts['data'] : array();
$pagination = isset($posts['pagination']) ? $posts['pagination'] : array('page' => 1, 'pages' => 1, 'total' => 0);
?>
<section class="page-stack">
    <div class="page-header page-header--split">
        <div>
            <p class="eyebrow">Publishing workflow</p>
            <h2>Drafts</h2>
        </div>
        <a class="btn btn-primary" href="<?php echo e(url('/admin/content/create')); ?>">New draft</a>
    </div>

    <section class="panel card-surface">
        <div class="table-wrap">
            <table class="admin-table">
                <thead>
                    <tr>
                        <th>Title</th>
                        <th>Type</th>
                        <th>Updated</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($drafts as $post) : ?>
                        <tr>
                            <td>
                                <strong><?php echo e($post['title']); ?></strong>
                                <span><?php echo e($post['slug']); ?></span>
                            </td>
                            <td><?php echo e(isset($post['content_type_name']) ? $post['content_type_name'] : '-'); ?></td>
                            <td><?php echo e(isset($post['updated_at']) ? $post['updated_at'] : '-'); ?></td>
                            <td>
                                <a class="btn btn-ghost" href="<?php echo e(url('/admin/content/' . $post['id'] . '/edit')); ?>">Edit</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <div class="pagination-summary">
            <span>Page <?php echo e($pagination['page']); ?> of <?php echo e($pagination['pages']); ?></span>
            <span>Total drafts: <?php echo e($pagination['total']); ?></span>
        </div>
    </section>
</section>
