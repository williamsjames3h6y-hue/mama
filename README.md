# EarningsLLC - Complete Working Platform

A modern web application for managing freelance projects with a 5-tier VIP level system.

## Features

- **User Authentication** - Secure Supabase authentication
- **VIP Level System** - 5 tiers (VIP 1-5) with different pay rates
- **Project Management** - Browse and apply to projects based on VIP level
- **Admin Dashboard** - Complete admin panel for managing users, projects, and settings
- **Payment Tracking** - Track earnings and payment history
- **Referral System** - Earn bonuses for referring new users
- **Responsive Design** - Works on all devices

## Tech Stack

- **Frontend**: Vanilla JavaScript with Vite
- **Database**: Supabase (PostgreSQL)
- **Authentication**: Supabase Auth
- **Styling**: Custom CSS with modern design

## Setup Complete

The application is already set up and ready to use:

✅ Database schema created
✅ Sample data inserted
✅ Row Level Security (RLS) policies applied
✅ Dependencies installed
✅ All pages created

## Running the Application

```bash
npm run dev
```

The app will be available at http://localhost:3000

## First Steps

### 1. Create an Admin Account

1. Visit the app and click "Sign Up"
2. Register with your email and password
3. You'll start as a regular user (VIP Level 1)

### 2. Make Yourself Admin

Run this SQL in Supabase SQL Editor:

```sql
UPDATE users
SET role = 'admin', vip_level = 5
WHERE email = 'your-email@example.com';
```

### 3. Access Admin Panel

- Login with your account
- Click the red "Admin" link in the navigation
- You can now manage users, projects, and settings

## User Features

### Landing Page
- Beautiful gradient design
- VIP level showcase
- Contact information footer
- Login/Register buttons

### Dashboard
- View balance and total earnings
- See VIP level
- Quick access to projects
- Referral code display

### Projects
- Browse available projects
- Filtered by user's VIP level
- Apply to projects
- View pay rates

### Profile
- Personal information
- Financial summary
- Referral program details

### Payments
- Complete payment history
- Transaction tracking
- Status monitoring

## Admin Features

### Dashboard
- Total users count
- Active projects overview
- Platform statistics
- Quick action buttons

### Users Management
- View all users
- Edit user VIP levels
- Change user roles (user/admin)
- Monitor user balances

### Projects Management
- Create new projects
- Set VIP level requirements
- Manage project status
- Delete projects

### Settings
- Contact information (email, phone, address)
- VIP level pay rates (VIP 1-5)
- Referral bonus amount
- Minimum withdrawal amount

## VIP Level System

| Level | Default Rate | Access |
|-------|-------------|---------|
| VIP 1 | $10/hr | Entry-level tasks |
| VIP 2 | $20/hr | Intermediate projects |
| VIP 3 | $50/hr | Professional work |
| VIP 4 | $100/hr | Expert projects |
| VIP 5 | $200/hr | Elite opportunities |

- New users start at VIP 1
- Users can only see projects at or below their VIP level
- Admins can upgrade user VIP levels
- Pay rates are customizable in admin settings

## Database Structure

### Tables
- `users` - User accounts with VIP levels
- `projects` - Available projects with requirements
- `user_projects` - Project applications
- `payments` - Payment history
- `site_settings` - Configurable settings

### Security
- Row Level Security (RLS) enabled on all tables
- Users can only access their own data
- Admins have full access
- Secure authentication via Supabase

## Environment Variables

Already configured in `.env`:
```
VITE_SUPABASE_URL=https://0ec90b57d6e95fcbda19832f.supabase.co
VITE_SUPABASE_SUPABASE_ANON_KEY=<your-key>
```

## Building for Production

```bash
npm run build
```

This creates a `dist` folder ready for deployment.

## Deployment

The app can be deployed to:
- Vercel
- Netlify
- Any static hosting service

Just deploy the `dist` folder after building.

## Support

For issues or questions, check the code comments or review the database schema in the Supabase dashboard.

## License

Proprietary - All rights reserved

---

**Created**: 2026-02-18
**Version**: 2.0.0
