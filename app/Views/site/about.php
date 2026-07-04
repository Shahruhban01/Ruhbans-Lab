<?php $faq = isset($faq) && is_array($faq) ? $faq : array(); ?>
<section class="container page-stack">
    <section class="page-hero card-surface">
        <div>
            <p class="eyebrow">About</p>
            <h1>Built for long-term publishing</h1>
            <p class="lead">Developer Ruhban is designed as a scalable knowledge platform for technical content, reusable workflows, and SEO-friendly public publishing.</p>
        </div>
    </section>

    <section class="two-column-grid">
        <article class="card-surface panel prose-content">
            <h2>What the platform does</h2>
            <p>It combines a shared-hosting friendly PHP architecture with a universal content model so articles, tutorials, guides, and reference pages can all live in one system.</p>
            <h3>Why it exists</h3>
            <p>The goal is to keep content fast to publish, easy to maintain, and structurally strong enough to grow into a long-running product.</p>
        </article>

        <aside class="card-surface panel">
            <p class="eyebrow">Principles</p>
            <ul class="stack-list">
                <li>Content-first design</li>
                <li>SEO-ready markup</li>
                <li>Reusable component system</li>
                <li>Shared-hosting compatibility</li>
            </ul>
        </aside>
    </section>

    <?php if ($faq !== array()) : ?>
        <section class="card-surface panel">
            <div class="section-head section-head--compact">
                <div>
                    <p class="eyebrow">FAQ</p>
                    <h2>Common questions</h2>
                </div>
            </div>
            <div class="faq-list">
                <?php foreach ($faq as $item) : ?>
                    <details class="faq-item">
                        <summary><?php echo e($item['question']); ?></summary>
                        <p><?php echo e($item['answer']); ?></p>
                    </details>
                <?php endforeach; ?>
            </div>
        </section>
    <?php endif; ?>
</section>
