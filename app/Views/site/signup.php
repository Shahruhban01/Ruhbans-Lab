<section class="container page-stack">
    <div class="row justify-content-center my-5">
        <div class="col-md-5">
            <div class="card-surface p-5">
                <p class="eyebrow text-primary">Member Registration</p>
                <h1 class="h2 fw-bold mb-4">Create account</h1>
                <p class="text-muted small mb-4">Register as a visitor to bookmark, react, and comment on lab modules and articles.</p>

                <?php if (!empty($errors['general'])) : ?>
                    <div class="alert alert-danger mb-4"><?php echo e($errors['general']); ?></div>
                <?php endif; ?>

                <form method="post" action="<?php echo e(url('/signup')); ?>" class="d-flex flex-column gap-3" novalidate>
                    <?php echo csrf_field(); ?>

                    <div class="form-group">
                        <label class="form-label text-muted small fw-semibold">Name</label>
                        <input type="text" name="name" class="form-control" value="<?php echo e(isset($old['name']) ? $old['name'] : ''); ?>" required autocomplete="name">
                        <?php if (!empty($errors['name'])) : ?><small class="text-danger"><?php echo e($errors['name']); ?></small><?php endif; ?>
                    </div>

                    <div class="form-group">
                        <label class="form-label text-muted small fw-semibold">Username</label>
                        <input type="text" name="username" class="form-control" value="<?php echo e(isset($old['username']) ? $old['username'] : ''); ?>" required>
                        <?php if (!empty($errors['username'])) : ?><small class="text-danger"><?php echo e($errors['username']); ?></small><?php endif; ?>
                    </div>

                    <div class="form-group">
                        <label class="form-label text-muted small fw-semibold">Email address</label>
                        <input type="email" name="email" class="form-control" value="<?php echo e(isset($old['email']) ? $old['email'] : ''); ?>" required autocomplete="email">
                        <?php if (!empty($errors['email'])) : ?><small class="text-danger"><?php echo e($errors['email']); ?></small><?php endif; ?>
                    </div>

                    <div class="form-group">
                        <label class="form-label text-muted small fw-semibold">Password</label>
                        <input type="password" name="password" class="form-control" required autocomplete="new-password">
                        <?php if (!empty($errors['password'])) : ?><small class="text-danger"><?php echo e($errors['password']); ?></small><?php endif; ?>
                    </div>

                    <button type="submit" class="btn btn-primary w-100 mt-2">Sign up</button>
                </form>

                <div class="text-center mt-3">
                    <span class="text-muted small">Already have an account? <a href="<?php echo e(url('/login')); ?>">Sign in</a></span>
                </div>
            </div>
        </div>
    </div>
</section>
