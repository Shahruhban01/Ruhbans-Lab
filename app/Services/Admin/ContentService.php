<?php

declare(strict_types=1);

namespace App\Services\Admin;

use App\Core\Application;
use App\Repositories\AuditLogRepository;
use App\Repositories\CategoryRepository;
use App\Repositories\ContentTypeRepository;
use App\Repositories\MediaRepository;
use App\Repositories\PostMetaRepository;
use App\Repositories\PostRepository;
use App\Repositories\PostRevisionRepository;
use App\Repositories\PostSeoRepository;
use App\Repositories\TagRepository;
use App\Services\BaseService;

final class ContentService extends BaseService
{
    private Application $app;
    private CategoryRepository $categoryRepository;
    private TagRepository $tagRepository;
    private ContentTypeRepository $contentTypeRepository;
    private MediaRepository $mediaRepository;
    private PostRevisionRepository $revisionRepository;
    private PostSeoRepository $seoRepository;
    private PostMetaRepository $metaRepository;
    private AuditLogRepository $auditLogRepository;

    public function __construct(Application $app, PostRepository $postRepository, CategoryRepository $categoryRepository, TagRepository $tagRepository, ContentTypeRepository $contentTypeRepository, MediaRepository $mediaRepository, PostRevisionRepository $revisionRepository, PostSeoRepository $seoRepository, PostMetaRepository $metaRepository, AuditLogRepository $auditLogRepository)
    {
        parent::__construct($postRepository);
        $this->app = $app;
        $this->categoryRepository = $categoryRepository;
        $this->tagRepository = $tagRepository;
        $this->contentTypeRepository = $contentTypeRepository;
        $this->mediaRepository = $mediaRepository;
        $this->revisionRepository = $revisionRepository;
        $this->seoRepository = $seoRepository;
        $this->metaRepository = $metaRepository;
        $this->auditLogRepository = $auditLogRepository;
    }

    public function overview(array $filters, int $page = 1, int $perPage = 15): array
    {
        return array(
            'stats' => $this->repository->counts(),
            'posts' => $this->repository->paginateAdmin($filters, $page, $perPage),
            'contentTypes' => $this->contentTypeRepository->allTypes(),
            'categories' => $this->categoryRepository->tree(),
            'tags' => $this->tagRepository->allTags(),
            'media' => $this->mediaRepository->recent(8),
        );
    }

    public function blankPostData(): array
    {
        return array(
            'post' => array(
                'id' => null,
                'content_type_id' => null,
                'author_id' => null,
                'title' => '',
                'slug' => '',
                'excerpt' => '',
                'content' => '',
                'featured_image' => '',
                'status' => 'draft',
                'visibility' => 'public',
                'reading_time' => 0,
                'published_at' => '',
                'featured_flag' => 0,
            ),
            'seo' => array(),
            'meta' => array(),
            'categories' => array(),
            'tags' => array(),
        );
    }

    public function loadPost($id): array
    {
        $post = $this->repository->findWithRelations($id);

        if (!$post) {
            return array();
        }

        return array(
            'post' => $post,
            'seo' => $this->seoRepository->findByPostId($id) ?: array(),
            'meta' => $this->metaRepository->getByPostId($id),
            'categories' => $this->repository->categoriesForPost($id),
            'tags' => $this->repository->tagsForPost($id),
            'revisions' => $this->revisionRepository->forPost($id),
        );
    }

    public function listDrafts(int $page = 1, int $perPage = 15): array
    {
        return array(
            'stats' => $this->repository->counts(),
            'posts' => $this->repository->allDrafts($page, $perPage),
        );
    }

    public function savePost(array $input, $authorId, $postId = null): array
    {
        $isUpdate = $postId !== null && $postId !== '';
        $title = trim((string) ($input['title'] ?? ''));
        $contentTypeId = isset($input['content_type_id']) ? (int) $input['content_type_id'] : 0;

        if ($title === '' || $contentTypeId <= 0) {
            return array('success' => false, 'message' => 'Title and content type are required.');
        }

        $content = (string) ($input['content'] ?? '');
        $status = $this->normalizeStatus(isset($input['status']) ? $input['status'] : 'draft', $input);
        $visibility = $this->normalizeVisibility(isset($input['visibility']) ? $input['visibility'] : 'public');
        $publishedAt = $this->normalizePublishedAt($status, isset($input['published_at']) ? $input['published_at'] : '');
        $slug = trim((string) ($input['slug'] ?? ''));

        if ($slug === '') {
            $slug = slugify($title);
        }

        $payload = array(
            'content_type_id' => $contentTypeId,
            'author_id' => (int) $authorId,
            'title' => $title,
            'slug' => $slug,
            'excerpt' => trim((string) ($input['excerpt'] ?? '')),
            'content' => $content,
            'featured_image' => trim((string) ($input['featured_image'] ?? '')),
            'status' => $status,
            'visibility' => $visibility,
            'reading_time' => $this->normalizeReadingTime($input, $content),
            'published_at' => $publishedAt,
            'featured_flag' => !empty($input['featured_flag']) ? 1 : 0,
            'updated_at' => date('Y-m-d H:i:s'),
        );

        if ($postId) {
            $payload['id'] = $postId;
            $this->repository->updatePost($postId, $payload);
        } else {
            $payload['created_at'] = date('Y-m-d H:i:s');
            $postId = $this->repository->createPost($payload);
        }

        $this->repository->syncCategories($postId, $this->normalizeIds(isset($input['category_ids']) ? $input['category_ids'] : array()));
        $this->repository->syncTags($postId, $this->normalizeIds(isset($input['tag_ids']) ? $input['tag_ids'] : array()));
        $this->metaRepository->syncMeta($postId, isset($input['meta_fields']) && is_array($input['meta_fields']) ? $input['meta_fields'] : array());
        $this->seoRepository->saveSeo($postId, isset($input['seo']) && is_array($input['seo']) ? $input['seo'] : array());

        $this->revisionRepository->saveRevision($postId, $authorId, $this->snapshotPost($postId, $payload, $input), $isUpdate ? 'Updated' : 'Created');
        $this->auditLogRepository->createLog($authorId, $isUpdate ? 'content_updated' : 'content_created', $isUpdate ? 'Updated content item.' : 'Created content item.', array('post_id' => $postId, 'status' => $status), $this->app->request()->ip(), (string) $this->app->request()->header('User-Agent', ''));

        return array('success' => true, 'post_id' => $postId);
    }

    public function publishPost($postId, $authorId): array
    {
        $post = $this->repository->findWithRelations($postId);

        if (!$post) {
            return array('success' => false, 'message' => 'Post not found.');
        }

        $this->repository->publish($postId);
        $this->revisionRepository->saveRevision($postId, $authorId, $this->snapshotPost($postId, $post, array()), 'Published');
        $this->auditLogRepository->createLog($authorId, 'content_published', 'Published content item.', array('post_id' => $postId), $this->app->request()->ip(), (string) $this->app->request()->header('User-Agent', ''));

        return array('success' => true);
    }

    public function schedulePost($postId, array $input, $authorId): array
    {
        $scheduledAt = isset($input['published_at']) ? trim((string) $input['published_at']) : '';

        if ($scheduledAt === '') {
            return array('success' => false, 'message' => 'Scheduled date is required.');
        }

        $normalizedDate = date('Y-m-d H:i:s', strtotime(str_replace('T', ' ', $scheduledAt)));
        $this->repository->schedule($postId, $normalizedDate);
        $this->auditLogRepository->createLog($authorId, 'content_scheduled', 'Scheduled content item.', array('post_id' => $postId, 'published_at' => $normalizedDate), $this->app->request()->ip(), (string) $this->app->request()->header('User-Agent', ''));

        return array('success' => true);
    }

    public function revisions($postId): array
    {
        return array(
            'post' => $this->repository->findWithRelations($postId),
            'revisions' => $this->revisionRepository->forPost($postId),
        );
    }

    public function restoreRevision($postId, $revisionId, $authorId): array
    {
        $revision = $this->revisionRepository->findById($revisionId);

        if (!$revision || (int) $revision['post_id'] !== (int) $postId) {
            return array('success' => false, 'message' => 'Revision not found.');
        }

        $snapshot = json_decode((string) $revision['snapshot_json'], true);

        if (!is_array($snapshot) || empty($snapshot['post'])) {
            return array('success' => false, 'message' => 'Invalid revision snapshot.');
        }

        $post = $snapshot['post'];
        $post['updated_at'] = date('Y-m-d H:i:s');
        $this->repository->updatePost($postId, $post);
        $this->repository->syncCategories($postId, isset($snapshot['categories']) ? (array) $snapshot['categories'] : array());
        $this->repository->syncTags($postId, isset($snapshot['tags']) ? (array) $snapshot['tags'] : array());
        $this->metaRepository->syncMeta($postId, isset($snapshot['meta']) ? (array) $snapshot['meta'] : array());
        $this->seoRepository->saveSeo($postId, isset($snapshot['seo']) ? (array) $snapshot['seo'] : array());
        $this->revisionRepository->saveRevision($postId, $authorId, $this->snapshotPost($postId, $post, $snapshot), 'Restored revision');
        $this->auditLogRepository->createLog($authorId, 'content_revision_restored', 'Restored a content revision.', array('post_id' => $postId, 'revision_id' => $revisionId), $this->app->request()->ip(), (string) $this->app->request()->header('User-Agent', ''));

        return array('success' => true);
    }

    public function categories(): array
    {
        return array('categories' => $this->categoryRepository->tree());
    }

    public function saveCategory(array $input, $id = null): array
    {
        $name = trim((string) ($input['name'] ?? ''));

        if ($name === '') {
            return array('success' => false, 'message' => 'Category name is required.');
        }

        $savedId = $this->categoryRepository->saveCategory($input, $id);
        return array('success' => true, 'id' => $savedId);
    }

    public function tags(): array
    {
        return array('tags' => $this->tagRepository->allTags());
    }

    public function saveTag(array $input, $id = null): array
    {
        $name = trim((string) ($input['name'] ?? ''));

        if ($name === '') {
            return array('success' => false, 'message' => 'Tag name is required.');
        }

        $savedId = $this->tagRepository->saveTag($input, $id);
        return array('success' => true, 'id' => $savedId);
    }

    public function contentTypes(): array
    {
        return array('contentTypes' => $this->contentTypeRepository->allTypes());
    }

    public function saveContentType(array $input, $id = null): array
    {
        $name = trim((string) ($input['name'] ?? ''));

        if ($name === '') {
            return array('success' => false, 'message' => 'Content type name is required.');
        }

        $savedId = $this->contentTypeRepository->saveType($input, $id);
        return array('success' => true, 'id' => $savedId);
    }

    public function media(string $search = '', int $page = 1, int $perPage = 20): array
    {
        return $this->mediaRepository->paginateMedia($search, $page, $perPage);
    }

    public function uploadMedia(array $file, $uploaderId, string $altText = ''): array
    {
        if (empty($file) || !isset($file['tmp_name']) || !is_uploaded_file($file['tmp_name'])) {
            return array('success' => false, 'message' => 'No file was uploaded.');
        }

        $uploadsDir = base_path('uploads/media');
        if (!is_dir($uploadsDir)) {
            mkdir($uploadsDir, 0755, true);
        }

        $originalName = basename((string) $file['name']);
        $extension = strtolower(pathinfo($originalName, PATHINFO_EXTENSION));
        $filename = uniqid('media_', true) . ($extension !== '' ? '.' . $extension : '');
        $destination = $uploadsDir . '/' . $filename;

        if (!move_uploaded_file($file['tmp_name'], $destination)) {
            return array('success' => false, 'message' => 'Unable to save uploaded file.');
        }

        $imageSize = @getimagesize($destination);
        $mediaId = $this->mediaRepository->saveMedia(array(
            'uploader_id' => $uploaderId,
            'filename' => $filename,
            'original_name' => $originalName,
            'path' => 'uploads/media/' . $filename,
            'mime_type' => isset($file['type']) ? $file['type'] : 'application/octet-stream',
            'extension' => $extension,
            'file_size' => isset($file['size']) ? (int) $file['size'] : filesize($destination),
            'width' => is_array($imageSize) ? (int) $imageSize[0] : null,
            'height' => is_array($imageSize) ? (int) $imageSize[1] : null,
            'alt_text' => $altText,
        ));

        $this->auditLogRepository->createLog($uploaderId, 'media_uploaded', 'Uploaded media asset.', array('media_id' => $mediaId, 'filename' => $filename), $this->app->request()->ip(), (string) $this->app->request()->header('User-Agent', ''));

        return array('success' => true, 'media_id' => $mediaId, 'path' => 'uploads/media/' . $filename);
    }

    private function normalizeStatus($status, array $input): string
    {
        $status = strtolower(trim((string) $status));
        $allowed = array('draft', 'published', 'scheduled', 'archived', 'review');

        if (!in_array($status, $allowed, true)) {
            $status = 'draft';
        }

        if ($status === 'published' && empty($input['published_at'])) {
            $status = 'published';
        }

        return $status;
    }

    private function normalizeVisibility($visibility): string
    {
        $visibility = strtolower(trim((string) $visibility));
        $allowed = array('public', 'members_only', 'pro', 'lifetime', 'private', 'hidden', 'unlisted');

        return in_array($visibility, $allowed, true) ? $visibility : 'public';
    }

    private function normalizePublishedAt(string $status, $publishedAt): ?string
    {
        $publishedAt = trim((string) $publishedAt);

        if ($status === 'published') {
            if ($publishedAt === '') {
                return date('Y-m-d H:i:s');
            }

            return date('Y-m-d H:i:s', strtotime(str_replace('T', ' ', $publishedAt)));
        }

        if ($status === 'scheduled' && $publishedAt !== '') {
            return date('Y-m-d H:i:s', strtotime(str_replace('T', ' ', $publishedAt)));
        }

        return null;
    }

    private function normalizeReadingTime(array $input, string $content): int
    {
        if (!empty($input['reading_time'])) {
            return max(1, (int) $input['reading_time']);
        }

        $words = str_word_count(strip_tags($content));
        return max(1, (int) ceil($words / 200));
    }

    private function normalizeIds(array $ids): array
    {
        $normalized = array();

        foreach ($ids as $id) {
            $id = (int) $id;
            if ($id > 0) {
                $normalized[] = $id;
            }
        }

        return array_values(array_unique($normalized));
    }

    private function snapshotPost($postId, array $post, array $input): array
    {
        $snapshot = array(
            'post' => $post,
            'categories' => $this->repository->categoriesForPost($postId),
            'tags' => $this->repository->tagsForPost($postId),
            'meta' => $this->metaRepository->getByPostId($postId),
            'seo' => $this->seoRepository->findByPostId($postId) ?: array(),
        );

        if (isset($input['category_ids']) && is_array($input['category_ids'])) {
            $snapshot['categories'] = $this->normalizeIds($input['category_ids']);
        }

        if (isset($input['tag_ids']) && is_array($input['tag_ids'])) {
            $snapshot['tags'] = $this->normalizeIds($input['tag_ids']);
        }

        if (isset($input['meta_fields']) && is_array($input['meta_fields'])) {
            $snapshot['meta'] = $input['meta_fields'];
        }

        if (isset($input['seo']) && is_array($input['seo'])) {
            $snapshot['seo'] = $input['seo'];
        }

        return $snapshot;
    }
}
