<?php
require_once '../config/database.php';

$database = new Database();
$db = $database->getConnection();

$method = $_SERVER['REQUEST_METHOD'];
$userProjectId = isset($_GET['id']) ? intval($_GET['id']) : 0;

if (!$userProjectId) {
    sendError('User project ID is required', 400);
}

if ($method === 'PUT') {
    $user = requireAuth();
    $data = json_decode(file_get_contents("php://input"));

    try {
        $query = "SELECT user_id FROM user_projects WHERE id = :id LIMIT 1";
        $stmt = $db->prepare($query);
        $stmt->bindParam(':id', $userProjectId);
        $stmt->execute();
        $userProject = $stmt->fetch();

        if (!$userProject) {
            sendError('User project not found', 404);
        }

        if ($user['id'] != $userProject['user_id'] && $user['role'] !== 'admin') {
            sendError('Forbidden', 403);
        }

        $updates = [];
        $params = [':id' => $userProjectId];

        if (isset($data->hours_worked)) {
            $updates[] = "hours_worked = :hours_worked";
            $params[':hours_worked'] = $data->hours_worked;
        }

        if (isset($data->status) && $user['role'] === 'admin') {
            $updates[] = "status = :status";
            $params[':status'] = $data->status;

            if ($data->status === 'approved') {
                $updates[] = "approved_at = NOW()";
            }
        }

        if (empty($updates)) {
            sendError('No valid fields to update', 400);
        }

        $query = "UPDATE user_projects SET " . implode(', ', $updates) . " WHERE id = :id";
        $stmt = $db->prepare($query);

        foreach ($params as $key => $value) {
            $stmt->bindValue($key, $value);
        }

        $stmt->execute();

        $query = "SELECT up.*, p.title, p.description, p.rate_per_hour, p.image_url
                  FROM user_projects up
                  JOIN projects p ON up.project_id = p.id
                  WHERE up.id = :id LIMIT 1";
        $stmt = $db->prepare($query);
        $stmt->bindParam(':id', $userProjectId);
        $stmt->execute();

        sendResponse($stmt->fetch());
    } catch (Exception $e) {
        error_log("Update user project error: " . $e->getMessage());
        sendError('Failed to update user project', 500);
    }
} else {
    sendError('Method not allowed', 405);
}
?>
