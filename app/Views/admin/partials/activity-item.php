<article class="activity-item">
    <div>
        <strong><?php echo e(isset($item['action']) ? $item['action'] : 'Activity'); ?></strong>
        <p><?php echo e(isset($item['description']) ? $item['description'] : ''); ?></p>
    </div>
    <time datetime="<?php echo e(isset($item['created_at']) ? $item['created_at'] : ''); ?>"><?php echo e(isset($item['created_at']) ? $item['created_at'] : ''); ?></time>
</article>
