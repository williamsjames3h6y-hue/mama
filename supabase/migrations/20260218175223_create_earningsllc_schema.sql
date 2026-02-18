/*
  # EarningsLLC Database Schema Migration
  
  ## Overview
  Complete database schema for EarningsLLC platform - a project-based earnings system with VIP levels
  
  ## New Tables
  
  ### 1. users
  - Core user account information
  - Fields: id, email, full_name, password_hash, role, vip_level, balance, total_earned, referral_code, referred_by, last_login
  - Indexes: email, referral_code, referred_by
  
  ### 2. projects  
  - Available projects/tasks for users
  - Fields: id, title, description, rate_min, rate_max, project_type, vip_level_required, status
  - Indexes: status, vip_level_required
  
  ### 3. user_projects
  - Tracks user participation in projects
  - Fields: id, user_id, project_id, status, hours_worked, amount_earned, submitted_at
  - Indexes: user_id, project_id, status
  
  ### 4. payments
  - All payment transactions
  - Fields: id, user_id, type, amount, status, description
  - Indexes: user_id, type, status
  
  ### 5. withdrawals
  - Withdrawal requests tracking
  - Fields: id, user_id, amount, method, account_details, status, processed_at
  - Indexes: user_id, status
  
  ### 6. site_settings
  - Platform configuration
  - Fields: id, key, value, type
  - Index: key
  
  ## Security
  - RLS enabled on all tables
  - Policies for authenticated user access
  - Admin-only policies for sensitive operations
  - Ownership checks for user data
  
  ## Notes
  - All IDs use UUID v4
  - Timestamps use timestamptz
  - Foreign keys with CASCADE delete
  - Default values for balance, rates, levels
*/

-- Enable UUID extension
CREATE EXTENSION IF NOT EXISTS "uuid-ossp";

-- ============================================
-- Table: users
-- ============================================
CREATE TABLE IF NOT EXISTS users (
  id UUID PRIMARY KEY DEFAULT uuid_generate_v4(),
  email TEXT NOT NULL UNIQUE,
  full_name TEXT NOT NULL,
  password_hash TEXT NOT NULL,
  role TEXT DEFAULT 'user' CHECK (role IN ('user', 'admin')),
  vip_level INTEGER DEFAULT 1 CHECK (vip_level >= 1 AND vip_level <= 5),
  balance DECIMAL(10, 2) DEFAULT 0.00,
  total_earned DECIMAL(10, 2) DEFAULT 0.00,
  referral_code TEXT UNIQUE,
  referred_by TEXT,
  last_login TIMESTAMPTZ,
  created_at TIMESTAMPTZ DEFAULT now(),
  updated_at TIMESTAMPTZ DEFAULT now()
);

CREATE INDEX IF NOT EXISTS idx_users_email ON users(email);
CREATE INDEX IF NOT EXISTS idx_users_referral_code ON users(referral_code);
CREATE INDEX IF NOT EXISTS idx_users_referred_by ON users(referred_by);

-- RLS for users
ALTER TABLE users ENABLE ROW LEVEL SECURITY;

CREATE POLICY "Users can read own data"
  ON users FOR SELECT
  TO authenticated
  USING (auth.uid() = id OR (SELECT role FROM users WHERE id = auth.uid()) = 'admin');

CREATE POLICY "Users can update own data"
  ON users FOR UPDATE
  TO authenticated
  USING (auth.uid() = id)
  WITH CHECK (auth.uid() = id);

CREATE POLICY "Admins can manage all users"
  ON users FOR ALL
  TO authenticated
  USING ((SELECT role FROM users WHERE id = auth.uid()) = 'admin')
  WITH CHECK ((SELECT role FROM users WHERE id = auth.uid()) = 'admin');

-- ============================================
-- Table: projects
-- ============================================
CREATE TABLE IF NOT EXISTS projects (
  id UUID PRIMARY KEY DEFAULT uuid_generate_v4(),
  title TEXT NOT NULL,
  description TEXT NOT NULL,
  rate_min DECIMAL(10, 2) NOT NULL,
  rate_max DECIMAL(10, 2) NOT NULL,
  project_type TEXT DEFAULT 'Remote',
  vip_level_required INTEGER DEFAULT 1 CHECK (vip_level_required >= 1 AND vip_level_required <= 5),
  status TEXT DEFAULT 'open' CHECK (status IN ('open', 'closed')),
  created_at TIMESTAMPTZ DEFAULT now()
);

CREATE INDEX IF NOT EXISTS idx_projects_status ON projects(status);
CREATE INDEX IF NOT EXISTS idx_projects_vip_level ON projects(vip_level_required);

-- RLS for projects
ALTER TABLE projects ENABLE ROW LEVEL SECURITY;

CREATE POLICY "Anyone can read open projects"
  ON projects FOR SELECT
  TO authenticated
  USING (status = 'open');

CREATE POLICY "Admins can manage projects"
  ON projects FOR ALL
  TO authenticated
  USING ((SELECT role FROM users WHERE id = auth.uid()) = 'admin')
  WITH CHECK ((SELECT role FROM users WHERE id = auth.uid()) = 'admin');

-- ============================================
-- Table: user_projects
-- ============================================
CREATE TABLE IF NOT EXISTS user_projects (
  id UUID PRIMARY KEY DEFAULT uuid_generate_v4(),
  user_id UUID NOT NULL REFERENCES users(id) ON DELETE CASCADE,
  project_id UUID NOT NULL REFERENCES projects(id) ON DELETE CASCADE,
  status TEXT DEFAULT 'submitted' CHECK (status IN ('submitted', 'approved', 'rejected')),
  hours_worked DECIMAL(10, 2) DEFAULT 0,
  amount_earned DECIMAL(10, 2) DEFAULT 0,
  submitted_at TIMESTAMPTZ DEFAULT now()
);

CREATE INDEX IF NOT EXISTS idx_user_projects_user_id ON user_projects(user_id);
CREATE INDEX IF NOT EXISTS idx_user_projects_project_id ON user_projects(project_id);
CREATE INDEX IF NOT EXISTS idx_user_projects_status ON user_projects(status);

-- RLS for user_projects
ALTER TABLE user_projects ENABLE ROW LEVEL SECURITY;

CREATE POLICY "Users can read own project submissions"
  ON user_projects FOR SELECT
  TO authenticated
  USING (auth.uid() = user_id OR (SELECT role FROM users WHERE id = auth.uid()) = 'admin');

CREATE POLICY "Users can create own project submissions"
  ON user_projects FOR INSERT
  TO authenticated
  WITH CHECK (auth.uid() = user_id);

CREATE POLICY "Admins can manage all project submissions"
  ON user_projects FOR ALL
  TO authenticated
  USING ((SELECT role FROM users WHERE id = auth.uid()) = 'admin')
  WITH CHECK ((SELECT role FROM users WHERE id = auth.uid()) = 'admin');

-- ============================================
-- Table: payments
-- ============================================
CREATE TABLE IF NOT EXISTS payments (
  id UUID PRIMARY KEY DEFAULT uuid_generate_v4(),
  user_id UUID NOT NULL REFERENCES users(id) ON DELETE CASCADE,
  type TEXT NOT NULL CHECK (type IN ('earning', 'deposit', 'withdrawal', 'bonus')),
  amount DECIMAL(10, 2) NOT NULL,
  status TEXT DEFAULT 'pending' CHECK (status IN ('pending', 'completed', 'failed')),
  description TEXT,
  created_at TIMESTAMPTZ DEFAULT now()
);

CREATE INDEX IF NOT EXISTS idx_payments_user_id ON payments(user_id);
CREATE INDEX IF NOT EXISTS idx_payments_type ON payments(type);
CREATE INDEX IF NOT EXISTS idx_payments_status ON payments(status);

-- RLS for payments
ALTER TABLE payments ENABLE ROW LEVEL SECURITY;

CREATE POLICY "Users can read own payments"
  ON payments FOR SELECT
  TO authenticated
  USING (auth.uid() = user_id OR (SELECT role FROM users WHERE id = auth.uid()) = 'admin');

CREATE POLICY "Admins can manage payments"
  ON payments FOR ALL
  TO authenticated
  USING ((SELECT role FROM users WHERE id = auth.uid()) = 'admin')
  WITH CHECK ((SELECT role FROM users WHERE id = auth.uid()) = 'admin');

-- ============================================
-- Table: withdrawals
-- ============================================
CREATE TABLE IF NOT EXISTS withdrawals (
  id UUID PRIMARY KEY DEFAULT uuid_generate_v4(),
  user_id UUID NOT NULL REFERENCES users(id) ON DELETE CASCADE,
  amount DECIMAL(10, 2) NOT NULL CHECK (amount > 0),
  method TEXT NOT NULL,
  account_details JSONB,
  status TEXT DEFAULT 'pending' CHECK (status IN ('pending', 'approved', 'rejected', 'completed')),
  processed_at TIMESTAMPTZ,
  created_at TIMESTAMPTZ DEFAULT now()
);

CREATE INDEX IF NOT EXISTS idx_withdrawals_user_id ON withdrawals(user_id);
CREATE INDEX IF NOT EXISTS idx_withdrawals_status ON withdrawals(status);

-- RLS for withdrawals
ALTER TABLE withdrawals ENABLE ROW LEVEL SECURITY;

CREATE POLICY "Users can read own withdrawals"
  ON withdrawals FOR SELECT
  TO authenticated
  USING (auth.uid() = user_id OR (SELECT role FROM users WHERE id = auth.uid()) = 'admin');

CREATE POLICY "Users can create own withdrawal requests"
  ON withdrawals FOR INSERT
  TO authenticated
  WITH CHECK (auth.uid() = user_id);

CREATE POLICY "Admins can manage all withdrawals"
  ON withdrawals FOR ALL
  TO authenticated
  USING ((SELECT role FROM users WHERE id = auth.uid()) = 'admin')
  WITH CHECK ((SELECT role FROM users WHERE id = auth.uid()) = 'admin');

-- ============================================
-- Table: site_settings
-- ============================================
CREATE TABLE IF NOT EXISTS site_settings (
  id UUID PRIMARY KEY DEFAULT uuid_generate_v4(),
  key TEXT NOT NULL UNIQUE,
  value TEXT,
  type TEXT DEFAULT 'string' CHECK (type IN ('string', 'number', 'boolean', 'json')),
  updated_at TIMESTAMPTZ DEFAULT now()
);

CREATE INDEX IF NOT EXISTS idx_site_settings_key ON site_settings(key);

-- RLS for site_settings
ALTER TABLE site_settings ENABLE ROW LEVEL SECURITY;

CREATE POLICY "Anyone can read settings"
  ON site_settings FOR SELECT
  TO authenticated
  USING (true);

CREATE POLICY "Admins can manage settings"
  ON site_settings FOR ALL
  TO authenticated
  USING ((SELECT role FROM users WHERE id = auth.uid()) = 'admin')
  WITH CHECK ((SELECT role FROM users WHERE id = auth.uid()) = 'admin');

-- ============================================
-- Insert default settings
-- ============================================
INSERT INTO site_settings (key, value, type) VALUES
('site_name', 'EarningsLLC', 'string'),
('contact_email', 'admin@earningsllc.com', 'string'),
('contact_phone', '+1 (555) 123-4567', 'string'),
('contact_address', '123 Business Street\nNew York, NY 10001\nUnited States', 'string'),
('vip1_rate', '10', 'number'),
('vip2_rate', '20', 'number'),
('vip3_rate', '50', 'number'),
('vip4_rate', '100', 'number'),
('vip5_rate', '200', 'number'),
('referral_bonus', '300', 'number'),
('min_withdrawal', '50', 'number')
ON CONFLICT (key) DO NOTHING;

-- ============================================
-- Insert sample projects
-- ============================================
INSERT INTO projects (title, description, rate_min, rate_max, project_type, vip_level_required, status) VALUES
('Basic Data Entry', 'Simple data entry tasks for beginners', 10.00, 15.00, 'Remote - Part-time', 1, 'open'),
('Content Review', 'Review and moderate user-generated content', 20.00, 25.00, 'Remote - Contract', 2, 'open'),
('Professional Writing', 'Create high-quality content for websites and blogs', 50.00, 75.00, 'Remote - Part-time', 3, 'open'),
('Data Science Projects', 'Work on machine learning and data analysis tasks', 100.00, 120.00, 'Remote - Full-time', 4, 'open'),
('Senior Consulting', 'High-level consulting requiring extensive experience', 200.00, 250.00, 'Remote - Contract', 5, 'open'),
('Data Cleaning & Validation', 'Clean and validate large datasets, remove duplicates, fix formatting issues', 15.00, 25.00, 'Remote', 1, 'open'),
('Database Query Optimization', 'Optimize SQL queries for better performance, create indexes', 30.00, 50.00, 'Remote', 2, 'open'),
('ETL Pipeline Development', 'Build Extract, Transform, Load pipelines for automated data processing', 50.00, 80.00, 'Remote', 3, 'open'),
('Data Visualization Dashboard', 'Create interactive dashboards using modern visualization tools', 40.00, 70.00, 'Remote', 2, 'open'),
('Machine Learning Model Training', 'Train and optimize machine learning models for predictive analytics', 80.00, 150.00, 'Remote', 4, 'open')
ON CONFLICT DO NOTHING;