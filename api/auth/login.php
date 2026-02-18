<?php
require_once '../config/database.php';

$database = new Database();
$db = $database->getConnection();

$data = json_decode(file_get_contents("php://input"));

if (!isset($data->email) || !isset($data->password)) {
    sendError('Email and password are required', 400);
}

try {
    $query = "SELECT id, email, password_hash, full_name, role, vip_level, balance, referral_code
              FROM users WHERE email = :email LIMIT 1";
    $stmt = $db->prepare($query);
    $stmt->bindParam(':email', $data->email);
    $stmt->execute();

    $user = $stmt->fetch();

    if (!$user || !password_verify($data->password, $user['password_hash'])) {
        sendError('Invalid email or password', 401);
    }

    $query = "UPDATE users SET last_login = NOW() WHERE id = :id";
    $stmt = $db->prepare($query);
    $stmt->bindParam(':id', $user['id']);
    $stmt->execute();

    $token = createToken($user['id']);

    unset($user['password_hash']);

    sendResponse([
        'token' => $token,
        'user' => $user
    ]);
} catch (Exception $e) {
    error_log("Login error: " . $e->getMessage());
    sendError('Login failed', 500);
}
?>
