<div class="panel card-surface">
    <h3 class="fw-bold mb-4">My Download History</h3>
    <?php if (empty($downloads)) : ?>
        <p class="text-muted">You have not downloaded any products or scripts yet.</p>
    <?php else : ?>
        <div class="table-responsive">
            <table class="table">
                <thead>
                    <tr>
                        <th>Product / File Name</th>
                        <th>Action Performed</th>
                        <th>Downloaded At</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($downloads as $d) : ?>
                        <tr>
                            <td>
                                <a href="<?php echo e(url('/lab/' . ($d['slug'] ?? ''))); ?>" class="fw-semibold">
                                    <?php echo e($d['title'] ?? 'Product download'); ?>
                                </a>
                            </td>
                            <td>Downloaded</td>
                            <td class="text-muted small"><?php echo e(date('M d, Y H:i', strtotime($d['created_at']))); ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>
</div>
