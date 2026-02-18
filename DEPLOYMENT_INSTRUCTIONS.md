# EarningsLLC - Hostinger Deployment Instructions

## Overview
Your application is now configured to work on Hostinger with PHP and MySQL. The backend API is built with PHP and will connect to your MySQL database.

## Prerequisites

1. **MySQL Database**: Your MySQL database should be set up with the schema from `database_export.sql`
2. **Hostinger Account**: With PHP and MySQL support
3. **FTP/File Manager Access**: To upload files

## Deployment Steps

### Step 1: Upload Files to Hostinger

1. Connect to your Hostinger account via FTP or File Manager
2. Upload ALL files from your project directory to the `public_html` folder (or your domain's root directory)
3. Make sure the following structure is preserved:
   ```
   public_html/
   ├── api/
   │   ├── index.php
   │   ├── health.php
   │   ├── config/
   │   ├── auth/
   │   ├── users/
   │   ├── projects/
   │   ├── user-projects/
   │   ├── payments/
   │   ├── withdrawals/
   │   └── settings/
   ├── src/
   ├── assets/
   ├── index.html
   └── .htaccess
   ```

### Step 2: Import MySQL Database

1. Log into your Hostinger control panel (hPanel)
2. Go to **Databases** → **phpMyAdmin**
3. Create a new database called `earningsllc` (or use an existing one)
4. Select the database
5. Click on the **Import** tab
6. Upload the `database_export.sql` file
7. Click **Go** to import

### Step 3: Configure Database Connection

1. In Hostinger hPanel, go to your website's directory
2. Create or edit the `.env` file in the root directory with your MySQL credentials:

```env
MYSQL_HOST=localhost
MYSQL_PORT=3306
MYSQL_USER=your_mysql_username
MYSQL_PASSWORD=your_mysql_password
MYSQL_DATABASE=earningsllc
JWT_SECRET=your-random-secret-key-here
```

**Important**:
- Replace `your_mysql_username` with your actual MySQL username from hPanel
- Replace `your_mysql_password` with your actual MySQL password from hPanel
- Generate a random JWT_SECRET (you can use: https://randomkeygen.com/)

### Step 4: Set Environment Variables (Alternative Method)

If Hostinger doesn't support `.env` files, you can set environment variables directly in the `api/config/database.php` file:

Replace the constructor in `api/config/database.php`:

```php
public function __construct() {
    $this->host = 'localhost';
    $this->db_name = 'your_database_name';
    $this->username = 'your_mysql_username';
    $this->password = 'your_mysql_password';
}
```

### Step 5: Verify File Permissions

Make sure your files have the correct permissions:
- **Directories**: 755
- **Files**: 644

You can set these in the Hostinger File Manager by right-clicking on files/folders.

### Step 6: Test the API

1. Open your browser and navigate to: `https://yourdomain.com/api/health`
2. You should see a JSON response like:
   ```json
   {
     "status": "ok",
     "message": "API is running",
     "database": "connected"
   }
   ```

If you see this, your API is working correctly!

### Step 7: Test the Application

1. Navigate to your domain: `https://yourdomain.com`
2. The application should load
3. Try registering a new account or logging in with:
   - **Email**: admin@earningsllc.com
   - **Password**: Check your database for the hashed password or create a new user

## Troubleshooting

### 403 Forbidden Error
- Check file permissions (directories: 755, files: 644)
- Make sure `.htaccess` is uploaded and readable
- Verify that `index.html` exists in the root directory

### API Not Working
- Check that all PHP files are uploaded to the `api/` folder
- Verify database credentials in `.env` or `api/config/database.php`
- Check PHP error logs in hPanel

### Database Connection Failed
- Verify MySQL credentials are correct
- Make sure the database exists and is imported
- Check that MySQL user has proper permissions
- Ensure MySQL host is correct (usually 'localhost' on Hostinger)

### CORS Errors
- The `.htaccess` file includes CORS headers
- Make sure `.htaccess` is not being ignored by the server
- Check that mod_headers is enabled (usually is on Hostinger)

## Security Notes

1. **Change Default Passwords**: Update the admin password in your database
2. **Secure JWT Secret**: Use a strong, random JWT secret
3. **Database Permissions**: Only grant necessary permissions to your MySQL user
4. **HTTPS**: Make sure your site is using HTTPS (Hostinger provides free SSL)
5. **Environment Variables**: Never commit `.env` with real credentials to version control

## Features

Your application includes:
- ✅ User registration and authentication
- ✅ VIP levels and project management
- ✅ Payment tracking
- ✅ Withdrawal requests
- ✅ Admin dashboard
- ✅ Referral system
- ✅ Site settings management

## Support

If you encounter issues:
1. Check the browser console for JavaScript errors
2. Check PHP error logs in hPanel
3. Verify all files are uploaded correctly
4. Ensure database is imported properly

## Default Admin Account

After importing the database, you can log in with:
- **Email**: admin@earningsllc.com
- **Referral Code**: ADMIN2026
- **Role**: Admin
- **VIP Level**: 5

You'll need to set a password or update the password_hash in the database.

## Next Steps

1. Update site settings in the admin panel
2. Add projects for different VIP levels
3. Customize the design and branding
4. Set up payment processing
5. Configure email notifications (if needed)

Your application is now ready to use on Hostinger!
