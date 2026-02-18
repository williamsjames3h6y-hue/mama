# Quick Start Guide

## Your New Application is Ready!

Everything has been set up and is working. Follow these simple steps:

## Step 1: Start the App

```bash
npm run dev
```

Visit: http://localhost:3000

## Step 2: Register Your Account

1. Click "Sign Up" on the landing page
2. Enter your name, email, and password
3. Click "Register"
4. You'll be automatically logged in

## Step 3: Make Yourself Admin

Open Supabase SQL Editor and run:

```sql
UPDATE users
SET role = 'admin', vip_level = 5
WHERE email = 'your-email@example.com';
```

Replace `your-email@example.com` with your actual email.

## Step 4: Access Admin Panel

1. Refresh the page
2. You'll see a red "Admin" link in the navigation
3. Click it to access the admin dashboard

## What You Can Do Now

### As a User:
- ✅ Browse projects filtered by VIP level
- ✅ Apply to projects
- ✅ View your profile and balance
- ✅ Check payment history
- ✅ Share your referral code

### As an Admin:
- ✅ Manage all users
- ✅ Upgrade user VIP levels
- ✅ Create and manage projects
- ✅ Set VIP pay rates
- ✅ Update contact information
- ✅ Configure site settings

## Default VIP Rates

- **VIP 1**: $10/hour - Entry level
- **VIP 2**: $20/hour - Intermediate
- **VIP 3**: $50/hour - Professional
- **VIP 4**: $100/hour - Expert
- **VIP 5**: $200/hour - Elite

You can change these in Admin → Settings.

## Sample Projects Already Created

5 sample projects are already in the database:
1. Basic Data Entry (VIP 1)
2. Content Review (VIP 2)
3. Professional Writing (VIP 3)
4. Data Science Projects (VIP 4)
5. Senior Consulting (VIP 5)

## Testing the VIP System

1. As VIP 1, you'll only see the first project
2. Upgrade yourself to VIP 3 in admin
3. Refresh projects page - you'll now see 3 projects
4. VIP 5 users see all projects

## Customization

### Change Contact Information
1. Go to Admin → Settings
2. Update email, phone, and address
3. Changes appear immediately on landing page footer

### Add New Projects
1. Go to Admin → Projects
2. Click "Add New Project"
3. Set title, description, rates, and VIP requirement
4. Click "Create Project"

### Manage Users
1. Go to Admin → Users
2. Click "Edit" on any user
3. Change their VIP level or role
4. Click "Save Changes"

## Database Connection

Everything is connected to Supabase:
- URL: https://0ec90b57d6e95fcbda19832f.supabase.co
- All data is secure with Row Level Security
- Users can only access their own data
- Admins have full access

## Build for Production

```bash
npm run build
```

Deploy the `dist` folder to any hosting service.

## Need Help?

Check `README.md` for complete documentation.

---

**Everything is working and ready to use!**
