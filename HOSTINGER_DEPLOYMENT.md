# Hostinger hPanel Deployment Guide

## Requirements
- Hostinger hosting account with hPanel access
- FTP/File Manager access
- Domain pointed to your hosting

## Deployment Steps

### 1. Build the Project
First, build the production files:
```bash
npm run build
```

This creates a `dist/` folder with all optimized files.

### 2. Upload Files to Hostinger

#### Option A: Using File Manager (Recommended)
1. Log in to your Hostinger hPanel
2. Go to **Files** > **File Manager**
3. Navigate to `public_html` (or your domain folder)
4. Delete all existing files in the folder (if any)
5. Upload ALL files from the `dist/` folder to `public_html`:
   - `index.html`
   - `assets/` folder
6. Upload the `assets/images/` folder from your project root to `public_html/assets/images/`

#### Option B: Using FTP
1. Use an FTP client (FileZilla, WinSCP, etc.)
2. Connect using your FTP credentials from hPanel
3. Navigate to `public_html`
4. Upload all files from `dist/` folder
5. Upload `assets/images/` folder

### 3. File Structure on Server
Your `public_html` folder should look like:
```
public_html/
├── index.html
├── assets/
│   ├── index-XXXXX.js
│   ├── index-XXXXX.css
│   └── images/
│       ├── 1.jpg
│       ├── 2.jpg
│       ├── 3.jpg
│       ├── 4.jpg
│       ├── 5.jpg
│       ├── 6.jpg
│       ├── 7.jpg
│       └── 8.jpg
```

### 4. Configure .htaccess (Important!)
Create a `.htaccess` file in `public_html` with this content:

```apache
# Enable Rewrite Engine
RewriteEngine On

# Force HTTPS (if you have SSL)
RewriteCond %{HTTPS} off
RewriteRule ^(.*)$ https://%{HTTP_HOST}%{REQUEST_URI} [L,R=301]

# Handle Single Page Application routing
RewriteCond %{REQUEST_FILENAME} !-f
RewriteCond %{REQUEST_FILENAME} !-d
RewriteRule ^ index.html [L]

# Enable Gzip Compression
<IfModule mod_deflate.c>
  AddOutputFilterByType DEFLATE text/html text/plain text/xml text/css text/javascript application/javascript application/json
</IfModule>

# Browser Caching
<IfModule mod_expires.c>
  ExpiresActive On
  ExpiresByType image/jpg "access plus 1 year"
  ExpiresByType image/jpeg "access plus 1 year"
  ExpiresByType image/png "access plus 1 year"
  ExpiresByType text/css "access plus 1 month"
  ExpiresByType application/javascript "access plus 1 month"
</IfModule>
```

### 5. Environment Variables
The `.env` file is NOT needed on the server. All environment variables are compiled into the build.

Your Supabase credentials are:
- URL: https://0ec90b57d6e95fcbda19832f.supabase.co
- Anon Key: eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJpc3MiOiJib2x0IiwicmVmIjoiMGVjOTBiNTdkNmU5NWZjYmRhMTk4MzJmIiwicm9sZSI6ImFub24iLCJpYXQiOjE3NTg4ODE1NzQsImV4cCI6MTc1ODg4MTU3NH0.9I8-U0x86Ak8t2DGaIk0HfvTSLsAyzdnz-Nw00mMkKw

These are already embedded in the built files.

### 6. Test Your Website
1. Visit your domain (e.g., https://yourdomain.com)
2. Test all features:
   - Landing page
   - User registration
   - User login
   - Dashboard
   - Projects page
   - Profile page
   - Payments page
   - Admin panel (login with admin account)

## Creating First Admin User

After deployment, you'll need to create an admin user:

1. Register a new account through the website
2. Go to your Supabase dashboard
3. Navigate to **Table Editor** > **users**
4. Find your newly created user
5. Edit the user and change `role` from `user` to `admin`
6. Save changes
7. Refresh the website and login - you'll now see the Admin link

## Troubleshooting

### Issue: Blank page on navigation
**Solution**: Make sure `.htaccess` is properly configured with the rewrite rules

### Issue: Images not showing
**Solution**: Verify that `assets/images/` folder is uploaded correctly

### Issue: Authentication not working
**Solution**: Check browser console for errors. Verify Supabase credentials are correct

### Issue: 404 errors on refresh
**Solution**: Ensure `.htaccess` rewrite rules are active

## Important Notes

1. **No PHP Required**: This is a pure JavaScript application using Supabase as backend
2. **No Database Setup**: Database is already configured on Supabase
3. **HTTPS Recommended**: For security, always use HTTPS (Hostinger provides free SSL)
4. **File Permissions**: Ensure files have correct permissions (644 for files, 755 for directories)

## Updating the Website

To update the website after making changes:
1. Run `npm run build` locally
2. Upload new files from `dist/` folder to `public_html`
3. Clear browser cache to see changes

## Support

For Hostinger-specific issues:
- Contact Hostinger support through hPanel
- Check their knowledge base at support.hostinger.com

For website functionality issues:
- Check Supabase dashboard for database issues
- Review browser console for JavaScript errors
