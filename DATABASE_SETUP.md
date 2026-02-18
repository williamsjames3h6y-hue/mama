# EarningsLLC Database Setup Guide

This guide explains how to set up the EarningsLLC database in MySQL.

## Database Overview

The database includes the following tables:

### Core Tables
- **users** - User accounts with VIP levels, balances, and referral system
- **projects** - Available projects/tasks with rates and VIP requirements
- **user_projects** - Tracks user participation in projects
- **payments** - All payment transactions (earnings, deposits, withdrawals)
- **withdrawals** - Withdrawal requests and processing

### Settings Tables
- **site_settings** - Platform configuration (rates, contact info, etc.)
- **settings** - Enhanced settings with type support

### Extended Features
- **profiles** - Extended user profile information
- **datasets** - User-uploaded datasets for optimization
- **optimization_jobs** - Data optimization job tracking

## Quick Setup

### Method 1: Import Full Database

```bash
# Login to MySQL
mysql -u root -p

# Import the database
mysql -u root -p < database_export.sql
```

### Method 2: Create Database First, Then Import

```bash
# Create the database
mysql -u root -p -e "CREATE DATABASE earningsllc CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"

# Import the tables and data
mysql -u root -p earningsllc < database_export.sql
```

## Default Data

### Admin Account
- **Email**: admin@earningsllc.com
- **Role**: Admin
- **VIP Level**: 5
- **Referral Code**: ADMIN2026

### Sample User
- **Email**: user@example.com
- **Name**: John Doe
- **Role**: User
- **VIP Level**: 1
- **Referral Code**: USER001

### Settings
- **Site Name**: EarningsLLC
- **Contact Email**: admin@earningsllc.com
- **Contact Phone**: +1 (555) 123-4567
- **VIP1 Rate**: $10/hour
- **VIP2 Rate**: $20/hour
- **VIP3 Rate**: $50/hour
- **VIP4 Rate**: $100/hour
- **VIP5 Rate**: $200/hour
- **Referral Bonus**: $300
- **Minimum Withdrawal**: $50

### Sample Projects
The database includes 15 sample projects across all VIP levels:
- **VIP 1**: Data Entry, Data Cleaning, Spreadsheet Automation
- **VIP 2**: Content Review, Database Optimization, Data Visualization, API Integration
- **VIP 3**: Professional Writing, ETL Development, Data Security Audit
- **VIP 4**: Data Science, Machine Learning, Real-time Processing
- **VIP 5**: Senior Consulting, Data Warehouse Design

## Database Features

### Automatic Triggers
1. **Balance Update on Approval** - Automatically updates user balance when project is approved
2. **Payment Record Creation** - Creates payment record when project is approved
3. **Withdrawal Balance Update** - Deducts amount from user balance when withdrawal is completed

### Views
1. **v_user_earnings** - Shows user earnings summary with project statistics
2. **v_project_stats** - Shows project submission and approval statistics

### Indexes
All tables have appropriate indexes for:
- Primary keys
- Foreign keys
- Frequently queried fields (email, status, user_id, etc.)

## Connecting from Your Application

### PHP (PDO)
```php
<?php
$host = 'localhost';
$db = 'earningsllc';
$user = 'your_username';
$pass = 'your_password';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$db;charset=utf8mb4", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Connection failed: " . $e->getMessage());
}
?>
```

### Node.js (MySQL2)
```javascript
const mysql = require('mysql2/promise');

const pool = mysql.createPool({
  host: 'localhost',
  user: 'your_username',
  password: 'your_password',
  database: 'earningsllc',
  waitForConnections: true,
  connectionLimit: 10,
  queueLimit: 0
});
```

### Python (PyMySQL)
```python
import pymysql

connection = pymysql.connect(
    host='localhost',
    user='your_username',
    password='your_password',
    database='earningsllc',
    charset='utf8mb4',
    cursorclass=pymysql.cursors.DictCursor
)
```

## Security Notes

1. **Change Default Passwords** - The default admin account should have its password changed immediately
2. **Use Strong Passwords** - Always use strong, hashed passwords (bcrypt, argon2, etc.)
3. **Database User Permissions** - Create a dedicated MySQL user with limited permissions for your application
4. **Environment Variables** - Store database credentials in environment variables, not in code
5. **SSL Connection** - Use SSL for database connections in production

## Creating a Database User

```sql
-- Create a user for the application
CREATE USER 'earningsllc_app'@'localhost' IDENTIFIED BY 'strong_password_here';

-- Grant necessary permissions
GRANT SELECT, INSERT, UPDATE, DELETE ON earningsllc.* TO 'earningsllc_app'@'localhost';

-- Flush privileges
FLUSH PRIVILEGES;
```

## Backup and Restore

### Create Backup
```bash
mysqldump -u root -p earningsllc > backup_$(date +%Y%m%d).sql
```

### Restore from Backup
```bash
mysql -u root -p earningsllc < backup_20260218.sql
```

## Troubleshooting

### Error: Table already exists
If you get errors about tables already existing, drop the database first:
```sql
DROP DATABASE IF EXISTS earningsllc;
```
Then run the import again.

### Error: Access denied
Make sure your MySQL user has permission to create databases and tables.

### Character Set Issues
Ensure your MySQL server supports utf8mb4:
```sql
SHOW VARIABLES LIKE 'character_set%';
```

## Current Setup (Supabase/PostgreSQL)

Your current application is using Supabase (PostgreSQL). To migrate to MySQL:

1. Import the database_export.sql file into MySQL
2. Update your connection configuration
3. Install MySQL client library instead of Supabase
4. Update queries if needed (PostgreSQL vs MySQL syntax differences)

## Support

For issues or questions:
- Email: admin@earningsllc.com
- Phone: +1 (555) 123-4567
