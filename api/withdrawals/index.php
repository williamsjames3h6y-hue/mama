<?php
require_once '../config/database.php';

$database = new Database();
$db = $database->getConnection();

$method = $_SERVER['REQUEST_METHOD'];

if ($method === 'GET') {
    $user = requireAuth();

    try {
        $userId = isset($_GET['user_id']) ? intval($_GET['user_id']) : null;

        if ($userId) {
            if ($user['id'] != $userId && $user['role'] !== 'admin') {
                sendError('Forbidden', 403);
            }

            $query = "SELECT * FROM withdrawals WHERE user_id = :user_id ORDER BY created_at DESC";
            $stmt = $db->prepare($query);
            $stmt->bindParam(':user_id', $userId);
        } else {
            if ($user['role'] !== 'admin') {
                sendError('Forbidden', 403);
            }

            $query = "SELECT w.*, u.full_name, u.email
                      FROM withdrawals w
                      JOIN users u ON w.user_id = u.id
                      ORDER BY w.created_at DESC";
            $stmt = $db->prepare($query);
        }

        $stmt->execute();
        $withdrawals = $stmt->fetchAll();
        sendResponse($withdrawals);
    } catch (Exception $e) {
        error_log("Get withdrawals error: " . $e->getMessage());
        sendError('Failed to fetch withdrawals', 500);
    }
} elseif ($method === 'POST') {
    $user = requireAuth();
    $data = json_decode(file_get_contents("php://input"));

    if (!isset($data->amount) || !isset($data->method) || !isset($data->account_details)) {
        sendError('Amount, method, and account details are required', 400);
    }

    try {
        $userId = $data->user_id ?? $user['id'];

        if ($user['id'] != $userId && $user['role'] !== 'admin') {
            sendError('Forbidden', 403);
        }

        $query = "SELECT balance FROM users WHERE id = :id LIMIT 1";
        $stmt = $db->prepare($query);
        $stmt->bindParam(':id', $userId);
        $stmt->execute();
        $userData = $stmt->fetch();

        if (!$userData) {
            sendError('User not found', 404);
        }

        $query = "SELECT value FROM site_settings WHERE setting_key = 'minimum_withdrawal' LIMIT 1";
        $stmt = $db->prepare($query);
        $stmt->execute();
        $minWithdrawal = $stmt->fetch();
        $minAmount = $minWithdrawal ? floatval($minWithdrawal['value']) : 50;

        if ($data->amount < $minAmount) {
            sendError("Minimum withdrawal amount is $$minAmount", 400);
        }

        if ($userData['balance'] < $data->amount) {
            sendError('Insufficient balance', 400);
        }

        $query = "INSERT INTO withdrawals (user_id, amount, method, account_details, status)
                  VALUES (:user_id, :amount, :method, :account_details, 'pending')";
        $stmt = $db->prepare($query);
        $stmt->bindParam(':user_id', $userId);
        $stmt->bindParam(':amount', $data->amount);
        $stmt->bindParam(':method', $data->method);
        $stmt->bindParam(':account_details', $data->account_details);
        $stmt->execute();

        $withdrawalId = $db->lastInsertId();

        $query = "SELECT * FROM withdrawals WHERE id = :id LIMIT 1";
        $stmt = $db->prepare($query);
        $stmt->bindParam(':id', $withdrawalId);
        $stmt->execute();

        sendResponse($stmt->fetch(), 201);
    } catch (Exception $e) {
        error_log("Create withdrawal error: " . $e->getMessage());
        sendError('Failed to create withdrawal', 500);
    }
} else {
    sendError('Method not allowed', 405);
}
?>
