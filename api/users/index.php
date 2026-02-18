<?php
require_once '../config/database.php';

$database = new Database();
$db = $database->getConnection();

$method = $_SERVER['REQUEST_METHOD'];

if ($method === 'GET') {
    $user = requireAdmin();

    try {
        $query = "SELECT id, email, full_name, role, vip_level, balance, referral_code, referred_by, created_at, last_login
                  FROM users ORDER BY created_at DESC";
        $stmt = $db->prepare($query);
        $stmt->execute();

        $users = $stmt->fetchAll();
        sendResponse($users);
    } catch (Exception $e) {
        error_log("Get users error: " . $e->getMessage());
        sendError('Failed to fetch users', 500);
    }
} elseif ($method === 'POST') {
    $user = requireAdmin();
    $data = json_decode(file_get_contents("php://input"));

    if (!isset($data->email) || !isset($data->password) || !isset($data->full_name)) {
        sendError('Email, password, and full name are required', 400);
    }

    try {
        $query = "SELECT id FROM users WHERE email = :email LIMIT 1";
        $stmt = $db->prepare($query);
        $stmt->bindParam(':email', $data->email);
        $stmt->execute();

        if ($stmt->fetch()) {
            sendError('Email already exists', 400);
        }

        $referralCode = strtoupper(substr(md5($data->email . time()), 0, 8));
        $passwordHash = password_hash($data->password, PASSWORD_BCRYPT);
        $role = isset($data->role) ? $data->role : 'user';
        $vipLevel = isset($data->vip_level) ? $data->vip_level : 1;
        $balance = isset($data->balance) ? $data->balance : 0;

        $query = "INSERT INTO users (email, password_hash, full_name, role, vip_level, balance, referral_code)
                  VALUES (:email, :password_hash, :full_name, :role, :vip_level, :balance, :referral_code)";
        $stmt = $db->prepare($query);
        $stmt->bindParam(':email', $data->email);
        $stmt->bindParam(':password_hash', $passwordHash);
        $stmt->bindParam(':full_name', $data->full_name);
        $stmt->bindParam(':role', $role);
        $stmt->bindParam(':vip_level', $vipLevel);
        $stmt->bindParam(':balance', $balance);
        $stmt->bindParam(':referral_code', $referralCode);
        $stmt->execute();

        $userId = $db->lastInsertId();

        $query = "SELECT id, email, full_name, role, vip_level, balance, referral_code
                  FROM users WHERE id = :id LIMIT 1";
        $stmt = $db->prepare($query);
        $stmt->bindParam(':id', $userId);
        $stmt->execute();

        sendResponse($stmt->fetch(), 201);
    } catch (Exception $e) {
        error_log("Create user error: " . $e->getMessage());
        sendError('Failed to create user', 500);
    }
} else {
    sendError('Method not allowed', 405);
}
?>
