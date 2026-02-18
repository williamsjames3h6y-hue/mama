-- DataOptimize Pro Database Schema
-- Ready to import into existing database
-- Compatible with phpMyAdmin and MySQL 5.7+

-- Users table
CREATE TABLE IF NOT EXISTS `users` (
  `id` VARCHAR(36) PRIMARY KEY,
  `email` VARCHAR(255) UNIQUE NOT NULL,
  `password` VARCHAR(255) NOT NULL,
  `full_name` VARCHAR(255) NOT NULL,
  `role` ENUM('user', 'admin') DEFAULT 'user',
  `balance` DECIMAL(10,2) DEFAULT 0.00,
  `total_earned` DECIMAL(10,2) DEFAULT 0.00,
  `referral_code` VARCHAR(50) UNIQUE,
  `referred_by` VARCHAR(36) NULL,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  INDEX `idx_email` (`email`),
  INDEX `idx_referral_code` (`referral_code`),
  FOREIGN KEY (`referred_by`) REFERENCES `users`(`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Projects table
CREATE TABLE IF NOT EXISTS `projects` (
  `id` VARCHAR(36) PRIMARY KEY,
  `title` VARCHAR(255) NOT NULL,
  `description` TEXT NOT NULL,
  `rate_min` DECIMAL(10,2) NOT NULL,
  `rate_max` DECIMAL(10,2) NOT NULL,
  `project_type` VARCHAR(50) DEFAULT 'Remote',
  `status` ENUM('open', 'closed') DEFAULT 'open',
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  INDEX `idx_status` (`status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- User Projects (applications/submissions)
CREATE TABLE IF NOT EXISTS `user_projects` (
  `id` VARCHAR(36) PRIMARY KEY,
  `user_id` VARCHAR(36) NOT NULL,
  `project_id` VARCHAR(36) NOT NULL,
  `status` ENUM('submitted', 'approved', 'rejected') DEFAULT 'submitted',
  `hours_worked` DECIMAL(10,2) DEFAULT 0,
  `amount_earned` DECIMAL(10,2) DEFAULT 0,
  `submitted_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  INDEX `idx_user_id` (`user_id`),
  INDEX `idx_project_id` (`project_id`),
  UNIQUE KEY `unique_user_project` (`user_id`, `project_id`),
  FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE CASCADE,
  FOREIGN KEY (`project_id`) REFERENCES `projects`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Payments table
CREATE TABLE IF NOT EXISTS `payments` (
  `id` VARCHAR(36) PRIMARY KEY,
  `user_id` VARCHAR(36) NOT NULL,
  `type` ENUM('earning', 'deposit', 'withdrawal') NOT NULL,
  `amount` DECIMAL(10,2) NOT NULL,
  `status` ENUM('pending', 'completed', 'failed') DEFAULT 'pending',
  `description` TEXT,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  INDEX `idx_user_id` (`user_id`),
  INDEX `idx_status` (`status`),
  FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Site settings table
CREATE TABLE IF NOT EXISTS `site_settings` (
  `id` VARCHAR(36) PRIMARY KEY,
  `key` VARCHAR(100) UNIQUE NOT NULL,
  `value` TEXT,
  `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Insert default settings
INSERT INTO `site_settings` (`id`, `key`, `value`) VALUES
  ('550e8400-e29b-41d4-a716-446655440001', 'site_name', 'DataOptimize Pro'),
  ('550e8400-e29b-41d4-a716-446655440002', 'site_currency', 'USD'),
  ('550e8400-e29b-41d4-a716-446655440003', 'site_currency_symbol', '$'),
  ('550e8400-e29b-41d4-a716-446655440004', 'referral_bonus', '300'),
  ('550e8400-e29b-41d4-a716-446655440005', 'min_withdrawal', '50'),
  ('550e8400-e29b-41d4-a716-446655440006', 'payment_gateway', 'stripe'),
  ('550e8400-e29b-41d4-a716-446655440007', 'contact_email', 'admin@dataoptimize.com')
ON DUPLICATE KEY UPDATE `value` = VALUES(`value`);

-- Insert default admin user
-- Email: admin@dataoptimize.com
-- Password: admin123
INSERT INTO `users` (`id`, `email`, `password`, `full_name`, `role`, `balance`, `total_earned`, `referral_code`, `referred_by`, `created_at`) VALUES
  ('a1b2c3d4-e5f6-7890-abcd-ef1234567890', 'admin@dataoptimize.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Administrator', 'admin', 0.00, 0.00, 'ADMIN2024', NULL, CURRENT_TIMESTAMP)
ON DUPLICATE KEY UPDATE `email` = `email`;

-- Insert sample demo user
-- Email: demo@example.com
-- Password: demo123
INSERT INTO `users` (`id`, `email`, `password`, `full_name`, `role`, `balance`, `total_earned`, `referral_code`, `referred_by`, `created_at`) VALUES
  ('b2c3d4e5-f6a7-8901-bcde-f12345678901', 'demo@example.com', '$2y$10$EUIYQFl3OdgF0.HLqB/L0O5HWJ/i0SLmjKJxWfqYh6Q.Xp1pN0H0G', 'Demo User', 'user', 150.00, 850.00, 'DEMO2024', NULL, CURRENT_TIMESTAMP)
ON DUPLICATE KEY UPDATE `email` = `email`;

-- Insert sample projects
INSERT INTO `projects` (`id`, `title`, `description`, `rate_min`, `rate_max`, `project_type`, `status`, `created_at`) VALUES
  ('p1a2b3c4-d5e6-7890-abcd-ef1234567890', 'Project Horizon', 'Use your expertise to show how real professionals work and how AI compares. Help us build better AI models by demonstrating professional workflows and decision-making processes.', 50.00, 300.00, 'Remote - Part-time', 'open', CURRENT_TIMESTAMP),
  ('p2b3c4d5-e6f7-8901-bcde-f12345678901', 'Legal Documentation Review', 'Complete a quick form to be considered for upcoming legal projects. Review legal documents, contracts, and provide professional opinions on various legal matters.', 90.00, 120.00, 'Remote - Contract', 'open', CURRENT_TIMESTAMP),
  ('p3c4d5e6-f7a8-9012-cdef-123456789012', 'Medical Consultation Services', 'Complete a quick form to be considered for upcoming medical projects. Provide medical advice, review patient cases, and assist with medical documentation.', 175.00, 200.00, 'Remote - Contract', 'open', CURRENT_TIMESTAMP),
  ('p4d5e6f7-a8b9-0123-def1-234567890123', 'Data Science & Analytics', 'Work on machine learning projects and data analysis tasks. Build predictive models, perform statistical analysis, and create data visualizations for various clients.', 80.00, 150.00, 'Remote - Full-time', 'open', CURRENT_TIMESTAMP),
  ('p5e6f7a8-b9c0-1234-ef12-345678901234', 'Software Development', 'Build and maintain web applications using modern technologies. Work with React, Node.js, Python, and cloud platforms to deliver enterprise solutions.', 70.00, 180.00, 'Remote - Contract', 'open', CURRENT_TIMESTAMP),
  ('p6f7a8b9-c0d1-2345-f123-456789012345', 'Content Writing & Editing', 'Create high-quality content for websites, blogs, and marketing materials. Edit and proofread technical documentation and business communications.', 40.00, 80.00, 'Remote - Part-time', 'open', CURRENT_TIMESTAMP),
  ('p7a8b9c0-d1e2-3456-1234-567890123456', 'Financial Analysis', 'Analyze financial statements, create forecasts, and provide investment recommendations. Work with startups and established businesses on financial planning.', 100.00, 160.00, 'Remote - Contract', 'open', CURRENT_TIMESTAMP),
  ('p8b9c0d1-e2f3-4567-2345-678901234567', 'Graphic Design Projects', 'Design logos, marketing materials, and user interfaces. Create visual content for web and mobile applications following brand guidelines.', 45.00, 95.00, 'Remote - Part-time', 'open', CURRENT_TIMESTAMP)
ON DUPLICATE KEY UPDATE `title` = `title`;

-- Insert sample payments for demo user
INSERT INTO `payments` (`id`, `user_id`, `type`, `amount`, `status`, `description`, `created_at`) VALUES
  ('pay1a2b3-c4d5-e6f7-8901-234567890123', 'b2c3d4e5-f6a7-8901-bcde-f12345678901', 'earning', 250.00, 'completed', 'Project Horizon - Week 1 Payment', DATE_SUB(CURRENT_TIMESTAMP, INTERVAL 10 DAY)),
  ('pay2b3c4-d5e6-f7a8-9012-345678901234', 'b2c3d4e5-f6a7-8901-bcde-f12345678901', 'earning', 300.00, 'completed', 'Data Science Project - Completion Bonus', DATE_SUB(CURRENT_TIMESTAMP, INTERVAL 7 DAY)),
  ('pay3c4d5-e6f7-a8b9-0123-456789012345', 'b2c3d4e5-f6a7-8901-bcde-f12345678901', 'withdrawal', 100.00, 'completed', 'Withdrawal to Bank Account', DATE_SUB(CURRENT_TIMESTAMP, INTERVAL 5 DAY)),
  ('pay4d5e6-f7a8-b9c0-1234-567890123456', 'b2c3d4e5-f6a7-8901-bcde-f12345678901', 'earning', 300.00, 'completed', 'Software Development - Sprint 1', DATE_SUB(CURRENT_TIMESTAMP, INTERVAL 2 DAY)),
  ('pay5e6f7-a8b9-c0d1-2345-678901234567', 'b2c3d4e5-f6a7-8901-bcde-f12345678901', 'earning', 150.00, 'pending', 'Content Writing Project - In Progress', CURRENT_TIMESTAMP)
ON DUPLICATE KEY UPDATE `description` = `description`;

-- Insert sample user projects (applications)
INSERT INTO `user_projects` (`id`, `user_id`, `project_id`, `status`, `hours_worked`, `amount_earned`, `submitted_at`) VALUES
  ('up1a2b3c-4d5e-6f78-90ab-cdef12345678', 'b2c3d4e5-f6a7-8901-bcde-f12345678901', 'p1a2b3c4-d5e6-7890-abcd-ef1234567890', 'approved', 15.50, 250.00, DATE_SUB(CURRENT_TIMESTAMP, INTERVAL 12 DAY)),
  ('up2b3c4d-5e6f-7890-abcd-ef1234567890', 'b2c3d4e5-f6a7-8901-bcde-f12345678901', 'p4d5e6f7-a8b9-0123-def1-234567890123', 'approved', 20.00, 600.00, DATE_SUB(CURRENT_TIMESTAMP, INTERVAL 8 DAY)),
  ('up3c4d5e-6f78-90ab-cdef-123456789012', 'b2c3d4e5-f6a7-8901-bcde-f12345678901', 'p5e6f7a8-b9c0-1234-ef12-345678901234', 'submitted', 0.00, 0.00, DATE_SUB(CURRENT_TIMESTAMP, INTERVAL 1 DAY))
ON DUPLICATE KEY UPDATE `status` = `status`;
