<div class="panel card-surface max-width-md mx-auto">
    <h3 class="fw-bold mb-4">Edit Profile</h3>
    <form method="post" action="<?php echo e(url('/account/profile')); ?>" class="d-flex flex-column gap-3">
        <?php echo csrf_field(); ?>

        <div class="row g-3">
            <div class="col-md-6">
                <label class="form-label">Full Name <span class="text-danger">*</span></label>
                <input type="text" name="name" class="form-control" value="<?php echo e($currentUser['name'] ?? ''); ?>" required maxlength="120">
            </div>
            <div class="col-md-6">
                <label class="form-label">Username</label>
                <input type="text" class="form-control" value="@<?php echo e($currentUser['username'] ?? ''); ?>" disabled>
            </div>
        </div>

        <div>
            <label class="form-label">Email</label>
            <input type="email" class="form-control" value="<?php echo e($currentUser['email'] ?? ''); ?>" disabled>
        </div>

        <div>
            <label class="form-label">Bio</label>
            <textarea name="bio" class="form-control" rows="3" maxlength="500" placeholder="A little about yourself..."><?php echo e($currentUser['bio'] ?? ''); ?></textarea>
        </div>

        <div>
            <label class="form-label">Avatar URL</label>
            <input type="url" name="avatar" class="form-control" value="<?php echo e($currentUser['avatar'] ?? ''); ?>" placeholder="https://example.com/avatar.jpg">
        </div>

        <div>
            <label class="form-label">Website</label>
            <input type="url" name="website" class="form-control" value="<?php echo e($currentUser['website'] ?? ''); ?>" placeholder="https://yoursite.com">
        </div>

        <div class="row g-3">
            <div class="col-md-4">
                <label class="form-label">GitHub</label>
                <input type="text" name="github" class="form-control" value="<?php echo e($currentUser['github'] ?? ''); ?>" placeholder="username">
            </div>
            <div class="col-md-4">
                <label class="form-label">Twitter / X</label>
                <input type="text" name="twitter" class="form-control" value="<?php echo e($currentUser['twitter'] ?? ''); ?>" placeholder="@handle">
            </div>
            <div class="col-md-4">
                <label class="form-label">LinkedIn</label>
                <input type="text" name="linkedin" class="form-control" value="<?php echo e($currentUser['linkedin'] ?? ''); ?>" placeholder="slug">
            </div>
        </div>

        <div class="d-flex gap-2 mt-2">
            <button type="submit" class="btn btn-primary">Save Profile</button>
            <a href="<?php echo e(url('/account/dashboard')); ?>" class="btn btn-secondary">Cancel</a>
        </div>
    </form>
</div>
