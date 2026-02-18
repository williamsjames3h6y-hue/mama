-- ============================================
-- EarningsLLC Database Export (MySQL Compatible)
-- Generated: 2026-02-18
-- ============================================

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;
SET SQL_MODE = 'NO_AUTO_VALUE_ON_ZERO';
SET time_zone = '+00:00';

-- ============================================
-- Create Database
-- ============================================
CREATE DATABASE IF NOT EXISTS `earningsllc` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE `earningsllc`;

-- ============================================
-- Table: users
-- Purpose: Store user account information
-- ============================================
DROP TABLE IF EXISTS `users`;
CREATE TABLE `users` (
  `id` VARCHAR(36) PRIMARY KEY,
  `email` VARCHAR(255) NOT NULL UNIQUE,
  `full_name` VARCHAR(255) NOT NULL,
  `role` ENUM('user', 'admin') DEFAULT 'user',
  `vip_level` INT DEFAULT 1 CHECK (`vip_level` >= 1 AND `vip_level` <= 5),
  `balance` DECIMAL(10, 2) DEFAULT 0.00,
  `total_earned` DECIMAL(10, 2) DEFAULT 0.00,
  `referral_code` VARCHAR(50) UNIQUE,
  `referred_by` VARCHAR(36) NULL,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  INDEX `idx_email` (`email`),
  INDEX `idx_referral_code` (`referral_code`),
  INDEX `idx_referred_by` (`referred_by`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- Table: projects
-- Purpose: Store available projects/tasks
-- ============================================
DROP TABLE IF EXISTS `projects`;
CREATE TABLE `projects` (
  `id` VARCHAR(36) PRIMARY KEY,
  `title` VARCHAR(255) NOT NULL,
  `description` TEXT NOT NULL,
  `rate_min` DECIMAL(10, 2) NOT NULL,
  `rate_max` DECIMAL(10, 2) NOT NULL,
  `project_type` VARCHAR(100) DEFAULT 'Remote',
  `vip_level_required` INT DEFAULT 1 CHECK (`vip_level_required` >= 1 AND `vip_level_required` <= 5),
  `status` ENUM('open', 'closed') DEFAULT 'open',
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  INDEX `idx_status` (`status`),
  INDEX `idx_vip_level` (`vip_level_required`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Insert sample projects
INSERT INTO `projects` (`id`, `title`, `description`, `rate_min`, `rate_max`, `project_type`, `vip_level_required`, `status`, `created_at`) VALUES
('6a3000bc-765a-4b5e-9918-1c514edce5ad', 'Basic Data Entry', 'Simple data entry tasks for beginners', 10.00, 15.00, 'Remote - Part-time', 1, 'open', NOW()),
('0fafaa69-e5be-4ad9-a446-6d597914363d', 'Content Review', 'Review and moderate user-generated content', 20.00, 25.00, 'Remote - Contract', 2, 'open', NOW()),
('884d5409-c66f-4969-b711-53a330831ee8', 'Professional Writing', 'Create high-quality content for websites and blogs', 50.00, 75.00, 'Remote - Part-time', 3, 'open', NOW()),
('34c5ce40-058b-48cf-9cda-718d1282556d', 'Data Science Projects', 'Work on machine learning and data analysis tasks', 100.00, 120.00, 'Remote - Full-time', 4, 'open', NOW()),
('8d3932c9-5685-420e-a3cc-ba5c16c4449f', 'Senior Consulting', 'High-level consulting requiring extensive experience', 200.00, 250.00, 'Remote - Contract', 5, 'open', NOW()),
('fc7b9117-ceb2-419e-8172-00e08d130f27', 'Data Cleaning & Validation', 'Clean and validate large datasets, remove duplicates, fix formatting issues, and ensure data quality standards', 15.00, 25.00, 'Remote', 1, 'open', NOW()),
('3e1731b1-c398-4aec-a8ee-c714b75eab46', 'Database Query Optimization', 'Optimize SQL queries for better performance, create indexes, and improve database efficiency', 30.00, 50.00, 'Remote', 2, 'open', NOW()),
('4b5c3b82-2359-4a63-8b6a-803d7da3dd5e', 'ETL Pipeline Development', 'Build Extract, Transform, Load pipelines for automated data processing and integration', 50.00, 80.00, 'Remote', 3, 'open', NOW()),
('41e7011e-4540-465c-abf0-ef105a59aeab', 'Data Visualization Dashboard', 'Create interactive dashboards using modern visualization tools to present key metrics and insights', 40.00, 70.00, 'Remote', 2, 'open', NOW()),
('49fbd32e-2638-46de-9ae2-6f2d319325a1', 'Machine Learning Model Training', 'Train and optimize machine learning models for predictive analytics and pattern recognition', 80.00, 150.00, 'Remote', 4, 'open', NOW()),
('bfc504f8-3e38-4e82-9092-78faa82c9017', 'API Data Integration', 'Integrate multiple API data sources, transform data formats, and automate data synchronization', 35.00, 60.00, 'Remote', 2, 'open', NOW()),
('97cf4af7-3b7f-4f82-8a75-97395e07c51b', 'Data Warehouse Design', 'Design and implement scalable data warehouse architecture for enterprise analytics', 100.00, 200.00, 'Remote', 5, 'open', NOW()),
('737cec57-1ded-48ab-9ed9-c11809dea2c6', 'Real-time Data Processing', 'Build real-time data processing systems using streaming technologies for instant insights', 70.00, 120.00, 'Remote', 4, 'open', NOW()),
('3372b733-28e1-40b2-b5f0-5075c223cc85', 'Data Security Audit', 'Perform comprehensive security audits on data systems, identify vulnerabilities, and implement fixes', 60.00, 100.00, 'Remote', 3, 'open', NOW()),
('51b3d0cb-c4c2-4ced-b5b6-62281ce4ac65', 'Spreadsheet Automation', 'Automate repetitive spreadsheet tasks, create formulas, and build custom scripts for efficiency', 20.00, 35.00, 'Remote', 1, 'open', NOW());

-- ============================================
-- Table: user_projects
-- Purpose: Track user participation in projects
-- ============================================
DROP TABLE IF EXISTS `user_projects`;
CREATE TABLE `user_projects` (
  `id` VARCHAR(36) PRIMARY KEY,
  `user_id` VARCHAR(36) NOT NULL,
  `project_id` VARCHAR(36) NOT NULL,
  `status` ENUM('submitted', 'approved', 'rejected') DEFAULT 'submitted',
  `hours_worked` DECIMAL(10, 2) DEFAULT 0,
  `amount_earned` DECIMAL(10, 2) DEFAULT 0,
  `submitted_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE CASCADE,
  FOREIGN KEY (`project_id`) REFERENCES `projects`(`id`) ON DELETE CASCADE,
  INDEX `idx_user_id` (`user_id`),
  INDEX `idx_project_id` (`project_id`),
  INDEX `idx_status` (`status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- Table: payments
-- Purpose: Track all payment transactions
-- ============================================
DROP TABLE IF EXISTS `payments`;
CREATE TABLE `payments` (
  `id` VARCHAR(36) PRIMARY KEY,
  `user_id` VARCHAR(36) NOT NULL,
  `type` ENUM('earning', 'deposit', 'withdrawal') NOT NULL,
  `amount` DECIMAL(10, 2) NOT NULL,
  `status` ENUM('pending', 'completed', 'failed') DEFAULT 'pending',
  `description` TEXT,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE CASCADE,
  INDEX `idx_user_id` (`user_id`),
  INDEX `idx_type` (`type`),
  INDEX `idx_status` (`status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- Table: withdrawals
-- Purpose: Track withdrawal requests
-- ============================================
DROP TABLE IF EXISTS `withdrawals`;
CREATE TABLE `withdrawals` (
  `id` VARCHAR(36) PRIMARY KEY,
  `user_id` VARCHAR(36) NOT NULL,
  `amount` DECIMAL(10, 2) NOT NULL CHECK (`amount` > 0),
  `method` VARCHAR(100) NOT NULL,
  `account_details` JSON,
  `status` ENUM('pending', 'approved', 'rejected', 'completed') DEFAULT 'pending',
  `processed_at` TIMESTAMP NULL,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE CASCADE,
  INDEX `idx_user_id` (`user_id`),
  INDEX `idx_status` (`status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- Table: site_settings
-- Purpose: Store platform configuration
-- ============================================
DROP TABLE IF EXISTS `site_settings`;
CREATE TABLE `site_settings` (
  `id` VARCHAR(36) PRIMARY KEY,
  `key` VARCHAR(100) NOT NULL UNIQUE,
  `value` TEXT,
  `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  INDEX `idx_key` (`key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Insert default settings
INSERT INTO `site_settings` (`id`, `key`, `value`) VALUES
(UUID(), 'site_name', 'EarningsLLC'),
(UUID(), 'contact_email', 'admin@earningsllc.com'),
(UUID(), 'contact_phone', '+1 (555) 123-4567'),
(UUID(), 'contact_address', '123 Business Street\nNew York, NY 10001\nUnited States'),
(UUID(), 'vip1_rate', '10'),
(UUID(), 'vip2_rate', '20'),
(UUID(), 'vip3_rate', '50'),
(UUID(), 'vip4_rate', '100'),
(UUID(), 'vip5_rate', '200'),
(UUID(), 'referral_bonus', '300'),
(UUID(), 'min_withdrawal', '50');

-- ============================================
-- Table: profiles (for extended user info)
-- Purpose: Store additional user profile details
-- ============================================
DROP TABLE IF EXISTS `profiles`;
CREATE TABLE `profiles` (
  `id` VARCHAR(36) PRIMARY KEY,
  `email` VARCHAR(255) NOT NULL UNIQUE,
  `full_name` VARCHAR(255),
  `phone` VARCHAR(50),
  `address` TEXT,
  `city` VARCHAR(100),
  `state` VARCHAR(100),
  `zip` VARCHAR(20),
  `country` VARCHAR(100) DEFAULT 'USA',
  `company` VARCHAR(255),
  `role` ENUM('user', 'admin') DEFAULT 'user',
  `status` ENUM('active', 'suspended', 'banned') DEFAULT 'active',
  `storage_used` BIGINT DEFAULT 0 CHECK (`storage_used` >= 0),
  `storage_limit` BIGINT DEFAULT 10737418240 CHECK (`storage_limit` > 0),
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  INDEX `idx_email` (`email`),
  INDEX `idx_status` (`status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- Table: datasets
-- Purpose: Store uploaded datasets for optimization
-- ============================================
DROP TABLE IF EXISTS `datasets`;
CREATE TABLE `datasets` (
  `id` VARCHAR(36) PRIMARY KEY,
  `user_id` VARCHAR(36) NOT NULL,
  `name` VARCHAR(255) NOT NULL,
  `description` TEXT,
  `size` BIGINT DEFAULT 0 CHECK (`size` >= 0),
  `format` VARCHAR(50),
  `file_url` TEXT,
  `status` ENUM('uploaded', 'processing', 'optimized', 'deleted') DEFAULT 'uploaded',
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  FOREIGN KEY (`user_id`) REFERENCES `profiles`(`id`) ON DELETE CASCADE,
  INDEX `idx_user_id` (`user_id`),
  INDEX `idx_status` (`status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- Table: optimization_jobs
-- Purpose: Track data optimization jobs
-- ============================================
DROP TABLE IF EXISTS `optimization_jobs`;
CREATE TABLE `optimization_jobs` (
  `id` VARCHAR(36) PRIMARY KEY,
  `user_id` VARCHAR(36) NOT NULL,
  `dataset_id` VARCHAR(36) NOT NULL,
  `optimization_type` ENUM('compression', 'deduplication', 'formatting', 'cleaning', 'full') NOT NULL,
  `parameters` JSON,
  `status` ENUM('pending', 'running', 'completed', 'failed', 'cancelled') DEFAULT 'pending',
  `original_size` BIGINT DEFAULT 0,
  `optimized_size` BIGINT DEFAULT 0,
  `compression_ratio` DECIMAL(5, 2) DEFAULT 0,
  `output_url` TEXT,
  `started_at` TIMESTAMP NULL,
  `completed_at` TIMESTAMP NULL,
  `error_message` TEXT,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (`user_id`) REFERENCES `profiles`(`id`) ON DELETE CASCADE,
  FOREIGN KEY (`dataset_id`) REFERENCES `datasets`(`id`) ON DELETE CASCADE,
  INDEX `idx_user_id` (`user_id`),
  INDEX `idx_dataset_id` (`dataset_id`),
  INDEX `idx_status` (`status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- Table: settings (alternative settings table)
-- Purpose: Enhanced settings with type support
-- ============================================
DROP TABLE IF EXISTS `settings`;
CREATE TABLE `settings` (
  `id` VARCHAR(36) PRIMARY KEY,
  `key` VARCHAR(100) NOT NULL UNIQUE,
  `value` TEXT,
  `type` ENUM('string', 'number', 'boolean', 'json') DEFAULT 'string',
  `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  INDEX `idx_key` (`key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Insert default settings with types
INSERT INTO `settings` (`id`, `key`, `value`, `type`) VALUES
(UUID(), 'site_name', 'EarningsLLC', 'string'),
(UUID(), 'contact_email', 'admin@earningsllc.com', 'string'),
(UUID(), 'vip1_rate', '10', 'number'),
(UUID(), 'vip2_rate', '20', 'number'),
(UUID(), 'referral_bonus', '300', 'number'),
(UUID(), 'min_withdrawal', '50', 'number');

-- ============================================
-- Create Admin User (default password: admin123)
-- Note: Password should be hashed in production
-- ============================================
INSERT INTO `users` (`id`, `email`, `full_name`, `role`, `vip_level`, `referral_code`, `created_at`) VALUES
(UUID(), 'admin@earningsllc.com', 'Admin User', 'admin', 5, 'ADMIN2026', NOW());

-- ============================================
-- Create Sample User (VIP Level 1)
-- ============================================
INSERT INTO `users` (`id`, `email`, `full_name`, `role`, `vip_level`, `referral_code`, `created_at`) VALUES
(UUID(), 'user@example.com', 'John Doe', 'user', 1, 'USER001', NOW());

-- ============================================
-- Views for reporting
-- ============================================

-- View: Active users with earnings
CREATE OR REPLACE VIEW `v_user_earnings` AS
SELECT
  u.id,
  u.email,
  u.full_name,
  u.vip_level,
  u.balance,
  u.total_earned,
  COUNT(up.id) as total_projects,
  SUM(CASE WHEN up.status = 'approved' THEN up.amount_earned ELSE 0 END) as approved_earnings
FROM users u
LEFT JOIN user_projects up ON u.id = up.user_id
GROUP BY u.id, u.email, u.full_name, u.vip_level, u.balance, u.total_earned;

-- View: Project statistics
CREATE OR REPLACE VIEW `v_project_stats` AS
SELECT
  p.id,
  p.title,
  p.vip_level_required,
  p.status,
  COUNT(up.id) as total_submissions,
  COUNT(CASE WHEN up.status = 'approved' THEN 1 END) as approved_count,
  COUNT(CASE WHEN up.status = 'pending' THEN 1 END) as pending_count
FROM projects p
LEFT JOIN user_projects up ON p.id = up.project_id
GROUP BY p.id, p.title, p.vip_level_required, p.status;

-- ============================================
-- Triggers for automatic updates
-- ============================================

-- Trigger: Update user balance when project approved
DELIMITER //
CREATE TRIGGER `trg_update_balance_on_approval`
AFTER UPDATE ON `user_projects`
FOR EACH ROW
BEGIN
  IF NEW.status = 'approved' AND OLD.status != 'approved' THEN
    UPDATE users
    SET
      balance = balance + NEW.amount_earned,
      total_earned = total_earned + NEW.amount_earned
    WHERE id = NEW.user_id;
  END IF;
END//
DELIMITER ;

-- Trigger: Create payment record on project approval
DELIMITER //
CREATE TRIGGER `trg_create_payment_on_approval`
AFTER UPDATE ON `user_projects`
FOR EACH ROW
BEGIN
  IF NEW.status = 'approved' AND OLD.status != 'approved' THEN
    INSERT INTO payments (id, user_id, type, amount, status, description, created_at)
    VALUES (UUID(), NEW.user_id, 'earning', NEW.amount_earned, 'completed',
            CONCAT('Payment for project: ', (SELECT title FROM projects WHERE id = NEW.project_id)),
            NOW());
  END IF;
END//
DELIMITER ;

-- Trigger: Update user balance on withdrawal
DELIMITER //
CREATE TRIGGER `trg_update_balance_on_withdrawal`
AFTER UPDATE ON `withdrawals`
FOR EACH ROW
BEGIN
  IF NEW.status = 'completed' AND OLD.status != 'completed' THEN
    UPDATE users
    SET balance = balance - NEW.amount
    WHERE id = NEW.user_id;
  END IF;
END//
DELIMITER ;

SET FOREIGN_KEY_CHECKS = 1;

-- ============================================
-- End of Database Export
-- ============================================
