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

            $query = "SELECT * FROM payments WHERE user_id = :user_id ORDER BY created_at DESC";
            $stmt = $db->prepare($query);
            $stmt->bindParam(':user_id', $userId);
        } else {
            if ($user['role'] !== 'admin') {
                sendError('Forbidden', 403);
            }

            $query = "SELECT p.*, u.full_name, u.email
                      FROM payments p
                      JOIN users u ON p.user_id = u.id
                      ORDER BY p.created_at DESC";
            $stmt = $db->prepare($query);
        }

        $stmt->execute();
        $payments = $stmt->fetchAll();
        sendResponse($payments);
    } catch (Exception $e) {
        error_log("Get payments error: " . $e->getMessage());
        sendError('Failed to fetch payments', 500);
    }
} else {
    sendError('Method not allowed', 405);
}
?>
