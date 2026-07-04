<section class="error-page container">
    <h1>Forbidden</h1>
    <p>You do not have permission to access this area.</p>
    <?php if (!empty($exception)) : ?>
        <pre><?php echo e($exception->getMessage()); ?></pre>
    <?php endif; ?>
</section>
