CREATE TABLE IF NOT EXISTS content_interactions (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    post_id BIGINT UNSIGNED NOT NULL,
    user_id BIGINT UNSIGNED NULL,
    guest_token VARCHAR(64) NULL,
    actor_key VARCHAR(80) NOT NULL,
    interaction_type VARCHAR(20) NOT NULL,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME NULL DEFAULT NULL,
    CONSTRAINT fk_content_interactions_post_id FOREIGN KEY (post_id) REFERENCES posts (id) ON DELETE CASCADE,
    CONSTRAINT fk_content_interactions_user_id FOREIGN KEY (user_id) REFERENCES users (id) ON DELETE SET NULL,
    UNIQUE KEY uq_content_interactions_actor (post_id, interaction_type, actor_key),
    INDEX idx_content_interactions_post_id (post_id),
    INDEX idx_content_interactions_user_id (user_id),
    INDEX idx_content_interactions_guest_token (guest_token),
    INDEX idx_content_interactions_type (interaction_type),
    INDEX idx_content_interactions_actor_key (actor_key)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;