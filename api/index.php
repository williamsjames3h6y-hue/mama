<?php
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization');
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

$requestUri = $_SERVER['REQUEST_URI'];
$requestMethod = $_SERVER['REQUEST_METHOD'];

$basePath = '/api';
$path = str_replace($basePath, '', parse_url($requestUri, PHP_URL_PATH));
$path = trim($path, '/');

$pathParts = explode('/', $path);

if (empty($pathParts[0])) {
    http_response_code(200);
    echo json_encode([
        'message' => 'EarningsLLC API',
        'version' => '1.0.0',
        'endpoints' => [
            '/health' => 'Health check',
            '/auth/login' => 'User login',
            '/auth/register' => 'User registration',
            '/users' => 'User management',
            '/projects' => 'Project management',
            '/user-projects' => 'User project applications',
            '/payments' => 'Payment history',
            '/withdrawals' => 'Withdrawal management',
            '/settings' => 'Site settings'
        ]
    ]);
    exit();
}

switch ($pathParts[0]) {
    case 'health':
        require_once 'health.php';
        break;

    case 'auth':
        if (isset($pathParts[1])) {
            if ($pathParts[1] === 'login') {
                require_once 'auth/login.php';
            } elseif ($pathParts[1] === 'register') {
                require_once 'auth/register.php';
            } else {
                http_response_code(404);
                echo json_encode(['error' => 'Not found']);
            }
        } else {
            http_response_code(404);
            echo json_encode(['error' => 'Not found']);
        }
        break;

    case 'users':
        if (isset($pathParts[1]) && is_numeric($pathParts[1])) {
            $_GET['id'] = $pathParts[1];

            if (isset($pathParts[2])) {
                if ($pathParts[2] === 'projects') {
                    $_GET['user_id'] = $pathParts[1];
                    require_once 'user-projects/index.php';
                } elseif ($pathParts[2] === 'payments') {
                    $_GET['user_id'] = $pathParts[1];
                    require_once 'payments/index.php';
                } elseif ($pathParts[2] === 'withdrawals') {
                    $_GET['user_id'] = $pathParts[1];
                    require_once 'withdrawals/index.php';
                } else {
                    http_response_code(404);
                    echo json_encode(['error' => 'Not found']);
                }
            } else {
                require_once 'users/detail.php';
            }
        } else {
            require_once 'users/index.php';
        }
        break;

    case 'projects':
        if (isset($pathParts[1]) && is_numeric($pathParts[1])) {
            $_GET['id'] = $pathParts[1];
            require_once 'projects/detail.php';
        } else {
            require_once 'projects/index.php';
        }
        break;

    case 'user-projects':
        if (isset($pathParts[1]) && is_numeric($pathParts[1])) {
            $_GET['id'] = $pathParts[1];
            require_once 'user-projects/detail.php';
        } else {
            require_once 'user-projects/index.php';
        }
        break;

    case 'payments':
        require_once 'payments/index.php';
        break;

    case 'withdrawals':
        if (isset($pathParts[1]) && is_numeric($pathParts[1])) {
            $_GET['id'] = $pathParts[1];
            require_once 'withdrawals/detail.php';
        } else {
            require_once 'withdrawals/index.php';
        }
        break;

    case 'settings':
        if (isset($pathParts[1])) {
            $_GET['key'] = $pathParts[1];
            require_once 'settings/update.php';
        } else {
            require_once 'settings/index.php';
        }
        break;

    default:
        http_response_code(404);
        echo json_encode(['error' => 'Endpoint not found']);
        break;
}
?>
