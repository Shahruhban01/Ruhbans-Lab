<?php

declare(strict_types=1);

namespace App\Repositories;

use PDO;

final class MediaRepository extends BaseRepository
{
    protected string $table = 'media';

    public function paginateMedia(string $search = '', int $page = 1, int $perPage = 20): array
    {
        $page = max(1, $page);
        $perPage = max(1, min(100, $perPage));
        $offset = ($page - 1) * $perPage;
        $where = 'WHERE deleted_at IS NULL';
        $bindings = array();

        $search = trim($search);

        if ($search !== '') {
            $search = function_exists('mb_substr') ? mb_substr($search, 0, 120) : substr($search, 0, 120);
            $where .= ' AND (filename LIKE :search OR original_name LIKE :search OR mime_type LIKE :search OR alt_text LIKE :search)';
            $bindings['search'] = '%' . $search . '%';
        }

        $count = $this->connection->prepare('SELECT COUNT(*) FROM media ' . $where);
        $count->execute($bindings);
        $total = (int) $count->fetchColumn();

        $statement = $this->connection->prepare('SELECT * FROM media ' . $where . ' ORDER BY created_at DESC LIMIT :limit OFFSET :offset');
        foreach ($bindings as $key => $value) {
            $statement->bindValue(':' . $key, $value);
        }
        $statement->bindValue(':limit', $perPage, PDO::PARAM_INT);
        $statement->bindValue(':offset', $offset, PDO::PARAM_INT);
        $statement->execute();

        return array(
            'data' => $statement->fetchAll(PDO::FETCH_ASSOC),
            'pagination' => array(
                'page' => $page,
                'per_page' => $perPage,
                'total' => $total,
                'pages' => (int) ceil($total / $perPage),
            ),
        );
    }

    public function recent(int $limit = 12): array
    {
        $limit = max(1, min(50, $limit));
        $statement = $this->connection->prepare('SELECT * FROM media WHERE deleted_at IS NULL ORDER BY created_at DESC LIMIT :limit');
        $statement->bindValue(':limit', $limit, PDO::PARAM_INT);
        $statement->execute();

        return $statement->fetchAll(PDO::FETCH_ASSOC);
    }

    public function saveMedia(array $data): string
    {
        $data['uploader_id'] = isset($data['uploader_id']) && $data['uploader_id'] !== null ? (int) $data['uploader_id'] : null;
        $data['file_size'] = isset($data['file_size']) ? max(0, (int) $data['file_size']) : 0;
        $data['width'] = isset($data['width']) && $data['width'] !== null ? max(0, (int) $data['width']) : null;
        $data['height'] = isset($data['height']) && $data['height'] !== null ? max(0, (int) $data['height']) : null;
        $statement = $this->connection->prepare('INSERT INTO media (uploader_id, filename, original_name, path, mime_type, extension, file_size, width, height, alt_text, created_at) VALUES (:uploader_id, :filename, :original_name, :path, :mime_type, :extension, :file_size, :width, :height, :alt_text, :created_at)');
        $statement->execute(array(
            'uploader_id' => isset($data['uploader_id']) ? $data['uploader_id'] : null,
            'filename' => $data['filename'],
            'original_name' => $data['original_name'],
            'path' => $data['path'],
            'mime_type' => $data['mime_type'],
            'extension' => $data['extension'],
            'file_size' => $data['file_size'],
            'width' => $data['width'],
            'height' => $data['height'],
            'alt_text' => isset($data['alt_text']) ? $data['alt_text'] : '',
            'created_at' => date('Y-m-d H:i:s'),
        ));

        return (string) $this->connection->lastInsertId();
    }
}
