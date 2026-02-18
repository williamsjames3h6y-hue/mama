-- DataOptimize Pro Database Schema
-- MySQL Database Structure

USE u800179901_70;

-- Users table
CREATE TABLE IF NOT EXISTS users (
  id VARCHAR(36) PRIMARY KEY DEFAULT (UUID()),
  email VARCHAR(255) UNIQUE NOT NULL,
  password VARCHAR(255) NOT NULL,
  full_name VARCHAR(255) NOT NULL,
  role ENUM('user', 'admin') DEFAULT 'user',
  vip_level INT DEFAULT 1,
  balance DECIMAL(10,2) DEFAULT 0.00,
  total_earned DECIMAL(10,2) DEFAULT 0.00,
  referral_code VARCHAR(50) UNIQUE,
  referred_by VARCHAR(36) NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  INDEX idx_email (email),
  INDEX idx_referral_code (referral_code),
  INDEX idx_vip_level (vip_level),
  FOREIGN KEY (referred_by) REFERENCES users(id) ON DELETE SET NULL
) ENGINE=InnoDB;

-- Projects table
CREATE TABLE IF NOT EXISTS projects (
  id VARCHAR(36) PRIMARY KEY DEFAULT (UUID()),
  title VARCHAR(255) NOT NULL,
  description TEXT NOT NULL,
  rate_min DECIMAL(10,2) NOT NULL,
  rate_max DECIMAL(10,2) NOT NULL,
  project_type VARCHAR(50) DEFAULT 'Remote',
  vip_level_required INT DEFAULT 1,
  status ENUM('open', 'closed') DEFAULT 'open',
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  INDEX idx_status (status),
  INDEX idx_vip_level (vip_level_required)
) ENGINE=InnoDB;

-- User Projects (applications/submissions)
CREATE TABLE IF NOT EXISTS user_projects (
  id VARCHAR(36) PRIMARY KEY DEFAULT (UUID()),
  user_id VARCHAR(36) NOT NULL,
  project_id VARCHAR(36) NOT NULL,
  status ENUM('submitted', 'approved', 'rejected') DEFAULT 'submitted',
  hours_worked DECIMAL(10,2) DEFAULT 0,
  amount_earned DECIMAL(10,2) DEFAULT 0,
  submitted_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  INDEX idx_user_id (user_id),
  INDEX idx_project_id (project_id),
  UNIQUE KEY unique_user_project (user_id, project_id),
  FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
  FOREIGN KEY (project_id) REFERENCES projects(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- Payments table
CREATE TABLE IF NOT EXISTS payments (
  id VARCHAR(36) PRIMARY KEY DEFAULT (UUID()),
  user_id VARCHAR(36) NOT NULL,
  type ENUM('earning', 'deposit', 'withdrawal') NOT NULL,
  amount DECIMAL(10,2) NOT NULL,
  status ENUM('pending', 'completed', 'failed') DEFAULT 'pending',
  description TEXT,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  INDEX idx_user_id (user_id),
  INDEX idx_status (status),
  FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- Site settings table
CREATE TABLE IF NOT EXISTS site_settings (
  id VARCHAR(36) PRIMARY KEY DEFAULT (UUID()),
  `key` VARCHAR(100) UNIQUE NOT NULL,
  `value` TEXT,
  updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- Insert default settings
INSERT INTO site_settings (`key`, `value`) VALUES
  ('site_name', 'EarningsLLC'),
  ('site_currency', 'USD'),
  ('site_currency_symbol', '$'),
  ('referral_bonus', '300'),
  ('min_withdrawal', '50'),
  ('payment_gateway', 'stripe'),
  ('contact_email', 'admin@earningsllc.com'),
  ('contact_phone', '+1 (555) 123-4567'),
  ('contact_address', '123 Business Street\nSuite 100\nNew York, NY 10001\nUnited States'),
  ('vip1_rate', '10'),
  ('vip2_rate', '20'),
  ('vip3_rate', '50'),
  ('vip4_rate', '100'),
  ('vip5_rate', '200')
ON DUPLICATE KEY UPDATE `value` = VALUES(`value`);

-- Insert default admin user
-- Password: admin123 (hashed with bcrypt)
INSERT INTO users (id, email, password, full_name, role, referral_code) VALUES
  (UUID(), 'admin@dataoptimize.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Administrator', 'admin', 'ADMIN2024')
ON DUPLICATE KEY UPDATE email = email;

-- Insert sample projects
INSERT INTO projects (id, title, description, rate_min, rate_max, project_type, vip_level_required, status) VALUES
  (UUID(), 'Basic Data Entry', 'Simple data entry tasks for beginners', 10, 15, 'Remote - Part-time', 1, 'open'),
  (UUID(), 'Content Review', 'Review and moderate user-generated content', 20, 25, 'Remote - Contract', 2, 'open'),
  (UUID(), 'Project Horizon', 'Use your expertise to show how real professionals work and how AI compares', 50, 75, 'Remote - Part-time', 3, 'open'),
  (UUID(), 'Data Scientists', 'Work on machine learning projects and data analysis tasks.', 100, 120, 'Remote - Full-time', 4, 'open'),
  (UUID(), 'Senior Consultants', 'High-level consulting projects requiring extensive experience', 200, 250, 'Remote - Contract', 5, 'open')
ON DUPLICATE KEY UPDATE title = title;
