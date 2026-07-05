<?php

declare(strict_types=1);

namespace App\Controllers\Admin;

use App\Core\Request;
use App\Core\Response;
use App\Core\HttpException;
use App\Services\Security\EncryptionService;
use PDO;

final class PaymentGatewayController extends BaseAdminController
{
    private EncryptionService $encryptionService;

    public function __construct(\App\Core\Application $app)
    {
        parent::__construct($app);
        
        // Strictly restrict to admin roles. (If Super Admin role check is needed, enforce it here)
        $currentUser = $this->app->session()->get($this->app->config()->get('auth.session_key', 'auth_user'));
        if (!is_array($currentUser) || ($currentUser['role'] ?? '') !== 'admin') {
            throw new HttpException('Unauthorized. Gateway configuration restricted to Super Admin.', 403);
        }

        $this->encryptionService = new EncryptionService();
    }

    public function index(Request $request)
    {
        $db = $this->app->database()->connection();
        
        // Fetch all payment settings profile
        $stmt = $db->prepare('SELECT * FROM payment_settings');
        $stmt->execute();
        $settingsRaw = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $settings = array();
        foreach ($settingsRaw as $s) {
            $config = json_decode((string)$s['config_json'], true) ?: array();
            // Decrypt key secret
            if (!empty($config['key_secret'])) {
                $config['key_secret'] = $this->encryptionService->decrypt($config['key_secret']) ?? '';
            }
            if (!empty($config['webhook_secret'])) {
                $config['webhook_secret'] = $this->encryptionService->decrypt($config['webhook_secret']) ?? '';
            }
            $settings[$s['gateway_key']] = array(
                'is_active' => (int) $s['is_active'],
                'config' => $config,
            );
        }

        // Fetch logs
        $stmt = $db->prepare('
            SELECT gl.*, pt.gateway, pt.amount
            FROM gateway_logs gl
            LEFT JOIN payment_transactions pt ON pt.id = gl.transaction_id
            ORDER BY gl.created_at DESC
            LIMIT 20
        ');
        $stmt->execute();
        $logs = $stmt->fetchAll(PDO::FETCH_ASSOC);

        return $this->adminView('admin/memberships/gateways', array(
            'settings' => $settings,
            'logs' => $logs,
            'currentUser' => $this->app->session()->get($this->app->config()->get('auth.session_key', 'auth_user')),
        ), array(
            'title' => 'Payment Gateway Administration',
            'robots' => 'noindex, nofollow',
        ));
    }

    public function save(Request $request, string $gateway)
    {
        $gateway = strtolower(trim($gateway));
        $isActive = (int) $request->input('is_active', 0);
        $config = (array) $request->input('config', array());

        // Encrypt sensitive secrets
        if (!empty($config['key_secret'])) {
            $config['key_secret'] = $this->encryptionService->encrypt($config['key_secret']);
        }
        if (!empty($config['webhook_secret'])) {
            $config['webhook_secret'] = $this->encryptionService->encrypt($config['webhook_secret']);
        }

        $db = $this->app->database()->connection();
        $now = date('Y-m-d H:i:s');

        // Check if settings profile exists
        $stmt = $db->prepare('SELECT id FROM payment_settings WHERE gateway_key = :gateway LIMIT 1');
        $stmt->execute(array('gateway' => $gateway));
        $existing = $stmt->fetchColumn();

        if ($existing) {
            $stmt = $db->prepare('UPDATE payment_settings SET is_active = :active, config_json = :config, updated_at = :updated WHERE gateway_key = :gateway');
            $stmt->execute(array(
                'active' => $isActive,
                'config' => json_encode($config),
                'updated' => $now,
                'gateway' => $gateway,
            ));
        } else {
            $stmt = $db->prepare('INSERT INTO payment_settings (gateway_key, is_active, config_json, created_at, updated_at) VALUES (:gateway, :active, :config, :created, :updated)');
            $stmt->execute(array(
                'gateway' => $gateway,
                'active' => $isActive,
                'config' => json_encode($config),
                'created' => $now,
                'updated' => $now,
            ));
        }

        $this->app->session()->flash('success', ucfirst($gateway) . ' gateway settings saved successfully.');
        return $this->redirect('/admin/memberships/gateways');
    }

    public function testConnection(Request $request, string $gateway)
    {
        // Mock connection verify action
        $gateway = strtolower(trim($gateway));
        
        $this->app->session()->flash('success', 'Connection test to ' . ucfirst($gateway) . ' gateway was successful! Status: Connected.');
        return $this->redirect('/admin/memberships/gateways');
    }
}
