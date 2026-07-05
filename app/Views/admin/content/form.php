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

$contentId = $postValue('id');
$isEditing = $mode === 'edit' && $contentId;
$previewUrl = $isEditing ? url('/content/' . $postValue('slug')) : '';

?>
<section class="page-stack">
    <section class="editor-toolbar card-surface panel sticky-toolbar">
        <div class="editor-toolbar__copy">
            <p class="eyebrow">Content editor</p>
            <h2><?php echo e($mode === 'edit' ? 'Edit post' : 'Create post'); ?></h2>
            <p class="editor-toolbar__status" data-dirty-state>Ready to publish</p>
        </div>
        <div class="content-actions">
            <?php if ($isEditing) : ?>
                <a class="btn btn-secondary" href="<?php echo e($previewUrl); ?>" target="_blank" rel="noopener">Preview</a>
                <form method="post" action="<?php echo e(url('/admin/content/' . $contentId . '/publish')); ?>" class="inline-form">
                    <?php echo csrf_field(); ?>
                    <button type="submit" class="btn btn-secondary">Publish</button>
                </form>
                <form method="post" action="<?php echo e(url('/admin/content/' . $contentId . '/schedule')); ?>" class="inline-form">
                    <?php echo csrf_field(); ?>
                    <input type="datetime-local" name="published_at" value="<?php echo e($postValue('published_at', '')); ?>">
                    <button type="submit" class="btn btn-secondary">Schedule</button>
                </form>
            <?php endif; ?>
            <button type="submit" form="content-editor-form" class="btn btn-primary"><?php echo e($mode === 'edit' ? 'Update post' : 'Save draft'); ?></button>
        </div>
    </section>

    <?php if (!empty($errors['general'])) : ?>
        <div class="form-alert form-alert--error"><?php echo e($errors['general']); ?></div>
    <?php endif; ?>

    <form method="post" action="<?php echo e($formAction); ?>" class="content-editor-grid" enctype="multipart/form-data" id="content-editor-form" data-dirty-form>
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
                        <?php 
                        $visibilities = array(
                            'public' => 'Public (everyone)',
                            'members_only' => 'Members Only (any plan)',
                            'pro' => 'Pro (Pro / Lifetime)',
                            'lifetime' => 'Lifetime (Lifetime only)',
                            'private' => 'Private (Admin only)',
                            'hidden' => 'Hidden (Direct URL only)'
                        );
                        foreach ($visibilities as $value => $label) : 
                        ?>
                            <option value="<?php echo e($value); ?>"<?php echo $postValue('visibility', 'public') === $value ? ' selected' : ''; ?>><?php echo e($label); ?></option>
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

        <aside class="content-editor-aside sticky-stack">
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
                <h3>Publish status</h3>
                <div class="status-summary">
                    <span class="status-pill status-pill--active"><?php echo e(ucfirst($postValue('status', 'draft'))); ?></span>
                    <span class="editor-toolbar__status">Visibility: <?php echo e(ucfirst($postValue('visibility', 'public'))); ?></span>
                </div>
                <div class="meta-list">
                    <div>
                        <dt>Reading time</dt>
                        <dd><?php echo e($postValue('reading_time', 0)); ?> min</dd>
                    </div>
                    <div>
                        <dt>Published date</dt>
                        <dd><?php echo e($postValue('published_at') ?: 'Not scheduled'); ?></dd>
                    </div>
                </div>
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

            <section class="panel card-surface" id="product-specific-fields" style="display: none;">
                <h3>Product Settings & Access Controls</h3>
                
                <div class="row g-3">
                    <div class="col-md-6">
                        <label><span>Product Version</span><input type="text" name="meta_fields[product_version]" value="<?php echo e($metaValue('product_version', '1.0.0')); ?>"></label>
                    </div>
                    <div class="col-md-6">
                        <label><span>License</span><input type="text" name="meta_fields[product_license]" value="<?php echo e($metaValue('product_license', 'MIT')); ?>"></label>
                    </div>
                </div>

                <label><span>Tech Stack (comma separated)</span><input type="text" name="meta_fields[tech_stack]" value="<?php echo e($metaValue('tech_stack')); ?>"></label>
                <label><span>Requirements</span><input type="text" name="meta_fields[requirements]" value="<?php echo e($metaValue('requirements')); ?>"></label>

                <div class="row g-3">
                    <div class="col-md-6">
                        <label><span>Product Type</span>
                            <select name="meta_fields[product_type]">
                                <?php foreach (array('Mobile App', 'Website', 'Web Application', 'AI Tool', 'API', 'Open Source Project', 'Desktop Application', 'Browser Extension', 'Template', 'Script', 'Package', 'Experiment') as $type) : ?>
                                    <option value="<?php echo e($type); ?>"<?php echo strtolower($metaValue('product_type')) === strtolower($type) ? ' selected' : ''; ?>><?php echo e($type); ?></option>
                                <?php endforeach; ?>
                            </select>
                        </label>
                    </div>
                    <div class="col-md-6">
                        <label><span>Product Status</span>
                            <select name="meta_fields[product_status]">
                                <?php foreach (array('Live', 'Beta', 'Coming Soon', 'Archived') as $status) : ?>
                                    <option value="<?php echo e($status); ?>"<?php echo strtolower($metaValue('product_status')) === strtolower($status) ? ' selected' : ''; ?>><?php echo e($status); ?></option>
                                <?php endforeach; ?>
                            </select>
                        </label>
                    </div>
                </div>

                <label><span>Live Demo URL</span><input type="url" name="meta_fields[live_demo]" value="<?php echo e($metaValue('live_demo')); ?>"></label>
                <label><span>Documentation URL</span><input type="url" name="meta_fields[documentation_url]" value="<?php echo e($metaValue('documentation_url')); ?>"></label>
                <label><span>GitHub Repository URL</span><input type="url" name="meta_fields[github_repository]" value="<?php echo e($metaValue('github_repository')); ?>"></label>
                <label><span>Download URL</span><input type="url" name="meta_fields[download_url]" value="<?php echo e($metaValue('download_url')); ?>"></label>
                <label><span>API Documentation</span><textarea name="meta_fields[api_documentation]" rows="3"><?php echo e($metaValue('api_documentation')); ?></textarea></label>
                <label><span>Installation Guide</span><textarea name="meta_fields[installation_guide]" rows="3"><?php echo e($metaValue('installation_guide')); ?></textarea></label>
                <label><span>Changelog</span><textarea name="meta_fields[product_changelog]" rows="3"><?php echo e($metaValue('product_changelog')); ?></textarea></label>
                <label><span>Version History</span><textarea name="meta_fields[product_version_history]" rows="3"><?php echo e($metaValue('product_version_history')); ?></textarea></label>

                <h4 class="mt-4 mb-2 text-muted small fw-semibold text-uppercase">Granular Access Levels</h4>
                <?php
                $permissions = array(
                    'live_demo' => 'Live Demo',
                    'documentation' => 'Documentation',
                    'source_code' => 'Source Code',
                    'download' => 'Download',
                    'github_repository' => 'GitHub Repository',
                    'api_documentation' => 'API Documentation',
                    'installation_guide' => 'Installation Guide'
                );
                foreach ($permissions as $key => $label) :
                ?>
                    <label class="mb-2">
                        <span><?php echo e($label); ?> Access Level</span>
                        <select name="meta_fields[access_<?php echo e($key); ?>]">
                            <option value="public"<?php echo $metaValue('access_' . $key) === 'public' ? ' selected' : ''; ?>>Public</option>
                            <option value="members_only"<?php echo $metaValue('access_' . $key) === 'members_only' ? ' selected' : ''; ?>>Members Only (Free)</option>
                            <option value="pro"<?php echo $metaValue('access_' . $key) === 'pro' ? ' selected' : ''; ?>>Pro</option>
                            <option value="lifetime"<?php echo $metaValue('access_' . $key) === 'lifetime' ? ' selected' : ''; ?>>Lifetime</option>
                        </select>
                    </label>
                <?php endforeach; ?>
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

<script>
document.addEventListener('DOMContentLoaded', function() {
    const contentTypeSelect = document.querySelector('select[name="content_type_id"]');
    const productFields = document.getElementById('product-specific-fields');
    
    function toggleProductFields() {
        if (contentTypeSelect && contentTypeSelect.value === '74') {
            productFields.style.display = 'block';
        } else if (productFields) {
            productFields.style.display = 'none';
        }
    }
    
    if (contentTypeSelect && productFields) {
        contentTypeSelect.addEventListener('change', toggleProductFields);
        toggleProductFields();
    }
});
</script>
<script src="https://cdn.ckeditor.com/ckeditor5/41.4.2/classic/ckeditor.js"></script>
