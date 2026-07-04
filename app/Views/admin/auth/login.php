<section class="auth-panel">
    <p class="eyebrow">Admin access</p>
    <h1>Sign in</h1>
    <p class="auth-panel__lead">Use your administrator, editor, or author account to access the dashboard.</p>

    <?php if (!empty($errors['general'])) : ?>
        <div class="form-alert form-alert--error"><?php echo e($errors['general']); ?></div>
    <?php endif; ?>

    <form method="post" action="<?php echo e(url('/admin/login')); ?>" class="auth-form" novalidate>
        <?php echo csrf_field(); ?>
        <label>
            <span>Email</span>
            <input type="email" name="email" value="<?php echo e(isset($old['email']) ? $old['email'] : ''); ?>" required autocomplete="email">
            <?php if (!empty($errors['email'])) : ?><small><?php echo e($errors['email']); ?></small><?php endif; ?>
        </label>

        <label>
            <span>Password</span>
            <input type="password" name="password" required autocomplete="current-password">
            <?php if (!empty($errors['password'])) : ?><small><?php echo e($errors['password']); ?></small><?php endif; ?>
        </label>

        <button type="submit" class="btn btn-primary">Login</button>
    </form>

    <a class="auth-panel__link" href="<?php echo e(url('/admin/forgot-password')); ?>">Forgot your password?</a>
</section>
