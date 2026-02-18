MYSQL DATABASE SETUP INSTRUCTIONS
==================================

Your website now supports BOTH MySQL and Supabase databases!

CURRENT SETUP: Using Supabase (cloud database)


TO SWITCH TO MYSQL:
-------------------

1. Create a MySQL database:
   - Login to phpMyAdmin or MySQL console
   - Create database: earningsllc

2. Import the database:
   mysql -u username -p earningsllc < database.sql

   OR use phpMyAdmin:
   - Select your database
   - Go to Import tab
   - Choose database.sql file
   - Click Go

3. Update .env file:
   Change: DB_DRIVER=supabase
   To:     DB_DRIVER=mysql

   Update MySQL credentials:
   DB_HOST=localhost
   DB_PORT=3306
   DB_DATABASE=earningsllc
   DB_USERNAME=your_mysql_username
   DB_PASSWORD=your_mysql_password

4. Done! Your website will now use MySQL


DEFAULT ADMIN ACCOUNT:
----------------------
Email: admin@earningsllc.com
Password: Admin@123

IMPORTANT: Change the admin password after first login!


DATABASE TABLES:
----------------
- users (user accounts and profiles)
- projects (available projects/jobs)
- user_projects (project submissions)
- payments (transaction history)
- withdrawals (withdrawal requests)
- site_settings (website configuration)


SAMPLE DATA INCLUDED:
---------------------
- 10 sample projects across all VIP levels
- Site settings with default values
- 1 admin user account


FEATURES:
---------
✓ Automatic UUID generation for IDs
✓ Foreign key constraints
✓ Proper indexes for performance
✓ Triggers for auto-populating IDs
✓ Sample data pre-loaded
✓ Referral system support
✓ VIP level system
✓ Withdrawal management
✓ Payment tracking


REQUIREMENTS:
-------------
- MySQL 5.7+ or MariaDB 10.2+
- PHP 7.4+ with PDO MySQL extension
- InnoDB storage engine


BACKUP:
-------
To backup your database:
mysqldump -u username -p earningsllc > backup.sql


RESTORE:
--------
To restore from backup:
mysql -u username -p earningsllc < backup.sql
