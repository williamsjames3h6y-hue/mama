<?php

require_once __DIR__ . '/core/Config.php';
require_once __DIR__ . '/core/Database.php';

echo "Testing Database Connection...\n\n";

echo "Database Configuration:\n";
echo "Host: " . Config::get('DB_HOST') . "\n";
echo "Database: " . Config::get('DB_NAME') . "\n";
echo "User: " . Config::get('DB_USER') . "\n";
echo "Password: " . (Config::get('DB_PASS') ? '***' : 'NOT SET') . "\n\n";

try {
    $db = new Database();
    echo "✓ Database connection successful!\n\n";

    $conn = $db->getConnection();
    $stmt = $conn->query("SHOW TABLES");
    $tables = $stmt->fetchAll(PDO::FETCH_COLUMN);

    if (empty($tables)) {
        echo "Database is empty. You need to run the schema.sql file.\n";
        echo "To set up the database, import the file: database/schema.sql\n";
    } else {
        echo "Existing tables found:\n";
        foreach ($tables as $table) {
            echo "  - $table\n";
        }
    }

} catch (Exception $e) {
    echo "✗ Database connection failed!\n";
    echo "Error: " . $e->getMessage() . "\n";
}
