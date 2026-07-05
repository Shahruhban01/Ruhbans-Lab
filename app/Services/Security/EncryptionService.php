<?php

declare(strict_types=1);

namespace App\Services\Security;

final class EncryptionService
{
    private string $key;
    private string $cipher = 'aes-256-cbc';

    public function __construct()
    {
        $this->key = hash('sha256', (string) config('app.key', 'default_secret_encryption_key_hash_salt_phrase'));
    }

    public function encrypt(string $value): string
    {
        $ivLength = openssl_cipher_iv_length($this->cipher);
        $iv = openssl_random_pseudo_bytes($ivLength);
        
        $encrypted = openssl_encrypt($value, $this->cipher, $this->key, 0, $iv);
        
        return base64_encode($iv . $encrypted);
    }

    public function decrypt(string $payload): ?string
    {
        try {
            $data = base64_decode($payload);
            $ivLength = openssl_cipher_iv_length($this->cipher);
            
            $iv = substr($data, 0, $ivLength);
            $encrypted = substr($data, $ivLength);
            
            $decrypted = openssl_decrypt($encrypted, $this->cipher, $this->key, 0, $iv);
            
            return $decrypted ?: null;
        } catch (\Throwable $e) {
            return null;
        }
    }
}
