# DataOptimize Pro - Data Optimization Platform

A complete data optimization platform with user management, project tracking, payment processing, and comprehensive admin panel.

## Features

### User Features
- User registration and authentication
- Dashboard with payment statistics
- Project browsing and applications
- Payment history tracking
- Profile management with balance tracking
- Deposit and withdrawal requests
- Referral system with unique codes
- Real-time stats (awaiting payout, total paid, tasks, hours)

### Admin Features
- Complete admin dashboard
- User management
- Project management (create, edit, delete)
- Payment approval/rejection system
- Site settings configuration
  - Site name and branding
  - Currency settings
  - Payment gateway selection
  - Minimum withdrawal limits
  - Referral bonus amounts
  - Contact information

## Technology Stack

- **Backend**: PHP 7.4+
- **Database**: Supabase (PostgreSQL)
- **Frontend**: HTML5, CSS3, JavaScript
- **Architecture**: MVC-like pattern with routing

## File Structure

```
project/
├── index.php              # Main application entry point
├── .htaccess             # Apache rewrite rules
├── .env                  # Environment configuration
├── core/                 # Core application classes
│   ├── Auth.php         # Authentication logic
│   ├── Config.php       # Configuration management
│   ├── Database.php     # Database operations
│   ├── Helper.php       # Utility functions
│   ├── Router.php       # URL routing
│   └── Session.php      # Session management
├── views/               # View templates
│   ├── header.php
│   ├── footer.php
│   ├── home.php
│   ├── login.php
│   ├── register.php
│   ├── projects.php
│   ├── profile.php
│   ├── payments.php
│   └── admin/           # Admin views
│       ├── dashboard.php
│       ├── users.php
│       ├── projects.php
│       ├── payments.php
│       └── settings.php
└── assets/              # Static assets
    ├── css/
    │   └── style.css
    └── js/
        └── main.js
```

## Installation

1. **Configure Environment**
   - Update `.env` with your Supabase credentials
   - Credentials are already configured in this project

2. **Server Requirements**
   - PHP 7.4 or higher
   - Apache with mod_rewrite enabled
   - cURL extension enabled

3. **Database Setup**
   - Database schema is already created via migrations
   - Sample data has been added

4. **Default Admin Account**
   - Email: admin@dataoptimize.com
   - Password: admin123
   - **IMPORTANT**: Change this password after first login!

## Usage

### For Users
1. Register a new account at `/register`
2. Login at `/login`
3. Browse available projects
4. Apply to projects
5. Track payments and earnings
6. Request deposits/withdrawals from profile page

### For Admins
1. Login with admin credentials
2. Access admin panel at `/admin`
3. Manage users, projects, and payments
4. Configure site settings
5. Approve/reject payment requests

## Security Features

- Password hashing with bcrypt
- Row Level Security (RLS) policies on all tables
- CSRF protection through session management
- SQL injection prevention via prepared statements
- XSS protection with output escaping
- Secure session handling

## API Endpoints

All routes are handled through index.php:

**Public Routes:**
- GET `/login` - Login page
- POST `/login` - Process login
- GET `/register` - Registration page
- POST `/register` - Process registration

**User Routes (Authentication Required):**
- GET `/home` - User dashboard
- GET `/projects` - Browse projects
- POST `/projects/apply` - Apply to project
- GET `/profile` - User profile
- POST `/profile/deposit` - Request deposit
- POST `/profile/withdraw` - Request withdrawal
- GET `/payments` - Payment history
- GET `/logout` - Logout

**Admin Routes (Admin Role Required):**
- GET `/admin` - Admin dashboard
- GET `/admin/users` - Manage users
- GET `/admin/projects` - Manage projects
- POST `/admin/projects/add` - Add new project
- POST `/admin/projects/delete` - Delete project
- GET `/admin/payments` - Manage payments
- POST `/admin/payments/approve` - Approve payment
- POST `/admin/payments/reject` - Reject payment
- GET `/admin/settings` - Site settings
- POST `/admin/settings/update` - Update settings

## Database Schema

### Tables
- **users** - User accounts with authentication
- **projects** - Available work opportunities
- **user_projects** - User project applications
- **payments** - Payment transactions
- **site_settings** - Configurable site settings

### Security
- Row Level Security enabled on all tables
- Separate policies for users and admins
- Authenticated access only

## Customization

### Changing Site Name
1. Login as admin
2. Go to `/admin/settings`
3. Update "Site Name" field
4. Save changes

### Configuring Payments
1. Login as admin
2. Go to `/admin/settings`
3. Configure:
   - Payment Gateway (Stripe/PayPal/Manual)
   - Minimum Withdrawal Amount
   - Referral Bonus
   - Currency Settings

### Adding Projects
1. Login as admin
2. Go to `/admin/projects`
3. Click "Add New Project"
4. Fill in project details
5. Submit

## Support

For issues or questions, contact the site administrator.

## License

Proprietary - All rights reserved
