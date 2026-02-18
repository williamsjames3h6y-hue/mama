<?php

require_once __DIR__ . '/core/Config.php';
require_once __DIR__ . '/core/Database.php';

echo "Running VIP Level Migration...\n\n";

try {
    $db = new Database();
    $conn = $db->getConnection();

    $migrationSQL = file_get_contents(__DIR__ . '/database/migration_add_vip_levels.sql');

    $statements = array_filter(array_map('trim', explode(';', $migrationSQL)));

    $conn->beginTransaction();

    foreach ($statements as $statement) {
        if (empty($statement) || strpos($statement, '--') === 0) {
            continue;
        }

        try {
            $conn->exec($statement);
            echo "✓ Executed statement successfully\n";
        } catch (PDOException $e) {
            if (strpos($e->getMessage(), 'Duplicate column name') !== false ||
                strpos($e->getMessage(), 'Duplicate key name') !== false) {
                echo "⚠ Column or index already exists, skipping...\n";
            } else {
                throw $e;
            }
        }
    }

    $conn->commit();

    echo "\n✓ Migration completed successfully!\n\n";

    echo "Verifying migration:\n";

    $stmt = $conn->query("SHOW COLUMNS FROM users LIKE 'vip_level'");
    if ($stmt->rowCount() > 0) {
        echo "✓ users.vip_level column exists\n";
    } else {
        echo "✗ users.vip_level column NOT found\n";
    }

    $stmt = $conn->query("SHOW COLUMNS FROM projects LIKE 'vip_level_required'");
    if ($stmt->rowCount() > 0) {
        echo "✓ projects.vip_level_required column exists\n";
    } else {
        echo "✗ projects.vip_level_required column NOT found\n";
    }

    $stmt = $conn->prepare("SELECT COUNT(*) FROM site_settings WHERE `key` = 'vip1_rate'");
    $stmt->execute();
    if ($stmt->fetchColumn() > 0) {
        echo "✓ VIP rate settings added\n";
    } else {
        echo "✗ VIP rate settings NOT found\n";
    }

    echo "\nMigration complete! Your database is now ready.\n";

} catch (Exception $e) {
    if (isset($conn) && $conn->inTransaction()) {
        $conn->rollBack();
    }
    echo "✗ Migration failed!\n";
    echo "Error: " . $e->getMessage() . "\n";
    exit(1);
}
