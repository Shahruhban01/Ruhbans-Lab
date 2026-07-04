<?php

declare(strict_types=1);

namespace App\Repositories;

use PDO;

final class PasswordResetRepository extends BaseRepository
{
    protected string $table = 'password_resets';

    public function createToken($userId, string $email, string $tokenHash, string $expiresAt): bool
    {
        $this->deleteByEmail($email);

        $statement = $this->connection->prepare('INSERT INTO password_resets (user_id, email, token_hash, expires_at, created_at) VALUES (:user_id, :email, :token_hash, :expires_at, :created_at)');

        return $statement->execute(array(
            'user_id' => $userId,
            'email' => $email,
            'token_hash' => $tokenHash,
            'expires_at' => $expiresAt,
            'created_at' => date('Y-m-d H:i:s'),
        ));
    }

    public function findValidToken(string $email, string $token)
    {
        $statement = $this->connection->prepare('SELECT * FROM password_resets WHERE email = :email AND used_at IS NULL AND expires_at >= :now ORDER BY id DESC LIMIT 1');
        $statement->execute(array(
            'email' => $email,
            'now' => date('Y-m-d H:i:s'),
        ));

        $record = $statement->fetch(PDO::FETCH_ASSOC);

        if (!$record) {
            return false;
        }

        if (!hash_equals($record['token_hash'], hash('sha256', $token))) {
            return false;
        }

        return $record;
    }

    public function markUsed($id): bool
    {
        $statement = $this->connection->prepare('UPDATE password_resets SET used_at = :used_at WHERE id = :id');

        return $statement->execute(array(
            'used_at' => date('Y-m-d H:i:s'),
            'id' => $id,
        ));
    }

    public function deleteByEmail(string $email): bool
    {
        $statement = $this->connection->prepare('DELETE FROM password_resets WHERE email = :email');

        return $statement->execute(array('email' => $email));
    }
}
