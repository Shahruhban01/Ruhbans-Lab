<section class="error-page container">
    <h1>Server Error</h1>
    <p>Something went wrong while processing the request.</p>
    <?php if (!empty($exception)) : ?>
        <pre><?php echo e($exception->getMessage()); ?></pre>
    <?php endif; ?>
</section>
