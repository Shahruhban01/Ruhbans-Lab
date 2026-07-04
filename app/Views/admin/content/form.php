<?php
$post = isset($post) && is_array($post) ? $post : array();
$seo = isset($seo) && is_array($seo) ? $seo : array();
$meta = isset($meta) && is_array($meta) ? $meta : array();
$categoryTree = isset($categoryTree) && is_array($categoryTree) ? $categoryTree : array();
$selectedCategoryIds = isset($selectedCategoryIds) && is_array($selectedCategoryIds) ? array_map('intval', $selectedCategoryIds) : array();
$tagList = isset($tagList) && is_array($tagList) ? $tagList : array();
$selectedTagIds = isset($selectedTagIds) && is_array($selectedTagIds) ? array_map('intval', $selectedTagIds) : array();
$contentTypeList = isset($contentTypes) && is_array($contentTypes) ? $contentTypes : array();
$mediaItems = isset($mediaItems) && is_array($mediaItems) ? $mediaItems : array();
$revisions = isset($revisions) && is_array($revisions) ? $revisions : array();
$errors = isset($errors) && is_array($errors) ? $errors : array();

$postValue = function ($key, $default = '') use ($post) {
    return isset($post[$key]) && $post[$key] !== null && $post[$key] !== '' ? $post[$key] : $default;
};

$seoValue = function ($key, $default = '') use ($seo) {
    return isset($seo[$key]) && $seo[$key] !== null && $seo[$key] !== '' ? $seo[$key] : $default;
};

$metaValue = function ($key, $default = '') use ($meta) {
    return isset($meta[$key]) && $meta[$key] !== null && $meta[$key] !== '' ? $meta[$key] : $default;
};

$renderCategoryTree = function (array $items, array $selected, int $depth = 0) use (&$renderCategoryTree) {
    foreach ($items as $item) {
        $id = (int) $item['id'];
        $padding = str_repeat('&mdash; ', $depth);
        echo '<label class="checkbox-item">';
        echo '<input type="checkbox" name="category_ids[]" value="' . e($id) . '"' . (in_array($id, $selected, true) ? ' checked' : '') . '>';
        echo '<span>' . $padding . e($item['name']) . '</span>';
        echo '</label>';

        if (!empty($item['children']) && is_array($item['children'])) {
            $renderCategoryTree($item['children'], $selected, $depth + 1);
        }
    }
};

?>
<section class="page-stack">
    <div class="page-header page-header--split">
        <div>
            <p class="eyebrow">Content editor</p>
            <h2><?php echo e($mode === 'edit' ? 'Edit post' : 'Create post'); ?></h2>
        </div>
        <div class="content-actions">
            <?php if ($mode === 'edit') : ?>
                <form method="post" action="<?php echo e(url('/admin/content/' . $postValue('id') . '/publish')); ?>" class="inline-form">
                    <?php echo csrf_field(); ?>
                    <button type="submit" class="btn btn-secondary">Publish</button>
                </form>
                <form method="post" action="<?php echo e(url('/admin/content/' . $postValue('id') . '/schedule')); ?>" class="inline-form">
                    <?php echo csrf_field(); ?>
                    <input type="datetime-local" name="published_at" value="<?php echo e($postValue('published_at', '')); ?>">
                    <button type="submit" class="btn btn-secondary">Schedule</button>
                </form>
            <?php endif; ?>
        </div>
    </div>

    <?php if (!empty($errors['general'])) : ?>
        <div class="form-alert form-alert--error"><?php echo e($errors['general']); ?></div>
    <?php endif; ?>

    <form method="post" action="<?php echo e($formAction); ?>" class="content-editor-grid" enctype="multipart/form-data">
        <?php echo csrf_field(); ?>
        <?php if ($mode === 'edit') : ?>
            <input type="hidden" name="_method" value="PATCH">
        <?php endif; ?>

        <section class="content-editor-main panel card-surface">
            <label>
                <span>Title</span>
                <input type="text" name="title" value="<?php echo e($postValue('title')); ?>" required>
                <?php if (!empty($errors['title'])) : ?><small><?php echo e($errors['title']); ?></small><?php endif; ?>
            </label>

            <label>
                <span>Slug</span>
                <input type="text" name="slug" value="<?php echo e($postValue('slug')); ?>" placeholder="Auto-generated if blank">
            </label>

            <label>
                <span>Short description</span>
                <textarea name="excerpt" rows="4"><?php echo e($postValue('excerpt')); ?></textarea>
            </label>

            <label>
                <span>Content</span>
                <textarea name="content" class="rich-editor" rows="18"><?php echo e($postValue('content')); ?></textarea>
            </label>

            <div class="content-split-grid">
                <label>
                    <span>Status</span>
                    <select name="status">
                        <?php foreach (array('draft', 'published', 'scheduled', 'archived') as $status) : ?>
                            <option value="<?php echo e($status); ?>"<?php echo $postValue('status', 'draft') === $status ? ' selected' : ''; ?>><?php echo e(ucfirst($status)); ?></option>
                        <?php endforeach; ?>
                    </select>
                </label>

                <label>
                    <span>Visibility</span>
                    <select name="visibility">
                        <?php foreach (array('public', 'private', 'unlisted') as $visibility) : ?>
                            <option value="<?php echo e($visibility); ?>"<?php echo $postValue('visibility', 'public') === $visibility ? ' selected' : ''; ?>><?php echo e(ucfirst($visibility)); ?></option>
                        <?php endforeach; ?>
                    </select>
                </label>

                <label>
                    <span>Reading time</span>
                    <input type="number" min="1" name="reading_time" value="<?php echo e($postValue('reading_time', 0)); ?>">
                </label>

                <label>
                    <span>Published date</span>
                    <input type="datetime-local" name="published_at" value="<?php echo e($postValue('published_at')); ?>">
                </label>
            </div>
        </section>

        <aside class="content-editor-aside">
            <section class="panel card-surface">
                <h3>Post settings</h3>
                <label>
                    <span>Content type</span>
                    <select name="content_type_id" required>
                        <option value="">Select type</option>
                        <?php foreach ($contentTypeList as $contentType) : ?>
                            <option value="<?php echo e($contentType['id']); ?>"<?php echo (int) $postValue('content_type_id') === (int) $contentType['id'] ? ' selected' : ''; ?>><?php echo e($contentType['name']); ?></option>
                        <?php endforeach; ?>
                    </select>
                </label>

                <label class="checkbox-inline">
                    <input type="checkbox" name="featured_flag" value="1"<?php echo (int) $postValue('featured_flag', 0) ? ' checked' : ''; ?>>
                    <span>Featured</span>
                </label>

                <label>
                    <span>Featured image</span>
                    <input type="text" name="featured_image" value="<?php echo e($postValue('featured_image')); ?>" placeholder="Media path or URL">
                </label>
            </section>

            <section class="panel card-surface">
                <h3>Media manager</h3>
                <div class="media-picker-grid">
                    <?php foreach ($mediaItems as $item) : ?>
                        <label class="media-picker-card">
                            <input type="radio" name="featured_image_pick" value="<?php echo e($item['path']); ?>" data-featured-image-pick>
                            <span><?php echo e($item['original_name']); ?></span>
                        </label>
                    <?php endforeach; ?>
                </div>
                <label>
                    <span>Upload media</span>
                    <input type="file" name="file" accept="image/*,application/pdf">
                </label>
                <label>
                    <span>ALT text</span>
                    <input type="text" name="alt_text" value="">
                </label>
                <small>Upload uses the media manager endpoint when saved separately.</small>
            </section>

            <section class="panel card-surface">
                <h3>Categories</h3>
                <div class="checkbox-list">
                    <?php $renderCategoryTree($categoryTree, $selectedCategoryIds); ?>
                </div>
            </section>

            <section class="panel card-surface">
                <h3>Tags</h3>
                <div class="checkbox-list">
                    <?php foreach ($tagList as $tag) : ?>
                        <label class="checkbox-item">
                            <input type="checkbox" name="tag_ids[]" value="<?php echo e($tag['id']); ?>"<?php echo in_array((int) $tag['id'], $selectedTagIds, true) ? ' checked' : ''; ?>>
                            <span><?php echo e($tag['name']); ?></span>
                        </label>
                    <?php endforeach; ?>
                </div>
            </section>

            <section class="panel card-surface">
                <h3>Meta fields</h3>
                <label><span>Key</span><input type="text" name="meta_fields[key]" value="<?php echo e($metaValue('key')); ?>"></label>
                <label><span>Value</span><textarea name="meta_fields[value]" rows="4"><?php echo e($metaValue('value')); ?></textarea></label>
            </section>

            <section class="panel card-surface">
                <h3>SEO fields</h3>
                <label><span>Meta title</span><input type="text" name="seo_meta_title" value="<?php echo e($seoValue('meta_title')); ?>"></label>
                <label><span>Meta description</span><textarea name="seo_meta_description" rows="3"><?php echo e($seoValue('meta_description')); ?></textarea></label>
                <label><span>Canonical URL</span><input type="url" name="seo_canonical_url" value="<?php echo e($seoValue('canonical_url')); ?>"></label>
                <label><span>Robots</span><input type="text" name="seo_robots" value="<?php echo e($seoValue('robots', 'index, follow')); ?>"></label>
                <label><span>Schema type</span><input type="text" name="seo_schema_type" value="<?php echo e($seoValue('schema_type', 'Article')); ?>"></label>
                <label><span>OG title</span><input type="text" name="seo_og_title" value="<?php echo e($seoValue('og_title')); ?>"></label>
                <label><span>OG description</span><textarea name="seo_og_description" rows="3"><?php echo e($seoValue('og_description')); ?></textarea></label>
                <label><span>OG image</span><input type="text" name="seo_og_image" value="<?php echo e($seoValue('og_image')); ?>"></label>
                <label><span>Twitter card</span><input type="text" name="seo_twitter_card" value="<?php echo e($seoValue('twitter_card', 'summary_large_image')); ?>"></label>
            </section>

            <section class="panel card-surface">
                <button type="submit" class="btn btn-primary"><?php echo e($mode === 'edit' ? 'Update post' : 'Save draft'); ?></button>
            </section>
        </aside>
    </form>

    <?php if ($mode === 'edit') : ?>
        <section class="panel card-surface">
            <div class="panel__head">
                <h3>Revision history</h3>
                <a href="<?php echo e(url('/admin/content/' . $postValue('id') . '/revisions')); ?>">View all revisions</a>
            </div>
            <div class="revision-list">
                <?php foreach ($revisions as $revision) : ?>
                    <article class="revision-item">
                        <div>
                            <strong><?php echo e($revision['label']); ?></strong>
                            <p><?php echo e($revision['created_at']); ?></p>
                        </div>
                    </article>
                <?php endforeach; ?>
            </div>
        </section>
    <?php endif; ?>
</section>

<script src="https://cdn.ckeditor.com/ckeditor5/41.4.2/classic/ckeditor.js"></script>
