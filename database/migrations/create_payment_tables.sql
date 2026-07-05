-- CREATE PAYMENT TABLES WITH COMPATIBLE COLLATION AND TYPE DECLARATIONS
CREATE TABLE IF NOT EXISTS payment_transactions (
    id bigint(20) unsigned AUTO_INCREMENT PRIMARY KEY,
    order_id bigint(20) unsigned NOT NULL,
    amount INT NOT NULL DEFAULT 0, -- Cents
    currency VARCHAR(10) NOT NULL DEFAULT 'USD',
    gateway VARCHAR(50) NOT NULL,
    gateway_transaction_id VARCHAR(100) NULL UNIQUE,
    status VARCHAR(30) NOT NULL DEFAULT 'pending',
    error_message TEXT NULL,
    created_at DATETIME NOT NULL,
    updated_at DATETIME NOT NULL,
    FOREIGN KEY (order_id) REFERENCES orders(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS gateway_logs (
    id bigint(20) unsigned AUTO_INCREMENT PRIMARY KEY,
    transaction_id bigint(20) unsigned NULL,
    direction VARCHAR(10) NOT NULL, -- inbound, outbound
    payload_json LONGTEXT NOT NULL,
    created_at DATETIME NOT NULL,
    FOREIGN KEY (transaction_id) REFERENCES payment_transactions(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS payment_settings (
    id bigint(20) unsigned AUTO_INCREMENT PRIMARY KEY,
    gateway_key VARCHAR(50) NOT NULL UNIQUE,
    is_active TINYINT(1) NOT NULL DEFAULT 1,
    config_json LONGTEXT NOT NULL,
    created_at DATETIME NOT NULL,
    updated_at DATETIME NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS payment_events (
    id bigint(20) unsigned AUTO_INCREMENT PRIMARY KEY,
    event_name VARCHAR(100) NOT NULL,
    status VARCHAR(30) NOT NULL,
    payload_json LONGTEXT NOT NULL,
    created_at DATETIME NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
