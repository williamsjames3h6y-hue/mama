# MySQL + PHP Backend Deployment Guide

This guide explains how to set up the complete backend API for EarningsLLC with MySQL database.

## Prerequisites

- Web hosting with PHP 7.4+ and MySQL 5.7+
- MySQL database created
- Composer (optional, for dependencies)

## Step 1: Import Database

Upload and import the `database_export.sql` file:

```bash
# Via command line
mysql -u username -p earningsllc < database_export.sql

# Or use phpMyAdmin
# 1. Login to phpMyAdmin
# 2. Select database
# 3. Click Import tab
# 4. Upload database_export.sql
# 5. Click Go
```

## Step 2: Create Backend API

### Directory Structure

Create this structure in your web root:

```
public_html/
├── api/
│   ├── .htaccess
│   ├── config.php
│   ├── index.php
│   ├── auth.php
│   ├── users.php
│   ├── projects.php
│   ├── settings.php
│   ├── payments.php
│   └── helpers.php
├── dist/             (frontend files)
│   ├── index.html
│   └── assets/
└── .htaccess
```

### API Configuration (api/config.php)

```php
<?php
// Database configuration
define('DB_HOST', 'localhost');
define('DB_USER', 'your_db_username');
define('DB_PASS', 'your_db_password');
define('DB_NAME', 'earningsllc');
define('DB_CHARSET', 'utf8mb4');

// JWT Secret (change this!)
define('JWT_SECRET', 'your-secret-key-' . bin2hex(random_bytes(32)));
define('JWT_EXPIRY', 86400); // 24 hours

// CORS Settings
define('CORS_ORIGIN', '*'); // Change to your domain in production
define('CORS_METHODS', 'GET, POST, PUT, DELETE, OPTIONS');
define('CORS_HEADERS', 'Content-Type, Authorization');

// Database connection
function getDB() {
    static $pdo = null;

    if ($pdo === null) {
        try {
            $dsn = "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=" . DB_CHARSET;
            $options = [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false,
            ];
            $pdo = new PDO($dsn, DB_USER, DB_PASS, $options);
        } catch (PDOException $e) {
            http_response_code(500);
            die(json_encode(['error' => 'Database connection failed']));
        }
    }

    return $pdo;
}

// Set CORS headers
function setCorsHeaders() {
    header('Access-Control-Allow-Origin: ' . CORS_ORIGIN);
    header('Access-Control-Allow-Methods: ' . CORS_METHODS);
    header('Access-Control-Allow-Headers: ' . CORS_HEADERS);
    header('Content-Type: application/json');

    if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
        http_response_code(200);
        exit();
    }
}

// Get request body
function getRequestBody() {
    return json_decode(file_get_contents('php://input'), true) ?? [];
}

// Send JSON response
function sendResponse($data, $code = 200) {
    http_response_code($code);
    echo json_encode($data);
    exit();
}

// Send error response
function sendError($message, $code = 400) {
    http_response_code($code);
    echo json_encode(['error' => $message]);
    exit();
}
?>
```

### API Router (api/index.php)

```php
<?php
require_once 'config.php';
require_once 'helpers.php';

setCorsHeaders();

$method = $_SERVER['REQUEST_METHOD'];
$path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$path = str_replace('/api/', '', $path);
$path = str_replace('/api', '', $path);
$segments = array_filter(explode('/', trim($path, '/')));
$segments = array_values($segments);

try {
    $db = getDB();
    $resource = $segments[0] ?? '';

    switch ($resource) {
        case '':
        case 'health':
            sendResponse(['status' => 'ok', 'timestamp' => time()]);
            break;

        case 'auth':
            require_once 'auth.php';
            handleAuth($method, $segments, $db);
            break;

        case 'users':
            require_once 'users.php';
            handleUsers($method, $segments, $db);
            break;

        case 'projects':
            require_once 'projects.php';
            handleProjects($method, $segments, $db);
            break;

        case 'user-projects':
            require_once 'projects.php';
            handleUserProjects($method, $segments, $db);
            break;

        case 'settings':
            require_once 'settings.php';
            handleSettings($method, $segments, $db);
            break;

        case 'payments':
            require_once 'payments.php';
            handlePayments($method, $segments, $db);
            break;

        case 'withdrawals':
            require_once 'payments.php';
            handleWithdrawals($method, $segments, $db);
            break;

        default:
            sendError('Endpoint not found', 404);
    }

} catch (Exception $e) {
    sendError($e->getMessage(), 500);
}
?>
```

### Helper Functions (api/helpers.php)

```php
<?php
// Generate JWT token
function generateToken($userId) {
    $header = json_encode(['typ' => 'JWT', 'alg' => 'HS256']);
    $payload = json_encode([
        'user_id' => $userId,
        'exp' => time() + JWT_EXPIRY
    ]);

    $base64UrlHeader = str_replace(['+', '/', '='], ['-', '_', ''], base64_encode($header));
    $base64UrlPayload = str_replace(['+', '/', '='], ['-', '_', ''], base64_encode($payload));

    $signature = hash_hmac('sha256', $base64UrlHeader . "." . $base64UrlPayload, JWT_SECRET, true);
    $base64UrlSignature = str_replace(['+', '/', '='], ['-', '_', ''], base64_encode($signature));

    return $base64UrlHeader . "." . $base64UrlPayload . "." . $base64UrlSignature;
}

// Verify JWT token
function verifyToken($token) {
    if (!$token) return null;

    $parts = explode('.', $token);
    if (count($parts) !== 3) return null;

    list($header, $payload, $signature) = $parts;

    $expectedSignature = hash_hmac('sha256', $header . "." . $payload, JWT_SECRET, true);
    $expectedBase64Signature = str_replace(['+', '/', '='], ['-', '_', ''], base64_encode($expectedSignature));

    if ($signature !== $expectedBase64Signature) return null;

    $payloadData = json_decode(base64_decode(str_replace(['-', '_'], ['+', '/'], $payload)), true);

    if ($payloadData['exp'] < time()) return null;

    return $payloadData['user_id'];
}

// Get authenticated user ID
function getAuthUserId() {
    $headers = apache_request_headers();
    $token = $headers['Authorization'] ?? $headers['authorization'] ?? '';
    $token = str_replace('Bearer ', '', $token);

    $userId = verifyToken($token);
    if (!$userId) {
        sendError('Unauthorized', 401);
    }

    return $userId;
}

// Generate UUID
function generateUUID() {
    return sprintf('%04x%04x-%04x-%04x-%04x-%04x%04x%04x',
        mt_rand(0, 0xffff), mt_rand(0, 0xffff),
        mt_rand(0, 0xffff),
        mt_rand(0, 0x0fff) | 0x4000,
        mt_rand(0, 0x3fff) | 0x8000,
        mt_rand(0, 0xffff), mt_rand(0, 0xffff), mt_rand(0, 0xffff)
    );
}

// Hash password
function hashPassword($password) {
    return password_hash($password, PASSWORD_BCRYPT);
}

// Verify password
function verifyPassword($password, $hash) {
    return password_verify($password, $hash);
}
?>
```

### Authentication Endpoints (api/auth.php)

```php
<?php
function handleAuth($method, $segments, $db) {
    $action = $segments[1] ?? '';

    switch ($action) {
        case 'signup':
            if ($method !== 'POST') sendError('Method not allowed', 405);
            handleSignup($db);
            break;

        case 'login':
            if ($method !== 'POST') sendError('Method not allowed', 405);
            handleLogin($db);
            break;

        case 'logout':
            if ($method !== 'POST') sendError('Method not allowed', 405);
            sendResponse(['message' => 'Logged out successfully']);
            break;

        case 'reset-password':
            if ($method !== 'POST') sendError('Method not allowed', 405);
            handleResetPassword($db);
            break;

        default:
            sendError('Invalid auth endpoint', 404);
    }
}

function handleSignup($db) {
    $data = getRequestBody();
    $email = $data['email'] ?? '';
    $password = $data['password'] ?? '';
    $fullName = $data['full_name'] ?? '';

    if (!$email || !$password || !$fullName) {
        sendError('Email, password, and full name are required');
    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        sendError('Invalid email format');
    }

    if (strlen($password) < 6) {
        sendError('Password must be at least 6 characters');
    }

    $stmt = $db->prepare("SELECT id FROM users WHERE email = ?");
    $stmt->execute([$email]);
    if ($stmt->fetch()) {
        sendError('Email already exists');
    }

    $userId = generateUUID();
    $passwordHash = hashPassword($password);
    $referralCode = strtoupper(substr(md5($email . time()), 0, 8));

    $stmt = $db->prepare("
        INSERT INTO users (id, email, full_name, password_hash, referral_code, vip_level, balance, total_earned)
        VALUES (?, ?, ?, ?, ?, 1, 0.00, 0.00)
    ");

    try {
        $stmt->execute([$userId, $email, $fullName, $passwordHash, $referralCode]);
        $token = generateToken($userId);

        $user = [
            'id' => $userId,
            'email' => $email,
            'full_name' => $fullName,
            'vip_level' => 1,
            'balance' => 0.00,
            'total_earned' => 0.00,
            'referral_code' => $referralCode
        ];

        sendResponse(['user' => $user, 'token' => $token], 201);
    } catch (PDOException $e) {
        sendError('Registration failed: ' . $e->getMessage());
    }
}

function handleLogin($db) {
    $data = getRequestBody();
    $email = $data['email'] ?? '';
    $password = $data['password'] ?? '';

    if (!$email || !$password) {
        sendError('Email and password are required');
    }

    $stmt = $db->prepare("SELECT * FROM users WHERE email = ?");
    $stmt->execute([$email]);
    $user = $stmt->fetch();

    if (!$user || !verifyPassword($password, $user['password_hash'] ?? '')) {
        sendError('Invalid email or password', 401);
    }

    unset($user['password_hash']);
    $token = generateToken($user['id']);

    sendResponse(['user' => $user, 'token' => $token]);
}

function handleResetPassword($db) {
    $data = getRequestBody();
    $email = $data['email'] ?? '';

    if (!$email) {
        sendError('Email is required');
    }

    // In production, send actual password reset email
    sendResponse(['message' => 'Password reset email sent']);
}
?>
```

### API .htaccess

Create `api/.htaccess`:

```apache
RewriteEngine On
RewriteCond %{REQUEST_FILENAME} !-f
RewriteCond %{REQUEST_FILENAME} !-d
RewriteRule ^(.*)$ index.php [QSA,L]
```

## Step 3: Update Database Schema

Add password_hash column to users table if not exists:

```sql
ALTER TABLE users ADD COLUMN IF NOT EXISTS password_hash VARCHAR(255) AFTER full_name;
```

## Step 4: Test API

Test with curl:

```bash
# Health check
curl https://yourdomain.com/api/health

# Signup
curl -X POST https://yourdomain.com/api/auth/signup \
  -H "Content-Type: application/json" \
  -d '{"email":"test@example.com","password":"test123","full_name":"Test User"}'

# Login
curl -X POST https://yourdomain.com/api/auth/login \
  -H "Content-Type: application/json" \
  -d '{"email":"test@example.com","password":"test123"}'
```

## Step 5: Security Hardening

1. **Change JWT Secret**: Update `JWT_SECRET` in config.php
2. **Restrict CORS**: Change `CORS_ORIGIN` to your domain
3. **Use HTTPS**: Ensure SSL certificate is installed
4. **File Permissions**: Set api/ folder to 755, files to 644
5. **Database User**: Use limited permissions database user

## Complete API Endpoints

```
GET    /api/health                    - Health check
POST   /api/auth/signup               - Register new user
POST   /api/auth/login                - Login user
POST   /api/auth/logout               - Logout user
POST   /api/auth/reset-password       - Reset password

GET    /api/users                     - Get all users (admin)
GET    /api/users/:id                 - Get user by ID
PUT    /api/users/:id                 - Update user
GET    /api/users/:id/projects        - Get user's projects
GET    /api/users/:id/payments        - Get user's payments
GET    /api/users/:id/withdrawals     - Get user's withdrawals

GET    /api/projects                  - Get all projects
GET    /api/projects/:id              - Get project by ID
POST   /api/projects                  - Create project (admin)
PUT    /api/projects/:id              - Update project (admin)
DELETE /api/projects/:id              - Delete project (admin)

GET    /api/user-projects             - Get all user projects (admin)
POST   /api/user-projects             - Apply to project
PUT    /api/user-projects/:id         - Update user project status (admin)

GET    /api/settings                  - Get all settings
PUT    /api/settings/:key             - Update setting (admin)

GET    /api/payments                  - Get all payments (admin)
GET    /api/withdrawals               - Get all withdrawals (admin)
POST   /api/withdrawals               - Create withdrawal request
PUT    /api/withdrawals/:id           - Update withdrawal status (admin)
```

## Frontend Configuration

Update API URL in your frontend `.env` before building:

```
VITE_API_URL=https://yourdomain.com/api
```

Then rebuild:
```bash
npm run build
```

## Troubleshooting

### 500 Internal Server Error
- Check PHP error logs
- Verify database credentials in config.php
- Ensure all required PHP extensions are installed

### CORS Errors
- Check .htaccess files
- Verify CORS headers in config.php
- Clear browser cache

### Database Connection Failed
- Verify DB credentials
- Check database exists
- Ensure user has permissions

## Support

For issues, check:
- PHP error logs in hosting panel
- Browser console for frontend errors
- Network tab for API response details
