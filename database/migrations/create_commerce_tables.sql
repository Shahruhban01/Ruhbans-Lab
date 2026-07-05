-- CREATE COMMERCE TABLES WITH COMPATIBLE COLLATION AND TYPE DECLARATIONS
CREATE TABLE IF NOT EXISTS coupons (
    id bigint(20) unsigned AUTO_INCREMENT PRIMARY KEY,
    code VARCHAR(50) NOT NULL UNIQUE,
    discount_percentage INT NOT NULL DEFAULT 0,
    expires_at DATETIME NULL,
    is_active TINYINT(1) NOT NULL DEFAULT 1,
    created_at DATETIME NOT NULL,
    updated_at DATETIME NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS orders (
    id bigint(20) unsigned AUTO_INCREMENT PRIMARY KEY,
    user_id bigint(20) unsigned NOT NULL,
    coupon_id bigint(20) unsigned NULL,
    total_amount INT NOT NULL DEFAULT 0, -- Price in cents
    status VARCHAR(30) NOT NULL DEFAULT 'pending', -- pending, paid, cancelled, refunded
    created_at DATETIME NOT NULL,
    updated_at DATETIME NOT NULL,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (coupon_id) REFERENCES coupons(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS order_items (
    id bigint(20) unsigned AUTO_INCREMENT PRIMARY KEY,
    order_id bigint(20) unsigned NOT NULL,
    post_id bigint(20) unsigned NOT NULL, -- The product/post in the lab
    price_cents INT NOT NULL DEFAULT 0,
    FOREIGN KEY (order_id) REFERENCES orders(id) ON DELETE CASCADE,
    FOREIGN KEY (post_id) REFERENCES posts(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS purchases (
    id bigint(20) unsigned AUTO_INCREMENT PRIMARY KEY,
    user_id bigint(20) unsigned NOT NULL,
    post_id bigint(20) unsigned NOT NULL,
    order_id bigint(20) unsigned NULL,
    purchase_type VARCHAR(30) NOT NULL DEFAULT 'one_time', -- one_time, plan_included
    created_at DATETIME NOT NULL,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (post_id) REFERENCES posts(id) ON DELETE CASCADE,
    FOREIGN KEY (order_id) REFERENCES orders(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS licenses (
    id bigint(20) unsigned AUTO_INCREMENT PRIMARY KEY,
    purchase_id bigint(20) unsigned NOT NULL,
    license_key VARCHAR(100) NOT NULL UNIQUE,
    status VARCHAR(30) NOT NULL DEFAULT 'active', -- active, expired, suspended
    expires_at DATETIME NULL,
    created_at DATETIME NOT NULL,
    FOREIGN KEY (purchase_id) REFERENCES purchases(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS transactions (
    id bigint(20) unsigned AUTO_INCREMENT PRIMARY KEY,
    order_id bigint(20) unsigned NOT NULL,
    provider VARCHAR(50) NOT NULL DEFAULT 'razorpay',
    transaction_reference VARCHAR(100) NOT NULL UNIQUE,
    status VARCHAR(30) NOT NULL DEFAULT 'success', -- success, failed, refunded
    amount INT NOT NULL DEFAULT 0,
    created_at DATETIME NOT NULL,
    FOREIGN KEY (order_id) REFERENCES orders(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
