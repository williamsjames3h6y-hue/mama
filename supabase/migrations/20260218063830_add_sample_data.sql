/*
  # Add Sample Data for Testing

  1. Sample Projects
    - Adding initial projects for users to explore
    - Various project types and rate ranges

  2. Admin User
    - Creating a default admin account for site management
    - Email: admin@dataoptimize.com
    - Password: admin123 (should be changed after first login)
*/

-- Insert sample projects
INSERT INTO projects (title, description, rate_min, rate_max, project_type, status) VALUES
  ('Project Horizon', 'Use your expertise to show how real professionals work and how AI compares', 50, 300, 'Part-time', 'open'),
  ('Lawyers', 'Complete a quick form to be considered for upcoming legal projects.', 90, 120, 'Contract', 'open'),
  ('Physicians (MD/DO)', 'Complete a quick form to be considered for upcoming medical projects.', 175, 200, 'Contract', 'open'),
  ('Data Labeling Specialists', 'Help improve AI models by labeling and categorizing data accurately.', 25, 50, 'Remote', 'open'),
  ('Content Moderators', 'Review and moderate user-generated content following platform guidelines.', 30, 45, 'Remote', 'open')
ON CONFLICT DO NOTHING;

-- Create admin user (password is 'admin123' - hashed with bcrypt)
-- Note: This is a sample password. Change it immediately after first login!
INSERT INTO users (email, password, full_name, role, referral_code) VALUES
  ('admin@dataoptimize.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Admin User', 'admin', 'ADMIN001')
ON CONFLICT (email) DO NOTHING;
