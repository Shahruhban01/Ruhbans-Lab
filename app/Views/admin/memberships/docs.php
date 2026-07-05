<div class="panel card-surface">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <p class="eyebrow">Developer Documentation</p>
            <h2 class="fw-bold m-0">Payment Gateway Setup Guides</h2>
        </div>
    </div>

    <!-- Razorpay Setup Guide -->
    <div class="card p-4 border mb-4">
        <h4 class="fw-bold text-primary mb-3">1. Razorpay Setup Guide</h4>
        <p>To configure your production Razorpay gateway credentials on Developer Ruhban:</p>
        <ol class="small">
            <li>Log into your <a href="https://dashboard.razorpay.com/" target="_blank" rel="noopener">Razorpay Dashboard</a>.</li>
            <li>Navigate to <strong>Account & Settings</strong> &gt; <strong>API Keys</strong>.</li>
            <li>Click <strong>Generate Key</strong> (or regenerate key for production).</li>
            <li>Copy the <strong>Key ID</strong> and <strong>Key Secret</strong>.</li>
            <li>Paste these into the <strong>Gateway Settings</strong> panel under the Razorpay tab. The keys will automatically be encrypted before database storage.</li>
        </ol>
    </div>

    <!-- Webhook Configuration Guide -->
    <div class="card p-4 border mb-4">
        <h4 class="fw-bold text-success mb-3">2. Webhook Configuration Guide</h4>
        <p>To ensure subscription upgrades work automatically via webhook capture alerts:</p>
        <ol class="small">
            <li>Go to your Razorpay Dashboard &gt; <strong>Webhooks</strong> page.</li>
            <li>Click <strong>Add New Webhook</strong>.</li>
            <li>Set the Webhook URL endpoint target:
                <code class="bg-light px-2 py-1 border rounded font-monospace">https://yourdomain.com/webhooks/razorpay</code>
            </li>
            <li>Select the following events:
                <ul class="mb-2 mt-1">
                    <li><code>payment.captured</code></li>
                    <li><code>order.paid</code></li>
                </ul>
            </li>
            <li>Set a secret verification token string in the **Secret** field.</li>
            <li>Save the secret and paste it inside the **Webhook Secret** input field on the **Gateway Settings** panel.</li>
        </ol>
    </div>

    <!-- Deployment Notes -->
    <div class="card p-4 border">
        <h4 class="fw-bold text-dark mb-3">3. Deployment Checklist</h4>
        <ul class="small mb-0">
            <li>Make sure the decryption key is set correctly in environment configs.</li>
            <li>Verify the site has SSL enabled (`https://`) to allow official Razorpay checkout client scripts to initialize.</li>
            <li>Review gateway transaction logs to inspect API capture behaviors.</li>
        </ul>
    </div>
</div>
