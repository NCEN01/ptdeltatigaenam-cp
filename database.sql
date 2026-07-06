-- =====================================================================
--  DATABASE SCHEMA — Website Company Profile PT DELTA TIGA ENAM
--  Stack target : Laravel 11 + Filament 3 + MySQL 8 + Midtrans
--  Charset       : utf8mb4 / utf8mb4_unicode_ci   | Engine: InnoDB
--  Fitur tambahan: multi-bahasa (ID/EN), auth admin & pelanggan,
--                  standar media, responsif/PWA, keamanan.
--
--  CATATAN MULTI-BAHASA:
--  Kolom bertipe JSON yang diberi komentar "i18n" menyimpan terjemahan
--  per-locale memakai spatie/laravel-translatable, contoh isi:
--      {"id": "Teks Indonesia", "en": "English Text"}
--  String UI statis (menu, tombol) disimpan di lang files Laravel
--  (resources/lang/id & resources/lang/en), bukan di database.
-- =====================================================================

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

-- =====================================================================
--  BAGIAN 1 — AUTENTIKASI: ADMIN, PELANGGAN, & TOKEN API
-- =====================================================================

-- 1a. Admin panel users (guard: web/admin)
CREATE TABLE `users` (
  `id`                BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `name`              VARCHAR(255) NOT NULL,
  `email`             VARCHAR(255) NOT NULL,
  `email_verified_at` TIMESTAMP NULL DEFAULT NULL,
  `password`          VARCHAR(255) NOT NULL,
  `avatar`            VARCHAR(255) NULL DEFAULT NULL,
  `is_active`         TINYINT(1) NOT NULL DEFAULT 1,
  `two_factor_secret` TEXT NULL DEFAULT NULL,            -- opsional 2FA admin
  `two_factor_confirmed_at` TIMESTAMP NULL DEFAULT NULL,
  `remember_token`    VARCHAR(100) NULL DEFAULT NULL,
  `created_at`        TIMESTAMP NULL DEFAULT NULL,
  `updated_at`        TIMESTAMP NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `users_email_unique` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 1b. Pelanggan / user publik yang membeli layanan (guard terpisah: customer)
CREATE TABLE `customers` (
  `id`                BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `name`              VARCHAR(255) NOT NULL,
  `email`             VARCHAR(255) NOT NULL,
  `email_verified_at` TIMESTAMP NULL DEFAULT NULL,        -- verifikasi email
  `password`          VARCHAR(255) NOT NULL,
  `phone`             VARCHAR(50) NULL DEFAULT NULL,
  `company`           VARCHAR(200) NULL DEFAULT NULL,
  `avatar`            VARCHAR(255) NULL DEFAULT NULL,
  `preferred_locale`  VARCHAR(5) NOT NULL DEFAULT 'id',   -- bahasa pilihan user
  `is_active`         TINYINT(1) NOT NULL DEFAULT 1,
  `remember_token`    VARCHAR(100) NULL DEFAULT NULL,
  `last_login_at`     TIMESTAMP NULL DEFAULT NULL,
  `created_at`        TIMESTAMP NULL DEFAULT NULL,
  `updated_at`        TIMESTAMP NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `customers_email_unique` (`email`),
  KEY `customers_active_idx` (`is_active`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 1c. Reset password (dipakai bersama; kolom guard membedakan admin/customer)
CREATE TABLE `password_reset_tokens` (
  `email`      VARCHAR(255) NOT NULL,
  `token`      VARCHAR(255) NOT NULL,
  `guard`      VARCHAR(20) NOT NULL DEFAULT 'web',    -- web | customer
  `created_at` TIMESTAMP NULL DEFAULT NULL,
  PRIMARY KEY (`email`,`guard`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 1d. Token API (Sanctum)
CREATE TABLE `personal_access_tokens` (
  `id`             BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `tokenable_type` VARCHAR(255) NOT NULL,
  `tokenable_id`   BIGINT UNSIGNED NOT NULL,
  `name`           VARCHAR(255) NOT NULL,
  `token`          VARCHAR(64) NOT NULL,
  `abilities`      TEXT NULL DEFAULT NULL,
  `last_used_at`   TIMESTAMP NULL DEFAULT NULL,
  `expires_at`     TIMESTAMP NULL DEFAULT NULL,
  `created_at`     TIMESTAMP NULL DEFAULT NULL,
  `updated_at`     TIMESTAMP NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `pat_token_unique` (`token`),
  KEY `pat_tokenable_idx` (`tokenable_type`,`tokenable_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `sessions` (
  `id`            VARCHAR(255) NOT NULL,
  `user_id`       BIGINT UNSIGNED NULL DEFAULT NULL,
  `ip_address`    VARCHAR(45) NULL DEFAULT NULL,
  `user_agent`    TEXT NULL DEFAULT NULL,
  `payload`       LONGTEXT NOT NULL,
  `last_activity` INT NOT NULL,
  PRIMARY KEY (`id`),
  KEY `sessions_user_id_index` (`user_id`),
  KEY `sessions_last_activity_index` (`last_activity`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================================
--  BAGIAN 2 — ROLE & PERMISSION (spatie/laravel-permission, guard web)
-- =====================================================================

CREATE TABLE `roles` (
  `id`         BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `name`       VARCHAR(125) NOT NULL,
  `guard_name` VARCHAR(125) NOT NULL DEFAULT 'web',
  `created_at` TIMESTAMP NULL DEFAULT NULL,
  `updated_at` TIMESTAMP NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `roles_name_guard_name_unique` (`name`,`guard_name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `permissions` (
  `id`         BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `name`       VARCHAR(125) NOT NULL,
  `guard_name` VARCHAR(125) NOT NULL DEFAULT 'web',
  `created_at` TIMESTAMP NULL DEFAULT NULL,
  `updated_at` TIMESTAMP NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `permissions_name_guard_name_unique` (`name`,`guard_name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `model_has_roles` (
  `role_id`    BIGINT UNSIGNED NOT NULL,
  `model_type` VARCHAR(255) NOT NULL,
  `model_id`   BIGINT UNSIGNED NOT NULL,
  PRIMARY KEY (`role_id`,`model_id`,`model_type`),
  KEY `model_has_roles_model_id_model_type_index` (`model_id`,`model_type`),
  CONSTRAINT `model_has_roles_role_id_fk`
    FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `model_has_permissions` (
  `permission_id` BIGINT UNSIGNED NOT NULL,
  `model_type`    VARCHAR(255) NOT NULL,
  `model_id`      BIGINT UNSIGNED NOT NULL,
  PRIMARY KEY (`permission_id`,`model_id`,`model_type`),
  KEY `model_has_permissions_model_id_model_type_index` (`model_id`,`model_type`),
  CONSTRAINT `model_has_permissions_permission_id_fk`
    FOREIGN KEY (`permission_id`) REFERENCES `permissions` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `role_has_permissions` (
  `permission_id` BIGINT UNSIGNED NOT NULL,
  `role_id`       BIGINT UNSIGNED NOT NULL,
  PRIMARY KEY (`permission_id`,`role_id`),
  CONSTRAINT `role_has_permissions_permission_id_fk`
    FOREIGN KEY (`permission_id`) REFERENCES `permissions` (`id`) ON DELETE CASCADE,
  CONSTRAINT `role_has_permissions_role_id_fk`
    FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================================
--  BAGIAN 3 — PENGATURAN SITUS & PROFIL PERUSAHAAN
-- =====================================================================

CREATE TABLE `settings` (
  `id`         BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `group`      VARCHAR(100) NOT NULL DEFAULT 'general',
  `key`        VARCHAR(150) NOT NULL,
  `value`      LONGTEXT NULL DEFAULT NULL,
  `type`       VARCHAR(50) NOT NULL DEFAULT 'text',
  `created_at` TIMESTAMP NULL DEFAULT NULL,
  `updated_at` TIMESTAMP NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `settings_key_unique` (`key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================================
--  BAGIAN 4 — LAYANAN (FITUR INTI) — multi-bahasa
-- =====================================================================

CREATE TABLE `service_categories` (
  `id`               BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `name`             JSON NOT NULL,
  `slug`             VARCHAR(170) NOT NULL,
  `short_description` JSON NULL DEFAULT NULL,
  `description`      JSON NULL DEFAULT NULL,
  `icon`             VARCHAR(100) NULL DEFAULT NULL,
  `image`            VARCHAR(255) NULL DEFAULT NULL,
  `is_featured`      TINYINT(1) NOT NULL DEFAULT 0,
  `sort_order`       INT NOT NULL DEFAULT 0,
  `is_active`        TINYINT(1) NOT NULL DEFAULT 1,
  `meta_title`       JSON NULL DEFAULT NULL,
  `meta_description` JSON NULL DEFAULT NULL,
  `created_at`       TIMESTAMP NULL DEFAULT NULL,
  `updated_at`       TIMESTAMP NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `service_categories_slug_unique` (`slug`),
  KEY `service_categories_active_featured_idx` (`is_active`,`is_featured`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `services` (
  `id`                  BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `service_category_id` BIGINT UNSIGNED NOT NULL,
  `title`               JSON NOT NULL,
  `slug`                VARCHAR(220) NOT NULL,
  `short_description`   JSON NULL DEFAULT NULL,
  `description`         JSON NULL DEFAULT NULL,
  `image`               VARCHAR(255) NULL DEFAULT NULL,
  `price`               DECIMAL(15,2) NOT NULL DEFAULT 0.00,
  `price_label`         JSON NULL DEFAULT NULL,
  `duration`            JSON NULL DEFAULT NULL,
  `location`            VARCHAR(200) NULL DEFAULT NULL,
  `is_purchasable`      TINYINT(1) NOT NULL DEFAULT 1,
  `is_featured`         TINYINT(1) NOT NULL DEFAULT 0,
  `sort_order`          INT NOT NULL DEFAULT 0,
  `is_active`           TINYINT(1) NOT NULL DEFAULT 1,
  `meta_title`          JSON NULL DEFAULT NULL,
  `meta_description`    JSON NULL DEFAULT NULL,
  `created_at`          TIMESTAMP NULL DEFAULT NULL,
  `updated_at`          TIMESTAMP NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `services_slug_unique` (`slug`),
  KEY `services_category_idx` (`service_category_id`),
  KEY `services_active_purchasable_idx` (`is_active`,`is_purchasable`),
  CONSTRAINT `services_category_fk`
    FOREIGN KEY (`service_category_id`) REFERENCES `service_categories` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `service_activities` (
  `id`          BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `service_id`  BIGINT UNSIGNED NOT NULL,
  `title`       JSON NOT NULL,
  `description` JSON NULL DEFAULT NULL,
  `sort_order`  INT NOT NULL DEFAULT 0,
  `created_at`  TIMESTAMP NULL DEFAULT NULL,
  `updated_at`  TIMESTAMP NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `service_activities_service_idx` (`service_id`),
  CONSTRAINT `service_activities_service_fk`
    FOREIGN KEY (`service_id`) REFERENCES `services` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `service_schedules` (
  `id`             BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `service_id`     BIGINT UNSIGNED NOT NULL,
  `start_date`     DATE NOT NULL,
  `end_date`       DATE NULL DEFAULT NULL,
  `start_time`     TIME NULL DEFAULT NULL,
  `end_time`       TIME NULL DEFAULT NULL,
  `location`       VARCHAR(200) NULL DEFAULT NULL,
  `mode`           ENUM('offline','online','hybrid') NOT NULL DEFAULT 'offline',
  `quota`          INT NULL DEFAULT NULL,
  `seats_taken`    INT NOT NULL DEFAULT 0,
  `price_override` DECIMAL(15,2) NULL DEFAULT NULL,
  `is_active`      TINYINT(1) NOT NULL DEFAULT 1,
  `created_at`     TIMESTAMP NULL DEFAULT NULL,
  `updated_at`     TIMESTAMP NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `service_schedules_service_idx` (`service_id`),
  KEY `service_schedules_date_idx` (`start_date`),
  CONSTRAINT `service_schedules_service_fk`
    FOREIGN KEY (`service_id`) REFERENCES `services` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================================
--  BAGIAN 5 — BANNER (multi-bahasa, dengan varian mobile)
-- =====================================================================

CREATE TABLE `banners` (
  `id`                  BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `title`               JSON NULL DEFAULT NULL,
  `subtitle`            JSON NULL DEFAULT NULL,
  `image`               VARCHAR(255) NOT NULL,
  `image_mobile`        VARCHAR(255) NULL DEFAULT NULL,
  `link_url`            VARCHAR(255) NULL DEFAULT NULL,
  `button_text`         JSON NULL DEFAULT NULL,
  `placement`           ENUM('home_hero','home_section','service_category','service','blog','portfolio','about','global') NOT NULL DEFAULT 'home_hero',
  `service_category_id` BIGINT UNSIGNED NULL DEFAULT NULL,
  `sort_order`          INT NOT NULL DEFAULT 0,
  `is_active`           TINYINT(1) NOT NULL DEFAULT 1,
  `starts_at`           TIMESTAMP NULL DEFAULT NULL,
  `ends_at`             TIMESTAMP NULL DEFAULT NULL,
  `created_at`          TIMESTAMP NULL DEFAULT NULL,
  `updated_at`          TIMESTAMP NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `banners_placement_idx` (`placement`,`is_active`),
  KEY `banners_category_idx` (`service_category_id`),
  CONSTRAINT `banners_category_fk`
    FOREIGN KEY (`service_category_id`) REFERENCES `service_categories` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================================
--  BAGIAN 6 — KEMITRAAN
-- =====================================================================

CREATE TABLE `partners` (
  `id`          BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `name`        VARCHAR(200) NOT NULL,
  `logo`        VARCHAR(255) NULL DEFAULT NULL,
  `website_url` VARCHAR(255) NULL DEFAULT NULL,
  `description` JSON NULL DEFAULT NULL,
  `sort_order`  INT NOT NULL DEFAULT 0,
  `is_active`   TINYINT(1) NOT NULL DEFAULT 1,
  `created_at`  TIMESTAMP NULL DEFAULT NULL,
  `updated_at`  TIMESTAMP NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `partnership_packages` (
  `id`             BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `tier`           ENUM('blue','silver','gold','platinum') NOT NULL,
  `name`           JSON NOT NULL,
  `slug`           VARCHAR(120) NOT NULL,
  `tagline`        JSON NULL DEFAULT NULL,
  `description`    JSON NULL DEFAULT NULL,
  `features`       JSON NULL DEFAULT NULL,
  `price`          DECIMAL(15,2) NULL DEFAULT NULL,
  `price_note`     JSON NULL DEFAULT NULL,
  `color`          VARCHAR(20) NULL DEFAULT NULL,
  `is_highlighted` TINYINT(1) NOT NULL DEFAULT 0,
  `sort_order`     INT NOT NULL DEFAULT 0,
  `is_active`      TINYINT(1) NOT NULL DEFAULT 1,
  `created_at`     TIMESTAMP NULL DEFAULT NULL,
  `updated_at`     TIMESTAMP NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `partnership_packages_tier_unique` (`tier`),
  UNIQUE KEY `partnership_packages_slug_unique` (`slug`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `partnership_benefits` (
  `id`          BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `title`       JSON NOT NULL,
  `description` JSON NULL DEFAULT NULL,
  `icon`        VARCHAR(100) NULL DEFAULT NULL,
  `sort_order`  INT NOT NULL DEFAULT 0,
  `is_active`   TINYINT(1) NOT NULL DEFAULT 1,
  `created_at`  TIMESTAMP NULL DEFAULT NULL,
  `updated_at`  TIMESTAMP NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `partnership_registrations` (
  `id`                     BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `registration_number`    VARCHAR(50) NOT NULL,
  `company_name`           VARCHAR(200) NOT NULL,
  `company_address`        TEXT NOT NULL,
  `pic_name`               VARCHAR(150) NOT NULL,
  `pic_position`           VARCHAR(150) NULL DEFAULT NULL,
  `phone`                  VARCHAR(50) NOT NULL,
  `email`                  VARCHAR(150) NOT NULL,
  `partnership_package_id` BIGINT UNSIGNED NULL DEFAULT NULL,
  `preferred_meeting_at`   DATETIME NULL DEFAULT NULL,
  `alternative_meeting_at` DATETIME NULL DEFAULT NULL,
  `notes`                  TEXT NULL DEFAULT NULL,
  `status`                 ENUM('baru','dihubungi','meeting_dijadwalkan','penawaran_dikirim','invoice_diterbitkan','lunas','selesai','dibatalkan') NOT NULL DEFAULT 'baru',
  `assigned_to`            BIGINT UNSIGNED NULL DEFAULT NULL,
  `locale`                 VARCHAR(5) NOT NULL DEFAULT 'id',
  `is_read`                TINYINT(1) NOT NULL DEFAULT 0,
  `created_at`             TIMESTAMP NULL DEFAULT NULL,
  `updated_at`             TIMESTAMP NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `partnership_registrations_number_unique` (`registration_number`),
  KEY `partnership_registrations_status_idx` (`status`),
  KEY `partnership_registrations_package_idx` (`partnership_package_id`),
  KEY `partnership_registrations_assigned_idx` (`assigned_to`),
  CONSTRAINT `partnership_registrations_package_fk`
    FOREIGN KEY (`partnership_package_id`) REFERENCES `partnership_packages` (`id`) ON DELETE SET NULL,
  CONSTRAINT `partnership_registrations_assigned_fk`
    FOREIGN KEY (`assigned_to`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `invoices` (
  `id`                          BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `invoice_number`              VARCHAR(50) NOT NULL,
  `partnership_registration_id` BIGINT UNSIGNED NULL DEFAULT NULL,
  `bill_to_company`             VARCHAR(200) NOT NULL,
  `bill_to_address`             TEXT NULL DEFAULT NULL,
  `bill_to_pic`                 VARCHAR(150) NULL DEFAULT NULL,
  `bill_to_email`               VARCHAR(150) NULL DEFAULT NULL,
  `description`                 TEXT NULL DEFAULT NULL,
  `subtotal`                    DECIMAL(15,2) NOT NULL DEFAULT 0.00,
  `tax`                         DECIMAL(15,2) NOT NULL DEFAULT 0.00,
  `total`                       DECIMAL(15,2) NOT NULL DEFAULT 0.00,
  `currency`                    VARCHAR(5) NOT NULL DEFAULT 'IDR',
  `status`                      ENUM('draft','terkirim','lunas','jatuh_tempo','dibatalkan') NOT NULL DEFAULT 'draft',
  `issued_date`                 DATE NULL DEFAULT NULL,
  `due_date`                    DATE NULL DEFAULT NULL,
  `paid_at`                     TIMESTAMP NULL DEFAULT NULL,
  `file_path`                   VARCHAR(255) NULL DEFAULT NULL,
  `notes`                       TEXT NULL DEFAULT NULL,
  `created_by`                  BIGINT UNSIGNED NULL DEFAULT NULL,
  `created_at`                  TIMESTAMP NULL DEFAULT NULL,
  `updated_at`                  TIMESTAMP NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `invoices_number_unique` (`invoice_number`),
  KEY `invoices_status_idx` (`status`),
  KEY `invoices_registration_idx` (`partnership_registration_id`),
  KEY `invoices_creator_idx` (`created_by`),
  CONSTRAINT `invoices_registration_fk`
    FOREIGN KEY (`partnership_registration_id`) REFERENCES `partnership_registrations` (`id`) ON DELETE SET NULL,
  CONSTRAINT `invoices_creator_fk`
    FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `invoice_items` (
  `id`          BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `invoice_id`  BIGINT UNSIGNED NOT NULL,
  `description` VARCHAR(255) NOT NULL,
  `quantity`    INT NOT NULL DEFAULT 1,
  `unit_price`  DECIMAL(15,2) NOT NULL DEFAULT 0.00,
  `amount`      DECIMAL(15,2) NOT NULL DEFAULT 0.00,
  `sort_order`  INT NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  KEY `invoice_items_invoice_idx` (`invoice_id`),
  CONSTRAINT `invoice_items_invoice_fk`
    FOREIGN KEY (`invoice_id`) REFERENCES `invoices` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================================
--  BAGIAN 7 — BLOG (multi-bahasa)
-- =====================================================================

CREATE TABLE `blog_categories` (
  `id`         BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `name`       JSON NOT NULL,
  `slug`       VARCHAR(170) NOT NULL,
  `sort_order` INT NOT NULL DEFAULT 0,
  `is_active`  TINYINT(1) NOT NULL DEFAULT 1,
  `created_at` TIMESTAMP NULL DEFAULT NULL,
  `updated_at` TIMESTAMP NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `blog_categories_slug_unique` (`slug`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `blog_posts` (
  `id`               BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `blog_category_id` BIGINT UNSIGNED NULL DEFAULT NULL,
  `author_id`        BIGINT UNSIGNED NULL DEFAULT NULL,
  `title`            JSON NOT NULL,
  `slug`             VARCHAR(280) NOT NULL,
  `excerpt`          JSON NULL DEFAULT NULL,
  `content`          JSON NULL DEFAULT NULL,
  `featured_image`   VARCHAR(255) NULL DEFAULT NULL,
  `banner_image`     VARCHAR(255) NULL DEFAULT NULL,
  `status`           ENUM('draft','published','archived') NOT NULL DEFAULT 'draft',
  `is_featured`      TINYINT(1) NOT NULL DEFAULT 0,
  `views`            INT UNSIGNED NOT NULL DEFAULT 0,
  `published_at`     TIMESTAMP NULL DEFAULT NULL,
  `meta_title`       JSON NULL DEFAULT NULL,
  `meta_description` JSON NULL DEFAULT NULL,
  `created_at`       TIMESTAMP NULL DEFAULT NULL,
  `updated_at`       TIMESTAMP NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `blog_posts_slug_unique` (`slug`),
  KEY `blog_posts_status_published_idx` (`status`,`published_at`),
  KEY `blog_posts_category_idx` (`blog_category_id`),
  KEY `blog_posts_author_idx` (`author_id`),
  CONSTRAINT `blog_posts_category_fk`
    FOREIGN KEY (`blog_category_id`) REFERENCES `blog_categories` (`id`) ON DELETE SET NULL,
  CONSTRAINT `blog_posts_author_fk`
    FOREIGN KEY (`author_id`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `blog_tags` (
  `id`   BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` JSON NOT NULL,
  `slug` VARCHAR(120) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `blog_tags_slug_unique` (`slug`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `blog_post_tag` (
  `blog_post_id` BIGINT UNSIGNED NOT NULL,
  `blog_tag_id`  BIGINT UNSIGNED NOT NULL,
  PRIMARY KEY (`blog_post_id`,`blog_tag_id`),
  CONSTRAINT `bpt_post_fk` FOREIGN KEY (`blog_post_id`) REFERENCES `blog_posts` (`id`) ON DELETE CASCADE,
  CONSTRAINT `bpt_tag_fk`  FOREIGN KEY (`blog_tag_id`)  REFERENCES `blog_tags` (`id`)  ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================================
--  BAGIAN 8 — PORTOFOLIO, KLIEN & TESTIMONI (multi-bahasa)
-- =====================================================================

CREATE TABLE `portfolios` (
  `id`                  BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `title`               JSON NOT NULL,
  `slug`                VARCHAR(280) NOT NULL,
  `client_name`         VARCHAR(200) NULL DEFAULT NULL,
  `service_category_id` BIGINT UNSIGNED NULL DEFAULT NULL,
  `short_description`   JSON NULL DEFAULT NULL,
  `content`             JSON NULL DEFAULT NULL,
  `cover_image`         VARCHAR(255) NULL DEFAULT NULL,
  `project_date`        DATE NULL DEFAULT NULL,
  `is_featured`         TINYINT(1) NOT NULL DEFAULT 0,
  `sort_order`          INT NOT NULL DEFAULT 0,
  `is_active`           TINYINT(1) NOT NULL DEFAULT 1,
  `created_at`          TIMESTAMP NULL DEFAULT NULL,
  `updated_at`          TIMESTAMP NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `portfolios_slug_unique` (`slug`),
  KEY `portfolios_category_idx` (`service_category_id`),
  CONSTRAINT `portfolios_category_fk`
    FOREIGN KEY (`service_category_id`) REFERENCES `service_categories` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `portfolio_images` (
  `id`           BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `portfolio_id` BIGINT UNSIGNED NOT NULL,
  `image`        VARCHAR(255) NOT NULL,
  `caption`      JSON NULL DEFAULT NULL,
  `sort_order`   INT NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  KEY `portfolio_images_portfolio_idx` (`portfolio_id`),
  CONSTRAINT `portfolio_images_portfolio_fk`
    FOREIGN KEY (`portfolio_id`) REFERENCES `portfolios` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `clients` (
  `id`          BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `name`        VARCHAR(200) NOT NULL,
  `logo`        VARCHAR(255) NULL DEFAULT NULL,
  `website_url` VARCHAR(255) NULL DEFAULT NULL,
  `sort_order`  INT NOT NULL DEFAULT 0,
  `is_active`   TINYINT(1) NOT NULL DEFAULT 1,
  `created_at`  TIMESTAMP NULL DEFAULT NULL,
  `updated_at`  TIMESTAMP NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `testimonials` (
  `id`              BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `client_id`       BIGINT UNSIGNED NULL DEFAULT NULL,
  `portfolio_id`    BIGINT UNSIGNED NULL DEFAULT NULL,
  `author_name`     VARCHAR(150) NOT NULL,
  `author_position` JSON NULL DEFAULT NULL,
  `author_company`  VARCHAR(200) NULL DEFAULT NULL,
  `author_photo`    VARCHAR(255) NULL DEFAULT NULL,
  `content`         JSON NOT NULL,
  `rating`          TINYINT NULL DEFAULT NULL,
  `is_featured`     TINYINT(1) NOT NULL DEFAULT 0,
  `is_active`       TINYINT(1) NOT NULL DEFAULT 1,
  `sort_order`      INT NOT NULL DEFAULT 0,
  `created_at`      TIMESTAMP NULL DEFAULT NULL,
  `updated_at`      TIMESTAMP NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `testimonials_client_idx` (`client_id`),
  KEY `testimonials_portfolio_idx` (`portfolio_id`),
  CONSTRAINT `testimonials_client_fk`
    FOREIGN KEY (`client_id`) REFERENCES `clients` (`id`) ON DELETE SET NULL,
  CONSTRAINT `testimonials_portfolio_fk`
    FOREIGN KEY (`portfolio_id`) REFERENCES `portfolios` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================================
--  BAGIAN 9 — PESANAN & PEMBAYARAN (MIDTRANS)
-- =====================================================================

CREATE TABLE `orders` (
  `id`                  BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `order_number`        VARCHAR(50) NOT NULL,
  `customer_id`         BIGINT UNSIGNED NULL DEFAULT NULL,
  `service_id`          BIGINT UNSIGNED NULL DEFAULT NULL,
  `service_schedule_id` BIGINT UNSIGNED NULL DEFAULT NULL,
  `customer_name`       VARCHAR(150) NOT NULL,
  `customer_email`      VARCHAR(150) NOT NULL,
  `customer_phone`      VARCHAR(50) NOT NULL,
  `customer_company`    VARCHAR(200) NULL DEFAULT NULL,
  `quantity`            INT NOT NULL DEFAULT 1,
  `unit_price`          DECIMAL(15,2) NOT NULL DEFAULT 0.00,
  `subtotal`            DECIMAL(15,2) NOT NULL DEFAULT 0.00,
  `tax`                 DECIMAL(15,2) NOT NULL DEFAULT 0.00,
  `total_amount`        DECIMAL(15,2) NOT NULL DEFAULT 0.00,
  `notes`               TEXT NULL DEFAULT NULL,
  `status`              ENUM('pending','paid','expired','cancelled','failed','refunded') NOT NULL DEFAULT 'pending',
  `paid_at`             TIMESTAMP NULL DEFAULT NULL,
  `created_at`          TIMESTAMP NULL DEFAULT NULL,
  `updated_at`          TIMESTAMP NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `orders_order_number_unique` (`order_number`),
  KEY `orders_status_idx` (`status`),
  KEY `orders_customer_idx` (`customer_id`),
  KEY `orders_service_idx` (`service_id`),
  KEY `orders_schedule_idx` (`service_schedule_id`),
  KEY `orders_email_idx` (`customer_email`),
  CONSTRAINT `orders_customer_fk`
    FOREIGN KEY (`customer_id`) REFERENCES `customers` (`id`) ON DELETE SET NULL,
  CONSTRAINT `orders_service_fk`
    FOREIGN KEY (`service_id`) REFERENCES `services` (`id`) ON DELETE SET NULL,
  CONSTRAINT `orders_schedule_fk`
    FOREIGN KEY (`service_schedule_id`) REFERENCES `service_schedules` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `transactions` (
  `id`                 BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `order_id`           BIGINT UNSIGNED NOT NULL,
  `midtrans_order_id`  VARCHAR(100) NOT NULL,
  `transaction_id`     VARCHAR(100) NULL DEFAULT NULL,
  `snap_token`         VARCHAR(255) NULL DEFAULT NULL,
  `payment_type`       VARCHAR(50) NULL DEFAULT NULL,
  `gross_amount`       DECIMAL(15,2) NOT NULL DEFAULT 0.00,
  `transaction_status` VARCHAR(50) NULL DEFAULT NULL,
  `fraud_status`       VARCHAR(50) NULL DEFAULT NULL,
  `status_code`        VARCHAR(10) NULL DEFAULT NULL,
  `va_number`          VARCHAR(100) NULL DEFAULT NULL,
  `bank`               VARCHAR(50) NULL DEFAULT NULL,
  `payment_code`       VARCHAR(100) NULL DEFAULT NULL,
  `signature_key`      VARCHAR(255) NULL DEFAULT NULL,
  `transaction_time`   TIMESTAMP NULL DEFAULT NULL,
  `settlement_time`    TIMESTAMP NULL DEFAULT NULL,
  `expiry_time`        TIMESTAMP NULL DEFAULT NULL,
  `raw_response`       JSON NULL DEFAULT NULL,
  `created_at`         TIMESTAMP NULL DEFAULT NULL,
  `updated_at`         TIMESTAMP NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `transactions_order_idx` (`order_id`),
  KEY `transactions_midtrans_order_idx` (`midtrans_order_id`),
  KEY `transactions_status_idx` (`transaction_status`),
  CONSTRAINT `transactions_order_fk`
    FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================================
--  BAGIAN 10 — PESAN KONTAK
-- =====================================================================

CREATE TABLE `contact_messages` (
  `id`         BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `name`       VARCHAR(150) NOT NULL,
  `email`      VARCHAR(150) NOT NULL,
  `phone`      VARCHAR(50) NULL DEFAULT NULL,
  `subject`    VARCHAR(200) NULL DEFAULT NULL,
  `message`    TEXT NOT NULL,
  `locale`     VARCHAR(5) NOT NULL DEFAULT 'id',
  `is_read`    TINYINT(1) NOT NULL DEFAULT 0,
  `created_at` TIMESTAMP NULL DEFAULT NULL,
  `updated_at` TIMESTAMP NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `contact_messages_read_idx` (`is_read`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

SET FOREIGN_KEY_CHECKS = 1;
-- END OF SCHEMA
