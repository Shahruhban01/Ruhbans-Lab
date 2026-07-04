CREATE TABLE IF NOT EXISTS reading_history (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    post_id BIGINT UNSIGNED NOT NULL,
    user_id BIGINT UNSIGNED NULL,
    guest_token VARCHAR(64) NULL,
    actor_key VARCHAR(80) NOT NULL,
    view_count INT UNSIGNED NOT NULL DEFAULT 1,
    last_viewed_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME NULL DEFAULT NULL,
    CONSTRAINT fk_reading_history_post_id FOREIGN KEY (post_id) REFERENCES posts (id) ON DELETE CASCADE,
    CONSTRAINT fk_reading_history_user_id FOREIGN KEY (user_id) REFERENCES users (id) ON DELETE SET NULL,
    UNIQUE KEY uq_reading_history_actor (post_id, actor_key),
    INDEX idx_reading_history_post_id (post_id),
    INDEX idx_reading_history_user_id (user_id),
    INDEX idx_reading_history_guest_token (guest_token),
    INDEX idx_reading_history_last_viewed_at (last_viewed_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;