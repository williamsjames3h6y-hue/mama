<?php

use App\Controllers\AuthController;
use App\Controllers\ProjectController;
use App\Controllers\UserController;
use App\Controllers\UserProjectController;
use App\Controllers\WithdrawalController;
use App\Controllers\PaymentController;
use App\Controllers\SettingController;

class Router
{
    private $routes = [];
    private $container;

    public function __construct($container)
    {
        $this->container = $container;
    }

    public function get($path, $handler)
    {
        $this->routes['GET'][$path] = $handler;
    }

    public function post($path, $handler)
    {
        $this->routes['POST'][$path] = $handler;
    }

    public function put($path, $handler)
    {
        $this->routes['PUT'][$path] = $handler;
    }

    public function patch($path, $handler)
    {
        $this->routes['PATCH'][$path] = $handler;
    }

    public function delete($path, $handler)
    {
        $this->routes['DELETE'][$path] = $handler;
    }

    public function dispatch($method, $path)
    {
        $path = parse_url($path, PHP_URL_PATH);
        $path = rtrim($path, '/');

        if (!isset($this->routes[$method])) {
            return $this->notFound();
        }

        foreach ($this->routes[$method] as $route => $handler) {
            $pattern = preg_replace('/\{([a-zA-Z0-9_]+)\}/', '([a-zA-Z0-9_-]+)', $route);
            $pattern = '#^' . $pattern . '$#';

            if (preg_match($pattern, $path, $matches)) {
                array_shift($matches);
                return $this->callHandler($handler, $matches);
            }
        }

        return $this->notFound();
    }

    private function callHandler($handler, $params = [])
    {
        if (is_array($handler)) {
            $controllerClass = $handler[0];
            $method = $handler[1];
            $middleware = $handler[2] ?? null;

            $controller = $this->container->make($controllerClass);

            $user = null;
            if ($middleware === 'auth') {
                $authMiddleware = $this->container->make(\App\Middleware\AuthMiddleware::class);
                $user = $authMiddleware->handle();
                array_unshift($params, $user);
            } elseif ($middleware === 'admin') {
                $authMiddleware = $this->container->make(\App\Middleware\AuthMiddleware::class);
                $user = $authMiddleware->handle();
                $authMiddleware->requireAdmin($user);
                array_unshift($params, $user);
            }

            array_unshift($params, $user);

            return call_user_func_array([$controller, $method], $params);
        }

        return call_user_func_array($handler, $params);
    }

    private function notFound()
    {
        http_response_code(404);
        echo json_encode(['error' => 'Not found']);
        exit();
    }
}

return function($container) {
    $router = new Router($container);

    $router->post('/api/auth/register', [AuthController::class, 'register']);
    $router->post('/api/auth/login', [AuthController::class, 'login']);
    $router->get('/api/auth/me', [AuthController::class, 'me', 'auth']);

    $router->get('/api/projects', [ProjectController::class, 'index']);
    $router->get('/api/projects/{id}', [ProjectController::class, 'show']);
    $router->post('/api/projects', [ProjectController::class, 'store', 'admin']);
    $router->put('/api/projects/{id}', [ProjectController::class, 'update', 'admin']);
    $router->delete('/api/projects/{id}', [ProjectController::class, 'destroy', 'admin']);

    $router->get('/api/users', [UserController::class, 'index', 'admin']);
    $router->get('/api/users/{id}', [UserController::class, 'show', 'auth']);
    $router->put('/api/users/{id}', [UserController::class, 'update', 'auth']);
    $router->delete('/api/users/{id}', [UserController::class, 'destroy', 'admin']);

    $router->get('/api/user-projects', [UserProjectController::class, 'index', 'auth']);
    $router->get('/api/user-projects/{id}', [UserProjectController::class, 'show', 'auth']);
    $router->post('/api/user-projects', [UserProjectController::class, 'store', 'auth']);
    $router->put('/api/user-projects/{id}', [UserProjectController::class, 'update', 'admin']);
    $router->delete('/api/user-projects/{id}', [UserProjectController::class, 'destroy', 'admin']);

    $router->get('/api/withdrawals', [WithdrawalController::class, 'index', 'auth']);
    $router->get('/api/withdrawals/{id}', [WithdrawalController::class, 'show', 'auth']);
    $router->post('/api/withdrawals', [WithdrawalController::class, 'store', 'auth']);
    $router->put('/api/withdrawals/{id}', [WithdrawalController::class, 'update', 'admin']);
    $router->delete('/api/withdrawals/{id}', [WithdrawalController::class, 'destroy', 'admin']);

    $router->get('/api/payments', [PaymentController::class, 'index', 'auth']);
    $router->get('/api/payments/{id}', [PaymentController::class, 'show', 'auth']);

    $router->get('/api/settings', [SettingController::class, 'index']);
    $router->put('/api/settings', [SettingController::class, 'update', 'admin']);

    $router->get('/api/health', function() {
        echo json_encode(['status' => 'ok', 'timestamp' => time()]);
    });

    return $router;
};
