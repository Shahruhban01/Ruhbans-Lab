<?php
$plans = isset($plans) && is_array($plans) ? $plans : array();
$activeMembership = isset($activeMembership) ? $activeMembership : null;
$currentUser = isset($currentUser) ? $currentUser : app()->session()->get(config('auth.session_key', 'auth_user'));
?>

<div class="pricing-page container my-5">
    <!-- Hero -->
    <div class="text-center mb-5">
        <span class="eyebrow text-primary">Membership Plans</span>
        <h1 class="display-4 fw-bold mt-2 mb-3">Choose Your Development Workspace</h1>
        <p class="lead text-muted max-width-md mx-auto">Get full access to production-grade source code, premium tutorials, interactive lab tools, and early-access code reviews.</p>
        
        <!-- Toggle (Simulated Billing Cycle) -->
        <div class="d-inline-flex align-items-center gap-3 mt-4 p-2 bg-light rounded-pill border">
            <span class="small fw-semibold text-primary">Monthly Billing</span>
            <div class="form-check form-switch mb-0">
                <input class="form-check-input" type="checkbox" id="billingCycleToggle" style="width: 2.5em; height: 1.25em; cursor: pointer;">
            </div>
            <span class="small fw-semibold text-muted">Yearly Billing <span class="badge bg-success text-white ms-1" style="font-size:0.7rem">Save 20%</span></span>
        </div>
    </div>

    <!-- Pricing Cards -->
    <div class="row g-4 mb-5 align-items-stretch">
        <?php foreach ($plans as $p) : 
            $planSlug = $p['slug'];
            $isPopular = $planSlug === 'pro';
            $isLifetime = $planSlug === 'lifetime';
            $price = number_format($p['price_cents']/100, 2);
            $billing = $p['billing_period'];
        ?>
            <div class="col-lg-4">
                <div class="card h-100 p-4 border <?php echo $isPopular ? 'border-primary border-2 shadow' : ''; ?> position-relative" style="border-radius: var(--radius-lg);">
                    <?php if ($isPopular) : ?>
                        <span class="position-absolute top-0 start-50 translate-middle badge rounded-pill bg-primary px-3 py-2 text-white">MOST POPULAR</span>
                    <?php endif; ?>
                    <div class="mb-4">
                        <h3 class="fw-bold mb-1"><?php echo e($p['name']); ?></h3>
                        <p class="text-muted small mb-0"><?php echo e($p['description'] ?? 'Plan Details'); ?></p>
                    </div>
                    <div class="mb-4">
                        <div class="d-flex align-items-baseline">
                            <span class="display-5 fw-bold">$<span class="plan-price-val" data-base-price="<?php echo e($p['price_cents']/100); ?>"><?php echo e($price); ?></span></span>
                            <span class="text-muted ms-2">/ <span class="plan-billing-cycle"><?php echo e($billing); ?></span></span>
                        </div>
                    </div>

                    <ul class="list-unstyled mb-5 d-flex flex-column gap-3">
                        <?php if ($planSlug === 'free') : ?>
                            <li>✓ Read standard public articles</li>
                            <li>✓ Browse basic project files</li>
                            <li>✓ Join discussion threads</li>
                            <li class="text-muted">✗ Download template packages</li>
                            <li class="text-muted">✗ Access premium AI prompts</li>
                            <li class="text-muted">✗ Developer Priority Support</li>
                        <?php elseif ($planSlug === 'pro') : ?>
                            <li>✓ <strong>All standard access</strong></li>
                            <li>✓ Read premium tutorial content</li>
                            <li>✓ Complete code downloads</li>
                            <li>✓ Access premium project files</li>
                            <li>✓ Complete lab live demo tests</li>
                            <li>✓ Email Priority Support</li>
                        <?php else : ?>
                            <li>✓ <strong>All Pro plan features</strong></li>
                            <li>✓ One-time lifetime billing</li>
                            <li>✓ No future billing cycle</li>
                            <li>✓ Unlimited template updates</li>
                            <li>✓ Early Access private beta labs</li>
                            <li>✓ 1-on-1 Discord Priority Support</li>
                        <?php endif; ?>
                    </ul>

                    <div class="mt-auto">
                        <?php if ($activeMembership && $activeMembership['plan_id'] === $p['id']) : ?>
                            <button class="btn btn-secondary w-100 py-2" disabled>Active Plan</button>
                        <?php elseif ($planSlug === 'free') : ?>
                            <a href="<?php echo e(url('/signup')); ?>" class="btn btn-outline-primary w-100 py-2">Sign Up Free</a>
                        <?php else : ?>
                            <?php if ($currentUser) : ?>
                                <button type="button" class="btn <?php echo $isPopular ? 'btn-primary' : 'btn-outline-primary'; ?> w-100 py-2" onclick="payWithRazorpay('<?php echo e($p['name']); ?>', '<?php echo e($p['id']); ?>', parseFloat(document.querySelector('.plan-price-val').textContent))">⚡ Pay Now</button>
                            <?php else : ?>
                                <a href="<?php echo e(url('/login')); ?>" class="btn <?php echo $isPopular ? 'btn-primary' : 'btn-outline-primary'; ?> w-100 py-2">Login to Upgrade</a>
                            <?php endif; ?>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>

    <!-- Why Upgrade -->
    <div class="bg-light p-5 rounded-4 mb-5 border">
        <div class="row align-items-center g-4">
            <div class="col-lg-6">
                <span class="eyebrow text-primary">Premium Benefits</span>
                <h2 class="fw-bold mt-2">Accelerate Your Workflow with Premium Resources</h2>
                <p class="text-muted mt-3">Skip configuring boilers, researching stack updates, and rewriting layouts. Get access to modern production templates ready to launch.</p>
            </div>
            <div class="col-lg-6">
                <div class="row g-3">
                    <div class="col-sm-6">
                        <div class="card p-3 border-0 h-100">
                            <strong>📦 Complete Source Code</strong>
                            <p class="text-muted small mb-0 mt-1">Get exact config structures, layouts, and scripts.</p>
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <div class="card p-3 border-0 h-100">
                            <strong>⚡ Fast Downloads</strong>
                            <p class="text-muted small mb-0 mt-1">Directly import project template packs.</p>
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <div class="card p-3 border-0 h-100">
                            <strong>💬 Priority Support</strong>
                            <p class="text-muted small mb-0 mt-1">Direct replies on comments and tickets.</p>
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <div class="card p-3 border-0 h-100">
                            <strong>🚀 Lifetime Updates</strong>
                            <p class="text-muted small mb-0 mt-1">No monthly subscription worries.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Feature Comparison Table -->
    <div class="mb-5">
        <h3 class="fw-bold mb-4 text-center">Detailed Feature Comparison</h3>
        <div class="table-responsive">
            <table class="table comparison-table align-middle text-center">
                <thead>
                    <tr>
                        <th class="text-start" style="width: 40%">Feature</th>
                        <th style="width: 20%">Free</th>
                        <th style="width: 20%">Pro</th>
                        <th style="width: 20%">Lifetime</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td class="text-start">Read Public Articles</td>
                        <td class="text-success">✓</td>
                        <td class="text-success">✓</td>
                        <td class="text-success">✓</td>
                    </tr>
                    <tr>
                        <td class="text-start">Premium Tutorials</td>
                        <td class="text-muted">✗</td>
                        <td class="text-success">✓</td>
                        <td class="text-success">✓</td>
                    </tr>
                    <tr>
                        <td class="text-start">Premium Source Code</td>
                        <td class="text-muted">✗</td>
                        <td class="text-success">✓</td>
                        <td class="text-success">✓</td>
                    </tr>
                    <tr>
                        <td class="text-start">Downloads Counter Access</td>
                        <td class="text-muted">✗</td>
                        <td class="text-success">✓</td>
                        <td class="text-success">✓</td>
                    </tr>
                    <tr>
                        <td class="text-start">Website Templates</td>
                        <td class="text-muted">✗</td>
                        <td class="text-success">✓</td>
                        <td class="text-success">✓</td>
                    </tr>
                    <tr>
                        <td class="text-start">AI Prompts Collections</td>
                        <td class="text-muted">✗</td>
                        <td class="text-success">✓</td>
                        <td class="text-success">✓</td>
                    </tr>
                    <tr>
                        <td class="text-start">Premium Apps & Tools</td>
                        <td class="text-muted">✗</td>
                        <td class="text-success">✓</td>
                        <td class="text-success">✓</td>
                    </tr>
                    <tr>
                        <td class="text-start">Create Custom Collections</td>
                        <td class="text-muted">✗</td>
                        <td class="text-success">✓</td>
                        <td class="text-success">✓</td>
                    </tr>
                    <tr>
                        <td class="text-start">Priority Support</td>
                        <td class="text-muted">✗</td>
                        <td class="text-success">✓ (Email)</td>
                        <td class="text-success">✓ (1-on-1 Discord)</td>
                    </tr>
                    <tr>
                        <td class="text-start">Early Access Private Labs</td>
                        <td class="text-muted">✗</td>
                        <td class="text-muted">✗</td>
                        <td class="text-success">✓</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>

    <!-- FAQ Accordion -->
    <div class="max-width-md mx-auto mb-5">
        <h3 class="fw-bold mb-4 text-center">Frequently Asked Questions</h3>
        <div class="accordion" id="faqAccordion">
            <div class="accordion-item card-surface mb-2 border rounded">
                <h2 class="accordion-header" id="faq1">
                    <button class="accordion-button collapsed fw-semibold bg-transparent" type="button" data-bs-toggle="collapse" data-bs-target="#collapse1">
                        Can I cancel my Pro subscription at any time?
                    </button>
                </h2>
                <div id="collapse1" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                    <div class="accordion-body text-muted">
                        Yes, you can cancel your subscription inside your Member settings tab at any time. You will continue to have Pro access until the end of your billing cycle.
                    </div>
                </div>
            </div>
            <div class="accordion-item card-surface mb-2 border rounded">
                <h2 class="accordion-header" id="faq2">
                    <button class="accordion-button collapsed fw-semibold bg-transparent" type="button" data-bs-toggle="collapse" data-bs-target="#collapse2">
                        How does Lifetime access work?
                    </button>
                </h2>
                <div id="collapse2" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                    <div class="accordion-body text-muted">
                        Lifetime access is a single one-time payment. You will have unlimited downloads, premium updates, and community access forever with no recurring bills.
                    </div>
                </div>
            </div>
            <div class="accordion-item card-surface mb-2 border rounded">
                <h2 class="accordion-header" id="faq3">
                    <button class="accordion-button collapsed fw-semibold bg-transparent" type="button" data-bs-toggle="collapse" data-bs-target="#collapse3">
                        Do I need payment gateways configured to start free trial?
                    </button>
                </h2>
                <div id="collapse3" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                    <div class="accordion-body text-muted">
                        No billing details are required for the standard Free tier. Just create a username and verify your email.
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    var toggle = document.getElementById('billingCycleToggle');
    if (toggle) {
        toggle.addEventListener('change', function () {
            var cycleTexts = document.querySelectorAll('.plan-billing-cycle');
            var priceVals = document.querySelectorAll('.plan-price-val');
            var isYearly = this.checked;

            priceVals.forEach(function (el) {
                var basePrice = parseFloat(el.getAttribute('data-base-price'));
                if (basePrice > 0) {
                    var newPrice = isYearly ? (basePrice * 12 * 0.8) : basePrice;
                    el.textContent = newPrice.toFixed(2);
                }
            });

            cycleTexts.forEach(function (el) {
                if (el.textContent !== 'one-time') {
                    el.textContent = isYearly ? 'yearly' : 'monthly';
                }
            });
        });
    }
});
</script>

<script src="https://checkout.razorpay.com/v1/checkout.js"></script>
<script>
function payWithRazorpay(planName, planId, amount) {
    // Call initialization route to fetch official transaction order metadata
    fetch("<?php echo e(url('/razorpay/initialize')); ?>", {
        method: "POST",
        headers: {
            "Content-Type": "application/x-www-form-urlencoded",
            "X-Requested-With": "XMLHttpRequest"
        },
        body: "plan_id=" + planId + "&_token=" + encodeURIComponent("<?php echo csrf_token(); ?>")
    })
    .then(response => response.json())
    .then(data => {
        if (data.error) {
            alert(data.error);
            return;
        }

        var options = {
            "key": data.key,
            "amount": data.amount,
            "currency": data.currency,
            "name": data.name,
            "description": "Plan Upgrade: " + planName,
            "order_id": data.order_id,
            "handler": function (response){
                // Post signature details to verify endpoint
                fetch("<?php echo e(url('/razorpay/verify')); ?>", {
                    method: "POST",
                    headers: {
                        "Content-Type": "application/json",
                        "X-CSRF-TOKEN": "<?php echo csrf_token(); ?>"
                    },
                    body: JSON.stringify({
                        razorpay_payment_id: response.razorpay_payment_id,
                        razorpay_order_id: response.razorpay_order_id,
                        razorpay_signature: response.razorpay_signature,
                        transaction_id: data.notes.transaction_id,
                        order_id: data.notes.order_id,
                        plan_id: planId
                    })
                })
                .then(res => res.json())
                .then(verifyData => {
                    if (verifyData.success) {
                        alert("Upgrade successful! Activating membership workspace...");
                        window.location.href = "<?php echo e(url('/account/membership')); ?>";
                    } else {
                        alert("Verification failed: " + verifyData.error);
                    }
                });
            },
            "prefill": data.prefill,
            "theme": {
                "color": "#6366f1"
            }
        };

        var rzp = new Razorpay(options);
        rzp.open();
    })
    .catch(err => {
        console.error(err);
        alert("An error occurred during payment setup.");
    });
}
</script>

