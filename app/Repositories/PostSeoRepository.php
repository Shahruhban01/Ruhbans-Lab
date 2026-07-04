<?php

declare(strict_types=1);

namespace App\Repositories;

use PDO;

final class PostSeoRepository extends BaseRepository
{
    protected string $table = 'post_seo';

    public function findByPostId($postId)
    {
        $postId = (int) $postId;
        $statement = $this->connection->prepare('SELECT * FROM post_seo WHERE post_id = :post_id LIMIT 1');
        $statement->execute(array('post_id' => $postId));

        return $statement->fetch(PDO::FETCH_ASSOC);
    }

    public function saveSeo($postId, array $seo): void
    {
        $postId = (int) $postId;
        $payload = array(
            'post_id' => $postId,
            'meta_title' => isset($seo['meta_title']) ? trim((string) $seo['meta_title']) : '',
            'meta_description' => isset($seo['meta_description']) ? trim((string) $seo['meta_description']) : '',
            'canonical_url' => isset($seo['canonical_url']) ? trim((string) $seo['canonical_url']) : '',
            'robots' => isset($seo['robots']) ? trim((string) $seo['robots']) : 'index, follow',
            'schema_type' => isset($seo['schema_type']) ? trim((string) $seo['schema_type']) : 'Article',
            'og_title' => isset($seo['og_title']) ? trim((string) $seo['og_title']) : '',
            'og_description' => isset($seo['og_description']) ? trim((string) $seo['og_description']) : '',
            'og_image' => isset($seo['og_image']) ? trim((string) $seo['og_image']) : '',
            'twitter_card' => isset($seo['twitter_card']) ? trim((string) $seo['twitter_card']) : 'summary_large_image',
            'updated_at' => date('Y-m-d H:i:s'),
        );

        if ($payload['meta_title'] === '' && $payload['meta_description'] === '' && $payload['canonical_url'] === '' && $payload['og_title'] === '' && $payload['og_description'] === '' && $payload['og_image'] === '') {
            $delete = $this->connection->prepare('DELETE FROM post_seo WHERE post_id = :post_id');
            $delete->execute(array('post_id' => $postId));

            return;
        }

        $existing = $this->findByPostId($postId);

        if ($existing) {
            $statement = $this->connection->prepare('UPDATE post_seo SET meta_title = :meta_title, meta_description = :meta_description, canonical_url = :canonical_url, robots = :robots, schema_type = :schema_type, og_title = :og_title, og_description = :og_description, og_image = :og_image, twitter_card = :twitter_card, updated_at = :updated_at WHERE post_id = :post_id');
            $statement->execute($payload);
            return;
        }

        $statement = $this->connection->prepare('INSERT INTO post_seo (post_id, meta_title, meta_description, canonical_url, robots, schema_type, og_title, og_description, og_image, twitter_card, created_at, updated_at) VALUES (:post_id, :meta_title, :meta_description, :canonical_url, :robots, :schema_type, :og_title, :og_description, :og_image, :twitter_card, :created_at, :updated_at)');
        $payload['created_at'] = date('Y-m-d H:i:s');
        $statement->execute($payload);
    }
}
