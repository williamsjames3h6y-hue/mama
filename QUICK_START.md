# Quick Start Guide - EarningsLLC Platform

## Immediate Next Steps

### 1. Run Database Migration (REQUIRED)

Your database needs to be updated with the VIP level system. Choose the easiest method:

#### Method 1: phpMyAdmin (Recommended - 2 minutes)
1. Login to your hosting control panel
2. Open phpMyAdmin
3. Select database: `u800179901_70`
4. Click "SQL" tab at the top
5. Open file: `database/migration_add_vip_levels.sql` on your computer
6. Copy ALL the contents
7. Paste into the SQL box
8. Click "Go"
9. You should see "Query completed successfully"

#### Method 2: Automated Script (Alternative)
1. The file `run-migration.php` is already on your server
2. Visit: https://earningsllc.online/run-migration.php
3. Wait for "Migration complete!"
4. **IMPORTANT**: Delete `run-migration.php` after running

---

## 2. Test Everything

### A. Test Main Landing Page
1. **Logout** if you're logged in
2. Visit: https://earningsllc.online
3. You should see:
   - Beautiful dark blue gradient background
   - Login and Sign Up buttons in header
   - Hero section with animations
   - VIP level pricing cards
   - Three image showcases
   - Footer with contact info

### B. Test Admin Panel
1. Login as admin:
   - Email: admin@dataoptimize.com
   - Password: admin123
2. Visit: https://earningsllc.online/admin
3. Should see admin dashboard (NOT redirect to login)

### C. Test Admin Settings
1. In admin panel, click "Settings"
2. You should see NEW sections:
   - Contact Phone field
   - Contact Address field
   - VIP 1 Rate through VIP 5 Rate fields
3. Update contact info with YOUR information
4. Click "Save Settings"

### D. Test User Management
1. In admin panel, click "Users"
2. You should see a "VIP Level" column
3. Click "Edit" on any user
4. You should be able to change their VIP level (1-5)

### E. Test Project Management
1. In admin panel, click "Projects"
2. Click "Add New Project"
3. You should see "VIP Level Required" dropdown
4. Create a test project with VIP Level 3
5. Projects list should show VIP level column

---

## 3. Customize Your Site

### Update Contact Information
1. Go to Admin > Settings
2. Update:
   ```
   Contact Email: your@email.com
   Contact Phone: +1 (555) 123-4567
   Contact Address: Your actual address
   ```
3. Save Settings

### Set Your VIP Rates
Default rates are already set, but you can customize:
- VIP 1: $10/hr (beginners)
- VIP 2: $20/hr (intermediate)
- VIP 3: $50/hr (advanced)
- VIP 4: $100/hr (expert)
- VIP 5: $200/hr (master)

### Create VIP-Specific Projects
1. Go to Admin > Projects
2. Create projects for each VIP level
3. Examples:
   - "Basic Data Entry" → VIP 1
   - "Content Moderation" → VIP 2
   - "Professional Writing" → VIP 3
   - "Software Development" → VIP 4
   - "Executive Consulting" → VIP 5

---

## 4. How Users Experience VIP Levels

### Registration
- New users automatically get **VIP Level 1**
- They can immediately see and apply to VIP 1 projects

### Browsing Projects
- VIP 1 user sees: VIP 1 projects only
- VIP 3 user sees: VIP 1, 2, and 3 projects
- Each project card shows "VIP X Required" badge

### Upgrading Users
Admins can upgrade users:
1. Admin > Users
2. Click "Edit" on user
3. Change VIP Level dropdown
4. Save

---

## 5. Quick Reference

### Admin Credentials
```
Email: admin@dataoptimize.com
Password: admin123
```
**Change this password immediately!**

### File Locations
```
Landing Page: views/landing.php
User Dashboard: views/home.php
Admin Panel: views/admin/
Database Migration: database/migration_add_vip_levels.sql
```

### Database Tables
```
users → has vip_level column
projects → has vip_level_required column
site_settings → has VIP rates and contact info
```

---

## 6. Common Issues & Solutions

### Issue: Landing page shows old login page
**Solution**: Clear browser cache, logout, visit home page

### Issue: Admin panel redirects to login
**Solution**:
1. Make sure you're logged in as admin user
2. Check user role in database is 'admin'
3. Clear session cookies

### Issue: VIP columns don't show
**Solution**:
1. Run the database migration first
2. Refresh the page
3. Check browser console for errors

### Issue: Projects not filtering by VIP level
**Solution**:
1. Verify migration was successful
2. Check user has vip_level value in database
3. Check projects have vip_level_required value

---

## 7. Important Security Notes

- Change admin password immediately
- Delete `run-migration.php` after use
- Keep `.env` file secure
- Regular database backups recommended

---

## 8. Support Files

Detailed documentation available:
- `MIGRATION_INSTRUCTIONS.md` - Step-by-step migration guide
- `CHANGES_SUMMARY.md` - Complete list of all changes
- `database/README.md` - Database documentation

---

## You're Done!

Your platform now has:
- Professional landing page with animations
- Complete VIP level system (1-5)
- Admin controls for VIP rates and user levels
- Project filtering by VIP level
- Contact information management
- Admin user management with VIP controls

**Next**: Start adding your projects and inviting users!

---

**Questions?** Check the detailed documentation files listed above.

**Last Updated**: 2026-02-18
