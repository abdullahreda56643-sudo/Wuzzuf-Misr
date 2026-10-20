-- ========================================
-- قاعدة بيانات منصة الوظائف الإلكترونية
-- ========================================

-- ========================================
-- إنشاء قاعدة البيانات
-- ========================================
CREATE DATABASE IF NOT EXISTS `jobs_platform` 
CHARACTER SET utf8mb4 
COLLATE utf8mb4_unicode_ci;

-- استخدام قاعدة البيانات
USE `jobs_platform`;

-- تعيين ترميز UTF-8
SET NAMES utf8mb4;
SET CHARACTER SET utf8mb4;

-- ========================================
-- 1. جدول المحافظات (Governorates)
-- ========================================
CREATE TABLE IF NOT EXISTS `governorates` (
  `id` INT PRIMARY KEY AUTO_INCREMENT,
  `name` VARCHAR(100) NOT NULL UNIQUE,
  `slug` VARCHAR(100) NOT NULL UNIQUE,
  `description` TEXT,
  `status` ENUM('active', 'inactive') DEFAULT 'active',
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  INDEX `idx_status` (`status`),
  INDEX `idx_slug` (`slug`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ========================================
-- 2. جدول المدن (Cities)
-- ========================================
CREATE TABLE IF NOT EXISTS `cities` (
  `id` INT PRIMARY KEY AUTO_INCREMENT,
  `governorate_id` INT NOT NULL,
  `name` VARCHAR(100) NOT NULL,
  `slug` VARCHAR(100) NOT NULL,
  `description` TEXT,
  `status` ENUM('active', 'inactive') DEFAULT 'active',
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  INDEX `idx_governorate_id` (`governorate_id`),
  INDEX `idx_status` (`status`),
  INDEX `idx_slug` (`slug`),
  CONSTRAINT `fk_cities_governorates` FOREIGN KEY (`governorate_id`) REFERENCES `governorates`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ========================================
-- 3. جدول تصنيفات الوظائف (Job Categories)
-- ========================================
CREATE TABLE IF NOT EXISTS `job_categories` (
  `id` INT PRIMARY KEY AUTO_INCREMENT,
  `name` VARCHAR(100) NOT NULL UNIQUE,
  `slug` VARCHAR(100) NOT NULL UNIQUE,
  `description` TEXT,
  `icon` VARCHAR(255),
  `status` ENUM('active', 'inactive') DEFAULT 'active',
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  INDEX `idx_status` (`status`),
  INDEX `idx_slug` (`slug`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ========================================
-- 4. جدول المستخدمين (Users)
-- ========================================
CREATE TABLE IF NOT EXISTS `users` (
  `id` INT PRIMARY KEY AUTO_INCREMENT,
  `first_name` VARCHAR(100) NOT NULL,
  `last_name` VARCHAR(100) NOT NULL,
  `email` VARCHAR(255) NOT NULL UNIQUE,
  `phone` VARCHAR(20),
  `password` VARCHAR(255) NOT NULL,
  `type` ENUM('job_seeker', 'employer', 'admin') DEFAULT 'job_seeker',
  `avatar` VARCHAR(255),
  `bio` TEXT,
  `date_of_birth` DATE,
  `gender` ENUM('male', 'female', 'other'),
  `qualification` VARCHAR(100),
  `experience` VARCHAR(255),
  `skills` JSON,
  `governorate_id` INT,
  `city_id` INT,
  `address` TEXT,
  `is_verified` TINYINT(1) DEFAULT 0,
  `verification_token` VARCHAR(255),
  `email_verified_at` TIMESTAMP NULL,
  `status` ENUM('active', 'inactive', 'suspended', 'banned') DEFAULT 'active',
  `last_login` TIMESTAMP NULL,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  INDEX `idx_email` (`email`),
  INDEX `idx_type` (`type`),
  INDEX `idx_status` (`status`),
  INDEX `idx_governorate_id` (`governorate_id`),
  INDEX `idx_city_id` (`city_id`),
  CONSTRAINT `fk_users_governorates` FOREIGN KEY (`governorate_id`) REFERENCES `governorates`(`id`) ON DELETE SET NULL,
  CONSTRAINT `fk_users_cities` FOREIGN KEY (`city_id`) REFERENCES `cities`(`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ========================================
-- 5. جدول الشركات (Companies)
-- ========================================
CREATE TABLE IF NOT EXISTS `companies` (
  `id` INT PRIMARY KEY AUTO_INCREMENT,
  `user_id` INT NOT NULL,
  `name` VARCHAR(255) NOT NULL UNIQUE,
  `slug` VARCHAR(255) NOT NULL UNIQUE,
  `email` VARCHAR(255) NOT NULL,
  `phone` VARCHAR(20),
  `website` VARCHAR(255),
  `description` TEXT,
  `logo` VARCHAR(255),
  `cover_image` VARCHAR(255),
  `industry` VARCHAR(100),
  `company_size` ENUM('startup', 'small', 'medium', 'large', 'enterprise') DEFAULT 'small',
  `founded_year` INT,
  `governorate_id` INT NOT NULL,
  `city_id` INT NOT NULL,
  `address` TEXT,
  `latitude` DECIMAL(10, 8),
  `longitude` DECIMAL(11, 8),
  `is_featured` TINYINT(1) DEFAULT 0,
  `featured_until` TIMESTAMP NULL,
  `verification_status` ENUM('pending', 'verified', 'rejected') DEFAULT 'pending',
  `status` ENUM('active', 'inactive', 'suspended') DEFAULT 'active',
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  UNIQUE KEY `uk_user_id` (`user_id`),
  INDEX `idx_governorate_id` (`governorate_id`),
  INDEX `idx_city_id` (`city_id`),
  INDEX `idx_status` (`status`),
  INDEX `idx_verification_status` (`verification_status`),
  INDEX `idx_slug` (`slug`),
  CONSTRAINT `fk_companies_users` FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_companies_governorates` FOREIGN KEY (`governorate_id`) REFERENCES `governorates`(`id`) ON DELETE RESTRICT,
  CONSTRAINT `fk_companies_cities` FOREIGN KEY (`city_id`) REFERENCES `cities`(`id`) ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ========================================
-- 6. جدول السير الذاتية (Resumes)
-- ========================================
CREATE TABLE IF NOT EXISTS `resumes` (
  `id` INT PRIMARY KEY AUTO_INCREMENT,
  `user_id` INT NOT NULL,
  `title` VARCHAR(255) NOT NULL,
  `file_path` VARCHAR(255) NOT NULL,
  `file_name` VARCHAR(255) NOT NULL,
  `file_size` INT,
  `is_default` TINYINT(1) DEFAULT 0,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  INDEX `idx_user_id` (`user_id`),
  INDEX `idx_is_default` (`is_default`),
  CONSTRAINT `fk_resumes_users` FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ========================================
-- 7. جدول الوظائف (Jobs)
-- ========================================
CREATE TABLE IF NOT EXISTS `company_ratings` (
  `id` INT PRIMARY KEY AUTO_INCREMENT,
  `company_id` INT NOT NULL,
  `user_id` INT NOT NULL,
  `rating` TINYINT NOT NULL,
  `review` TEXT,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  INDEX `idx_company_id` (`company_id`),
  INDEX `idx_user_id` (`user_id`),
  UNIQUE KEY `uk_company_user` (`company_id`, `user_id`),
  CONSTRAINT `fk_company_ratings_company` FOREIGN KEY (`company_id`) REFERENCES `companies`(`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_company_ratings_user` FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `jobs` (
  `id` INT PRIMARY KEY AUTO_INCREMENT,
  `company_id` INT NOT NULL,
  `title` VARCHAR(255) NOT NULL,
  `slug` VARCHAR(255) NOT NULL,
  `category_id` INT NOT NULL,
  `description` LONGTEXT NOT NULL,
  `requirements` LONGTEXT,
  `benefits` LONGTEXT,
  `job_type` ENUM('full-time', 'part-time', 'contract', 'temporary', 'internship') DEFAULT 'full-time',
  `experience_level` ENUM('entry', 'junior', 'mid', 'senior', 'lead', 'executive') DEFAULT 'junior',
  `salary_min` DECIMAL(12, 2),
  `salary_max` DECIMAL(12, 2),
  `salary_currency` VARCHAR(3) DEFAULT 'EGP',
  `salary_negotiable` TINYINT(1) DEFAULT 0,
  `qualification` VARCHAR(255),
  `governorate_id` INT NOT NULL,
  `city_id` INT NOT NULL,
  `application_method` ENUM('internal', 'whatsapp', 'phone', 'email', 'external_link') DEFAULT 'internal',
  `application_whatsapp` VARCHAR(20),
  `application_phone` VARCHAR(20),
  `application_email` VARCHAR(255),
  `application_link` VARCHAR(255),
  `published_at` TIMESTAMP NULL,
  `expires_at` TIMESTAMP NULL,
  `status` ENUM('draft', 'published', 'expired', 'closed') DEFAULT 'draft',
  `views_count` INT DEFAULT 0,
  `applications_count` INT DEFAULT 0,
  `is_featured` TINYINT(1) DEFAULT 0,
  `featured_until` TIMESTAMP NULL,
  `is_urgent` TINYINT(1) DEFAULT 0,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  INDEX `idx_company_id` (`company_id`),
  INDEX `idx_category_id` (`category_id`),
  INDEX `idx_status` (`status`),
  INDEX `idx_published_at` (`published_at`),
  INDEX `idx_expires_at` (`expires_at`),
  INDEX `idx_governorate_id` (`governorate_id`),
  INDEX `idx_city_id` (`city_id`),
  INDEX `idx_is_featured` (`is_featured`),
  INDEX `idx_slug` (`slug`),
  CONSTRAINT `fk_jobs_companies` FOREIGN KEY (`company_id`) REFERENCES `companies`(`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_jobs_categories` FOREIGN KEY (`category_id`) REFERENCES `job_categories`(`id`) ON DELETE RESTRICT,
  CONSTRAINT `fk_jobs_governorates` FOREIGN KEY (`governorate_id`) REFERENCES `governorates`(`id`) ON DELETE RESTRICT,
  CONSTRAINT `fk_jobs_cities` FOREIGN KEY (`city_id`) REFERENCES `cities`(`id`) ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ========================================
-- 8. جدول تطبيقات الوظائف (Job Applications)
-- ========================================
CREATE TABLE IF NOT EXISTS `job_applications` (
  `id` INT PRIMARY KEY AUTO_INCREMENT,
  `job_id` INT NOT NULL,
  `user_id` INT NOT NULL,
  `resume_id` INT,
  `cover_letter` LONGTEXT,
  `status` ENUM('pending', 'reviewed', 'shortlisted', 'rejected', 'accepted', 'withdrawn') DEFAULT 'pending',
  `applied_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `reviewed_at` TIMESTAMP NULL,
  `rejection_reason` TEXT,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  UNIQUE KEY `uk_job_user` (`job_id`, `user_id`),
  INDEX `idx_user_id` (`user_id`),
  INDEX `idx_status` (`status`),
  INDEX `idx_applied_at` (`applied_at`),
  CONSTRAINT `fk_applications_jobs` FOREIGN KEY (`job_id`) REFERENCES `jobs`(`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_applications_users` FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_applications_resumes` FOREIGN KEY (`resume_id`) REFERENCES `resumes`(`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ========================================
-- 9. جدول الوظائف المحفوظة (Saved Jobs)
-- ========================================
CREATE TABLE IF NOT EXISTS `saved_jobs` (
  `id` INT PRIMARY KEY AUTO_INCREMENT,
  `user_id` INT NOT NULL,
  `job_id` INT NOT NULL,
  `saved_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  UNIQUE KEY `uk_user_job` (`user_id`, `job_id`),
  INDEX `idx_user_id` (`user_id`),
  INDEX `idx_job_id` (`job_id`),
  CONSTRAINT `fk_saved_jobs_users` FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_saved_jobs_jobs` FOREIGN KEY (`job_id`) REFERENCES `jobs`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ========================================
-- 10. جدول مدة الإعلانات الديناميكية (Ad Durations)
-- ========================================
CREATE TABLE IF NOT EXISTS `ad_durations` (
  `id` INT PRIMARY KEY AUTO_INCREMENT,
  `name` VARCHAR(100) NOT NULL UNIQUE,
  `days` INT NOT NULL UNIQUE,
  `description` TEXT,
  `price` DECIMAL(12, 2),
  `is_active` TINYINT(1) DEFAULT 1,
  `sort_order` INT DEFAULT 0,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  INDEX `idx_is_active` (`is_active`),
  INDEX `idx_sort_order` (`sort_order`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ========================================
-- 11. جدول مواقع الإعلانات (Ad Locations)
-- ========================================
CREATE TABLE IF NOT EXISTS `ad_locations` (
  `id` INT PRIMARY KEY AUTO_INCREMENT,
  `name` VARCHAR(100) NOT NULL UNIQUE,
  `slug` VARCHAR(100) NOT NULL UNIQUE,
  `description` TEXT,
  `width` INT,
  `height` INT,
  `price_per_day` DECIMAL(12, 2),
  `is_active` TINYINT(1) DEFAULT 1,
  `sort_order` INT DEFAULT 0,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  INDEX `idx_is_active` (`is_active`),
  INDEX `idx_slug` (`slug`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ========================================
-- 12. جدول الإعلانات (Ads)
-- ========================================
CREATE TABLE IF NOT EXISTS `ads` (
  `id` INT PRIMARY KEY AUTO_INCREMENT,
  `user_id` INT NOT NULL,
  `company_id` INT,
  `title` VARCHAR(255) NOT NULL,
  `ad_code` LONGTEXT,
  `description` LONGTEXT,
  `image` VARCHAR(255),
  `link` VARCHAR(255),
  `ad_location_id` INT NOT NULL,
  `ad_duration_id` INT NOT NULL,
  `ad_type` ENUM('banner', 'featured_job', 'company_promo', 'other') DEFAULT 'banner',
  `start_date` TIMESTAMP NULL,
  `end_date` TIMESTAMP NULL,
  `status` ENUM('draft', 'pending', 'active', 'expired', 'rejected') DEFAULT 'draft',
  `views_count` INT DEFAULT 0,
  `clicks_count` INT DEFAULT 0,
  `cost` DECIMAL(12, 2),
  `payment_status` ENUM('pending', 'paid', 'partial') DEFAULT 'pending',
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  INDEX `idx_user_id` (`user_id`),
  INDEX `idx_company_id` (`company_id`),
  INDEX `idx_status` (`status`),
  INDEX `idx_ad_location_id` (`ad_location_id`),
  INDEX `idx_ad_duration_id` (`ad_duration_id`),
  INDEX `idx_start_date` (`start_date`),
  INDEX `idx_end_date` (`end_date`),
  CONSTRAINT `fk_ads_users` FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_ads_companies` FOREIGN KEY (`company_id`) REFERENCES `companies`(`id`) ON DELETE SET NULL,
  CONSTRAINT `fk_ads_locations` FOREIGN KEY (`ad_location_id`) REFERENCES `ad_locations`(`id`) ON DELETE RESTRICT,
  CONSTRAINT `fk_ads_durations` FOREIGN KEY (`ad_duration_id`) REFERENCES `ad_durations`(`id`) ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ========================================
-- 13. جدول ضغطات الإعلانات (Ad Clicks)
-- ========================================
CREATE TABLE IF NOT EXISTS `ad_clicks` (
  `id` INT PRIMARY KEY AUTO_INCREMENT,
  `ad_id` INT NOT NULL,
  `user_id` INT,
  `ip_address` VARCHAR(45),
  `user_agent` VARCHAR(255),
  `clicked_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  INDEX `idx_ad_id` (`ad_id`),
  INDEX `idx_user_id` (`user_id`),
  INDEX `idx_clicked_at` (`clicked_at`),
  CONSTRAINT `fk_ad_clicks_ads` FOREIGN KEY (`ad_id`) REFERENCES `ads`(`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_ad_clicks_users` FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ========================================
-- 14. جدول مشاهدات الإعلانات (Ad Impressions)
-- ========================================
CREATE TABLE IF NOT EXISTS `ad_impressions` (
  `id` INT PRIMARY KEY AUTO_INCREMENT,
  `ad_id` INT NOT NULL,
  `user_id` INT,
  `ip_address` VARCHAR(45),
  `user_agent` VARCHAR(255),
  `viewed_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  INDEX `idx_ad_id` (`ad_id`),
  INDEX `idx_user_id` (`user_id`),
  INDEX `idx_viewed_at` (`viewed_at`),
  CONSTRAINT `fk_ad_impressions_ads` FOREIGN KEY (`ad_id`) REFERENCES `ads`(`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_ad_impressions_users` FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ========================================
-- 15. جدول المدفوعات (Payments)
-- ========================================
CREATE TABLE IF NOT EXISTS `payments` (
  `id` INT PRIMARY KEY AUTO_INCREMENT,
  `user_id` INT NOT NULL,
  `company_id` INT,
  `ad_id` INT,
  `amount` DECIMAL(12, 2) NOT NULL,
  `currency` VARCHAR(3) DEFAULT 'EGP',
  `payment_method` ENUM('credit_card', 'debit_card', 'bank_transfer', 'mobile_wallet', 'cash') DEFAULT 'credit_card',
  `transaction_id` VARCHAR(255),
  `status` ENUM('pending', 'processing', 'completed', 'failed', 'refunded') DEFAULT 'pending',
  `description` TEXT,
  `paid_at` TIMESTAMP NULL,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  INDEX `idx_user_id` (`user_id`),
  INDEX `idx_company_id` (`company_id`),
  INDEX `idx_ad_id` (`ad_id`),
  INDEX `idx_status` (`status`),
  INDEX `idx_transaction_id` (`transaction_id`),
  CONSTRAINT `fk_payments_users` FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_payments_companies` FOREIGN KEY (`company_id`) REFERENCES `companies`(`id`) ON DELETE SET NULL,
  CONSTRAINT `fk_payments_ads` FOREIGN KEY (`ad_id`) REFERENCES `ads`(`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ========================================
-- 16. جدول البلاغات (Reports)
-- ========================================
CREATE TABLE IF NOT EXISTS `reports` (
  `id` INT PRIMARY KEY AUTO_INCREMENT,
  `reporter_id` INT NOT NULL,
  `reported_type` ENUM('job', 'company', 'user', 'ad') DEFAULT 'job',
  `reported_id` INT NOT NULL,
  `reason` ENUM('spam', 'inappropriate', 'fraud', 'harassment', 'copyright', 'other') DEFAULT 'other',
  `description` LONGTEXT NOT NULL,
  `evidence_file` VARCHAR(255),
  `status` ENUM('pending', 'reviewing', 'resolved', 'rejected') DEFAULT 'pending',
  `admin_notes` TEXT,
  `action_taken` VARCHAR(255),
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  INDEX `idx_reporter_id` (`reporter_id`),
  INDEX `idx_reported_type` (`reported_type`),
  INDEX `idx_status` (`status`),
  INDEX `idx_reason` (`reason`),
  CONSTRAINT `fk_reports_users` FOREIGN KEY (`reporter_id`) REFERENCES `users`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ========================================
-- 17. جدول الإشعارات (Notifications)
-- ========================================
CREATE TABLE IF NOT EXISTS `notifications` (
  `id` INT PRIMARY KEY AUTO_INCREMENT,
  `user_id` INT NOT NULL,
  `title` VARCHAR(255) NOT NULL,
  `message` LONGTEXT NOT NULL,
  `type` ENUM('job_applied', 'job_rejected', 'job_shortlisted', 'job_accepted', 'job_expired', 'company_message', 'system', 'payment', 'other') DEFAULT 'system',
  `related_type` VARCHAR(100),
  `related_id` INT,
  `is_read` TINYINT(1) DEFAULT 0,
  `read_at` TIMESTAMP NULL,
  `action_url` VARCHAR(255),
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  INDEX `idx_user_id` (`user_id`),
  INDEX `idx_is_read` (`is_read`),
  INDEX `idx_type` (`type`),
  INDEX `idx_created_at` (`created_at`),
  CONSTRAINT `fk_notifications_users` FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ========================================
-- 18. طلبات توظيف الباحثين (Employment Requests)
-- ========================================
CREATE TABLE IF NOT EXISTS `employment_requests` (
  `id` INT PRIMARY KEY AUTO_INCREMENT,
  `requester_id` INT NOT NULL,
  `seeker_id` INT NOT NULL,
  `message` TEXT,
  `status` ENUM('pending', 'contacted', 'closed') DEFAULT 'pending',
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  INDEX `idx_employment_requester_id` (`requester_id`),
  INDEX `idx_employment_seeker_id` (`seeker_id`),
  INDEX `idx_employment_status` (`status`),
  CONSTRAINT `fk_employment_requester` FOREIGN KEY (`requester_id`) REFERENCES `users`(`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_employment_seeker` FOREIGN KEY (`seeker_id`) REFERENCES `users`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ========================================
-- 19. جدول الإعدادات (Settings)
-- ========================================
CREATE TABLE IF NOT EXISTS `settings` (
  `id` INT PRIMARY KEY AUTO_INCREMENT,
  `key` VARCHAR(255) NOT NULL UNIQUE,
  `value` LONGTEXT,
  `type` ENUM('string', 'number', 'boolean', 'json', 'text') DEFAULT 'string',
  `group` VARCHAR(100),
  `description` TEXT,
  `is_editable` TINYINT(1) DEFAULT 1,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  INDEX `idx_key` (`key`),
  INDEX `idx_group` (`group`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ========================================
-- 20. جدول الصفحات (Pages)
-- ========================================
CREATE TABLE IF NOT EXISTS `pages` (
  `id` INT PRIMARY KEY AUTO_INCREMENT,
  `title` VARCHAR(255) NOT NULL,
  `slug` VARCHAR(255) NOT NULL UNIQUE,
  `content` LONGTEXT NOT NULL,
  `excerpt` TEXT,
  `featured_image` VARCHAR(255),
  `meta_description` VARCHAR(255),
  `meta_keywords` VARCHAR(255),
  `author_id` INT,
  `status` ENUM('draft', 'published', 'archived') DEFAULT 'draft',
  `views_count` INT DEFAULT 0,
  `published_at` TIMESTAMP NULL,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  INDEX `idx_status` (`status`),
  INDEX `idx_slug` (`slug`),
  INDEX `idx_author_id` (`author_id`),
  CONSTRAINT `fk_pages_users` FOREIGN KEY (`author_id`) REFERENCES `users`(`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ========================================
-- 20. جدول المقالات (Posts)
-- ========================================
CREATE TABLE IF NOT EXISTS `posts` (
  `id` INT PRIMARY KEY AUTO_INCREMENT,
  `title` VARCHAR(255) NOT NULL,
  `slug` VARCHAR(255) NOT NULL UNIQUE,
  `content` LONGTEXT NOT NULL,
  `excerpt` TEXT,
  `featured_image` VARCHAR(255),
  `meta_description` VARCHAR(255),
  `meta_keywords` VARCHAR(255),
  `author_id` INT NOT NULL,
  `category` VARCHAR(100),
  `status` ENUM('draft', 'published', 'archived') DEFAULT 'draft',
  `views_count` INT DEFAULT 0,
  `likes_count` INT DEFAULT 0,
  `published_at` TIMESTAMP NULL,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  INDEX `idx_status` (`status`),
  INDEX `idx_slug` (`slug`),
  INDEX `idx_author_id` (`author_id`),
  INDEX `idx_category` (`category`),
  CONSTRAINT `fk_posts_users` FOREIGN KEY (`author_id`) REFERENCES `users`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ========================================
-- بيانات افتراضية (Default Data)
-- ========================================

-- إضافة محافظات مصرية (بما فيها كفر الشيخ)
INSERT INTO `governorates` (`name`, `slug`, `status`) VALUES
('القاهرة', 'cairo', 'active'),
('الإسكندرية', 'alexandria', 'active'),
('الجيزة', 'giza', 'active'),
('كفر الشيخ', 'kafr-el-sheikh', 'active'),
('الدقهلية', 'dakahlia', 'active'),
('البحيرة', 'beheira', 'active'),
('الشرقية', 'sharqia', 'active'),
('الغربية', 'gharbia', 'active'),
('المنوفية', 'menufia', 'active'),
('القليوبية', 'qalubia', 'active'),
('السويس', 'suez', 'active'),
('بورسعيد', 'port-said', 'active'),
('الإسماعيلية', 'ismailia', 'active'),
('الفيوم', 'faiyum', 'active'),
('بني سويف', 'beni-suef', 'active'),
('المنيا', 'minya', 'active'),
('أسيوط', 'assiut', 'active'),
('سوهاج', 'sohag', 'active'),
('قنا', 'qena', 'active'),
('الأقصر', 'luxor', 'active'),
('أسوان', 'aswan', 'active'),
('مطروح', 'matrouh', 'active'),
('البحر الأحمر', 'red-sea', 'active'),
('جنوب سيناء', 'south-sinai', 'active'),
('شمال سيناء', 'north-sinai', 'active'),
('الوادي الجديد', 'new-valley', 'active');

-- إضافة مدن في كفر الشيخ
INSERT INTO `cities` (`governorate_id`, `name`, `slug`, `status`) VALUES
(4, 'كفر الشيخ', 'kafr-el-sheikh-city', 'active'),
(4, 'دسوق', 'disouq', 'active'),
(4, 'فوة', 'foua', 'active'),
(4, 'قلين', 'qallin', 'active'),
(4, 'بيلا', 'bila', 'active'),
(4, 'سيدي سالم', 'sidi-salem', 'active');

-- إضافة تصنيفات الوظائف
INSERT INTO `job_categories` (`name`, `slug`, `status`) VALUES
('تكنولوجيا المعلومات', 'information-technology', 'active'),
('المبيعات والتسويق', 'sales-marketing', 'active'),
('الموارد البشرية', 'human-resources', 'active'),
('المحاسبة والمالية', 'accounting-finance', 'active'),
('الهندسة', 'engineering', 'active'),
('الطب والصحة', 'medical-health', 'active'),
('التعليم', 'education', 'active'),
('الخدمات اللوجستية', 'logistics-supply-chain', 'active'),
('الإعلام والإعلان', 'media-advertising', 'active'),
('الضيافة والسياحة', 'hospitality-tourism', 'active'),
('البيع بالتجزئة', 'retail', 'active'),
('الهندسة المدنية', 'civil-engineering', 'active'),
('الإدارة والعمليات', 'management-operations', 'active'),
('البحث والتطوير', 'research-development', 'active'),
('الدعم الفني', 'technical-support', 'active');

-- إضافة مدة الإعلانات الديناميكية
INSERT INTO `ad_durations` (`name`, `days`, `price`, `is_active`, `sort_order`) VALUES
('10 أيام', 10, 500.00, 1, 1),
('15 يوم', 15, 650.00, 1, 2),
('30 يوم', 30, 1200.00, 1, 3),
('60 يوم', 60, 2000.00, 1, 4),
('90 يوم', 90, 2700.00, 1, 5);

-- إضافة مواقع الإعلانات
INSERT INTO `ad_locations` (`name`, `slug`, `width`, `height`, `price_per_day`, `is_active`, `sort_order`) VALUES
('رأس الصفحة الرئيسية', 'header-home', 970, 250, 150.00, 1, 1),
('منتصف قائمة الوظائف', 'middle-jobs', 728, 90, 100.00, 1, 2),
('الشريط الجانبي', 'sidebar', 300, 250, 75.00, 1, 3),
('تذييل الصفحة', 'footer', 970, 90, 50.00, 1, 4),
('رأس صفحة الشركة', 'company-header', 970, 250, 200.00, 1, 5);

-- إضافة الإعدادات الافتراضية
INSERT INTO `settings` (`key`, `value`, `type`, `group`, `description`) VALUES
('site_name', 'منصة الوظائف الإلكترونية', 'string', 'general', 'اسم الموقع'),
('site_description', 'منصة وظائف إلكترونية تربط الباحثين عن عمل مع الشركات', 'text', 'general', 'وصف الموقع'),
('site_email', 'info@jobsplatform.com', 'string', 'general', 'البريد الإلكتروني للموقع'),
('site_phone', '+20123456789', 'string', 'general', 'رقم الموقع'),
('site_address', 'كفر الشيخ، مصر', 'string', 'general', 'عنوان الموقع'),
('google_adsense_enabled', 'true', 'boolean', 'adsense', 'تفعيل Google AdSense'),
('google_adsense_client_id', '', 'string', 'adsense', 'معرف العميل لـ Google AdSense'),
('jobs_per_page', '20', 'number', 'general', 'عدد الوظائف في الصفحة الواحدة'),
('default_job_duration', '30', 'number', 'jobs', 'المدة الافتراضية للوظيفة بالأيام'),
('email_notifications_enabled', 'true', 'boolean', 'email', 'تفعيل إشعارات البريد الإلكتروني'),
('sms_notifications_enabled', 'false', 'boolean', 'sms', 'تفعيل إشعارات الرسائل النصية'),
('max_upload_size', '5242880', 'number', 'uploads', 'الحد الأقصى لحجم الملف (بالبايت)'),
('allowed_file_types', 'pdf,doc,docx', 'string', 'uploads', 'أنواع الملفات المسموحة'),
('maintenance_mode', 'false', 'boolean', 'general', 'وضع الصيانة'),
('currency', 'EGP', 'string', 'payment', 'العملة المستخدمة'),
('tax_rate', '14', 'number', 'payment', 'نسبة الضريبة (%)'),
('commission_rate', '10', 'number', 'payment', 'نسبة العمولة (%)'),
('featured_job_boost_days', '7', 'number', 'jobs', 'عدد الأيام لتعزيز الوظيفة'),
('password_reset_expiry', '24', 'number', 'security', 'انتهاء صلاحية إعادة تعيين كلمة المرور (بالساعات)'),
('verification_token_expiry', '48', 'number', 'security', 'انتهاء صلاحية رمز التحقق (بالساعات)');

-- ========================================
-- نهاية ملف قاعدة البيانات
-- ========================================
