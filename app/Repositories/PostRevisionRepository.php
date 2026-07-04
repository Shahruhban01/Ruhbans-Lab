<?php

declare(strict_types=1);

namespace App\Repositories;

use PDO;

final class PostRevisionRepository extends BaseRepository
{
    protected string $table = 'post_revisions';

    public function forPost($postId): array
    {
        $postId = (int) $postId;
        $statement = $this->connection->prepare('SELECT * FROM post_revisions WHERE post_id = :post_id ORDER BY created_at DESC');
        $statement->execute(array('post_id' => $postId));

        return $statement->fetchAll(PDO::FETCH_ASSOC);
    }

    public function findById($id)
    {
        $id = (int) $id;
        $statement = $this->connection->prepare('SELECT * FROM post_revisions WHERE id = :id LIMIT 1');
        $statement->execute(array('id' => $id));

        return $statement->fetch(PDO::FETCH_ASSOC);
    }

    public function saveRevision($postId, $userId, array $snapshot, string $label = 'Updated'): string
    {
        $postId = (int) $postId;
        $userId = $userId !== null && $userId !== '' ? (int) $userId : null;
        $statement = $this->connection->prepare('INSERT INTO post_revisions (post_id, user_id, label, snapshot_json, created_at) VALUES (:post_id, :user_id, :label, :snapshot_json, :created_at)');
        $statement->execute(array(
            'post_id' => $postId,
            'user_id' => $userId,
            'label' => $label,
            'snapshot_json' => json_encode($snapshot),
            'created_at' => date('Y-m-d H:i:s'),
        ));

        return (string) $this->connection->lastInsertId();
    }
}
