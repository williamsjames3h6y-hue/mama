# Laravel-Style Migration Guide

## Overview

This application has been successfully migrated to a Laravel-style architecture with MVC pattern, using Supabase as the database backend. All original functionality has been preserved.

## Directory Structure

```
project/
├── app/
│   ├── Controllers/        # HTTP Controllers
│   │   ├── AuthController.php
│   │   ├── ProjectController.php
│   │   ├── UserController.php
│   │   ├── UserProjectController.php
│   │   ├── WithdrawalController.php
│   │   ├── PaymentController.php
│   │   └── SettingController.php
│   ├── Models/            # Data Models
│   │   ├── Model.php (Base Model)
│   │   ├── User.php
│   │   ├── Project.php
│   │   ├── UserProject.php
│   │   ├── Withdrawal.php
│   │   ├── Payment.php
│   │   └── SiteSetting.php
│   ├── Middleware/        # Request Middleware
│   │   ├── AuthMiddleware.php
│   │   └── CorsMiddleware.php
│   └── Services/          # Business Logic Services
│       ├── SupabaseService.php
│       └── AuthService.php
├── bootstrap/
│   └── app.php           # Application Bootstrap
├── config/
│   ├── app.php           # App Configuration
│   └── container.php     # Dependency Injection Container
├── public/
│   └── index.php         # API Entry Point
├── routes/
│   └── api.php           # API Routes Definition
├── src/                  # Frontend JavaScript
├── index.html            # Frontend Entry Point
└── .htaccess            # Apache Routing Rules
```

## Key Features

### 1. Laravel-Style MVC Architecture
- **Controllers**: Handle HTTP requests and responses
- **Models**: Interact with Supabase database
- **Services**: Business logic layer
- **Middleware**: Request filtering and authentication

### 2. Supabase Integration
- All data is stored in Supabase PostgreSQL database
- Row Level Security (RLS) policies implemented
- User authentication handled via custom JWT tokens
- RESTful API using Supabase REST API

### 3. Routing System
- Custom router with RESTful route definitions
- Middleware support for authentication and CORS
- Dynamic route parameters
- .htaccess rules for clean URLs

### 4. Dependency Injection Container
- Automatic dependency resolution
- Singleton pattern support
- Service binding and instantiation

## API Endpoints

### Authentication
- `POST /api/auth/register` - User registration
- `POST /api/auth/login` - User login
- `GET /api/auth/me` - Get current user (requires auth)

### Projects
- `GET /api/projects` - List all projects
- `GET /api/projects/{id}` - Get project details
- `POST /api/projects` - Create project (admin only)
- `PUT /api/projects/{id}` - Update project (admin only)
- `DELETE /api/projects/{id}` - Delete project (admin only)

### Users
- `GET /api/users` - List all users (admin only)
- `GET /api/users/{id}` - Get user details (auth required)
- `PUT /api/users/{id}` - Update user (auth required)
- `DELETE /api/users/{id}` - Delete user (admin only)

### User Projects
- `GET /api/user-projects` - List user projects (auth required)
- `GET /api/user-projects/{id}` - Get user project details (auth required)
- `POST /api/user-projects` - Submit project work (auth required)
- `PUT /api/user-projects/{id}` - Update/approve project (admin only)
- `DELETE /api/user-projects/{id}` - Delete user project (admin only)

### Withdrawals
- `GET /api/withdrawals` - List withdrawals (auth required)
- `GET /api/withdrawals/{id}` - Get withdrawal details (auth required)
- `POST /api/withdrawals` - Request withdrawal (auth required)
- `PUT /api/withdrawals/{id}` - Process withdrawal (admin only)
- `DELETE /api/withdrawals/{id}` - Delete withdrawal (admin only)

### Payments
- `GET /api/payments` - List payments (auth required)
- `GET /api/payments/{id}` - Get payment details (auth required)

### Settings
- `GET /api/settings` - Get all settings
- `PUT /api/settings` - Update settings (admin only)

## Database Schema

All tables are in Supabase with RLS enabled:

- **users** - User accounts with VIP levels, balance, referrals
- **projects** - Available projects with rates and requirements
- **user_projects** - User project submissions and approvals
- **payments** - Transaction history
- **withdrawals** - Withdrawal requests
- **site_settings** - Platform configuration

## Authentication

The system uses custom JWT tokens for authentication:
- Tokens are generated on login/registration
- Tokens expire after 7 days
- Bearer token authentication in headers
- Middleware validates tokens on protected routes

## Deployment

1. Ensure all environment variables are set in `.env`
2. Supabase database is already configured and migrated
3. Point web server document root to project directory
4. Ensure Apache mod_rewrite is enabled
5. API requests go through `public/index.php`
6. Frontend is served from `index.html`

## Frontend Integration

The frontend code remains unchanged and communicates with the new API structure through the existing `/api` endpoints. All API calls are proxied through the Laravel-style backend.

## Security Features

- Row Level Security (RLS) on all Supabase tables
- JWT token authentication
- CORS middleware
- Admin-only routes protected
- Input validation in controllers
- Password hashing with bcrypt

## Migration Benefits

1. **Better Organization**: Clear separation of concerns with MVC pattern
2. **Scalability**: Easy to add new features and endpoints
3. **Maintainability**: Modular code structure
4. **Type Safety**: Better error handling and validation
5. **Security**: Centralized authentication and authorization
6. **Testability**: Controllers and models are easily testable
7. **Database Integration**: Seamless Supabase integration with RLS

## Maintenance

To add new features:
1. Create model in `app/Models/`
2. Create controller in `app/Controllers/`
3. Add routes in `routes/api.php`
4. Implement business logic in services if needed

All original functionality has been preserved and enhanced with better structure and security.
