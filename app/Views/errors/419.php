<section class="error-page container">
    <h1>Session Expired</h1>
    <p>Your form session expired or the CSRF token was invalid. Please try again.</p>
    <?php if (!empty($exception)) : ?>
        <pre><?php echo e($exception->getMessage()); ?></pre>
    <?php endif; ?>
</section>
