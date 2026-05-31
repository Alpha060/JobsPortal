-- ============================================================
-- JobsPortal Database Schema
-- Government Job Information Portal
-- ============================================================

SET NAMES utf8mb4;
SET CHARACTER SET utf8mb4;
SET collation_connection = 'utf8mb4_unicode_ci';
-- ============================================================
-- Settings Table
-- ============================================================
CREATE TABLE IF NOT EXISTS `settings` (
    `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `setting_key` VARCHAR(100) NOT NULL UNIQUE,
    `setting_value` TEXT,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- Admins Table
-- ============================================================
CREATE TABLE IF NOT EXISTS `admins` (
    `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `username` VARCHAR(50) NOT NULL UNIQUE,
    `email` VARCHAR(100) NOT NULL UNIQUE,
    `password_hash` VARCHAR(255) NOT NULL,
    `full_name` VARCHAR(100) DEFAULT NULL,
    `avatar` VARCHAR(255) DEFAULT NULL,
    `last_login` TIMESTAMP NULL DEFAULT NULL,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- Categories Table
-- ============================================================
CREATE TABLE IF NOT EXISTS `categories` (
    `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `name_en` VARCHAR(100) NOT NULL,
    `name_hi` VARCHAR(100) NOT NULL,
    `slug` VARCHAR(100) NOT NULL UNIQUE,
    `description_en` VARCHAR(255) DEFAULT NULL,
    `description_hi` VARCHAR(255) DEFAULT NULL,
    `icon` VARCHAR(50) DEFAULT '📋',
    `color` VARCHAR(7) DEFAULT '#6366F1',
    `gradient_from` VARCHAR(7) DEFAULT '#6366F1',
    `gradient_to` VARCHAR(7) DEFAULT '#8B5CF6',
    `sort_order` INT DEFAULT 0,
    `is_active` TINYINT(1) DEFAULT 1,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX `idx_categories_sort` (`sort_order`),
    INDEX `idx_categories_active` (`is_active`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- Posts Table
-- ============================================================
CREATE TABLE IF NOT EXISTS `posts` (
    `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `category_id` INT UNSIGNED NOT NULL,
    `title_en` VARCHAR(255) NOT NULL,
    `title_hi` VARCHAR(255) DEFAULT NULL,
    `slug` VARCHAR(255) NOT NULL UNIQUE,
    `excerpt_en` TEXT DEFAULT NULL,
    `excerpt_hi` TEXT DEFAULT NULL,
    `content_en` LONGTEXT DEFAULT NULL,
    `content_hi` LONGTEXT DEFAULT NULL,
    `featured_image` VARCHAR(255) DEFAULT NULL,
    `organization` VARCHAR(200) DEFAULT NULL,
    `post_date` DATE DEFAULT NULL,
    `last_date` DATE DEFAULT NULL,
    `exam_date` DATE DEFAULT NULL,
    `total_vacancies` INT DEFAULT NULL,
    `qualification` VARCHAR(255) DEFAULT NULL,
    `age_limit` VARCHAR(100) DEFAULT NULL,
    `application_fee` VARCHAR(255) DEFAULT NULL,
    `important_links` JSON DEFAULT NULL,
    `important_dates` JSON DEFAULT NULL,
    `is_featured` TINYINT(1) DEFAULT 0,
    `is_trending` TINYINT(1) DEFAULT 0,
    `is_active` TINYINT(1) DEFAULT 1,
    `status` ENUM('draft', 'published', 'archived') DEFAULT 'draft',
    `views` INT UNSIGNED DEFAULT 0,
    `meta_title` VARCHAR(255) DEFAULT NULL,
    `meta_description` VARCHAR(500) DEFAULT NULL,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX `idx_posts_category` (`category_id`),
    INDEX `idx_posts_status` (`status`),
    INDEX `idx_posts_featured` (`is_featured`),
    INDEX `idx_posts_trending` (`is_trending`),
    INDEX `idx_posts_active` (`is_active`),
    INDEX `idx_posts_post_date` (`post_date`),
    INDEX `idx_posts_last_date` (`last_date`),
    INDEX `idx_posts_views` (`views`),
    INDEX `idx_posts_created` (`created_at`),
    FULLTEXT `ft_posts_search` (`title_en`, `title_hi`, `excerpt_en`, `organization`),
    CONSTRAINT `fk_posts_category`
        FOREIGN KEY (`category_id`) REFERENCES `categories`(`id`)
        ON DELETE RESTRICT ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- Tags Table
-- ============================================================
CREATE TABLE IF NOT EXISTS `tags` (
    `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `name` VARCHAR(50) NOT NULL UNIQUE,
    `slug` VARCHAR(50) NOT NULL UNIQUE,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- Post Tags (Pivot Table)
-- ============================================================
CREATE TABLE IF NOT EXISTS `post_tags` (
    `post_id` INT UNSIGNED NOT NULL,
    `tag_id` INT UNSIGNED NOT NULL,
    PRIMARY KEY (`post_id`, `tag_id`),
    CONSTRAINT `fk_pt_post`
        FOREIGN KEY (`post_id`) REFERENCES `posts`(`id`)
        ON DELETE CASCADE ON UPDATE CASCADE,
    CONSTRAINT `fk_pt_tag`
        FOREIGN KEY (`tag_id`) REFERENCES `tags`(`id`)
        ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- Media Table
-- ============================================================
CREATE TABLE IF NOT EXISTS `media` (
    `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `filename` VARCHAR(255) NOT NULL,
    `original_name` VARCHAR(255) NOT NULL,
    `mime_type` VARCHAR(100) DEFAULT NULL,
    `size` INT UNSIGNED DEFAULT 0,
    `alt_text` VARCHAR(255) DEFAULT NULL,
    `uploaded_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- Login Attempts (Rate Limiting)
-- ============================================================
CREATE TABLE IF NOT EXISTS `login_attempts` (
    `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `ip_address` VARCHAR(45) NOT NULL,
    `attempted_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX `idx_login_ip` (`ip_address`),
    INDEX `idx_login_time` (`attempted_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- SEED DATA
-- ============================================================

-- Default Admin (password: admin123 — CHANGE IN PRODUCTION!)
INSERT INTO `admins` (`username`, `email`, `password_hash`, `full_name`) VALUES
('admin', 'admin@jobsportal.com', '$2y$12$LJ3m4ys3Gl1sPqHY3wXbxuN6nEJJHkGp.nCTYr6WxK5CiMKzBQSwa', 'Portal Admin');

-- Default Categories
INSERT INTO `categories` (`name_en`, `name_hi`, `slug`, `description_en`, `description_hi`, `icon`, `color`, `gradient_from`, `gradient_to`, `sort_order`) VALUES
('Latest Jobs',  'नवीनतम नौकरियां', 'latest-jobs',  'Latest government job notifications', 'नवीनतम सरकारी नौकरी अधिसूचनाएं', '💼', '#6366F1', '#6366F1', '#8B5CF6', 1),
('Results',      'परिणाम',          'results',      'Exam results and merit lists',        'परीक्षा परिणाम और मेरिट सूची',      '📊', '#10B981', '#10B981', '#059669', 2),
('Admit Card',   'प्रवेश पत्र',     'admit-card',   'Download admit cards and hall tickets','प्रवेश पत्र और हॉल टिकट डाउनलोड करें','🎫', '#F59E0B', '#F59E0B', '#D97706', 3),
('Answer Key',   'उत्तर कुंजी',     'answer-key',   'Official answer keys',                'आधिकारिक उत्तर कुंजी',              '🔑', '#EF4444', '#EF4444', '#DC2626', 4),
('Syllabus',     'पाठ्यक्रम',       'syllabus',     'Exam patterns and syllabi',           'परीक्षा पैटर्न और पाठ्यक्रम',        '📚', '#3B82F6', '#3B82F6', '#2563EB', 5),
('Admission',    'प्रवेश',          'admission',    'College and university admissions',    'कॉलेज और विश्वविद्यालय प्रवेश',     '🎓', '#EC4899', '#EC4899', '#DB2777', 6);

-- Default Settings
INSERT INTO `settings` (`setting_key`, `setting_value`) VALUES
('site_name_en',        'JobsPortal'),
('site_name_hi',        'जॉब्स पोर्टल'),
('site_tagline_en',     'Your Gateway to Government Jobs'),
('site_tagline_hi',     'सरकारी नौकरियों का प्रवेश द्वार'),
('site_description_en', 'Find latest government job notifications, results, admit cards, answer keys, syllabus and admission updates.'),
('site_description_hi', 'नवीनतम सरकारी नौकरी अधिसूचनाएं, परिणाम, प्रवेश पत्र, उत्तर कुंजी, पाठ्यक्रम और प्रवेश अपडेट खोजें।'),
('site_logo',           ''),
('site_favicon',        ''),
('footer_text_en',      '© 2026 JobsPortal. All rights reserved.'),
('footer_text_hi',      '© 2026 जॉब्स पोर्टल। सर्वाधिकार सुरक्षित।'),
('contact_email',       'contact@jobsportal.com'),
('social_facebook',     ''),
('social_twitter',      ''),
('social_telegram',     ''),
('social_youtube',      ''),
('ticker_text_en',      'Welcome to JobsPortal — Your one-stop destination for government job updates!'),
('ticker_text_hi',      'जॉब्स पोर्टल में स्वागत है — सरकारी नौकरी अपडेट के लिए आपका एक-स्टॉप गंतव्य!'),
('posts_per_page',      '20'),
('default_language',    'en'),
('maintenance_mode',    '0');
