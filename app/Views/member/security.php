<div class="panel card-surface max-width-md mx-auto">
    <h3 class="fw-bold mb-4">Security Settings</h3>
    <p class="text-muted">Manage your security credentials and active sessions.</p>
    <hr class="my-4">
    <form method="post" action="<?php echo e(url('/logout')); ?>" onsubmit="return confirm('Change password feature is currently linked to your initial registration details. Reach support if you lost credentials. Sign out?')">
        <button type="submit" class="btn btn-outline-danger">Sign Out from All Devices</button>
    </form>
</div>
