INSERT INTO content_types (name, slug, description, icon) VALUES
    ('Article', 'article', 'Long-form editorial content.', 'article'),
    ('News', 'news', 'Time-sensitive updates and announcements.', 'newspaper'),
    ('Tutorial', 'tutorial', 'Step-by-step educational content.', 'graduation-cap'),
    ('Guide', 'guide', 'Evergreen reference and how-to content.', 'book')
ON DUPLICATE KEY UPDATE
    name = VALUES(name),
    description = VALUES(description),
    icon = VALUES(icon);

INSERT INTO content_types (name, slug, description, icon) VALUES
    ('Projects', 'projects', 'Showcase software projects.', 'folder-open'),
    ('Blogs', 'blogs', 'Long-form technical articles.', 'newspaper'),
    ('Problem Solution', 'problem-solution', 'Document bugs and their solutions.', 'triangle-exclamation'),
    ('AI Prompts', 'ai-prompts', 'Reusable prompts for AI workflows.', 'robot'),
    ('Code Snippets', 'code-snippets', 'Reusable code snippets and examples.', 'code'),
    ('Tools', 'tools', 'Recommend online tools.', 'screwdriver-wrench'),
    ('Websites', 'websites', 'Useful websites and references.', 'globe'),
    ('Applications', 'applications', 'Desktop or mobile applications.', 'mobile-screen-button'),
    ('Reviews', 'reviews', 'Reviews of software, hardware, books, hosting, or courses.', 'star'),
    ('Resources', 'resources', 'Useful learning resources.', 'book-open'),
    ('Videos', 'videos', 'Educational videos and embeds.', 'video'),
    ('Downloads', 'downloads', 'Downloadable files and assets.', 'download'),
    ('Developer Notes', 'developer-notes', 'Personal technical notes.', 'note-sticky'),
    ('Thoughts', 'thoughts', 'Engineering opinions and ideas.', 'comment-dots'),
    ('Experiments', 'experiments', 'Document experiments and outcomes.', 'flask'),
    ('Roadmaps', 'roadmaps', 'Learning paths and milestone plans.', 'route'),
    ('Cheat Sheets', 'cheat-sheets', 'Quick reference pages.', 'list-check'),
    ('Recommendations', 'recommendations', 'Recommend products, services, or software.', 'thumbs-up'),
    ('Collections', 'collections', 'Group multiple posts together.', 'layer-group')
ON DUPLICATE KEY UPDATE
    name = VALUES(name),
    description = VALUES(description),
    icon = VALUES(icon);

INSERT INTO categories (parent_id, name, slug, description, icon) VALUES
    (NULL, 'Programming', 'programming', 'Programming tutorials, snippets, and engineering notes.', 'code'),
    (NULL, 'Design', 'design', 'Visual design, UI, and product craft content.', 'palette'),
    (NULL, 'Business', 'business', 'Strategy, operations, and business insights.', 'briefcase'),
    (NULL, 'Technology', 'technology', 'Technology news, analysis, and reviews.', 'cpu')
ON DUPLICATE KEY UPDATE
    name = VALUES(name),
    description = VALUES(description),
    icon = VALUES(icon);

INSERT INTO tags (name, slug) VALUES
    ('PHP', 'php'),
    ('MySQL', 'mysql'),
    ('Architecture', 'architecture'),
    ('SEO', 'seo'),
    ('Security', 'security')
ON DUPLICATE KEY UPDATE
    name = VALUES(name);

INSERT INTO users (role_id, username, name, email, password, avatar, bio, website, github, linkedin, twitter, is_active)
SELECT r.id, 'editorial-author', 'Editorial Author', 'editorial@developer-ruhban.example', '$2y$10$XeqUzRTLSSb17RtKChtIZu894CYxLz.Hl0y/Pw6czOVwoG965JbRC', NULL, 'Editorial contributor for the public knowledge base.', 'https://developer-ruhban.example', NULL, NULL, NULL, 1
FROM roles r
WHERE r.slug = 'author'
LIMIT 1
ON DUPLICATE KEY UPDATE
    role_id = VALUES(role_id),
    name = VALUES(name),
    email = VALUES(email),
    password = VALUES(password),
    bio = VALUES(bio),
    website = VALUES(website),
    is_active = VALUES(is_active),
    updated_at = CURRENT_TIMESTAMP;

INSERT INTO posts (content_type_id, author_id, title, slug, excerpt, content, featured_image, status, visibility, reading_time, published_at, featured_flag, created_at, updated_at)
SELECT ct.id, u.id, 'Getting Started with Developer Ruhban', 'getting-started-with-developer-ruhban', 'A practical introduction to the platform structure, content flow, and SEO-first publishing model.', '<p>Developer Ruhban is built to keep content fast to publish and easy to maintain.</p><h2>What this platform is</h2><p>It combines a custom MVC backend, a reusable content model, and a clean public frontend.</p><h2>What you can publish</h2><p>Tutorials, guides, notes, and reference material can all live in the same system.</p>', NULL, 'published', 'public', 6, '2026-07-04 08:00:00', 1, '2026-07-04 08:00:00', '2026-07-04 08:00:00'
FROM users u
JOIN content_types ct ON ct.slug = 'guide'
WHERE u.username = 'editorial-author'
LIMIT 1
ON DUPLICATE KEY UPDATE
    content_type_id = VALUES(content_type_id),
    author_id = VALUES(author_id),
    title = VALUES(title),
    excerpt = VALUES(excerpt),
    content = VALUES(content),
    featured_image = VALUES(featured_image),
    status = VALUES(status),
    visibility = VALUES(visibility),
    reading_time = VALUES(reading_time),
    published_at = VALUES(published_at),
    featured_flag = VALUES(featured_flag),
    updated_at = VALUES(updated_at);

INSERT INTO posts (content_type_id, author_id, title, slug, excerpt, content, featured_image, status, visibility, reading_time, published_at, featured_flag, created_at, updated_at)
SELECT ct.id, u.id, 'Building SEO-Friendly Public Content Pages', 'building-seo-friendly-public-content-pages', 'A focused walkthrough of metadata, breadcrumbs, internal links, and structured public pages.', '<p>Search engines reward clarity, structure, and internal linking.</p><h2>Core rules</h2><ul><li>Use descriptive titles.</li><li>Keep URLs short and readable.</li><li>Add a single H1 and consistent headings.</li></ul><h2>Page structure</h2><p>Every page should have a clear purpose and support one main topic.</p>', NULL, 'published', 'public', 7, '2026-07-04 09:00:00', 1, '2026-07-04 09:00:00', '2026-07-04 09:00:00'
FROM users u
JOIN content_types ct ON ct.slug = 'tutorial'
WHERE u.username = 'editorial-author'
LIMIT 1
ON DUPLICATE KEY UPDATE
    content_type_id = VALUES(content_type_id),
    author_id = VALUES(author_id),
    title = VALUES(title),
    excerpt = VALUES(excerpt),
    content = VALUES(content),
    featured_image = VALUES(featured_image),
    status = VALUES(status),
    visibility = VALUES(visibility),
    reading_time = VALUES(reading_time),
    published_at = VALUES(published_at),
    featured_flag = VALUES(featured_flag),
    updated_at = VALUES(updated_at);

INSERT INTO posts (content_type_id, author_id, title, slug, excerpt, content, featured_image, status, visibility, reading_time, published_at, featured_flag, created_at, updated_at)
SELECT ct.id, u.id, 'Universal Content Workflow and Revisions', 'universal-content-workflow-and-revisions', 'A practical look at drafts, publishing, revision history, and metadata management.', '<p>The universal content workflow starts with a draft and ends with a traceable revision history.</p><h2>Workflow</h2><p>Edit, preview, publish, schedule, and restore revisions from one place.</p><h2>Why revisions matter</h2><p>Version history keeps publishing safe when content changes over time.</p>', NULL, 'published', 'public', 5, '2026-07-04 10:00:00', 0, '2026-07-04 10:00:00', '2026-07-04 10:00:00'
FROM users u
JOIN content_types ct ON ct.slug = 'article'
WHERE u.username = 'editorial-author'
LIMIT 1
ON DUPLICATE KEY UPDATE
    content_type_id = VALUES(content_type_id),
    author_id = VALUES(author_id),
    title = VALUES(title),
    excerpt = VALUES(excerpt),
    content = VALUES(content),
    featured_image = VALUES(featured_image),
    status = VALUES(status),
    visibility = VALUES(visibility),
    reading_time = VALUES(reading_time),
    published_at = VALUES(published_at),
    featured_flag = VALUES(featured_flag),
    updated_at = VALUES(updated_at);

INSERT IGNORE INTO post_categories (post_id, category_id, created_at)
SELECT p.id, c.id, CURRENT_TIMESTAMP
FROM posts p
JOIN categories c ON c.slug = 'programming'
WHERE p.slug = 'getting-started-with-developer-ruhban';

INSERT IGNORE INTO post_categories (post_id, category_id, created_at)
SELECT p.id, c.id, CURRENT_TIMESTAMP
FROM posts p
JOIN categories c ON c.slug = 'technology'
WHERE p.slug = 'building-seo-friendly-public-content-pages';

INSERT IGNORE INTO post_categories (post_id, category_id, created_at)
SELECT p.id, c.id, CURRENT_TIMESTAMP
FROM posts p
JOIN categories c ON c.slug = 'business'
WHERE p.slug = 'universal-content-workflow-and-revisions';

INSERT IGNORE INTO post_tags (post_id, tag_id, created_at)
SELECT p.id, t.id, CURRENT_TIMESTAMP
FROM posts p
JOIN tags t ON t.slug = 'architecture'
WHERE p.slug = 'getting-started-with-developer-ruhban';

INSERT IGNORE INTO post_tags (post_id, tag_id, created_at)
SELECT p.id, t.id, CURRENT_TIMESTAMP
FROM posts p
JOIN tags t ON t.slug = 'php'
WHERE p.slug = 'getting-started-with-developer-ruhban';

INSERT IGNORE INTO post_tags (post_id, tag_id, created_at)
SELECT p.id, t.id, CURRENT_TIMESTAMP
FROM posts p
JOIN tags t ON t.slug = 'seo'
WHERE p.slug = 'building-seo-friendly-public-content-pages';

INSERT IGNORE INTO post_tags (post_id, tag_id, created_at)
SELECT p.id, t.id, CURRENT_TIMESTAMP
FROM posts p
JOIN tags t ON t.slug = 'security'
WHERE p.slug = 'universal-content-workflow-and-revisions';

INSERT IGNORE INTO content_metrics (post_id, view_count, search_count, last_viewed_at, last_searched_at, created_at, updated_at)
SELECT p.id, 24, 14, '2026-07-04 12:00:00', '2026-07-04 12:15:00', '2026-07-04 12:00:00', '2026-07-04 12:15:00'
FROM posts p
WHERE p.slug = 'getting-started-with-developer-ruhban';

INSERT IGNORE INTO content_metrics (post_id, view_count, search_count, last_viewed_at, last_searched_at, created_at, updated_at)
SELECT p.id, 31, 19, '2026-07-04 12:10:00', '2026-07-04 12:20:00', '2026-07-04 12:10:00', '2026-07-04 12:20:00'
FROM posts p
WHERE p.slug = 'building-seo-friendly-public-content-pages';

INSERT IGNORE INTO content_metrics (post_id, view_count, search_count, last_viewed_at, last_searched_at, created_at, updated_at)
SELECT p.id, 18, 11, '2026-07-04 12:20:00', '2026-07-04 12:25:00', '2026-07-04 12:20:00', '2026-07-04 12:25:00'
FROM posts p
WHERE p.slug = 'universal-content-workflow-and-revisions';

INSERT INTO search_queries (search_term, normalized_term, filters_json, result_count, ip_address, user_agent, referrer, created_at) VALUES
    ('php routing', 'php routing', NULL, 2, '127.0.0.1', 'Seed', NULL, '2026-07-04 12:00:00'),
    ('seo content', 'seo content', NULL, 2, '127.0.0.1', 'Seed', NULL, '2026-07-04 12:05:00'),
    ('content workflow', 'content workflow', NULL, 1, '127.0.0.1', 'Seed', NULL, '2026-07-04 12:10:00'),
    ('developer notes', 'developer notes', NULL, 1, '127.0.0.1', 'Seed', NULL, '2026-07-04 12:15:00')
ON DUPLICATE KEY UPDATE
    result_count = VALUES(result_count),
    created_at = VALUES(created_at);
