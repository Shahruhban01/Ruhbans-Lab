<section class="auth-panel">
    <p class="eyebrow">Password recovery</p>
    <h1>Reset password</h1>
    <p class="auth-panel__lead">Create a new password for your admin account.</p>

    <?php if (!empty($errors['general'])) : ?>
        <div class="form-alert form-alert--error"><?php echo e($errors['general']); ?></div>
    <?php endif; ?>

    <form method="post" action="<?php echo e(url('/admin/reset-password')); ?>" class="auth-form" novalidate>
        <?php echo csrf_field(); ?>
        <label>
            <span>Email</span>
            <input type="email" name="email" value="<?php echo e(isset($old['email']) ? $old['email'] : ''); ?>" required autocomplete="email">
            <?php if (!empty($errors['email'])) : ?><small><?php echo e($errors['email']); ?></small><?php endif; ?>
        </label>

        <label>
            <span>Token</span>
            <input type="text" name="token" value="<?php echo e(isset($old['token']) ? $old['token'] : ''); ?>" required>
            <?php if (!empty($errors['token'])) : ?><small><?php echo e($errors['token']); ?></small><?php endif; ?>
        </label>

        <label>
            <span>New password</span>
            <input type="password" name="password" required autocomplete="new-password">
            <?php if (!empty($errors['password'])) : ?><small><?php echo e($errors['password']); ?></small><?php endif; ?>
        </label>

        <label>
            <span>Confirm password</span>
            <input type="password" name="password_confirmation" required autocomplete="new-password">
            <?php if (!empty($errors['password_confirmation'])) : ?><small><?php echo e($errors['password_confirmation']); ?></small><?php endif; ?>
        </label>

        <button type="submit" class="btn btn-primary">Reset password</button>
    </form>
</section>
