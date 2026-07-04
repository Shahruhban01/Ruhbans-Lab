<?php

declare(strict_types=1);

namespace App\Repositories;

use PDO;

final class PostMetaRepository extends BaseRepository
{
    protected string $table = 'post_meta';

    public function getByPostId($postId): array
    {
        $postId = (int) $postId;
        $statement = $this->connection->prepare('SELECT meta_key, meta_value FROM post_meta WHERE post_id = :post_id ORDER BY id ASC');
        $statement->execute(array('post_id' => $postId));

        return $statement->fetchAll(PDO::FETCH_KEY_PAIR);
    }

    public function syncMeta($postId, array $meta): void
    {
        $postId = (int) $postId;
        $delete = $this->connection->prepare('DELETE FROM post_meta WHERE post_id = :post_id');
        $delete->execute(array('post_id' => $postId));

        if ($meta === array()) {
            return;
        }

        $insert = $this->connection->prepare('INSERT INTO post_meta (post_id, meta_key, meta_value, created_at) VALUES (:post_id, :meta_key, :meta_value, :created_at)');

        foreach ($meta as $key => $value) {
            $key = trim((string) $key);
            if ($key === '' || preg_match('/^[a-zA-Z0-9_\-:]+$/', $key) !== 1 || $value === '') {
                continue;
            }

            $insert->execute(array(
                'post_id' => $postId,
                'meta_key' => $key,
                'meta_value' => is_array($value) ? json_encode($value) : (string) $value,
                'created_at' => date('Y-m-d H:i:s'),
            ));
        }
    }
}
