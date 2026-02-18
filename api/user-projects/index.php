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

            $query = "SELECT up.*, p.title, p.description, p.rate_per_hour, p.image_url
                      FROM user_projects up
                      JOIN projects p ON up.project_id = p.id
                      WHERE up.user_id = :user_id
                      ORDER BY up.applied_at DESC";
            $stmt = $db->prepare($query);
            $stmt->bindParam(':user_id', $userId);
        } else {
            if ($user['role'] !== 'admin') {
                sendError('Forbidden', 403);
            }

            $query = "SELECT up.*, p.title, p.description, p.rate_per_hour, u.full_name, u.email
                      FROM user_projects up
                      JOIN projects p ON up.project_id = p.id
                      JOIN users u ON up.user_id = u.id
                      ORDER BY up.applied_at DESC";
            $stmt = $db->prepare($query);
        }

        $stmt->execute();
        $userProjects = $stmt->fetchAll();
        sendResponse($userProjects);
    } catch (Exception $e) {
        error_log("Get user projects error: " . $e->getMessage());
        sendError('Failed to fetch user projects', 500);
    }
} elseif ($method === 'POST') {
    $user = requireAuth();
    $data = json_decode(file_get_contents("php://input"));

    if (!isset($data->project_id)) {
        sendError('Project ID is required', 400);
    }

    try {
        $userId = $data->user_id ?? $user['id'];

        if ($user['id'] != $userId && $user['role'] !== 'admin') {
            sendError('Forbidden', 403);
        }

        $query = "SELECT id FROM user_projects WHERE user_id = :user_id AND project_id = :project_id LIMIT 1";
        $stmt = $db->prepare($query);
        $stmt->bindParam(':user_id', $userId);
        $stmt->bindParam(':project_id', $data->project_id);
        $stmt->execute();

        if ($stmt->fetch()) {
            sendError('Already applied to this project', 400);
        }

        $hoursWorked = isset($data->hours_worked) ? $data->hours_worked : 0;

        $query = "INSERT INTO user_projects (user_id, project_id, hours_worked, status)
                  VALUES (:user_id, :project_id, :hours_worked, 'pending')";
        $stmt = $db->prepare($query);
        $stmt->bindParam(':user_id', $userId);
        $stmt->bindParam(':project_id', $data->project_id);
        $stmt->bindParam(':hours_worked', $hoursWorked);
        $stmt->execute();

        $userProjectId = $db->lastInsertId();

        $query = "SELECT up.*, p.title, p.description, p.rate_per_hour, p.image_url
                  FROM user_projects up
                  JOIN projects p ON up.project_id = p.id
                  WHERE up.id = :id LIMIT 1";
        $stmt = $db->prepare($query);
        $stmt->bindParam(':id', $userProjectId);
        $stmt->execute();

        sendResponse($stmt->fetch(), 201);
    } catch (Exception $e) {
        error_log("Apply to project error: " . $e->getMessage());
        sendError('Failed to apply to project', 500);
    }
} else {
    sendError('Method not allowed', 405);
}
?>
