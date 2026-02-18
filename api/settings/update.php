<?php
require_once '../config/database.php';

$database = new Database();
$db = $database->getConnection();

$method = $_SERVER['REQUEST_METHOD'];
$key = isset($_GET['key']) ? $_GET['key'] : '';

if (!$key) {
    sendError('Setting key is required', 400);
}

if ($method === 'PUT') {
    $user = requireAdmin();
    $data = json_decode(file_get_contents("php://input"));

    if (!isset($data->value)) {
        sendError('Value is required', 400);
    }

    try {
        $query = "UPDATE site_settings SET setting_value = :value WHERE setting_key = :key";
        $stmt = $db->prepare($query);
        $stmt->bindParam(':value', $data->value);
        $stmt->bindParam(':key', $key);
        $stmt->execute();

        if ($stmt->rowCount() === 0) {
            $query = "INSERT INTO site_settings (setting_key, setting_value) VALUES (:key, :value)";
            $stmt = $db->prepare($query);
            $stmt->bindParam(':key', $key);
            $stmt->bindParam(':value', $data->value);
            $stmt->execute();
        }

        sendResponse([
            'key' => $key,
            'value' => $data->value
        ]);
    } catch (Exception $e) {
        error_log("Update setting error: " . $e->getMessage());
        sendError('Failed to update setting', 500);
    }
} else {
    sendError('Method not allowed', 405);
}
?>
