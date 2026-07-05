<div class="panel card-surface max-width-md mx-auto">
    <h3 class="fw-bold mb-4">Help & Support</h3>
    <p class="text-muted">Need assistance with your membership, billing, or downloads? Submit a support request or contact us directly.</p>
    <hr class="my-4">
    <form method="post" action="<?php echo e(url('/contact')); ?>">
        <?php echo csrf_field(); ?>
        <div class="mb-3">
            <label class="form-label">Subject</label>
            <input type="text" name="subject" class="form-control" placeholder="How can we help?" required>
        </div>
        <div class="mb-3">
            <label class="form-label">Message Details</label>
            <textarea name="message" class="form-control" rows="4" placeholder="Explain the problem or request..." required></textarea>
        </div>
        <button type="submit" class="btn btn-primary">Submit Ticket</button>
    </form>
</div>
