<?php

declare(strict_types=1);

function ded_schema_ensure(PDO $pdo): void
{
    static $done = false;
    if ($done) {
        return;
    }
    $done = true;

    ded_schema_ensure_auxiliary($pdo);
    ded_schema_ensure_shop($pdo);
    require_once __DIR__ . '/ekstra.php';
    ded_extras_schema_ensure($pdo);
}

function ded_schema_ensure_auxiliary(PDO $pdo): void
{
    require_once __DIR__ . '/kimlik.php';
    require_once __DIR__ . '/medyameta.php';
    require_once __DIR__ . '/vitrinlayout.php';
    ded_panel_auth_ensure_table($pdo);
    ded_media_meta_ensure_table($pdo);
    ded_vitrin_layout_ensure_table($pdo);
}

function ded_schema_ensure_shop(PDO $pdo): void
{
    static $sql = null;
    if ($sql === null) {
        $sql = <<<'SQL'
CREATE TABLE IF NOT EXISTS ded_shop_settings (
  id TINYINT UNSIGNED NOT NULL PRIMARY KEY,
  settings_json JSON NOT NULL,
  updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT IGNORE INTO ded_shop_settings (id, settings_json) VALUES (1, '{}');

CREATE TABLE IF NOT EXISTS ded_orders (
  id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
  order_number VARCHAR(32) NOT NULL,
  customer_email VARCHAR(255) NOT NULL,
  customer_name VARCHAR(255) NOT NULL,
  customer_phone VARCHAR(64) NOT NULL DEFAULT '',
  shipping_address_line TEXT,
  shipping_city VARCHAR(128) NOT NULL DEFAULT '',
  shipping_country VARCHAR(64) NOT NULL DEFAULT 'TR',
  cart_json JSON NOT NULL,
  subtotal DECIMAL(12,2) NOT NULL DEFAULT 0,
  discount_amount DECIMAL(12,2) NOT NULL DEFAULT 0,
  shipping_fee DECIMAL(12,2) NOT NULL DEFAULT 0,
  total DECIMAL(12,2) NOT NULL DEFAULT 0,
  currency VARCHAR(8) NOT NULL DEFAULT 'TRY',
  coupon_code VARCHAR(64) NULL,
  status VARCHAR(32) NOT NULL DEFAULT 'pending',
  payment_status VARCHAR(32) NOT NULL DEFAULT 'unpaid',
  payment_method VARCHAR(64) NOT NULL DEFAULT '',
  tracking_number VARCHAR(128) NOT NULL DEFAULT '',
  carrier VARCHAR(128) NOT NULL DEFAULT '',
  admin_notes TEXT,
  created_at DATETIME NULL DEFAULT NULL,
  updated_at DATETIME NULL DEFAULT NULL,
  UNIQUE KEY uq_order_number (order_number),
  KEY idx_order_created (created_at),
  KEY idx_order_status (status),
  KEY idx_order_payment (payment_status)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS ded_order_items (
  id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
  order_id BIGINT UNSIGNED NOT NULL,
  product_slug VARCHAR(181) NOT NULL,
  product_title VARCHAR(512) NOT NULL,
  unit_price DECIMAL(12,2) NOT NULL,
  qty INT NOT NULL,
  variant_label VARCHAR(255) NOT NULL DEFAULT '',
  line_total DECIMAL(12,2) NOT NULL,
  CONSTRAINT fk_oi_order FOREIGN KEY (order_id) REFERENCES ded_orders(id) ON DELETE CASCADE,
  KEY idx_oi_order (order_id),
  KEY idx_oi_slug (product_slug)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS ded_coupons (
  id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
  code VARCHAR(64) NOT NULL,
  discount_type ENUM('percent','fixed') NOT NULL DEFAULT 'percent',
  discount_value DECIMAL(12,2) NOT NULL DEFAULT 0,
  min_subtotal DECIMAL(12,2) NOT NULL DEFAULT 0,
  max_uses INT NULL,
  used_count INT NOT NULL DEFAULT 0,
  starts_at DATE NULL,
  ends_at DATE NULL,
  active TINYINT(1) NOT NULL DEFAULT 1,
  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  UNIQUE KEY uq_coupon_code (code)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS ded_notification_log (
  id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
  channel ENUM('email','sms') NOT NULL,
  recipient VARCHAR(255) NOT NULL,
  subject VARCHAR(512) NOT NULL DEFAULT '',
  body_preview VARCHAR(512) NOT NULL DEFAULT '',
  status VARCHAR(32) NOT NULL DEFAULT 'queued',
  meta_json JSON NULL,
  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  KEY idx_nl_created (created_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
SQL;
    }

    $prev = $pdo->getAttribute(PDO::ATTR_ERRMODE);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    try {
        $pdo->setAttribute(PDO::MYSQL_ATTR_MULTI_STATEMENTS, true);
        $pdo->exec($sql);
    } catch (Throwable) {
    } finally {
        $pdo->setAttribute(PDO::ATTR_ERRMODE, $prev);
    }
}
