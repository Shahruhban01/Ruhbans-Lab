CREATE TABLE IF NOT EXISTS activity_events (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id BIGINT UNSIGNED NULL,
    post_id BIGINT UNSIGNED NULL,
    actor_key VARCHAR(80) NOT NULL,
    event_type VARCHAR(30) NOT NULL,
    title VARCHAR(180) NOT NULL,
    body VARCHAR(255) NULL,
    url VARCHAR(255) NULL,
    metadata_json JSON NULL,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_activity_events_user_id FOREIGN KEY (user_id) REFERENCES users (id) ON DELETE SET NULL,
    CONSTRAINT fk_activity_events_post_id FOREIGN KEY (post_id) REFERENCES posts (id) ON DELETE SET NULL,
    INDEX idx_activity_events_user_id (user_id),
    INDEX idx_activity_events_post_id (post_id),
    INDEX idx_activity_events_event_type (event_type),
    INDEX idx_activity_events_created_at (created_at),
    INDEX idx_activity_events_actor_key (actor_key)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;