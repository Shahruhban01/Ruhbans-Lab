<section class="container page-stack">
    <section class="page-hero card-surface">
        <div>
            <p class="eyebrow">Contact</p>
            <h1>Let’s talk</h1>
            <p class="lead">Use the contact details below for feedback, collaborations, or technical questions about the platform.</p>
        </div>
    </section>

    <?php if (!empty($flashSuccess)) : ?>
        <div class="flash-message flash-message--success"><?php echo e($flashSuccess); ?></div>
    <?php endif; ?>
    <?php if (!empty($flashError)) : ?>
        <div class="flash-message flash-message--error"><?php echo e($flashError); ?></div>
    <?php endif; ?>

    <section class="contact-grid">
        <article class="card-surface panel">
            <h2>Email</h2>
            <p>For general inquiries, write to <a href="mailto:hello@developer-ruhban.example">hello@developer-ruhban.example</a>.</p>
        </article>
        <article class="card-surface panel">
            <h2>Social</h2>
            <p>Follow the public profile links from the author pages or project pages for updates.</p>
        </article>
        <article class="card-surface panel">
            <h2>Response time</h2>
            <p>Messages are typically reviewed during active development windows.</p>
        </article>
    </section>

    <section class="card-surface panel">
        <div class="section-head section-head--compact">
            <div>
                <p class="eyebrow">Contact form</p>
                <h2>Send a message</h2>
            </div>
        </div>
        <form class="contact-form" method="post" action="<?php echo e(url('/contact')); ?>">
            <?php echo csrf_field(); ?>
            <div class="contact-form__grid">
                <label>
                    <span>Name</span>
                    <input type="text" name="name" maxlength="120" required>
                </label>
                <label>
                    <span>Email</span>
                    <input type="email" name="email" maxlength="190" required>
                </label>
            </div>
            <label>
                <span>Subject</span>
                <input type="text" name="subject" maxlength="190" required>
            </label>
            <label>
                <span>Message</span>
                <textarea name="message" rows="6" maxlength="4000" required></textarea>
            </label>
            <button class="btn btn-primary" type="submit">Send message</button>
        </form>
    </section>
</section>
