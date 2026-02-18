<?php
require_once '../config/database.php';

$database = new Database();
$db = $database->getConnection();

$method = $_SERVER['REQUEST_METHOD'];
$projectId = isset($_GET['id']) ? intval($_GET['id']) : 0;

if (!$projectId) {
    sendError('Project ID is required', 400);
}

if ($method === 'PUT') {
    $user = requireAdmin();
    $data = json_decode(file_get_contents("php://input"));

    try {
        $updates = [];
        $params = [':id' => $projectId];

        if (isset($data->title)) {
            $updates[] = "title = :title";
            $params[':title'] = $data->title;
        }

        if (isset($data->description)) {
            $updates[] = "description = :description";
            $params[':description'] = $data->description;
        }

        if (isset($data->rate_per_hour)) {
            $updates[] = "rate_per_hour = :rate_per_hour";
            $params[':rate_per_hour'] = $data->rate_per_hour;
        }

        if (isset($data->vip_level)) {
            $updates[] = "vip_level = :vip_level";
            $params[':vip_level'] = $data->vip_level;
        }

        if (isset($data->status)) {
            $updates[] = "status = :status";
            $params[':status'] = $data->status;
        }

        if (isset($data->image_url)) {
            $updates[] = "image_url = :image_url";
            $params[':image_url'] = $data->image_url;
        }

        if (empty($updates)) {
            sendError('No valid fields to update', 400);
        }

        $query = "UPDATE projects SET " . implode(', ', $updates) . " WHERE id = :id";
        $stmt = $db->prepare($query);

        foreach ($params as $key => $value) {
            $stmt->bindValue($key, $value);
        }

        $stmt->execute();

        $query = "SELECT * FROM projects WHERE id = :id LIMIT 1";
        $stmt = $db->prepare($query);
        $stmt->bindParam(':id', $projectId);
        $stmt->execute();

        sendResponse($stmt->fetch());
    } catch (Exception $e) {
        error_log("Update project error: " . $e->getMessage());
        sendError('Failed to update project', 500);
    }
} elseif ($method === 'DELETE') {
    $user = requireAdmin();

    try {
        $query = "DELETE FROM projects WHERE id = :id";
        $stmt = $db->prepare($query);
        $stmt->bindParam(':id', $projectId);
        $stmt->execute();

        sendResponse(['message' => 'Project deleted successfully']);
    } catch (Exception $e) {
        error_log("Delete project error: " . $e->getMessage());
        sendError('Failed to delete project', 500);
    }
} else {
    sendError('Method not allowed', 405);
}
?>
