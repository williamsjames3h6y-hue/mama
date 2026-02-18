# Quick Setup Guide

## Project Structure

```
project/
├── index.php                 # Main entry point - all requests go here
├── .htaccess                # Apache rewrite rules
├── .env                     # Database configuration
├── README.md                # Full documentation
│
├── core/                    # Core application logic
│   ├── Auth.php            # User authentication
│   ├── Config.php          # Configuration loader
│   ├── Database.php        # Supabase database wrapper
│   ├── Helper.php          # Utility functions
│   ├── Router.php          # URL routing system
│   └── Session.php         # Session management
│
├── views/                   # HTML templates
│   ├── header.php          # Common header
│   ├── footer.php          # Common footer
│   ├── login.php           # Login page
│   ├── register.php        # Registration page
│   ├── home.php            # User dashboard
│   ├── projects.php        # Projects listing
│   ├── profile.php         # User profile
│   ├── payments.php        # Payment history
│   ├── 404.php             # Error page
│   └── admin/              # Admin panel views
│       ├── dashboard.php   # Admin overview
│       ├── users.php       # User management
│       ├── projects.php    # Project management
│       ├── payments.php    # Payment management
│       └── settings.php    # Site settings
│
└── assets/                  # Static files
    ├── css/
    │   └── style.css       # Main stylesheet
    └── js/
        └── main.js         # JavaScript functionality
```

## Quick Start

### 1. Database Setup
✅ Database is already configured and ready
✅ All tables and sample data are created
✅ Supabase connection is active

### 2. Default Admin Account
- **Email**: admin@dataoptimize.com
- **Password**: admin123
- **⚠️ IMPORTANT**: Change this password immediately after first login!

### 3. Accessing the Site

**User Routes:**
- `/` - Home (redirects to dashboard if logged in)
- `/register` - Create new account
- `/login` - Login page
- `/home` - User dashboard
- `/projects` - Browse and apply to projects
- `/profile` - View profile, deposit/withdraw
- `/payments` - View payment history

**Admin Routes:**
- `/admin` - Admin dashboard
- `/admin/users` - Manage users
- `/admin/projects` - Manage projects
- `/admin/payments` - Approve/reject payments
- `/admin/settings` - Configure site settings

## Key Features

### User Features
✅ Registration with unique referral codes
✅ Secure login system
✅ Dashboard with real-time stats:
  - Awaiting payout
  - Total paid
  - Tasks this week
  - Payable hours
✅ Project browsing and applications
✅ Profile management
✅ Deposit and withdrawal requests
✅ Payment history tracking
✅ Referral system

### Admin Features
✅ Complete admin dashboard
✅ User management and overview
✅ Project creation and management
✅ Payment approval system
✅ Site-wide settings:
  - Site name customization
  - Currency configuration
  - Payment gateway selection
  - Minimum withdrawal limits
  - Referral bonus amounts
  - Contact information

## Database Tables

1. **users** - User accounts with authentication
2. **projects** - Available work opportunities
3. **user_projects** - User applications to projects
4. **payments** - Deposit, withdrawal, and earning transactions
5. **site_settings** - Configurable platform settings

## Security Features

✅ Password hashing (bcrypt)
✅ Row Level Security (RLS) on all tables
✅ Session-based authentication
✅ CSRF protection
✅ SQL injection prevention
✅ XSS protection with output escaping
✅ Role-based access control (User/Admin)

## Testing the Platform

### As a User:
1. Register a new account at `/register`
2. Login at `/login`
3. View your dashboard with stats
4. Browse projects at `/projects`
5. Apply to projects
6. Request a deposit from your profile
7. Check payment history

### As an Admin:
1. Login with admin credentials
2. Go to `/admin`
3. View platform statistics
4. Add a new project
5. Approve pending payments
6. Customize site settings
7. Manage users

## Customization

### Change Site Name
1. Login as admin
2. Navigate to `/admin/settings`
3. Update "Site Name" field
4. Save changes

### Add New Projects
1. Go to `/admin/projects`
2. Click "Add New Project"
3. Fill in details (title, description, rates, type)
4. Submit

### Approve Payments
1. Go to `/admin/payments`
2. Review pending payments
3. Click "Approve" or "Reject"
4. User balances update automatically

## Payment Flow

1. **Deposits**: User requests → Admin approves → Balance increases
2. **Withdrawals**: User requests → Admin approves → Balance decreases
3. **Earnings**: Admin creates earning payment → Approves → Balance increases + Total earned updates

## Technical Details

- **PHP Version**: 7.4+
- **Database**: Supabase (PostgreSQL)
- **Web Server**: Apache with mod_rewrite
- **Architecture**: MVC-inspired with routing
- **Frontend**: Pure HTML/CSS/JS (no frameworks)

## File Highlights

- **index.php**: Main application with all routes
- **.htaccess**: Redirects all requests to index.php
- **core/Database.php**: Supabase REST API wrapper
- **core/Router.php**: Clean URL routing system
- **core/Auth.php**: Authentication logic
- **assets/css/style.css**: Complete responsive design

## Notes

- All passwords are securely hashed
- Admin can manage all aspects via web interface
- Users can only access their own data
- Real-time statistics on dashboard
- Responsive design works on all devices
- No external dependencies required

## Support

For questions or issues:
1. Check the README.md for detailed documentation
2. Review the code comments
3. Contact site administrator

---

**Ready to go!** 🚀

Just open the site in your browser and start with:
- Admin login: admin@dataoptimize.com / admin123
- Or create a new user account
