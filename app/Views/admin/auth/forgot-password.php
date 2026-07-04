<section class="auth-panel">
    <p class="eyebrow">Password recovery</p>
    <h1>Forgot password</h1>
    <p class="auth-panel__lead">Enter your email and we will send a secure reset link if the account exists.</p>

    <?php if (!empty($errors['general'])) : ?>
        <div class="form-alert form-alert--error"><?php echo e($errors['general']); ?></div>
    <?php endif; ?>

    <form method="post" action="<?php echo e(url('/admin/forgot-password')); ?>" class="auth-form" novalidate>
        <?php echo csrf_field(); ?>
        <label>
            <span>Email</span>
            <input type="email" name="email" value="<?php echo e(isset($old['email']) ? $old['email'] : ''); ?>" required autocomplete="email">
            <?php if (!empty($errors['email'])) : ?><small><?php echo e($errors['email']); ?></small><?php endif; ?>
        </label>

        <button type="submit" class="btn btn-primary">Send reset link</button>
    </form>

    <a class="auth-panel__link" href="<?php echo e(url('/admin/login')); ?>">Back to login</a>
</section>
