<?php

class Config {
    private static $config = null;

    public static function get($key, $default = null) {
        if (self::$config === null) {
            self::load();
        }
        return self::$config[$key] ?? $default;
    }

    private static function load() {
        self::$config = [];

        $envFile = __DIR__ . '/../.env';
        if (file_exists($envFile)) {
            $lines = file($envFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
            foreach ($lines as $line) {
                if (strpos(trim($line), '#') === 0) continue;

                list($key, $value) = explode('=', $line, 2);
                self::$config[trim($key)] = trim($value);
            }
        }
    }
}
