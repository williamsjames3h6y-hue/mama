<?php
require_once 'config/database.php';

try {
    $database = new Database();
    $db = $database->getConnection();

    $query = "SELECT 1";
    $stmt = $db->prepare($query);
    $stmt->execute();

    sendResponse([
        'status' => 'ok',
        'message' => 'API is running',
        'database' => 'connected'
    ]);
} catch (Exception $e) {
    error_log("Health check error: " . $e->getMessage());
    sendError('Service unavailable', 503);
}
?>
