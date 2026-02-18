<?php
require_once '../config/database.php';

$database = new Database();
$db = $database->getConnection();

$method = $_SERVER['REQUEST_METHOD'];

if ($method === 'GET') {
    try {
        $vipLevel = isset($_GET['vipLevel']) ? intval($_GET['vipLevel']) : null;

        if ($vipLevel) {
            $query = "SELECT * FROM projects WHERE vip_level <= :vip_level AND status = 'active' ORDER BY vip_level ASC, created_at DESC";
            $stmt = $db->prepare($query);
            $stmt->bindParam(':vip_level', $vipLevel);
        } else {
            $user = requireAuth();
            if ($user['role'] !== 'admin') {
                sendError('Forbidden', 403);
            }
            $query = "SELECT * FROM projects ORDER BY created_at DESC";
            $stmt = $db->prepare($query);
        }

        $stmt->execute();
        $projects = $stmt->fetchAll();
        sendResponse($projects);
    } catch (Exception $e) {
        error_log("Get projects error: " . $e->getMessage());
        sendError('Failed to fetch projects', 500);
    }
} elseif ($method === 'POST') {
    $user = requireAdmin();
    $data = json_decode(file_get_contents("php://input"));

    if (!isset($data->title) || !isset($data->description) || !isset($data->rate_per_hour) || !isset($data->vip_level)) {
        sendError('Title, description, rate per hour, and VIP level are required', 400);
    }

    try {
        $query = "INSERT INTO projects (title, description, rate_per_hour, vip_level, status, image_url)
                  VALUES (:title, :description, :rate_per_hour, :vip_level, :status, :image_url)";
        $stmt = $db->prepare($query);
        $stmt->bindParam(':title', $data->title);
        $stmt->bindParam(':description', $data->description);
        $stmt->bindParam(':rate_per_hour', $data->rate_per_hour);
        $stmt->bindParam(':vip_level', $data->vip_level);

        $status = isset($data->status) ? $data->status : 'active';
        $stmt->bindParam(':status', $status);

        $imageUrl = isset($data->image_url) ? $data->image_url : null;
        $stmt->bindParam(':image_url', $imageUrl);

        $stmt->execute();

        $projectId = $db->lastInsertId();

        $query = "SELECT * FROM projects WHERE id = :id LIMIT 1";
        $stmt = $db->prepare($query);
        $stmt->bindParam(':id', $projectId);
        $stmt->execute();

        sendResponse($stmt->fetch(), 201);
    } catch (Exception $e) {
        error_log("Create project error: " . $e->getMessage());
        sendError('Failed to create project', 500);
    }
} else {
    sendError('Method not allowed', 405);
}
?>
