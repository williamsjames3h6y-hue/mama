<?php
require_once '../config/database.php';

$database = new Database();
$db = $database->getConnection();

$method = $_SERVER['REQUEST_METHOD'];
$withdrawalId = isset($_GET['id']) ? intval($_GET['id']) : 0;

if (!$withdrawalId) {
    sendError('Withdrawal ID is required', 400);
}

if ($method === 'PUT') {
    $user = requireAdmin();
    $data = json_decode(file_get_contents("php://input"));

    try {
        $updates = [];
        $params = [':id' => $withdrawalId];

        if (isset($data->status)) {
            $updates[] = "status = :status";
            $params[':status'] = $data->status;

            if ($data->status === 'completed') {
                $updates[] = "processed_at = NOW()";
            } elseif ($data->status === 'rejected') {
                $updates[] = "processed_at = NOW()";
            }
        }

        if (isset($data->admin_notes)) {
            $updates[] = "admin_notes = :admin_notes";
            $params[':admin_notes'] = $data->admin_notes;
        }

        if (empty($updates)) {
            sendError('No valid fields to update', 400);
        }

        $query = "UPDATE withdrawals SET " . implode(', ', $updates) . " WHERE id = :id";
        $stmt = $db->prepare($query);

        foreach ($params as $key => $value) {
            $stmt->bindValue($key, $value);
        }

        $stmt->execute();

        $query = "SELECT * FROM withdrawals WHERE id = :id LIMIT 1";
        $stmt = $db->prepare($query);
        $stmt->bindParam(':id', $withdrawalId);
        $stmt->execute();

        sendResponse($stmt->fetch());
    } catch (Exception $e) {
        error_log("Update withdrawal error: " . $e->getMessage());
        sendError('Failed to update withdrawal', 500);
    }
} else {
    sendError('Method not allowed', 405);
}
?>
