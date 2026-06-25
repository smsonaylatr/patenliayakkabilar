SET NAMES utf8mb4;

CREATE TABLE IF NOT EXISTS ded_site (
  id TINYINT UNSIGNED NOT NULL PRIMARY KEY,
  name VARCHAR(255) NOT NULL DEFAULT '',
  title VARCHAR(512) NOT NULL DEFAULT '',
  description TEXT,
  home_collection_heading VARCHAR(512) NOT NULL DEFAULT '',
  home_collection_subtext TEXT,
  home_collection_more_url VARCHAR(512) NOT NULL DEFAULT '#',
  CONSTRAINT chk_site_singleton CHECK (id = 1)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT IGNORE INTO ded_site (id, name, title, description, home_collection_heading, home_collection_subtext, home_collection_more_url) VALUES
(1, 'Laykidsofficial', 'Laykids Official | Premium Tekerlekli Çocuk Ayakkabıları', '', 'YETİŞKİNLER VE ÇOCUKLAR İÇİN', 'Ayakkabılarını değiştirmeyi unut! Sadece tek bir düğmeye basarak, yürüyüşün tadını çıkarmanı sağlayacak şık spor ayakkabılara ve patenlere sahip olacaksın.', '#');

CREATE TABLE IF NOT EXISTS ded_products (
  id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
  external_id VARCHAR(64) NULL,
  slug VARCHAR(190) NOT NULL,
  title VARCHAR(512) NOT NULL,
  description MEDIUMTEXT,
  brand VARCHAR(255) NOT NULL DEFAULT '',
  audience VARCHAR(32) NOT NULL DEFAULT '' COMMENT 'kiz|erkek|unisex|yetiskin — vitrin etiketi',
  price DECIMAL(12,2) NOT NULL DEFAULT 0,
  compare_at_price DECIMAL(12,2) NULL DEFAULT NULL COMMENT 'isteğe bağlı liste fiyatı (üstü çizili); dolu ise indirimli satış görünümü',
  currency VARCHAR(8) NOT NULL DEFAULT 'TRY',
  source_html VARCHAR(512) NULL,
  sort_order INT NOT NULL DEFAULT 0,
  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  UNIQUE KEY uq_product_slug (slug),
  KEY idx_product_sort (sort_order)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS ded_product_images (
  id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
  product_id BIGINT UNSIGNED NOT NULL,
  path VARCHAR(1024) NOT NULL,
  sort_order INT NOT NULL DEFAULT 0,
  CONSTRAINT fk_img_product FOREIGN KEY (product_id) REFERENCES ded_products(id) ON DELETE CASCADE,
  KEY idx_img_product (product_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS ded_product_variants (
  id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
  product_id BIGINT UNSIGNED NOT NULL,
  name VARCHAR(128) NOT NULL,
  price DECIMAL(12,2) NOT NULL DEFAULT 0,
  currency VARCHAR(8) NOT NULL DEFAULT 'TRY',
  sku VARCHAR(128) NULL,
  in_stock TINYINT(1) NOT NULL DEFAULT 1,
  stock_qty INT NOT NULL DEFAULT 0,
  sort_order INT NOT NULL DEFAULT 0,
  CONSTRAINT fk_var_product FOREIGN KEY (product_id) REFERENCES ded_products(id) ON DELETE CASCADE,
  KEY idx_var_product (product_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS ded_collections (
  id VARCHAR(190) NOT NULL PRIMARY KEY,
  title VARCHAR(512) NOT NULL,
  description TEXT,
  image_path VARCHAR(1024) NOT NULL DEFAULT '',
  source_html VARCHAR(512) NULL,
  sort_order INT NOT NULL DEFAULT 0,
  KEY idx_col_sort (sort_order)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS ded_collection_products (
  collection_id VARCHAR(190) NOT NULL,
  product_id BIGINT UNSIGNED NOT NULL,
  sort_order INT NOT NULL DEFAULT 0,
  PRIMARY KEY (collection_id, product_id),
  CONSTRAINT fk_cp_col FOREIGN KEY (collection_id) REFERENCES ded_collections(id) ON DELETE CASCADE,
  CONSTRAINT fk_cp_prod FOREIGN KEY (product_id) REFERENCES ded_products(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS ded_pages (
  slug VARCHAR(190) NOT NULL PRIMARY KEY,
  title VARCHAR(512) NOT NULL,
  description TEXT,
  source_html LONGTEXT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Mağaza: sipariş, kupon, bildirim, ayarlar (kurulum: tools/magazatablo.php)

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
  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  UNIQUE KEY uq_order_number (order_number),
  KEY idx_order_created (created_at),
  KEY idx_order_status (status),
  KEY idx_order_payment (payment_status)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS ded_order_items (
  id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
  order_id BIGINT UNSIGNED NOT NULL,
  product_slug VARCHAR(190) NOT NULL,
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

CREATE TABLE IF NOT EXISTS ded_panel_auth (
  id TINYINT UNSIGNED NOT NULL PRIMARY KEY,
  password_hash VARCHAR(255) NOT NULL,
  api_token VARCHAR(128) NULL,
  username VARCHAR(190) NOT NULL DEFAULT 'admin',
  email VARCHAR(255) NOT NULL DEFAULT '',
  avatar_path VARCHAR(512) NULL,
  updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  CONSTRAINT chk_panel_auth_singleton CHECK (id = 1)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS ded_media_meta (
  path VARCHAR(512) NOT NULL PRIMARY KEY,
  alt VARCHAR(255) NOT NULL DEFAULT '',
  updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS ded_newsletter_subscribers (
  id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
  email VARCHAR(255) NOT NULL,
  name VARCHAR(255) NOT NULL DEFAULT '',
  active TINYINT(1) NOT NULL DEFAULT 1,
  source VARCHAR(64) NOT NULL DEFAULT 'footer',
  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  UNIQUE KEY uq_nl_email (email),
  KEY idx_nl_created (created_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS ded_faq (
  id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
  question VARCHAR(512) NOT NULL,
  answer MEDIUMTEXT NOT NULL,
  sort_order INT NOT NULL DEFAULT 0,
  active TINYINT(1) NOT NULL DEFAULT 1,
  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  KEY idx_faq_sort (sort_order)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS ded_product_reviews (
  id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
  product_slug VARCHAR(190) NOT NULL,
  author_name VARCHAR(255) NOT NULL,
  author_email VARCHAR(255) NOT NULL DEFAULT '',
  rating TINYINT UNSIGNED NOT NULL DEFAULT 5,
  body TEXT NOT NULL,
  approved TINYINT(1) NOT NULL DEFAULT 0,
  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  KEY idx_rev_slug (product_slug),
  KEY idx_rev_approved (approved),
  KEY idx_rev_created (created_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
