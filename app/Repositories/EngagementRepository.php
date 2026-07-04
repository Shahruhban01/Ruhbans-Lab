<?php

declare(strict_types=1);

namespace App\Repositories;

use PDO;

final class EngagementRepository extends BaseRepository
{
    private const REACTION_TYPES = array('like', 'bookmark', 'favorite');

    private array $tableCache = array();

    public function commentsForPost(int $postId): array
    {
        if (!$this->hasTable('comments')) {
            return array();
        }

        $statement = $this->connection->prepare('SELECT c.*, u.name AS user_name, u.username AS user_username, u.avatar AS user_avatar FROM comments c LEFT JOIN users u ON u.id = c.user_id WHERE c.post_id = :post_id AND c.deleted_at IS NULL AND c.status = "published" ORDER BY c.created_at ASC, c.id ASC');
        $statement->execute(array('post_id' => $postId));

        return $this->threadComments($statement->fetchAll(PDO::FETCH_ASSOC));
    }

    public function commentCount(int $postId): int
    {
        if (!$this->hasTable('comments')) {
            return 0;
        }

        $statement = $this->connection->prepare('SELECT COUNT(*) FROM comments WHERE post_id = :post_id AND deleted_at IS NULL AND status = "published"');
        $statement->execute(array('post_id' => $postId));

        return (int) $statement->fetchColumn();
    }

    public function interactionCounts(int $postId): array
    {
        $counts = array(
            'likes' => 0,
            'bookmarks' => 0,
            'favorites' => 0,
            'comments' => $this->commentCount($postId),
        );

        if (!$this->hasTable('content_interactions')) {
            return $counts;
        }

        $statement = $this->connection->prepare('SELECT interaction_type, COUNT(*) AS total FROM content_interactions WHERE post_id = :post_id GROUP BY interaction_type');
        $statement->execute(array('post_id' => $postId));

        foreach ($statement->fetchAll(PDO::FETCH_ASSOC) as $row) {
            $type = isset($row['interaction_type']) ? (string) $row['interaction_type'] : '';
            $total = isset($row['total']) ? (int) $row['total'] : 0;

            if ($type === 'like') {
                $counts['likes'] = $total;
            } elseif ($type === 'bookmark') {
                $counts['bookmarks'] = $total;
            } elseif ($type === 'favorite') {
                $counts['favorites'] = $total;
            }
        }

        return $counts;
    }

    public function interactionState(int $postId, array $identity): array
    {
        $state = array('like' => false, 'bookmark' => false, 'favorite' => false);

        if (!$this->hasTable('content_interactions')) {
            return $state;
        }

        $statement = $this->connection->prepare('SELECT interaction_type FROM content_interactions WHERE post_id = :post_id AND actor_key = :actor_key');
        $statement->execute(array('post_id' => $postId, 'actor_key' => $this->actorKey($identity)));

        foreach ($statement->fetchAll(PDO::FETCH_COLUMN) as $interactionType) {
            $interactionType = (string) $interactionType;
            if (isset($state[$interactionType])) {
                $state[$interactionType] = true;
            }
        }

        return $state;
    }

    public function toggleReaction(int $postId, string $interactionType, array $identity): array
    {
        if (!$this->hasTable('content_interactions') || !in_array($interactionType, self::REACTION_TYPES, true)) {
            return array('active' => false, 'count' => 0);
        }

        $actorKey = $this->actorKey($identity);
        $existing = $this->findReaction($postId, $interactionType, $actorKey);

        if ($existing) {
            $delete = $this->connection->prepare('DELETE FROM content_interactions WHERE id = :id');
            $delete->execute(array('id' => (int) $existing['id']));

            return array('active' => false, 'count' => $this->reactionCount($postId, $interactionType));
        }

        $statement = $this->connection->prepare('INSERT INTO content_interactions (post_id, user_id, guest_token, actor_key, interaction_type, created_at, updated_at) VALUES (:post_id, :user_id, :guest_token, :actor_key, :interaction_type, :created_at, :updated_at)');
        $now = date('Y-m-d H:i:s');
        $statement->execute(array(
            'post_id' => $postId,
            'user_id' => isset($identity['user_id']) && $identity['user_id'] !== null ? (int) $identity['user_id'] : null,
            'guest_token' => isset($identity['guest_token']) ? (string) $identity['guest_token'] : null,
            'actor_key' => $actorKey,
            'interaction_type' => $interactionType,
            'created_at' => $now,
            'updated_at' => $now,
        ));

        $this->logActivity($identity, $postId, $interactionType, ucfirst($interactionType) . 'd content.', array('interaction_type' => $interactionType));
        $this->notifyReactionTarget($postId, $identity, $interactionType);

        return array('active' => true, 'count' => $this->reactionCount($postId, $interactionType));
    }

    public function addComment(int $postId, array $identity, string $body, ?int $parentId = null, string $guestName = '', string $guestEmail = ''): array
    {
        if (!$this->hasTable('comments')) {
            return array('success' => false, 'message' => 'Comments are not available yet.');
        }

        $body = $this->normalizeBody($body);

        if ($body === '') {
            return array('success' => false, 'message' => 'Comment body is required.');
        }

        if (strlen($body) > 2000) {
            return array('success' => false, 'message' => 'Comment is too long.');
        }

        if ($this->isGuest($identity)) {
            $guestName = trim($guestName);
            $guestEmail = trim($guestEmail);

            if ($guestName === '' || $guestEmail === '' || !filter_var($guestEmail, FILTER_VALIDATE_EMAIL)) {
                return array('success' => false, 'message' => 'Guest name and email are required.');
            }
        }

        $parentComment = null;
        if ($parentId !== null && $parentId > 0) {
            $parentComment = $this->findComment($parentId, $postId);
            if (!$parentComment) {
                return array('success' => false, 'message' => 'Reply target not found.');
            }
        }

        $statement = $this->connection->prepare('INSERT INTO comments (post_id, parent_id, user_id, guest_name, guest_email, body, status, actor_key, created_at, updated_at) VALUES (:post_id, :parent_id, :user_id, :guest_name, :guest_email, :body, :status, :actor_key, :created_at, :updated_at)');
        $now = date('Y-m-d H:i:s');
        $statement->execute(array(
            'post_id' => $postId,
            'parent_id' => $parentId > 0 ? $parentId : null,
            'user_id' => isset($identity['user_id']) && $identity['user_id'] !== null ? (int) $identity['user_id'] : null,
            'guest_name' => $this->truncate($guestName, 120),
            'guest_email' => $this->truncate($guestEmail, 190),
            'body' => $body,
            'status' => 'published',
            'actor_key' => $this->actorKey($identity),
            'created_at' => $now,
            'updated_at' => $now,
        ));

        $commentId = (int) $this->connection->lastInsertId();
        $comment = $this->findComment($commentId, $postId);

        $this->logActivity($identity, $postId, $parentId > 0 ? 'reply' : 'comment', $parentId > 0 ? 'Replied to a discussion.' : 'Posted a comment.', array('comment_id' => $commentId, 'parent_id' => $parentId));
        $this->notifyCommentTargets($postId, $identity, $comment, $parentComment);

        return array('success' => true, 'comment' => $comment ?: array());
    }

    public function recordHistory(int $postId, array $identity): void
    {
        if (!$this->hasTable('reading_history')) {
            return;
        }

        $statement = $this->connection->prepare('INSERT INTO reading_history (post_id, user_id, guest_token, actor_key, view_count, last_viewed_at, created_at, updated_at) VALUES (:post_id, :user_id, :guest_token, :actor_key, 1, :last_viewed_at, :created_at, :updated_at) ON DUPLICATE KEY UPDATE view_count = view_count + 1, last_viewed_at = VALUES(last_viewed_at), updated_at = VALUES(updated_at)');
        $now = date('Y-m-d H:i:s');
        $statement->execute(array(
            'post_id' => $postId,
            'user_id' => isset($identity['user_id']) && $identity['user_id'] !== null ? (int) $identity['user_id'] : null,
            'guest_token' => isset($identity['guest_token']) ? (string) $identity['guest_token'] : null,
            'actor_key' => $this->actorKey($identity),
            'last_viewed_at' => $now,
            'created_at' => $now,
            'updated_at' => $now,
        ));
    }

    public function recentHistory(array $identity, int $limit = 5): array
    {
        if (!$this->hasTable('reading_history')) {
            return array();
        }

        $limit = max(1, min(12, $limit));
        $statement = $this->connection->prepare('SELECT rh.*, p.title, p.slug, p.excerpt, ct.name AS content_type_name FROM reading_history rh LEFT JOIN posts p ON p.id = rh.post_id LEFT JOIN content_types ct ON ct.id = p.content_type_id WHERE rh.actor_key = :actor_key ORDER BY rh.last_viewed_at DESC, rh.id DESC LIMIT :limit');
        $statement->bindValue(':actor_key', $this->actorKey($identity));
        $statement->bindValue(':limit', $limit, PDO::PARAM_INT);
        $statement->execute();

        return $statement->fetchAll(PDO::FETCH_ASSOC);
    }

    public function recentActivity(int $limit = 10): array
    {
        if (!$this->hasTable('activity_events')) {
            return array();
        }

        $limit = max(1, min(25, $limit));
        $statement = $this->connection->prepare('SELECT ae.*, p.title AS post_title, p.slug AS post_slug, u.name AS user_name FROM activity_events ae LEFT JOIN posts p ON p.id = ae.post_id LEFT JOIN users u ON u.id = ae.user_id ORDER BY ae.created_at DESC, ae.id DESC LIMIT :limit');
        $statement->bindValue(':limit', $limit, PDO::PARAM_INT);
        $statement->execute();

        return $statement->fetchAll(PDO::FETCH_ASSOC);
    }

    public function subscribeNewsletter(string $email, string $name = ''): array
    {
        if (!$this->hasTable('newsletter_subscribers')) {
            return array('success' => false, 'message' => 'Newsletter subscriptions are not available yet.');
        }

        $email = strtolower(trim($email));
        $name = trim($name);

        if ($email === '' || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return array('success' => false, 'message' => 'Enter a valid email address.');
        }

        $token = bin2hex(random_bytes(16));
        $statement = $this->connection->prepare('INSERT INTO newsletter_subscribers (email, name, status, verification_token, subscribed_at, created_at, updated_at) VALUES (:email, :name, :status, :verification_token, :subscribed_at, :created_at, :updated_at) ON DUPLICATE KEY UPDATE name = VALUES(name), status = VALUES(status), verification_token = VALUES(verification_token), subscribed_at = VALUES(subscribed_at), updated_at = VALUES(updated_at)');
        $now = date('Y-m-d H:i:s');
        $statement->execute(array(
            'email' => $email,
            'name' => $this->truncate($name, 120),
            'status' => 'subscribed',
            'verification_token' => $token,
            'subscribed_at' => $now,
            'created_at' => $now,
            'updated_at' => $now,
        ));

        return array('success' => true, 'message' => 'Subscription saved.');
    }

    public function storeContactMessage(array $data): array
    {
        if (!$this->hasTable('contact_messages')) {
            return array('success' => false, 'message' => 'Contact form is not available yet.');
        }

        $name = trim((string) ($data['name'] ?? ''));
        $email = strtolower(trim((string) ($data['email'] ?? '')));
        $subject = trim((string) ($data['subject'] ?? ''));
        $message = trim((string) ($data['message'] ?? ''));

        if ($name === '' || $email === '' || $subject === '' || $message === '') {
            return array('success' => false, 'message' => 'All contact fields are required.');
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return array('success' => false, 'message' => 'Enter a valid email address.');
        }

        $statement = $this->connection->prepare('INSERT INTO contact_messages (name, email, subject, message, status, ip_address, user_agent, created_at, updated_at) VALUES (:name, :email, :subject, :message, :status, :ip_address, :user_agent, :created_at, :updated_at)');
        $now = date('Y-m-d H:i:s');
        $statement->execute(array(
            'name' => $this->truncate($name, 120),
            'email' => $this->truncate($email, 190),
            'subject' => $this->truncate($subject, 190),
            'message' => $message,
            'status' => 'new',
            'ip_address' => request()->ip(),
            'user_agent' => $this->truncate((string) request()->header('User-Agent', ''), 255),
            'created_at' => $now,
            'updated_at' => $now,
        ));

        return array('success' => true, 'message' => 'Message sent.');
    }

    public function notificationsForUser(int $userId, int $limit = 10): array
    {
        if (!$this->hasTable('notifications')) {
            return array();
        }

        $limit = max(1, min(25, $limit));
        $statement = $this->connection->prepare('SELECT * FROM notifications WHERE user_id = :user_id ORDER BY is_read ASC, created_at DESC, id DESC LIMIT :limit');
        $statement->bindValue(':user_id', $userId, PDO::PARAM_INT);
        $statement->bindValue(':limit', $limit, PDO::PARAM_INT);
        $statement->execute();

        return $statement->fetchAll(PDO::FETCH_ASSOC);
    }

    public function unreadNotificationCount(int $userId): int
    {
        if (!$this->hasTable('notifications')) {
            return 0;
        }

        $statement = $this->connection->prepare('SELECT COUNT(*) FROM notifications WHERE user_id = :user_id AND is_read = 0');
        $statement->execute(array('user_id' => $userId));

        return (int) $statement->fetchColumn();
    }

    public function markNotificationRead(int $notificationId, int $userId): bool
    {
        if (!$this->hasTable('notifications')) {
            return false;
        }

        $statement = $this->connection->prepare('UPDATE notifications SET is_read = 1, read_at = :read_at, updated_at = :updated_at WHERE id = :id AND user_id = :user_id');

        return $statement->execute(array(
            'read_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s'),
            'id' => $notificationId,
            'user_id' => $userId,
        ));
    }

    public function recentBookmarkedIds(array $identity, int $limit = 8): array
    {
        if (!$this->hasTable('content_interactions')) {
            return array();
        }

        $statement = $this->connection->prepare('SELECT post_id FROM content_interactions WHERE interaction_type = "bookmark" AND actor_key = :actor_key ORDER BY created_at DESC LIMIT :limit');
        $statement->bindValue(':actor_key', $this->actorKey($identity));
        $statement->bindValue(':limit', max(1, min(12, $limit)), PDO::PARAM_INT);
        $statement->execute();

        return array_map('intval', $statement->fetchAll(PDO::FETCH_COLUMN));
    }

    private function actorKey(array $identity): string
    {
        return isset($identity['actor_key']) ? (string) $identity['actor_key'] : 'guest:' . bin2hex(random_bytes(8));
    }

    private function isGuest(array $identity): bool
    {
        return !isset($identity['user_id']) || $identity['user_id'] === null;
    }

    private function normalizeBody(string $body): string
    {
        $body = trim(strip_tags($body));

        return function_exists('mb_substr') ? mb_substr($body, 0, 2000) : substr($body, 0, 2000);
    }

    private function truncate(string $value, int $length): string
    {
        return function_exists('mb_substr') ? mb_substr($value, 0, $length) : substr($value, 0, $length);
    }

    private function reactionCount(int $postId, string $interactionType): int
    {
        $statement = $this->connection->prepare('SELECT COUNT(*) FROM content_interactions WHERE post_id = :post_id AND interaction_type = :interaction_type');
        $statement->execute(array('post_id' => $postId, 'interaction_type' => $interactionType));

        return (int) $statement->fetchColumn();
    }

    private function findReaction(int $postId, string $interactionType, string $actorKey)
    {
        $statement = $this->connection->prepare('SELECT * FROM content_interactions WHERE post_id = :post_id AND interaction_type = :interaction_type AND actor_key = :actor_key LIMIT 1');
        $statement->execute(array('post_id' => $postId, 'interaction_type' => $interactionType, 'actor_key' => $actorKey));

        $reaction = $statement->fetch(PDO::FETCH_ASSOC);

        return $reaction ?: false;
    }

    private function findComment(int $commentId, int $postId)
    {
        if (!$this->hasTable('comments')) {
            return false;
        }

        $statement = $this->connection->prepare('SELECT c.*, u.name AS user_name, u.username AS user_username FROM comments c LEFT JOIN users u ON u.id = c.user_id WHERE c.id = :id AND c.post_id = :post_id AND c.deleted_at IS NULL LIMIT 1');
        $statement->execute(array('id' => $commentId, 'post_id' => $postId));

        $comment = $statement->fetch(PDO::FETCH_ASSOC);

        return $comment ?: false;
    }

    private function threadComments(array $comments): array
    {
        $indexed = array();

        foreach ($comments as $comment) {
            $comment['replies'] = array();
            $indexed[(int) $comment['id']] = $comment;
        }

        $tree = array();

        foreach ($indexed as $id => $comment) {
            $parentId = !empty($comment['parent_id']) ? (int) $comment['parent_id'] : 0;

            if ($parentId > 0 && isset($indexed[$parentId])) {
                $indexed[$parentId]['replies'][] = &$indexed[$id];
                continue;
            }

            $tree[] = &$indexed[$id];
        }

        return $tree;
    }

    private function logActivity(array $identity, int $postId, string $eventType, string $title, array $metadata = array()): void
    {
        if (!$this->hasTable('activity_events')) {
            return;
        }

        $statement = $this->connection->prepare('INSERT INTO activity_events (user_id, post_id, actor_key, event_type, title, body, url, metadata_json, created_at) VALUES (:user_id, :post_id, :actor_key, :event_type, :title, :body, :url, :metadata_json, :created_at)');
        $statement->execute(array(
            'user_id' => isset($identity['user_id']) && $identity['user_id'] !== null ? (int) $identity['user_id'] : null,
            'post_id' => $postId,
            'actor_key' => $this->actorKey($identity),
            'event_type' => $eventType,
            'title' => $title,
            'body' => '',
            'url' => $this->postUrl($postId),
            'metadata_json' => $metadata === array() ? null : json_encode($metadata, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE),
            'created_at' => date('Y-m-d H:i:s'),
        ));
    }

    private function notifyCommentTargets(int $postId, array $identity, $comment, $parentComment): void
    {
        if (!$this->hasTable('notifications')) {
            return;
        }

        $authorId = $this->postAuthorId($postId);
        $actorLabel = isset($identity['display_name']) ? (string) $identity['display_name'] : 'Someone';
        $commentSnippet = isset($comment['body']) ? $this->truncate((string) $comment['body'], 120) : 'a comment';

        if ($authorId > 0 && (!$this->isGuest($identity) || (int) $identity['user_id'] !== $authorId)) {
            $this->createNotification($authorId, 'comment', $actorLabel . ' commented on your content.', $commentSnippet, $this->postUrl($postId));
        }

        if (is_array($parentComment) && !empty($parentComment['user_id'])) {
            $parentAuthorId = (int) $parentComment['user_id'];
            if ($parentAuthorId > 0 && $parentAuthorId !== $authorId && (isset($identity['user_id']) ? (int) $identity['user_id'] : 0) !== $parentAuthorId) {
                $this->createNotification($parentAuthorId, 'reply', $actorLabel . ' replied to your comment.', $commentSnippet, $this->postUrl($postId));
            }
        }
    }

    private function notifyReactionTarget(int $postId, array $identity, string $interactionType): void
    {
        if (!$this->hasTable('notifications')) {
            return;
        }

        $authorId = $this->postAuthorId($postId);
        if ($authorId <= 0 || (isset($identity['user_id']) && (int) $identity['user_id'] === $authorId)) {
            return;
        }

        $labels = array(
            'like' => 'liked',
            'bookmark' => 'bookmarked',
            'favorite' => 'favorited',
        );

        $title = 'Someone ' . (isset($labels[$interactionType]) ? $labels[$interactionType] : 'reacted to') . ' your content.';
        $this->createNotification($authorId, $interactionType, $title, ucfirst($interactionType) . ' on your post.', $this->postUrl($postId));
    }

    private function createNotification(int $userId, string $type, string $title, string $body, string $url): void
    {
        if (!$this->hasTable('notifications')) {
            return;
        }

        $statement = $this->connection->prepare('INSERT INTO notifications (user_id, type, title, body, url, is_read, created_at, updated_at) VALUES (:user_id, :type, :title, :body, :url, 0, :created_at, :updated_at)');
        $now = date('Y-m-d H:i:s');
        $statement->execute(array(
            'user_id' => $userId,
            'type' => $type,
            'title' => $this->truncate($title, 180),
            'body' => $this->truncate($body, 255),
            'url' => $this->truncate($url, 255),
            'created_at' => $now,
            'updated_at' => $now,
        ));
    }

    private function postAuthorId(int $postId): int
    {
        $statement = $this->connection->prepare('SELECT author_id FROM posts WHERE id = :id LIMIT 1');
        $statement->execute(array('id' => $postId));

        return (int) $statement->fetchColumn();
    }

    private function postUrl(int $postId): string
    {
        $statement = $this->connection->prepare('SELECT slug FROM posts WHERE id = :id LIMIT 1');
        $statement->execute(array('id' => $postId));
        $slug = (string) $statement->fetchColumn();

        return $slug !== '' ? url('/content/' . $slug) : url('/archive');
    }

    private function hasTable(string $tableName): bool
    {
        if (isset($this->tableCache[$tableName])) {
            return $this->tableCache[$tableName];
        }

        try {
            $statement = $this->connection->prepare('SELECT 1 FROM information_schema.tables WHERE table_schema = DATABASE() AND table_name = :table_name LIMIT 1');
            $statement->execute(array('table_name' => $tableName));
            $this->tableCache[$tableName] = (bool) $statement->fetchColumn();
        } catch (\Throwable $exception) {
            $this->tableCache[$tableName] = false;
        }

        return $this->tableCache[$tableName];
    }
}