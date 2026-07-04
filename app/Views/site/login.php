<section class="container page-stack">
    <div class="row justify-content-center my-5">
        <div class="col-md-5">
            <div class="card-surface p-5">
                <p class="eyebrow text-primary">Member Access</p>
                <h1 class="h2 fw-bold mb-4">Sign in</h1>
                <p class="text-muted small mb-4">Sign in to your visitor or developer account to access Ruhban's Lab and interactive modules.</p>

                <?php if (!empty($errors['general'])) : ?>
                    <div class="alert alert-danger mb-4"><?php echo e($errors['general']); ?></div>
                <?php endif; ?>

                <form method="post" action="<?php echo e(url('/login')); ?>" class="d-flex flex-column gap-3" novalidate>
                    <?php echo csrf_field(); ?>
                    
                    <div class="form-group">
                        <label class="form-label text-muted small fw-semibold">Email</label>
                        <input type="email" name="email" class="form-control" value="<?php echo e(isset($old['email']) ? $old['email'] : ''); ?>" required autocomplete="email">
                        <?php if (!empty($errors['email'])) : ?><small class="text-danger"><?php echo e($errors['email']); ?></small><?php endif; ?>
                    </div>

                    <div class="form-group">
                        <label class="form-label text-muted small fw-semibold">Password</label>
                        <input type="password" name="password" class="form-control" required autocomplete="current-password">
                        <?php if (!empty($errors['password'])) : ?><small class="text-danger"><?php echo e($errors['password']); ?></small><?php endif; ?>
                    </div>

                    <button type="submit" class="btn btn-primary w-100 mt-2">Login</button>
                </form>

                <div class="text-center mt-3">
                    <span class="text-muted small">Don't have an account? <a href="<?php echo e(url('/signup')); ?>">Sign up</a></span>
                </div>
            </div>
        </div>
    </div>
</section>
