-- Add VIP Level System Migration
-- This migration adds VIP levels to users and projects

-- Add vip_level column to users table if it doesn't exist
SET @dbname = DATABASE();
SET @tablename = 'users';
SET @columnname = 'vip_level';
SET @preparedStatement = (SELECT IF(
  (
    SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS
    WHERE
      (table_name = @tablename)
      AND (table_schema = @dbname)
      AND (column_name = @columnname)
  ) > 0,
  'SELECT 1',
  CONCAT('ALTER TABLE ', @tablename, ' ADD ', @columnname, ' INT DEFAULT 1 AFTER role')
));
PREPARE alterIfNotExists FROM @preparedStatement;
EXECUTE alterIfNotExists;
DEALLOCATE PREPARE alterIfNotExists;

-- Add index for vip_level in users table if it doesn't exist
SET @indexname = 'idx_vip_level';
SET @preparedStatement = (SELECT IF(
  (
    SELECT COUNT(*) FROM INFORMATION_SCHEMA.STATISTICS
    WHERE
      (table_name = @tablename)
      AND (table_schema = @dbname)
      AND (index_name = @indexname)
  ) > 0,
  'SELECT 1',
  CONCAT('ALTER TABLE ', @tablename, ' ADD INDEX ', @indexname, ' (vip_level)')
));
PREPARE alterIfNotExists FROM @preparedStatement;
EXECUTE alterIfNotExists;
DEALLOCATE PREPARE alterIfNotExists;

-- Add vip_level_required column to projects table if it doesn't exist
SET @tablename = 'projects';
SET @columnname = 'vip_level_required';
SET @preparedStatement = (SELECT IF(
  (
    SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS
    WHERE
      (table_name = @tablename)
      AND (table_schema = @dbname)
      AND (column_name = @columnname)
  ) > 0,
  'SELECT 1',
  CONCAT('ALTER TABLE ', @tablename, ' ADD ', @columnname, ' INT DEFAULT 1 AFTER project_type')
));
PREPARE alterIfNotExists FROM @preparedStatement;
EXECUTE alterIfNotExists;
DEALLOCATE PREPARE alterIfNotExists;

-- Add index for vip_level_required in projects table if it doesn't exist
SET @indexname = 'idx_vip_level';
SET @preparedStatement = (SELECT IF(
  (
    SELECT COUNT(*) FROM INFORMATION_SCHEMA.STATISTICS
    WHERE
      (table_name = @tablename)
      AND (table_schema = @dbname)
      AND (index_name = @indexname)
  ) > 0,
  'SELECT 1',
  CONCAT('ALTER TABLE ', @tablename, ' ADD INDEX ', @indexname, ' (vip_level_required)')
));
PREPARE alterIfNotExists FROM @preparedStatement;
EXECUTE alterIfNotExists;
DEALLOCATE PREPARE alterIfNotExists;

-- Insert VIP rate settings
INSERT INTO site_settings (`key`, `value`) VALUES
  ('vip1_rate', '10'),
  ('vip2_rate', '20'),
  ('vip3_rate', '50'),
  ('vip4_rate', '100'),
  ('vip5_rate', '200'),
  ('contact_phone', '+1 (555) 123-4567'),
  ('contact_address', '123 Business Street\nSuite 100\nNew York, NY 10001\nUnited States')
ON DUPLICATE KEY UPDATE `value` = VALUES(`value`);

-- Update site name
UPDATE site_settings SET `value` = 'EarningsLLC' WHERE `key` = 'site_name';
UPDATE site_settings SET `value` = 'admin@earningsllc.com' WHERE `key` = 'contact_email';
