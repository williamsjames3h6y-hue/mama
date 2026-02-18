-- EarningsLLC MySQL Database Schema
-- Generated for production deployment
--
-- Usage: mysql -u username -p database_name < database.sql

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";

--
-- Database: `earningsllc`
--

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE IF NOT EXISTS `users` (
  `id` char(36) NOT NULL,
  `email` varchar(255) NOT NULL,
  `full_name` varchar(255) NOT NULL,
  `password_hash` varchar(255) NOT NULL,
  `role` enum('user','admin') DEFAULT 'user',
  `vip_level` int(11) DEFAULT 1,
  `balance` decimal(10,2) DEFAULT 0.00,
  `total_earned` decimal(10,2) DEFAULT 0.00,
  `referral_code` varchar(50) DEFAULT NULL,
  `referred_by` varchar(50) DEFAULT NULL,
  `last_login` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `email` (`email`),
  UNIQUE KEY `referral_code` (`referral_code`),
  KEY `idx_users_referred_by` (`referred_by`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `projects`
--

CREATE TABLE IF NOT EXISTS `projects` (
  `id` char(36) NOT NULL,
  `title` varchar(255) NOT NULL,
  `description` text NOT NULL,
  `rate_min` decimal(10,2) NOT NULL,
  `rate_max` decimal(10,2) NOT NULL,
  `project_type` varchar(100) DEFAULT 'Remote',
  `vip_level_required` int(11) DEFAULT 1,
  `status` enum('open','closed') DEFAULT 'open',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_projects_status` (`status`),
  KEY `idx_projects_vip_level` (`vip_level_required`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `projects`
--

INSERT INTO `projects` (`id`, `title`, `description`, `rate_min`, `rate_max`, `project_type`, `vip_level_required`, `status`, `created_at`) VALUES
(UUID(), 'Basic Data Entry', 'Simple data entry tasks for beginners', 10.00, 15.00, 'Remote - Part-time', 1, 'open', NOW()),
(UUID(), 'Content Review', 'Review and moderate user-generated content', 20.00, 25.00, 'Remote - Contract', 2, 'open', NOW()),
(UUID(), 'Professional Writing', 'Create high-quality content for websites and blogs', 50.00, 75.00, 'Remote - Part-time', 3, 'open', NOW()),
(UUID(), 'Data Science Projects', 'Work on machine learning and data analysis tasks', 100.00, 120.00, 'Remote - Full-time', 4, 'open', NOW()),
(UUID(), 'Senior Consulting', 'High-level consulting requiring extensive experience', 200.00, 250.00, 'Remote - Contract', 5, 'open', NOW()),
(UUID(), 'Data Cleaning & Validation', 'Clean and validate large datasets, remove duplicates, fix formatting issues', 15.00, 25.00, 'Remote', 1, 'open', NOW()),
(UUID(), 'Database Query Optimization', 'Optimize SQL queries for better performance, create indexes', 30.00, 50.00, 'Remote', 2, 'open', NOW()),
(UUID(), 'ETL Pipeline Development', 'Build Extract, Transform, Load pipelines for automated data processing', 50.00, 80.00, 'Remote', 3, 'open', NOW()),
(UUID(), 'Data Visualization Dashboard', 'Create interactive dashboards using modern visualization tools', 40.00, 70.00, 'Remote', 2, 'open', NOW()),
(UUID(), 'Machine Learning Model Training', 'Train and optimize machine learning models for predictive analytics', 80.00, 150.00, 'Remote', 4, 'open', NOW());

-- --------------------------------------------------------

--
-- Table structure for table `user_projects`
--

CREATE TABLE IF NOT EXISTS `user_projects` (
  `id` char(36) NOT NULL,
  `user_id` char(36) NOT NULL,
  `project_id` char(36) NOT NULL,
  `status` enum('submitted','approved','rejected') DEFAULT 'submitted',
  `hours_worked` decimal(10,2) DEFAULT 0.00,
  `amount_earned` decimal(10,2) DEFAULT 0.00,
  `submitted_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_user_projects_user_id` (`user_id`),
  KEY `idx_user_projects_project_id` (`project_id`),
  KEY `idx_user_projects_status` (`status`),
  CONSTRAINT `fk_user_projects_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_user_projects_project` FOREIGN KEY (`project_id`) REFERENCES `projects` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `payments`
--

CREATE TABLE IF NOT EXISTS `payments` (
  `id` char(36) NOT NULL,
  `user_id` char(36) NOT NULL,
  `type` enum('earning','deposit','withdrawal','bonus') NOT NULL,
  `amount` decimal(10,2) NOT NULL,
  `status` enum('pending','completed','failed') DEFAULT 'pending',
  `description` text,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_payments_user_id` (`user_id`),
  KEY `idx_payments_type` (`type`),
  KEY `idx_payments_status` (`status`),
  CONSTRAINT `fk_payments_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `withdrawals`
--

CREATE TABLE IF NOT EXISTS `withdrawals` (
  `id` char(36) NOT NULL,
  `user_id` char(36) NOT NULL,
  `amount` decimal(10,2) NOT NULL,
  `method` varchar(100) NOT NULL,
  `account_details` text,
  `status` enum('pending','approved','rejected','completed') DEFAULT 'pending',
  `processed_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_withdrawals_user_id` (`user_id`),
  KEY `idx_withdrawals_status` (`status`),
  CONSTRAINT `fk_withdrawals_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `site_settings`
--

CREATE TABLE IF NOT EXISTS `site_settings` (
  `id` char(36) NOT NULL,
  `key` varchar(100) NOT NULL,
  `value` text,
  `type` enum('string','number','boolean','json') DEFAULT 'string',
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `key` (`key`),
  KEY `idx_site_settings_key` (`key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `site_settings`
--

INSERT INTO `site_settings` (`id`, `key`, `value`, `type`, `updated_at`) VALUES
(UUID(), 'site_name', 'EarningsLLC', 'string', NOW()),
(UUID(), 'contact_email', 'admin@earningsllc.com', 'string', NOW()),
(UUID(), 'contact_phone', '+1 (555) 123-4567', 'string', NOW()),
(UUID(), 'contact_address', '123 Business Street\nNew York, NY 10001\nUnited States', 'string', NOW()),
(UUID(), 'vip1_rate', '10', 'number', NOW()),
(UUID(), 'vip2_rate', '20', 'number', NOW()),
(UUID(), 'vip3_rate', '50', 'number', NOW()),
(UUID(), 'vip4_rate', '100', 'number', NOW()),
(UUID(), 'vip5_rate', '200', 'number', NOW()),
(UUID(), 'referral_bonus', '300', 'number', NOW()),
(UUID(), 'min_withdrawal', '50', 'number', NOW());

-- --------------------------------------------------------

--
-- Triggers for automatic UUID generation (if your MySQL version supports it)
--

DELIMITER $$

CREATE TRIGGER `before_insert_users` BEFORE INSERT ON `users`
FOR EACH ROW
BEGIN
  IF NEW.id IS NULL OR NEW.id = '' THEN
    SET NEW.id = UUID();
  END IF;
END$$

CREATE TRIGGER `before_insert_projects` BEFORE INSERT ON `projects`
FOR EACH ROW
BEGIN
  IF NEW.id IS NULL OR NEW.id = '' THEN
    SET NEW.id = UUID();
  END IF;
END$$

CREATE TRIGGER `before_insert_user_projects` BEFORE INSERT ON `user_projects`
FOR EACH ROW
BEGIN
  IF NEW.id IS NULL OR NEW.id = '' THEN
    SET NEW.id = UUID();
  END IF;
END$$

CREATE TRIGGER `before_insert_payments` BEFORE INSERT ON `payments`
FOR EACH ROW
BEGIN
  IF NEW.id IS NULL OR NEW.id = '' THEN
    SET NEW.id = UUID();
  END IF;
END$$

CREATE TRIGGER `before_insert_withdrawals` BEFORE INSERT ON `withdrawals`
FOR EACH ROW
BEGIN
  IF NEW.id IS NULL OR NEW.id = '' THEN
    SET NEW.id = UUID();
  END IF;
END$$

CREATE TRIGGER `before_insert_site_settings` BEFORE INSERT ON `site_settings`
FOR EACH ROW
BEGIN
  IF NEW.id IS NULL OR NEW.id = '' THEN
    SET NEW.id = UUID();
  END IF;
END$$

DELIMITER ;

-- --------------------------------------------------------

--
-- Create default admin user
-- Username: admin@earningsllc.com
-- Password: Admin@123 (change this after first login!)
--

INSERT INTO `users` (`id`, `email`, `full_name`, `password_hash`, `role`, `vip_level`, `balance`, `total_earned`, `referral_code`, `referred_by`, `last_login`, `created_at`, `updated_at`) VALUES
(UUID(), 'admin@earningsllc.com', 'System Administrator', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin', 5, 0.00, 0.00, 'ADMIN001', NULL, NULL, NOW(), NOW());

-- Note: Default admin password is 'Admin@123' - Please change it after first login!
