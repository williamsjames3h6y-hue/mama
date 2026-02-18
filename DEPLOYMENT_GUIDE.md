# Deployment Guide for earningsllc.online

## Pre-Deployment Checklist

### 1. Domain & Hosting Setup
- Ensure your domain `earningsllc.online` is pointed to your hosting server
- Verify SSL certificate is installed for HTTPS
- Confirm PHP 7.4+ and MySQL/MariaDB are available

### 2. File Upload
Upload all project files to your hosting public directory (usually `public_html` or `www`)

### 3. Database Configuration

#### Create Database
1. Log into your hosting control panel (cPanel, Plesk, etc.)
2. Create a new MySQL database named `dataoptimize_pro` (or your preferred name)
3. Create a database user with full privileges
4. Note down the database credentials

#### Update .env File
Edit the `.env` file with your actual database credentials:

```env
DB_HOST=localhost
DB_NAME=your_database_name
DB_USER=your_database_user
DB_PASS=your_database_password
SITE_URL=https://earningsllc.online
```

#### Import Database Schema
1. Access phpMyAdmin from your hosting control panel
2. Select your database
3. Import the `database/schema.sql` file
4. Verify all tables are created successfully

### 4. File Permissions
Set proper permissions for security:
```bash
chmod 644 .env
chmod 644 index.php
chmod 755 core/
chmod 755 views/
chmod 755 assets/
```

### 5. Security Configuration

#### Update .htaccess
The `.htaccess` file is already configured with:
- HTTPS redirection
- Security headers
- URL rewriting

Ensure your hosting supports `.htaccess` files (Apache with mod_rewrite enabled).

#### Secure Your .env File
Add this to your `.htaccess` to prevent direct access:
```apache
<Files ".env">
    Order allow,deny
    Deny from all
</Files>
```

### 6. Admin Account Creation
After deployment, create your admin account:
1. Visit `https://earningsllc.online/register`
2. Create your account
3. Manually update the database to set `role = 'admin'` for your user:

```sql
UPDATE users SET role = 'admin' WHERE email = 'your@email.com';
```

### 7. Testing Checklist
- [ ] Homepage loads correctly
- [ ] Login/Register functionality works
- [ ] Dashboard displays without errors
- [ ] Projects page loads
- [ ] Payment history accessible
- [ ] Admin panel accessible (for admin users)
- [ ] All forms submit properly
- [ ] HTTPS enforced automatically
- [ ] Mobile responsive design works

### 8. Post-Deployment Tasks

#### Configure Site Settings
Log into the admin panel and configure:
- Minimum withdrawal amount
- Referral bonuses
- Currency settings
- Site policies

#### Monitor Logs
Check your hosting error logs regularly for any PHP errors or warnings.

## Common Issues & Solutions

### Issue: 500 Internal Server Error
- Check file permissions
- Review error logs
- Verify `.htaccess` syntax
- Ensure mod_rewrite is enabled

### Issue: Database Connection Failed
- Verify database credentials in `.env`
- Check database user privileges
- Confirm database server is running
- Test connection from phpMyAdmin

### Issue: CSS/JS Not Loading
- Check file paths in browser console
- Verify `/assets/` directory permissions
- Clear browser cache
- Check `.htaccess` rewrite rules

### Issue: Email Functions Not Working
- Configure PHP mail settings or SMTP
- Check hosting email quotas
- Verify sender email addresses
- Test with hosting email logs

## Maintenance

### Regular Backups
- Back up database weekly
- Keep file backups before updates
- Store backups off-server

### Updates
- Test updates in staging environment first
- Keep PHP and MySQL updated
- Monitor security advisories

### Performance
- Enable caching if available
- Optimize database indexes
- Monitor server resources
- Use CDN for static assets if needed

## Support
For issues specific to your hosting environment, contact your hosting provider's support team.

## Security Best Practices
1. Never commit `.env` to public repositories
2. Use strong database passwords
3. Regularly update dependencies
4. Monitor access logs
5. Implement rate limiting for login attempts
6. Keep backups encrypted

---

**Platform**: EarningsLLC
**Domain**: https://earningsllc.online
**Version**: 1.0
