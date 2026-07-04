<?php

declare(strict_types=1);

namespace App\Controllers\Admin;

use App\Core\Request;
use App\Repositories\AuditLogRepository;
use App\Repositories\CategoryRepository;
use App\Repositories\ContentTypeRepository;
use App\Repositories\MediaRepository;
use App\Repositories\PostMetaRepository;
use App\Repositories\PostRepository;
use App\Repositories\PostRevisionRepository;
use App\Repositories\PostSeoRepository;
use App\Repositories\TagRepository;
use App\Services\Admin\ContentService;

final class ContentController extends BaseAdminController
{
    private ContentService $contentService;

    public function __construct(\App\Core\Application $app)
    {
        parent::__construct($app);

        $connection = $this->app->database()->connection();
        $postRepository = new PostRepository($connection);
        $categoryRepository = new CategoryRepository($connection);
        $tagRepository = new TagRepository($connection);
        $contentTypeRepository = new ContentTypeRepository($connection);
        $mediaRepository = new MediaRepository($connection);
        $revisionRepository = new PostRevisionRepository($connection);
        $seoRepository = new PostSeoRepository($connection);
        $metaRepository = new PostMetaRepository($connection);
        $auditLogRepository = new AuditLogRepository($connection);

        $this->contentService = new ContentService($this->app, $postRepository, $categoryRepository, $tagRepository, $contentTypeRepository, $mediaRepository, $revisionRepository, $seoRepository, $metaRepository, $auditLogRepository);
    }

    public function index(Request $request)
    {
        $filters = array(
            'status' => (string) $request->input('status', 'all'),
            'content_type_id' => (int) $request->input('content_type_id', 0),
            'search' => trim((string) $request->input('search', '')),
        );

        return $this->adminView('admin/content/index', array_merge($this->contentService->overview($filters, (int) $request->input('page', 1), 15), array(
            'filters' => $filters,
            'currentUser' => $this->app->session()->get($this->app->config()->get('auth.session_key', 'auth_user')),
        )), array(
            'title' => 'Content',
            'description' => 'Universal content management for posts, categories, tags, media, and revisions.',
            'canonical' => url('/admin/content'),
            'robots' => 'noindex, nofollow',
        ));
    }

    public function drafts(Request $request)
    {
        return $this->adminView('admin/content/drafts', array_merge($this->contentService->listDrafts((int) $request->input('page', 1), 15), array(
            'currentUser' => $this->app->session()->get($this->app->config()->get('auth.session_key', 'auth_user')),
        )), array(
            'title' => 'Drafts',
            'description' => 'Draft posts waiting for publishing or scheduling.',
            'canonical' => url('/admin/content/drafts'),
            'robots' => 'noindex, nofollow',
        ));
    }

    public function create(Request $request)
    {
        $form = $this->contentService->blankPostData();
        $categoryData = $this->contentService->categories();
        $tagData = $this->contentService->tags();

        return $this->adminView('admin/content/form', array_merge($form, array(
            'mode' => 'create',
            'formAction' => url('/admin/content'),
            'contentTypes' => $this->contentService->contentTypes()['contentTypes'],
            'categoryTree' => $categoryData['categories'],
            'selectedCategoryIds' => array(),
            'tagList' => $tagData['tags'],
            'selectedTagIds' => array(),
            'mediaItems' => $this->contentService->media('', 1, 12)['data'],
            'revisions' => array(),
            'errors' => array(),
            'currentUser' => $this->app->session()->get($this->app->config()->get('auth.session_key', 'auth_user')),
        )), array(
            'title' => 'Create Content',
            'description' => 'Create a new universal post.',
            'canonical' => url('/admin/content/create'),
            'robots' => 'noindex, nofollow',
        ));
    }

    public function store(Request $request)
    {
        $result = $this->contentService->savePost($this->extractPostInput($request), $this->currentUserId(), null);

        if (empty($result['success'])) {
            return $this->renderFormWithErrors('create', array(), $result['message']);
        }

        $this->app->session()->flash('success', 'Content created successfully.');

        return $this->redirect('/admin/content/' . $result['post_id'] . '/edit');
    }

    public function edit(Request $request, $id)
    {
        $data = $this->contentService->loadPost($id);
        $categoryData = $this->contentService->categories();
        $tagData = $this->contentService->tags();

        if ($data === array()) {
            $this->app->session()->flash('error', 'Content item not found.');
            return $this->redirect('/admin/content');
        }

        return $this->adminView('admin/content/form', array_merge($this->contentService->blankPostData(), $data, array(
            'mode' => 'edit',
            'formAction' => url('/admin/content/' . $id),
            'contentTypes' => $this->contentService->contentTypes()['contentTypes'],
            'categoryTree' => $categoryData['categories'],
            'selectedCategoryIds' => isset($data['categories']) ? $data['categories'] : array(),
            'tagList' => $tagData['tags'],
            'selectedTagIds' => isset($data['tags']) ? $data['tags'] : array(),
            'mediaItems' => $this->contentService->media('', 1, 12)['data'],
            'errors' => array(),
            'currentUser' => $this->app->session()->get($this->app->config()->get('auth.session_key', 'auth_user')),
        )), array(
            'title' => 'Edit Content',
            'description' => 'Edit universal content details, SEO fields, and revisions.',
            'canonical' => url('/admin/content/' . $id . '/edit'),
            'robots' => 'noindex, nofollow',
        ));
    }

    public function update(Request $request, $id)
    {
        $result = $this->contentService->savePost($this->extractPostInput($request), $this->currentUserId(), $id);

        if (empty($result['success'])) {
            return $this->renderFormWithErrors('edit', $id, $result['message']);
        }

        $this->app->session()->flash('success', 'Content updated successfully.');

        return $this->redirect('/admin/content/' . $id . '/edit');
    }

    public function publish(Request $request, $id)
    {
        $result = $this->contentService->publishPost($id, $this->currentUserId());

        if (empty($result['success'])) {
            $this->app->session()->flash('error', $result['message']);
            return $this->redirect('/admin/content/' . $id . '/edit');
        }

        $this->app->session()->flash('success', 'Content published successfully.');
        return $this->redirect('/admin/content/' . $id . '/edit');
    }

    public function schedule(Request $request, $id)
    {
        $result = $this->contentService->schedulePost($id, array('published_at' => $request->input('published_at', '')), $this->currentUserId());

        if (empty($result['success'])) {
            $this->app->session()->flash('error', $result['message']);
            return $this->redirect('/admin/content/' . $id . '/edit');
        }

        $this->app->session()->flash('success', 'Content scheduled successfully.');
        return $this->redirect('/admin/content/' . $id . '/edit');
    }

    public function revisions(Request $request, $id)
    {
        $data = $this->contentService->revisions($id);

        return $this->adminView('admin/content/revisions', array_merge($data, array(
            'currentUser' => $this->app->session()->get($this->app->config()->get('auth.session_key', 'auth_user')),
        )), array(
            'title' => 'Revisions',
            'description' => 'Version history for content items.',
            'canonical' => url('/admin/content/' . $id . '/revisions'),
            'robots' => 'noindex, nofollow',
        ));
    }

    public function restoreRevision(Request $request, $id, $revisionId)
    {
        $result = $this->contentService->restoreRevision($id, $revisionId, $this->currentUserId());

        if (empty($result['success'])) {
            $this->app->session()->flash('error', $result['message']);
        } else {
            $this->app->session()->flash('success', 'Revision restored successfully.');
        }

        return $this->redirect('/admin/content/' . $id . '/revisions');
    }

    public function categories(Request $request)
    {
        $data = $this->contentService->categories();

        return $this->adminView('admin/content/categories', array_merge($data, array(
            'errors' => array(),
            'form' => array('id' => null, 'name' => '', 'slug' => '', 'description' => '', 'icon' => '', 'featured_image' => '', 'parent_id' => ''),
            'currentUser' => $this->app->session()->get($this->app->config()->get('auth.session_key', 'auth_user')),
        )), array(
            'title' => 'Categories',
            'description' => 'Manage nested content categories.',
            'canonical' => url('/admin/content/categories'),
            'robots' => 'noindex, nofollow',
        ));
    }

    public function saveCategory(Request $request)
    {
        $result = $this->contentService->saveCategory(array(
            'id' => $request->input('id', null),
            'parent_id' => $request->input('parent_id', ''),
            'name' => $request->input('name', ''),
            'slug' => $request->input('slug', ''),
            'description' => $request->input('description', ''),
            'icon' => $request->input('icon', ''),
            'featured_image' => $request->input('featured_image', ''),
        ), $request->input('id', null));

        if (empty($result['success'])) {
            $this->app->session()->flash('error', $result['message']);
        } else {
            $this->app->session()->flash('success', 'Category saved successfully.');
        }

        return $this->redirect('/admin/content/categories');
    }

    public function tags(Request $request)
    {
        $data = $this->contentService->tags();

        return $this->adminView('admin/content/tags', array_merge($data, array(
            'errors' => array(),
            'form' => array('id' => null, 'name' => '', 'slug' => ''),
            'currentUser' => $this->app->session()->get($this->app->config()->get('auth.session_key', 'auth_user')),
        )), array(
            'title' => 'Tags',
            'description' => 'Manage content tags.',
            'canonical' => url('/admin/content/tags'),
            'robots' => 'noindex, nofollow',
        ));
    }

    public function saveTag(Request $request)
    {
        $result = $this->contentService->saveTag(array(
            'id' => $request->input('id', null),
            'name' => $request->input('name', ''),
            'slug' => $request->input('slug', ''),
        ), $request->input('id', null));

        if (empty($result['success'])) {
            $this->app->session()->flash('error', $result['message']);
        } else {
            $this->app->session()->flash('success', 'Tag saved successfully.');
        }

        return $this->redirect('/admin/content/tags');
    }

    public function contentTypes(Request $request)
    {
        $data = $this->contentService->contentTypes();

        return $this->adminView('admin/content/types', array_merge($data, array(
            'errors' => array(),
            'form' => array('id' => null, 'name' => '', 'slug' => '', 'description' => '', 'icon' => ''),
            'currentUser' => $this->app->session()->get($this->app->config()->get('auth.session_key', 'auth_user')),
        )), array(
            'title' => 'Content Types',
            'description' => 'Manage universal content types.',
            'canonical' => url('/admin/content/types'),
            'robots' => 'noindex, nofollow',
        ));
    }

    public function saveContentType(Request $request)
    {
        $result = $this->contentService->saveContentType(array(
            'id' => $request->input('id', null),
            'name' => $request->input('name', ''),
            'slug' => $request->input('slug', ''),
            'description' => $request->input('description', ''),
            'icon' => $request->input('icon', ''),
        ), $request->input('id', null));

        if (empty($result['success'])) {
            $this->app->session()->flash('error', $result['message']);
        } else {
            $this->app->session()->flash('success', 'Content type saved successfully.');
        }

        return $this->redirect('/admin/content/types');
    }

    public function media(Request $request)
    {
        return $this->adminView('admin/content/media', array(
            'media' => $this->contentService->media(trim((string) $request->input('search', '')), (int) $request->input('page', 1), 20),
            'currentUser' => $this->app->session()->get($this->app->config()->get('auth.session_key', 'auth_user')),
        ), array(
            'title' => 'Media Manager',
            'description' => 'Manage uploaded media assets.',
            'canonical' => url('/admin/content/media'),
            'robots' => 'noindex, nofollow',
        ));
    }

    public function uploadMedia(Request $request)
    {
        $result = $this->contentService->uploadMedia($request->file('file'), $this->currentUserId(), (string) $request->input('alt_text', ''));

        if (empty($result['success'])) {
            $this->app->session()->flash('error', $result['message']);
        } else {
            $this->app->session()->flash('success', 'Media uploaded successfully.');
        }

        return $this->redirect('/admin/content/media');
    }

    private function currentUserId(): int
    {
        $currentUser = $this->app->session()->get($this->app->config()->get('auth.session_key', 'auth_user'));

        return is_array($currentUser) && isset($currentUser['id']) ? (int) $currentUser['id'] : 0;
    }

    private function extractPostInput(Request $request): array
    {
        return array(
            'content_type_id' => $request->input('content_type_id', 0),
            'title' => $request->input('title', ''),
            'slug' => $request->input('slug', ''),
            'excerpt' => $request->input('excerpt', ''),
            'content' => $request->input('content', ''),
            'featured_image' => $request->input('featured_image', ''),
            'status' => $request->input('status', 'draft'),
            'visibility' => $request->input('visibility', 'public'),
            'reading_time' => $request->input('reading_time', ''),
            'published_at' => $request->input('published_at', ''),
            'featured_flag' => $request->input('featured_flag', 0),
            'category_ids' => (array) $request->input('category_ids', array()),
            'tag_ids' => (array) $request->input('tag_ids', array()),
            'meta_fields' => (array) $request->input('meta_fields', array()),
            'seo' => array(
                'meta_title' => $request->input('seo_meta_title', ''),
                'meta_description' => $request->input('seo_meta_description', ''),
                'canonical_url' => $request->input('seo_canonical_url', ''),
                'robots' => $request->input('seo_robots', 'index, follow'),
                'schema_type' => $request->input('seo_schema_type', 'Article'),
                'og_title' => $request->input('seo_og_title', ''),
                'og_description' => $request->input('seo_og_description', ''),
                'og_image' => $request->input('seo_og_image', ''),
                'twitter_card' => $request->input('seo_twitter_card', 'summary_large_image'),
            ),
        );
    }

    private function renderFormWithErrors(string $mode, $id, string $message)
    {
        $data = $mode === 'edit' ? $this->contentService->loadPost($id) : $this->contentService->blankPostData();
        $categoryData = $this->contentService->categories();
        $tagData = $this->contentService->tags();
        $form = array_merge($this->contentService->blankPostData(), $data, array(
            'mode' => $mode,
            'formAction' => $mode === 'edit' ? url('/admin/content/' . $id) : url('/admin/content'),
            'contentTypes' => $this->contentService->contentTypes()['contentTypes'],
            'categoryTree' => $categoryData['categories'],
            'selectedCategoryIds' => isset($data['categories']) ? $data['categories'] : array(),
            'tagList' => $tagData['tags'],
            'selectedTagIds' => isset($data['tags']) ? $data['tags'] : array(),
            'mediaItems' => $this->contentService->media('', 1, 12)['data'],
            'errors' => array('general' => $message),
            'currentUser' => $this->app->session()->get($this->app->config()->get('auth.session_key', 'auth_user')),
        ));

        return $this->adminView('admin/content/form', $form, array(
            'title' => $mode === 'edit' ? 'Edit Content' : 'Create Content',
            'description' => 'Universal content editor.',
            'canonical' => $mode === 'edit' ? url('/admin/content/' . $id . '/edit') : url('/admin/content/create'),
            'robots' => 'noindex, nofollow',
        ));
    }
}
