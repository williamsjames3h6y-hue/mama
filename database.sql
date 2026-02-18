CREATE DATABASE IF NOT EXISTS earningsllc;
USE earningsllc;

CREATE TABLE IF NOT EXISTS users (
  id INT AUTO_INCREMENT PRIMARY KEY,
  email VARCHAR(255) UNIQUE NOT NULL,
  password VARCHAR(255) NOT NULL,
  full_name VARCHAR(255) NOT NULL,
  role ENUM('user', 'admin') DEFAULT 'user',
  vip_level INT DEFAULT 1,
  balance DECIMAL(10, 2) DEFAULT 0.00,
  total_earned DECIMAL(10, 2) DEFAULT 0.00,
  referral_code VARCHAR(50) UNIQUE NOT NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  INDEX idx_email (email),
  INDEX idx_referral_code (referral_code)
);

CREATE TABLE IF NOT EXISTS projects (
  id INT AUTO_INCREMENT PRIMARY KEY,
  title VARCHAR(255) NOT NULL,
  description TEXT,
  project_type VARCHAR(100) NOT NULL,
  rate_min DECIMAL(10, 2) NOT NULL,
  rate_max DECIMAL(10, 2) NOT NULL,
  vip_level_required INT DEFAULT 1,
  status ENUM('open', 'closed') DEFAULT 'open',
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  INDEX idx_vip_level (vip_level_required),
  INDEX idx_status (status)
);

CREATE TABLE IF NOT EXISTS user_projects (
  id INT AUTO_INCREMENT PRIMARY KEY,
  user_id INT NOT NULL,
  project_id INT NOT NULL,
  status ENUM('submitted', 'approved', 'rejected') DEFAULT 'submitted',
  submitted_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
  FOREIGN KEY (project_id) REFERENCES projects(id) ON DELETE CASCADE,
  INDEX idx_user_id (user_id),
  INDEX idx_project_id (project_id)
);

CREATE TABLE IF NOT EXISTS payments (
  id INT AUTO_INCREMENT PRIMARY KEY,
  user_id INT NOT NULL,
  type ENUM('deposit', 'withdrawal', 'referral', 'project') DEFAULT 'project',
  amount DECIMAL(10, 2) NOT NULL,
  status ENUM('pending', 'completed', 'failed') DEFAULT 'pending',
  description TEXT,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
  INDEX idx_user_id (user_id),
  INDEX idx_status (status)
);

CREATE TABLE IF NOT EXISTS site_settings (
  `key` VARCHAR(100) PRIMARY KEY,
  value TEXT NOT NULL
);

INSERT INTO site_settings (`key`, value) VALUES
  ('vip1_rate', '10'),
  ('vip2_rate', '20'),
  ('vip3_rate', '50'),
  ('vip4_rate', '100'),
  ('vip5_rate', '200'),
  ('referral_bonus', '300'),
  ('min_withdrawal', '50')
ON DUPLICATE KEY UPDATE value = VALUES(value);
