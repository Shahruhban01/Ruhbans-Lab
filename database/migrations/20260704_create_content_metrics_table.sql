CREATE TABLE IF NOT EXISTS content_metrics (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    post_id BIGINT UNSIGNED NOT NULL UNIQUE,
    view_count INT UNSIGNED NOT NULL DEFAULT 0,
    search_count INT UNSIGNED NOT NULL DEFAULT 0,
    last_viewed_at DATETIME NULL,
    last_searched_at DATETIME NULL,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME NULL DEFAULT NULL,
    CONSTRAINT fk_content_metrics_post_id FOREIGN KEY (post_id) REFERENCES posts (id) ON DELETE CASCADE,
    INDEX idx_content_metrics_view_count (view_count),
    INDEX idx_content_metrics_search_count (search_count),
    INDEX idx_content_metrics_last_viewed_at (last_viewed_at),
    INDEX idx_content_metrics_last_searched_at (last_searched_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;