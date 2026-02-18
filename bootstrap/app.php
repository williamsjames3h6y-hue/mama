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

$config = require __DIR__ . '/../config/database.php';
$dbDriver = $config['driver'];

if ($dbDriver === 'mysql') {
    $container->singleton(\App\Services\MySQLService::class, function($c) {
        return new \App\Services\MySQLService();
    });

    $container->singleton(\App\Services\DatabaseService::class, function($c) {
        return new \App\Services\DatabaseService();
    });
} else {
    $container->singleton(\App\Services\SupabaseService::class, function($c) {
        return new \App\Services\SupabaseService();
    });

    $container->singleton(\App\Services\DatabaseService::class, function($c) {
        return new \App\Services\DatabaseService();
    });
}

$container->singleton(\App\Services\AuthService::class, function($c) use ($dbDriver) {
    if ($dbDriver === 'mysql') {
        return new \App\Services\AuthService($c->make(\App\Services\MySQLService::class));
    }
    return new \App\Services\AuthService($c->make(\App\Services\SupabaseService::class));
});

return $container;
