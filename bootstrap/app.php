<?php

spl_autoload_register(function ($class) {
    $prefix = 'App\\';
    $base_dir = __DIR__ . '/../app/';

    $len = strlen($prefix);
    if (strncmp($prefix, $class, $len) !== 0) {
        return;
    }

    $relative_class = substr($class, $len);
    $file = $base_dir . str_replace('\\', '/', $relative_class) . '.php';

    if (file_exists($file)) {
        require $file;
    }
});

if (file_exists(__DIR__ . '/../vendor/autoload.php')) {
    require __DIR__ . '/../vendor/autoload.php';
}

require_once __DIR__ . '/../config/container.php';

$container = new Container();

$container->singleton(\App\Services\SupabaseService::class, function($c) {
    return new \App\Services\SupabaseService();
});

$container->singleton(\App\Services\AuthService::class, function($c) {
    return new \App\Services\AuthService($c->make(\App\Services\SupabaseService::class));
});

return $container;
