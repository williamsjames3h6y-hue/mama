# DataOptimize Pro - MySQL Installation Guide

## Complete Installation Instructions

This guide will help you set up the DataOptimize Pro platform with MySQL database.

---

## Prerequisites

Before you begin, ensure you have the following installed:

1. **Web Server**: Apache 2.4+
2. **PHP**: Version 7.4 or higher
3. **MySQL**: Version 5.7+ or MariaDB 10.3+
4. **Required PHP Extensions**:
   - PDO
   - pdo_mysql
   - mbstring
   - openssl
   - json

---

## Step 1: Install XAMPP/WAMP/MAMP (Recommended for Local Development)

### Option A: XAMPP (Cross-platform)
1. Download XAMPP from [https://www.apachefriends.org/](https://www.apachefriends.org/)
2. Install XAMPP with Apache, MySQL, and PHP
3. Start Apache and MySQL from XAMPP Control Panel

### Option B: WAMP (Windows)
1. Download WAMP from [https://www.wampserver.com/](https://www.wampserver.com/)
2. Install and start all services

### Option C: MAMP (Mac)
1. Download MAMP from [https://www.mamp.info/](https://www.mamp.info/)
2. Install and start servers

---

## Step 2: Set Up the Project Files

1. **Copy project files**:
   ```
   Copy the entire project folder to your web server's document root:

   - XAMPP: C:/xampp/htdocs/dataoptimize_pro/
   - WAMP: C:/wamp64/www/dataoptimize_pro/
   - MAMP: /Applications/MAMP/htdocs/dataoptimize_pro/
   - Linux: /var/www/html/dataoptimize_pro/
   ```

2. **Set folder permissions** (Linux/Mac only):
   ```bash
   chmod -R 755 /var/www/html/dataoptimize_pro
   chmod -R 777 /var/www/html/dataoptimize_pro/assets
   ```

---

## Step 3: Create MySQL Database

### Method 1: Using phpMyAdmin (Easiest)

1. Open phpMyAdmin:
   - XAMPP: http://localhost/phpmyadmin
   - WAMP: http://localhost/phpmyadmin
   - MAMP: http://localhost:8888/phpmyadmin

2. Click on "Import" tab

3. Click "Choose File" and select: `database/schema.sql`

4. Click "Go" at the bottom

5. Done! The database `dataoptimize_pro` is created with all tables and sample data.

### Method 2: Using MySQL Command Line

1. Open Terminal/Command Prompt

2. Navigate to project directory:
   ```bash
   cd /path/to/dataoptimize_pro
   ```

3. Login to MySQL:
   ```bash
   mysql -u root -p
   ```
   (Press Enter when asked for password if there's no password)

4. Execute the schema file:
   ```sql
   source database/schema.sql;
   ```

5. Verify database creation:
   ```sql
   SHOW DATABASES;
   USE dataoptimize_pro;
   SHOW TABLES;
   ```

6. Exit MySQL:
   ```sql
   exit;
   ```

---

## Step 4: Configure Environment Variables

1. Open the `.env` file in the project root

2. Update database credentials if needed:
   ```
   DB_HOST=localhost
   DB_NAME=dataoptimize_pro
   DB_USER=root
   DB_PASS=
   SITE_URL=http://localhost/dataoptimize_pro
   ```

   **Note**:
   - Default XAMPP/WAMP MySQL username is `root` with no password
   - MAMP default password is usually `root`
   - Update `SITE_URL` to match your local setup

---

## Step 5: Configure Apache

### Enable mod_rewrite

#### XAMPP:
1. Open `C:/xampp/apache/conf/httpd.conf`
2. Find the line: `#LoadModule rewrite_module modules/mod_rewrite.so`
3. Remove the `#` to uncomment it
4. Save and restart Apache

#### Linux:
```bash
sudo a2enmod rewrite
sudo systemctl restart apache2
```

### Update .htaccess (Already configured)

The `.htaccess` file is already included in the project root. It contains:

```apache
RewriteEngine On
RewriteCond %{REQUEST_FILENAME} !-f
RewriteCond %{REQUEST_FILENAME} !-d
RewriteRule ^(.*)$ index.php?url=$1 [QSA,L]
```

---

## Step 6: Test the Installation

1. **Open your browser** and navigate to:
   ```
   http://localhost/dataoptimize_pro
   ```
   Or if using MAMP:
   ```
   http://localhost:8888/dataoptimize_pro
   ```

2. You should be redirected to the login page

3. **Login with default admin account**:
   - Email: `admin@dataoptimize.com`
   - Password: `admin123`

4. **Test user registration**:
   - Click "Register" and create a new user account
   - Login with your new account

---

## Default Accounts

### Admin Account
- **Email**: admin@dataoptimize.com
- **Password**: admin123
- **IMPORTANT**: Change this password immediately after first login!

### Database Content
The schema includes:
- Default admin user
- 5 sample projects
- Site settings configured
- All necessary tables with proper relationships

---

## Database Structure

The following tables are created:

1. **users** - User accounts and authentication
2. **projects** - Available work opportunities
3. **user_projects** - User applications to projects
4. **payments** - Financial transactions (deposits, withdrawals, earnings)
5. **site_settings** - Configurable platform settings

---

## Accessing the Application

### User Routes:
- `/` - Home (redirects based on login status)
- `/register` - New user registration
- `/login` - User login
- `/home` - User dashboard
- `/projects` - Browse available projects
- `/profile` - User profile and balance
- `/payments` - Payment history
- `/logout` - Logout

### Admin Routes:
- `/admin` - Admin dashboard
- `/admin/users` - User management
- `/admin/projects` - Project management
- `/admin/payments` - Payment approval
- `/admin/settings` - Site configuration

---

## Troubleshooting

### Issue: "Connection failed"
- **Solution**: Check your MySQL service is running
- **Solution**: Verify database credentials in `.env` file
- **Solution**: Ensure database `dataoptimize_pro` exists

### Issue: "404 Not Found" or URLs not working
- **Solution**: Enable mod_rewrite in Apache
- **Solution**: Check .htaccess file is present in project root
- **Solution**: Verify Apache has permission to read .htaccess files

### Issue: Blank page or errors
- **Solution**: Check PHP error logs
- **Solution**: Enable error display in index.php (already enabled)
- **Solution**: Verify all required PHP extensions are installed

### Issue: Can't login
- **Solution**: Verify database has been imported correctly
- **Solution**: Check if users table has the admin account
- **Solution**: Try registering a new account

### Issue: CSS/Images not loading
- **Solution**: Check file paths in .htaccess
- **Solution**: Verify assets folder permissions
- **Solution**: Clear browser cache

---

## File Permissions (Linux/Mac)

```bash
# Set proper ownership
sudo chown -R www-data:www-data /var/www/html/dataoptimize_pro

# Set folder permissions
find /var/www/html/dataoptimize_pro -type d -exec chmod 755 {} \;

# Set file permissions
find /var/www/html/dataoptimize_pro -type f -exec chmod 644 {} \;

# Make assets writable
chmod -R 777 /var/www/html/dataoptimize_pro/assets
```

---

## Security Recommendations

After installation:

1. **Change default admin password**
2. **Update database password** from default
3. **Disable display_errors** in production (in index.php)
4. **Set secure file permissions**
5. **Regular database backups**
6. **Keep PHP and MySQL updated**

---

## Need Help?

### Verify Installation:

1. **Check PHP version**:
   ```bash
   php -v
   ```
   Should be 7.4 or higher

2. **Check MySQL is running**:
   ```bash
   mysql -u root -p -e "SHOW DATABASES;"
   ```

3. **Check Apache modules**:
   ```bash
   apache2ctl -M | grep rewrite
   ```
   Should show: `rewrite_module`

### Common URLs:
- phpMyAdmin: http://localhost/phpmyadmin
- XAMPP Control: http://localhost/dashboard
- Your site: http://localhost/dataoptimize_pro

---

## Next Steps

1. Login as admin
2. Go to `/admin/settings` and customize:
   - Site name
   - Currency
   - Payment gateway
   - Contact email
3. Add your own projects at `/admin/projects`
4. Invite users to register
5. Start managing your data optimization platform!

---

## Production Deployment

For production deployment:

1. Use a proper hosting service with MySQL
2. Update `.env` with production database credentials
3. Disable `display_errors` in `index.php`
4. Set up SSL certificate (HTTPS)
5. Configure proper backups
6. Set strong database passwords
7. Review and update security settings

---

**Congratulations!** Your DataOptimize Pro platform is ready to use.

For questions or issues, refer to the README.md file or check the troubleshooting section above.
