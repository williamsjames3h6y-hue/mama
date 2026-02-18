<?php
require_once '../config/database.php';

$database = new Database();
$db = $database->getConnection();

$method = $_SERVER['REQUEST_METHOD'];

if ($method === 'GET') {
    try {
        $query = "SELECT setting_key AS 'key', setting_value AS 'value' FROM site_settings";
        $stmt = $db->prepare($query);
        $stmt->execute();

        $settings = $stmt->fetchAll();
        sendResponse($settings);
    } catch (Exception $e) {
        error_log("Get settings error: " . $e->getMessage());
        sendError('Failed to fetch settings', 500);
    }
} else {
    sendError('Method not allowed', 405);
}
?>
