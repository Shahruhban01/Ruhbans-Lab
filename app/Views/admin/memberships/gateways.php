<div class="panel card-surface">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <p class="eyebrow">System Config</p>
            <h2 class="fw-bold m-0">Payment Gateway Settings</h2>
        </div>
    </div>

    <!-- TABS NAVIGATION -->
    <ul class="nav nav-tabs mb-4" id="gatewayTabs" role="tablist">
        <li class="nav-item" role="presentation">
            <button class="nav-link active" id="razorpay-tab" data-bs-toggle="tab" data-bs-target="#razorpay-panel" type="button" role="tab" aria-controls="razorpay-panel" aria-selected="true">Razorpay</button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link" id="stripe-tab" data-bs-toggle="tab" data-bs-target="#stripe-panel" type="button" role="tab" aria-controls="stripe-panel" aria-selected="false">Stripe</button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link" id="paypal-tab" data-bs-toggle="tab" data-bs-target="#paypal-panel" type="button" role="tab" aria-controls="paypal-panel" aria-selected="false">PayPal</button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link" id="logs-tab" data-bs-toggle="tab" data-bs-target="#logs-panel" type="button" role="tab" aria-controls="logs-panel" aria-selected="false">Transaction Logs</button>
        </li>
    </ul>

    <!-- TABS CONTENT -->
    <div class="tab-content" id="gatewayTabsContent">
        <!-- RAZORPAY -->
        <div class="tab-pane fade show active" id="razorpay-panel" role="tabpanel" aria-labelledby="razorpay-tab">
            <?php 
            $rzp = $settings['razorpay'] ?? array('is_active' => 0, 'config' => array());
            $rzpCfg = $rzp['config'] ?? array();
            ?>
            <form method="post" action="<?php echo e(url('/admin/memberships/gateways/razorpay/save')); ?>">
                <?php echo csrf_field(); ?>
                <div class="card p-4 border mb-4 bg-light">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h4 class="fw-bold m-0">Razorpay Integration</h4>
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" name="is_active" value="1" id="rzpActive" <?php echo $rzp['is_active'] ? 'checked' : ''; ?>>
                            <label class="form-check-label small fw-semibold" for="rzpActive">Enable Gateway</label>
                        </div>
                    </div>

                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label small fw-semibold">Merchant Name</label>
                            <input type="text" name="config[merchant_name]" class="form-control" value="<?php echo e($rzpCfg['merchant_name'] ?? 'Developer Ruhban'); ?>">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-semibold">Merchant Logo URL</label>
                            <input type="text" name="config[merchant_logo]" class="form-control" value="<?php echo e($rzpCfg['merchant_logo'] ?? ''); ?>">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label small fw-semibold">Currency</label>
                            <input type="text" name="config[currency]" class="form-control" value="<?php echo e($rzpCfg['currency'] ?? 'INR'); ?>">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label small fw-semibold">Country</label>
                            <input type="text" name="config[country]" class="form-control" value="<?php echo e($rzpCfg['country'] ?? 'IN'); ?>">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label small fw-semibold">Theme Color</label>
                            <input type="color" name="config[theme_color]" class="form-control form-control-color w-100" value="<?php echo e($rzpCfg['theme_color'] ?? '#6366f1'); ?>">
                        </div>
                        
                        <div class="col-md-6">
                            <label class="form-label small fw-semibold">Key ID</label>
                            <input type="text" name="config[key_id]" class="form-control" value="<?php echo e($rzpCfg['key_id'] ?? ''); ?>">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-semibold">Key Secret (Masked)</label>
                            <input type="password" name="config[key_secret]" class="form-control" value="<?php echo e($rzpCfg['key_secret'] ?? ''); ?>" placeholder="••••••••••••••••">
                        </div>
                        <div class="col-md-12">
                            <label class="form-label small fw-semibold">Webhook Secret (Masked)</label>
                            <input type="password" name="config[webhook_secret]" class="form-control" value="<?php echo e($rzpCfg['webhook_secret'] ?? ''); ?>" placeholder="••••••••••••••••">
                        </div>

                        <div class="col-md-12">
                            <label class="form-label small fw-semibold">Environment Mode</label>
                            <select name="config[mode]" class="form-select">
                                <option value="test" <?php echo ($rzpCfg['mode'] ?? 'test') === 'test' ? 'selected' : ''; ?>>Test Mode</option>
                                <option value="live" <?php echo ($rzpCfg['mode'] ?? '') === 'live' ? 'selected' : ''; ?>>Live Mode</option>
                            </select>
                        </div>
                    </div>

                    <div class="d-flex justify-content-between mt-4">
                        <button type="submit" class="btn btn-primary">Save Settings</button>
                        <button type="button" class="btn btn-outline-secondary" onclick="document.getElementById('testRzpForm').submit();">Test Connection</button>
                    </div>
                </div>
            </form>
            <form id="testRzpForm" method="post" action="<?php echo e(url('/admin/memberships/gateways/razorpay/test')); ?>"><?php echo csrf_field(); ?></form>
        </div>

        <!-- STRIPE -->
        <div class="tab-pane fade" id="stripe-panel" role="tabpanel" aria-labelledby="stripe-tab">
            <?php 
            $str = $settings['stripe'] ?? array('is_active' => 0, 'config' => array());
            $strCfg = $str['config'] ?? array();
            ?>
            <form method="post" action="<?php echo e(url('/admin/memberships/gateways/stripe/save')); ?>">
                <?php echo csrf_field(); ?>
                <div class="card p-4 border mb-4 bg-light">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h4 class="fw-bold m-0">Stripe Integration</h4>
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" name="is_active" value="1" id="strActive" <?php echo $str['is_active'] ? 'checked' : ''; ?>>
                            <label class="form-check-label small fw-semibold" for="strActive">Enable Gateway</label>
                        </div>
                    </div>

                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label small fw-semibold">Key ID (Publishable Key)</label>
                            <input type="text" name="config[key_id]" class="form-control" value="<?php echo e($strCfg['key_id'] ?? ''); ?>">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-semibold">Key Secret (Secret Key)</label>
                            <input type="password" name="config[key_secret]" class="form-control" value="<?php echo e($strCfg['key_secret'] ?? ''); ?>" placeholder="••••••••••••••••">
                        </div>
                        <div class="col-md-12">
                            <label class="form-label small fw-semibold">Webhook Secret (Signing Secret)</label>
                            <input type="password" name="config[webhook_secret]" class="form-control" value="<?php echo e($strCfg['webhook_secret'] ?? ''); ?>" placeholder="••••••••••••••••">
                        </div>
                    </div>

                    <div class="d-flex justify-content-between mt-4">
                        <button type="submit" class="btn btn-primary">Save Settings</button>
                        <button type="button" class="btn btn-outline-secondary" onclick="document.getElementById('testStrForm').submit();">Test Connection</button>
                    </div>
                </div>
            </form>
            <form id="testStrForm" method="post" action="<?php echo e(url('/admin/memberships/gateways/stripe/test')); ?>"><?php echo csrf_field(); ?></form>
        </div>

        <!-- PAYPAL -->
        <div class="tab-pane fade" id="paypal-panel" role="tabpanel" aria-labelledby="paypal-tab">
            <?php 
            $pp = $settings['paypal'] ?? array('is_active' => 0, 'config' => array());
            $ppCfg = $pp['config'] ?? array();
            ?>
            <form method="post" action="<?php echo e(url('/admin/memberships/gateways/paypal/save')); ?>">
                <?php echo csrf_field(); ?>
                <div class="card p-4 border mb-4 bg-light">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h4 class="fw-bold m-0">PayPal Integration</h4>
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" name="is_active" value="1" id="ppActive" <?php echo $pp['is_active'] ? 'checked' : ''; ?>>
                            <label class="form-check-label small fw-semibold" for="ppActive">Enable Gateway</label>
                        </div>
                    </div>

                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label small fw-semibold">Client ID</label>
                            <input type="text" name="config[key_id]" class="form-control" value="<?php echo e($ppCfg['key_id'] ?? ''); ?>">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-semibold">Secret Key</label>
                            <input type="password" name="config[key_secret]" class="form-control" value="<?php echo e($ppCfg['key_secret'] ?? ''); ?>" placeholder="••••••••••••••••">
                        </div>
                    </div>

                    <div class="d-flex justify-content-between mt-4">
                        <button type="submit" class="btn btn-primary">Save Settings</button>
                        <button type="button" class="btn btn-outline-secondary" onclick="document.getElementById('testPpForm').submit();">Test Connection</button>
                    </div>
                </div>
            </form>
            <form id="testPpForm" method="post" action="<?php echo e(url('/admin/memberships/gateways/paypal/test')); ?>"><?php echo csrf_field(); ?></form>
        </div>

        <!-- LOGS PANEL -->
        <div class="tab-pane fade" id="logs-panel" role="tabpanel" aria-labelledby="logs-tab">
            <h4 class="fw-bold mb-3">Gateway Transaction Payloads</h4>
            <?php if (empty($logs)) : ?>
                <p class="text-muted">No API request logs recorded.</p>
            <?php else : ?>
                <div class="table-responsive">
                    <table class="table table-sm font-monospace">
                        <thead>
                            <tr>
                                <th>Timestamp</th>
                                <th>Gateway</th>
                                <th>Direction</th>
                                <th>Transaction Amount</th>
                                <th>Payload JSON</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($logs as $l) : ?>
                                <tr>
                                    <td class="small text-muted"><?php echo e(date('M d, H:i:s', strtotime($l['created_at']))); ?></td>
                                    <td><span class="badge bg-secondary"><?php echo e(ucfirst($l['gateway'] ?? 'system')); ?></span></td>
                                    <td>
                                        <span class="badge <?php echo $l['direction'] === 'inbound' ? 'bg-success' : 'bg-primary'; ?>">
                                            <?php echo e($l['direction']); ?>
                                        </span>
                                    </td>
                                    <td>$<?php echo e(number_format(($l['amount'] ?? 0)/100, 2)); ?></td>
                                    <td>
                                        <details>
                                            <summary class="small text-primary cursor-pointer">View JSON</summary>
                                            <pre class="bg-dark text-white p-2 rounded small mt-1"><code><?php echo e($l['payload_json']); ?></code></pre>
                                        </details>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>
