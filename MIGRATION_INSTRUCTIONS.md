# Database Migration Instructions for VIP Level System

## Important: Read Before Proceeding

This migration adds the VIP level system to your EarningsLLC platform. It's safe to run and will not delete any existing data.

## What This Migration Does

1. Adds `vip_level` column to the `users` table (default: 1)
2. Adds `vip_level_required` column to the `projects` table (default: 1)
3. Adds VIP rate settings to `site_settings` table
4. Adds contact information fields to `site_settings` table
5. Updates site name to "EarningsLLC"

## How to Run the Migration

### Option 1: Via phpMyAdmin (Recommended)

1. Log in to your hosting control panel (cPanel, etc.)
2. Open phpMyAdmin
3. Select your database: `u800179901_70`
4. Click on the "SQL" tab
5. Copy the entire contents of `database/migration_add_vip_levels.sql`
6. Paste it into the SQL query box
7. Click "Go" to execute

### Option 2: Via MySQL Command Line

If you have SSH access to your server:

```bash
mysql -u your_username -p u800179901_70 < database/migration_add_vip_levels.sql
```

### Option 3: Via PHP Script

1. Upload the `run-migration.php` file to your server root
2. Visit: https://earningsllc.online/run-migration.php
3. The script will automatically run the migration
4. **IMPORTANT:** Delete `run-migration.php` after running it for security

## Verification

After running the migration, verify it worked by:

1. Log in to the admin panel: https://earningsllc.online/admin
2. Go to Settings - you should see new fields:
   - Contact Phone
   - Contact Address
   - VIP 1-5 Rate settings
3. Go to Users - you should see a "VIP Level" column
4. Go to Projects - you should see a "VIP Level" column
5. Try creating a new project - you should see a "VIP Level Required" dropdown

## Default VIP Rates

After migration, these default rates will be set:
- VIP 1: $10/hr
- VIP 2: $20/hr
- VIP 3: $50/hr
- VIP 4: $100/hr
- VIP 5: $200/hr

You can change these rates anytime in Admin Settings.

## Troubleshooting

### "Column already exists" errors
This is normal if you've already run the migration. The migration is designed to be safe and won't overwrite existing data.

### "Table doesn't exist" errors
Make sure you've imported the base schema first from `database/schema.sql`

### Permission errors
Make sure your database user has ALTER TABLE permissions

## Need Help?

If you encounter any issues, check:
1. Your database connection settings in `.env` file
2. Your database user has proper permissions
3. The base schema has been imported first

## Rollback (if needed)

If you need to remove the VIP level columns (not recommended after users start using the system):

```sql
ALTER TABLE users DROP COLUMN vip_level;
ALTER TABLE projects DROP COLUMN vip_level_required;
DELETE FROM site_settings WHERE `key` IN ('vip1_rate', 'vip2_rate', 'vip3_rate', 'vip4_rate', 'vip5_rate', 'contact_phone', 'contact_address');
```

**WARNING:** Only run this if you haven't started using the VIP system yet!
