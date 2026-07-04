CREATE TABLE IF NOT EXISTS search_queries (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    search_term VARCHAR(255) NOT NULL,
    normalized_term VARCHAR(255) NOT NULL,
    filters_json JSON NULL,
    result_count INT UNSIGNED NOT NULL DEFAULT 0,
    ip_address VARCHAR(45) NULL,
    user_agent VARCHAR(255) NULL,
    referrer VARCHAR(255) NULL,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_search_queries_normalized_term (normalized_term),
    INDEX idx_search_queries_created_at (created_at),
    INDEX idx_search_queries_result_count (result_count)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;