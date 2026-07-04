<?php

declare(strict_types=1);

namespace App\Repositories;

use PDO;

final class PostRepository extends BaseRepository
{
    protected string $table = 'posts';

    private const ALLOWED_STATUSES = array('draft', 'published', 'scheduled', 'archived');

    public function paginateAdmin(array $filters = array(), int $page = 1, int $perPage = 15): array
    {
        $page = max(1, $page);
        $perPage = max(1, min(100, $perPage));
        $offset = ($page - 1) * $perPage;
        $where = array('p.deleted_at IS NULL');
        $bindings = array();

        $status = $this->normalizeStatusFilter(isset($filters['status']) ? (string) $filters['status'] : 'all');
        if ($status !== null) {
            $where[] = 'p.status = :status';
            $bindings['status'] = $status;
        }

        $contentTypeId = isset($filters['content_type_id']) ? (int) $filters['content_type_id'] : 0;
        if ($contentTypeId > 0) {
            $where[] = 'p.content_type_id = :content_type_id';
            $bindings['content_type_id'] = $contentTypeId;
        }

        $search = $this->normalizeSearchTerm(isset($filters['search']) ? (string) $filters['search'] : '');
        if ($search !== '') {
            $where[] = '(p.title LIKE :search OR p.slug LIKE :search OR p.excerpt LIKE :search OR ct.name LIKE :search OR u.name LIKE :search)';
            $bindings['search'] = '%' . $search . '%';
        }

        $whereSql = implode(' AND ', $where);

        $countSql = 'SELECT COUNT(*) FROM posts p LEFT JOIN content_types ct ON ct.id = p.content_type_id LEFT JOIN users u ON u.id = p.author_id WHERE ' . $whereSql;
        $countStatement = $this->connection->prepare($countSql);
        $countStatement->execute($bindings);
        $total = (int) $countStatement->fetchColumn();

        $sql = 'SELECT p.*, ct.name AS content_type_name, ct.slug AS content_type_slug, u.name AS author_name FROM posts p LEFT JOIN content_types ct ON ct.id = p.content_type_id LEFT JOIN users u ON u.id = p.author_id WHERE ' . $whereSql . ' ORDER BY p.created_at DESC LIMIT :limit OFFSET :offset';
        $statement = $this->connection->prepare($sql);

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

    public function countByStatus(string $status): int
    {
        $status = $this->normalizeStatusFilter($status);

        if ($status === null) {
            return 0;
        }

        $statement = $this->connection->prepare('SELECT COUNT(*) FROM posts WHERE status = :status AND deleted_at IS NULL');
        $statement->execute(array('status' => $status));

        return (int) $statement->fetchColumn();
    }

    public function counts(): array
    {
        return array(
            'total' => (int) $this->connection->query('SELECT COUNT(*) FROM posts WHERE deleted_at IS NULL')->fetchColumn(),
            'drafts' => $this->countByStatus('draft'),
            'published' => $this->countByStatus('published'),
            'scheduled' => $this->countByStatus('scheduled'),
            'archived' => $this->countByStatus('archived'),
        );
    }

    public function findWithRelations($id)
    {
        $id = (int) $id;
        $statement = $this->connection->prepare('SELECT p.*, ct.name AS content_type_name, ct.slug AS content_type_slug, u.name AS author_name, u.email AS author_email FROM posts p LEFT JOIN content_types ct ON ct.id = p.content_type_id LEFT JOIN users u ON u.id = p.author_id WHERE p.id = :id AND p.deleted_at IS NULL LIMIT 1');
        $statement->execute(array('id' => $id));

        $post = $statement->fetch(PDO::FETCH_ASSOC);

        if (!$post) {
            return false;
        }

        return $post;
    }

    public function createPost(array $data): string
    {
        $data['content_type_id'] = isset($data['content_type_id']) ? (int) $data['content_type_id'] : 0;
        $data['author_id'] = isset($data['author_id']) ? (int) $data['author_id'] : 0;
        $data['reading_time'] = isset($data['reading_time']) ? max(1, (int) $data['reading_time']) : 1;
        $data['featured_flag'] = !empty($data['featured_flag']) ? 1 : 0;
        $statement = $this->connection->prepare('INSERT INTO posts (content_type_id, author_id, title, slug, excerpt, content, featured_image, status, visibility, reading_time, published_at, featured_flag, created_at, updated_at) VALUES (:content_type_id, :author_id, :title, :slug, :excerpt, :content, :featured_image, :status, :visibility, :reading_time, :published_at, :featured_flag, :created_at, :updated_at)');
        $statement->execute($data);

        return (string) $this->connection->lastInsertId();
    }

    public function updatePost($id, array $data): bool
    {
        $id = (int) $id;
        $data['content_type_id'] = isset($data['content_type_id']) ? (int) $data['content_type_id'] : 0;
        $data['author_id'] = isset($data['author_id']) ? (int) $data['author_id'] : 0;
        $data['reading_time'] = isset($data['reading_time']) ? max(1, (int) $data['reading_time']) : 1;
        $data['featured_flag'] = !empty($data['featured_flag']) ? 1 : 0;
        $data['id'] = $id;
        $statement = $this->connection->prepare('UPDATE posts SET content_type_id = :content_type_id, author_id = :author_id, title = :title, slug = :slug, excerpt = :excerpt, content = :content, featured_image = :featured_image, status = :status, visibility = :visibility, reading_time = :reading_time, published_at = :published_at, featured_flag = :featured_flag, updated_at = :updated_at WHERE id = :id AND deleted_at IS NULL');

        return $statement->execute($data);
    }

    public function publish($id): bool
    {
        $id = (int) $id;
        $statement = $this->connection->prepare('UPDATE posts SET status = :status, published_at = :published_at, updated_at = :updated_at WHERE id = :id AND deleted_at IS NULL');

        return $statement->execute(array(
            'status' => 'published',
            'published_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s'),
            'id' => $id,
        ));
    }

    public function schedule($id, string $publishedAt): bool
    {
        $id = (int) $id;
        $statement = $this->connection->prepare('UPDATE posts SET status = :status, published_at = :published_at, updated_at = :updated_at WHERE id = :id AND deleted_at IS NULL');

        return $statement->execute(array(
            'status' => 'scheduled',
            'published_at' => $publishedAt,
            'updated_at' => date('Y-m-d H:i:s'),
            'id' => $id,
        ));
    }

    public function allDrafts(int $page = 1, int $perPage = 15): array
    {
        return $this->paginateAdmin(array('status' => 'draft'), $page, $perPage);
    }

    public function syncCategories($postId, array $categoryIds): void
    {
        $postId = (int) $postId;
        $delete = $this->connection->prepare('DELETE FROM post_categories WHERE post_id = :post_id');
        $delete->execute(array('post_id' => $postId));

        if ($categoryIds === array()) {
            return;
        }

        $insert = $this->connection->prepare('INSERT INTO post_categories (post_id, category_id, created_at) VALUES (:post_id, :category_id, :created_at)');

        foreach ($categoryIds as $categoryId) {
            $categoryId = (int) $categoryId;
            if ($categoryId <= 0) {
                continue;
            }

            $insert->execute(array(
                'post_id' => $postId,
                'category_id' => $categoryId,
                'created_at' => date('Y-m-d H:i:s'),
            ));
        }
    }

    public function syncTags($postId, array $tagIds): void
    {
        $postId = (int) $postId;
        $delete = $this->connection->prepare('DELETE FROM post_tags WHERE post_id = :post_id');
        $delete->execute(array('post_id' => $postId));

        if ($tagIds === array()) {
            return;
        }

        $insert = $this->connection->prepare('INSERT INTO post_tags (post_id, tag_id, created_at) VALUES (:post_id, :tag_id, :created_at)');

        foreach ($tagIds as $tagId) {
            $tagId = (int) $tagId;
            if ($tagId <= 0) {
                continue;
            }

            $insert->execute(array(
                'post_id' => $postId,
                'tag_id' => $tagId,
                'created_at' => date('Y-m-d H:i:s'),
            ));
        }
    }

    public function categoriesForPost($postId): array
    {
        $postId = (int) $postId;
        $statement = $this->connection->prepare('SELECT category_id FROM post_categories WHERE post_id = :post_id');
        $statement->execute(array('post_id' => $postId));

        return array_map('intval', $statement->fetchAll(PDO::FETCH_COLUMN));
    }

    public function tagsForPost($postId): array
    {
        $postId = (int) $postId;
        $statement = $this->connection->prepare('SELECT tag_id FROM post_tags WHERE post_id = :post_id');
        $statement->execute(array('post_id' => $postId));

        return array_map('intval', $statement->fetchAll(PDO::FETCH_COLUMN));
    }

    public function recent(int $limit = 5): array
    {
        $limit = max(1, min(50, $limit));
        $statement = $this->connection->prepare('SELECT p.*, ct.name AS content_type_name, ct.slug AS content_type_slug, COALESCE(cm.view_count, 0) AS view_count FROM posts p LEFT JOIN content_types ct ON ct.id = p.content_type_id LEFT JOIN content_metrics cm ON cm.post_id = p.id WHERE p.deleted_at IS NULL ORDER BY p.created_at DESC LIMIT :limit');
        $statement->bindValue(':limit', $limit, PDO::PARAM_INT);
        $statement->execute();

        return $statement->fetchAll(PDO::FETCH_ASSOC);
    }

    public function recentPublished(int $limit = 6): array
    {
        return $this->paginatePublic(array(), 1, $limit)['data'];
    }

    public function featuredPublished(int $limit = 3): array
    {
        return $this->paginatePublic(array('featured' => 1), 1, $limit)['data'];
    }

    public function findPublishedBySlug(string $slug)
    {
        $statement = $this->connection->prepare('SELECT p.*, ct.name AS content_type_name, ct.slug AS content_type_slug, u.name AS author_name, u.username AS author_username, u.avatar AS author_avatar, u.bio AS author_bio, u.website AS author_website, u.github AS author_github, u.linkedin AS author_linkedin, u.twitter AS author_twitter FROM posts p LEFT JOIN content_types ct ON ct.id = p.content_type_id LEFT JOIN users u ON u.id = p.author_id WHERE p.slug = :slug AND p.status = "published" AND p.visibility = "public" AND p.deleted_at IS NULL LIMIT 1');
        $statement->execute(array('slug' => trim($slug)));

        return $statement->fetch(PDO::FETCH_ASSOC);
    }

    public function findPublishedById($id)
    {
        $statement = $this->connection->prepare('SELECT p.*, ct.name AS content_type_name, ct.slug AS content_type_slug, u.name AS author_name, u.username AS author_username FROM posts p LEFT JOIN content_types ct ON ct.id = p.content_type_id LEFT JOIN users u ON u.id = p.author_id WHERE p.id = :id AND p.status = "published" AND p.visibility = "public" AND p.deleted_at IS NULL LIMIT 1');
        $statement->execute(array('id' => (int) $id));

        return $statement->fetch(PDO::FETCH_ASSOC);
    }

    public function publicListing(array $filters = array(), int $page = 1, int $perPage = 12): array
    {
        $page = max(1, $page);
        $perPage = max(1, min(50, $perPage));
        $offset = ($page - 1) * $perPage;
        $bindings = array();
        $joins = array(
            'LEFT JOIN content_types ct ON ct.id = p.content_type_id',
            'LEFT JOIN users u ON u.id = p.author_id',
            'LEFT JOIN content_metrics cm ON cm.post_id = p.id',
        );
        $where = array('p.deleted_at IS NULL', 'p.status = "published"', 'p.visibility = "public"');
        $sort = isset($filters['sort']) ? trim((string) $filters['sort']) : 'relevance';

        $search = $this->normalizeSearchTerm(isset($filters['search']) ? (string) $filters['search'] : '');
        if ($search !== '') {
            $where[] = '(p.title LIKE :search OR p.slug LIKE :search OR p.excerpt LIKE :search OR p.content LIKE :search OR ct.name LIKE :search OR u.name LIKE :search)';
            $bindings['search'] = '%' . $search . '%';
        }

        $typeSlug = isset($filters['type_slug']) ? trim((string) $filters['type_slug']) : '';
        if ($typeSlug !== '') {
            $where[] = 'ct.slug = :type_slug';
            $bindings['type_slug'] = $typeSlug;
        }

        $categorySlug = isset($filters['category_slug']) ? trim((string) $filters['category_slug']) : '';
        if ($categorySlug !== '') {
            $joins[] = 'INNER JOIN post_categories pc_filter ON pc_filter.post_id = p.id INNER JOIN categories c_filter ON c_filter.id = pc_filter.category_id';
            $where[] = 'c_filter.slug = :category_slug';
            $bindings['category_slug'] = $categorySlug;
        }

        $tagSlug = isset($filters['tag_slug']) ? trim((string) $filters['tag_slug']) : '';
        if ($tagSlug !== '') {
            $joins[] = 'INNER JOIN post_tags pt_filter ON pt_filter.post_id = p.id INNER JOIN tags t_filter ON t_filter.id = pt_filter.tag_id';
            $where[] = 't_filter.slug = :tag_slug';
            $bindings['tag_slug'] = $tagSlug;
        }

        $authorUsername = isset($filters['author_username']) ? trim((string) $filters['author_username']) : '';
        if ($authorUsername !== '') {
            $where[] = 'u.username = :author_username';
            $bindings['author_username'] = $authorUsername;
        }

        if (!empty($filters['featured'])) {
            $where[] = 'p.featured_flag = 1';
        }

        if (!empty($filters['year'])) {
            $where[] = 'YEAR(COALESCE(p.published_at, p.created_at)) = :year';
            $bindings['year'] = (int) $filters['year'];
        }

        if (!empty($filters['month'])) {
            $where[] = 'MONTH(COALESCE(p.published_at, p.created_at)) = :month';
            $bindings['month'] = (int) $filters['month'];
        }

        $whereSql = ' WHERE ' . implode(' AND ', $where);
        $joinSql = ' ' . implode(' ', $joins);
        $orderSql = $this->buildPublicOrderBy($sort, $search !== '');

        $countSql = 'SELECT COUNT(DISTINCT p.id) FROM posts p' . $joinSql . $whereSql;
        $countStatement = $this->connection->prepare($countSql);
        $countStatement->execute($bindings);
        $total = (int) $countStatement->fetchColumn();

        $sql = 'SELECT DISTINCT p.*, ct.name AS content_type_name, ct.slug AS content_type_slug, u.name AS author_name, u.username AS author_username, COALESCE(cm.view_count, 0) AS view_count, COALESCE(cm.search_count, 0) AS search_count FROM posts p' . $joinSql . $whereSql . ' ORDER BY ' . $orderSql . ' LIMIT :limit OFFSET :offset';
        $statement = $this->connection->prepare($sql);

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

    public function paginatePublic(array $filters = array(), int $page = 1, int $perPage = 12): array
    {
        return $this->publicListing($filters, $page, $perPage);
    }

    public function archiveMonths(int $limit = 12): array
    {
        $limit = max(1, min(36, $limit));
        $statement = $this->connection->prepare('SELECT YEAR(COALESCE(published_at, created_at)) AS year, MONTH(COALESCE(published_at, created_at)) AS month, COUNT(*) AS total FROM posts WHERE status = "published" AND visibility = "public" AND deleted_at IS NULL GROUP BY YEAR(COALESCE(published_at, created_at)), MONTH(COALESCE(published_at, created_at)) ORDER BY year DESC, month DESC LIMIT :limit');
        $statement->bindValue(':limit', $limit, PDO::PARAM_INT);
        $statement->execute();

        return $statement->fetchAll(PDO::FETCH_ASSOC);
    }

    public function relatedPublished($postId, $contentTypeId, array $categoryIds = array(), int $limit = 3): array
    {
        $postId = (int) $postId;
        $contentTypeId = (int) $contentTypeId;
        $limit = max(1, min(12, $limit));
        $bindings = array('post_id' => $postId, 'content_type_id' => $contentTypeId);
        $joins = 'LEFT JOIN content_types ct ON ct.id = p.content_type_id LEFT JOIN users u ON u.id = p.author_id';
        $conditions = array('p.deleted_at IS NULL', 'p.status = "published"', 'p.visibility = "public"', 'p.id <> :post_id');

        if ($categoryIds !== array()) {
            $placeholders = array();

            foreach (array_values(array_unique(array_map('intval', $categoryIds))) as $index => $categoryId) {
                if ($categoryId <= 0) {
                    continue;
                }

                $placeholder = ':category_' . $index;
                $placeholders[] = $placeholder;
                $bindings[trim($placeholder, ':')] = $categoryId;
            }

            if ($placeholders !== array()) {
                $joins .= ' INNER JOIN post_categories pc_related ON pc_related.post_id = p.id';
                $conditions[] = '(p.content_type_id = :content_type_id OR pc_related.category_id IN (' . implode(', ', $placeholders) . '))';
            } else {
                $conditions[] = 'p.content_type_id = :content_type_id';
            }
        } else {
            $conditions[] = 'p.content_type_id = :content_type_id';
        }

        $sql = 'SELECT DISTINCT p.*, ct.name AS content_type_name, ct.slug AS content_type_slug, u.name AS author_name, u.username AS author_username FROM posts p ' . $joins . ' WHERE ' . implode(' AND ', $conditions) . ' ORDER BY COALESCE(p.published_at, p.created_at) DESC, p.id DESC LIMIT :limit';
        $statement = $this->connection->prepare($sql);

        foreach ($bindings as $key => $value) {
            $statement->bindValue(':' . $key, $value, is_int($value) ? PDO::PARAM_INT : PDO::PARAM_STR);
        }

        $statement->bindValue(':limit', $limit, PDO::PARAM_INT);
        $statement->execute();

        return $statement->fetchAll(PDO::FETCH_ASSOC);
    }

    public function trendingPublished(int $limit = 6): array
    {
        return $this->publicListing(array('sort' => 'popular'), 1, $limit)['data'];
    }

    public function popularPublished(int $limit = 6): array
    {
        return $this->publicListing(array('sort' => 'popular'), 1, $limit)['data'];
    }

    public function recentlyUpdatedPublished(int $limit = 6): array
    {
        return $this->publicListing(array('sort' => 'updated'), 1, $limit)['data'];
    }

    public function recordView($postId): void
    {
        $this->upsertMetrics((int) $postId, 1, 0, true);
    }

    public function recordSearchResults(array $posts): void
    {
        foreach ($posts as $post) {
            if (!isset($post['id'])) {
                continue;
            }

            $this->upsertMetrics((int) $post['id'], 0, 1, false);
        }
    }

    private function upsertMetrics(int $postId, int $viewIncrement, int $searchIncrement, bool $trackView): void
    {
        $statement = $this->connection->prepare('INSERT INTO content_metrics (post_id, view_count, search_count, last_viewed_at, last_searched_at, created_at, updated_at) VALUES (:post_id, :view_count, :search_count, :last_viewed_at, :last_searched_at, :created_at, :updated_at) ON DUPLICATE KEY UPDATE view_count = view_count + VALUES(view_count), search_count = search_count + VALUES(search_count), last_viewed_at = COALESCE(VALUES(last_viewed_at), last_viewed_at), last_searched_at = COALESCE(VALUES(last_searched_at), last_searched_at), updated_at = VALUES(updated_at)');
        $now = date('Y-m-d H:i:s');
        $statement->execute(array(
            'post_id' => $postId,
            'view_count' => max(0, $viewIncrement),
            'search_count' => max(0, $searchIncrement),
            'last_viewed_at' => $trackView ? $now : null,
            'last_searched_at' => !$trackView ? $now : null,
            'created_at' => $now,
            'updated_at' => $now,
        ));
    }

    private function buildPublicOrderBy(string $sort, bool $hasSearch): string
    {
        switch ($sort) {
            case 'newest':
                return 'COALESCE(p.published_at, p.created_at) DESC, p.id DESC';
            case 'oldest':
                return 'COALESCE(p.published_at, p.created_at) ASC, p.id ASC';
            case 'updated':
                return 'COALESCE(p.updated_at, p.created_at) DESC, p.id DESC';
            case 'popular':
                return 'COALESCE(cm.view_count, 0) DESC, COALESCE(cm.search_count, 0) DESC, COALESCE(cm.last_viewed_at, p.published_at, p.created_at) DESC';
            case 'featured':
                return 'p.featured_flag DESC, COALESCE(p.published_at, p.created_at) DESC, p.id DESC';
            case 'title':
                return 'p.title ASC, p.id ASC';
            case 'relevance':
            default:
                if ($hasSearch) {
                    return 'CASE WHEN p.title LIKE :search THEN 0 WHEN p.slug LIKE :search THEN 1 WHEN p.excerpt LIKE :search THEN 2 WHEN p.content LIKE :search THEN 3 WHEN ct.name LIKE :search THEN 4 WHEN u.name LIKE :search THEN 5 ELSE 6 END ASC, COALESCE(p.published_at, p.created_at) DESC, p.id DESC';
                }

                return 'COALESCE(p.published_at, p.created_at) DESC, p.id DESC';
        }
    }

    private function normalizeStatusFilter(string $status): ?string
    {
        $status = trim($status);

        if ($status === '' || $status === 'all') {
            return null;
        }

        return in_array($status, self::ALLOWED_STATUSES, true) ? $status : null;
    }

    private function normalizeSearchTerm(string $search): string
    {
        $search = trim($search);

        if ($search === '') {
            return '';
        }

        return function_exists('mb_substr') ? mb_substr($search, 0, 120) : substr($search, 0, 120);
    }
}
