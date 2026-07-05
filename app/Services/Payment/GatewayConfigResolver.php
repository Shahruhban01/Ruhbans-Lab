<?php

declare(strict_types=1);

namespace App\Services\Payment;

use App\Core\Application;
use App\Services\Security\EncryptionService;
use PDO;

final class GatewayConfigResolver
{
    private Application $app;
    private EncryptionService $encryptionService;

    public function __construct(Application $app)
    {
        $this->app = $app;
        $this->encryptionService = new EncryptionService();
    }

    public function resolve(string $gateway): array
    {
        $gateway = strtolower(trim($gateway));
        $db = $this->app->database()->connection();

        $stmt = $db->prepare('SELECT * FROM payment_settings WHERE gateway_key = :gateway LIMIT 1');
        $stmt->execute(array('gateway' => $gateway));
        $record = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$record) {
            return array(
                'is_active' => 0,
                'mode' => 'test',
                'config' => array(),
            );
        }

        $config = json_decode((string) $record['config_json'], true) ?: array();
        
        // Decrypt sensitive keys
        if (!empty($config['key_secret'])) {
            $config['key_secret'] = $this->encryptionService->decrypt($config['key_secret']) ?? '';
        }
        if (!empty($config['webhook_secret'])) {
            $config['webhook_secret'] = $this->encryptionService->decrypt($config['webhook_secret']) ?? '';
        }

        return array(
            'is_active' => (int) $record['is_active'],
            'mode' => $config['mode'] ?? 'test',
            'config' => $config,
        );
    }
}
