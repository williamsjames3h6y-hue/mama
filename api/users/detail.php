<?php
require_once '../config/database.php';

$database = new Database();
$db = $database->getConnection();

$method = $_SERVER['REQUEST_METHOD'];
$userId = isset($_GET['id']) ? intval($_GET['id']) : 0;

if (!$userId) {
    sendError('User ID is required', 400);
}

if ($method === 'GET') {
    $currentUser = requireAuth();

    if ($currentUser['id'] != $userId && $currentUser['role'] !== 'admin') {
        sendError('Forbidden', 403);
    }

    try {
        $query = "SELECT id, email, full_name, role, vip_level, balance, referral_code, referred_by, created_at, last_login
                  FROM users WHERE id = :id LIMIT 1";
        $stmt = $db->prepare($query);
        $stmt->bindParam(':id', $userId);
        $stmt->execute();

        $user = $stmt->fetch();

        if (!$user) {
            sendError('User not found', 404);
        }

        sendResponse($user);
    } catch (Exception $e) {
        error_log("Get user error: " . $e->getMessage());
        sendError('Failed to fetch user', 500);
    }
} elseif ($method === 'PUT') {
    $currentUser = requireAuth();
    $data = json_decode(file_get_contents("php://input"));

    if ($currentUser['id'] != $userId && $currentUser['role'] !== 'admin') {
        sendError('Forbidden', 403);
    }

    try {
        $updates = [];
        $params = [':id' => $userId];

        if (isset($data->full_name)) {
            $updates[] = "full_name = :full_name";
            $params[':full_name'] = $data->full_name;
        }

        if (isset($data->vip_level) && $currentUser['role'] === 'admin') {
            $updates[] = "vip_level = :vip_level";
            $params[':vip_level'] = $data->vip_level;
        }

        if (isset($data->balance) && $currentUser['role'] === 'admin') {
            $updates[] = "balance = :balance";
            $params[':balance'] = $data->balance;
        }

        if (isset($data->role) && $currentUser['role'] === 'admin') {
            $updates[] = "role = :role";
            $params[':role'] = $data->role;
        }

        if (empty($updates)) {
            sendError('No valid fields to update', 400);
        }

        $query = "UPDATE users SET " . implode(', ', $updates) . " WHERE id = :id";
        $stmt = $db->prepare($query);

        foreach ($params as $key => $value) {
            $stmt->bindValue($key, $value);
        }

        $stmt->execute();

        $query = "SELECT id, email, full_name, role, vip_level, balance, referral_code
                  FROM users WHERE id = :id LIMIT 1";
        $stmt = $db->prepare($query);
        $stmt->bindParam(':id', $userId);
        $stmt->execute();

        sendResponse($stmt->fetch());
    } catch (Exception $e) {
        error_log("Update user error: " . $e->getMessage());
        sendError('Failed to update user', 500);
    }
} else {
    sendError('Method not allowed', 405);
}
?>
