<?php
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization');
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

class Database {
    private $host;
    private $db_name;
    private $username;
    private $password;
    private $conn;

    public function __construct() {
        $this->host = getenv('MYSQL_HOST') ?: 'localhost';
        $this->db_name = getenv('MYSQL_DATABASE') ?: 'earningsllc';
        $this->username = getenv('MYSQL_USER') ?: 'root';
        $this->password = getenv('MYSQL_PASSWORD') ?: '';
    }

    public function getConnection() {
        $this->conn = null;

        try {
            $this->conn = new PDO(
                "mysql:host=" . $this->host . ";dbname=" . $this->db_name . ";charset=utf8mb4",
                $this->username,
                $this->password
            );
            $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $this->conn->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
        } catch(PDOException $e) {
            error_log("Connection error: " . $e->getMessage());
            throw new Exception("Database connection failed");
        }

        return $this->conn;
    }
}

function sendResponse($data, $statusCode = 200) {
    http_response_code($statusCode);
    echo json_encode($data);
    exit();
}

function sendError($message, $statusCode = 400) {
    http_response_code($statusCode);
    echo json_encode(['error' => $message]);
    exit();
}

function getAuthToken() {
    $headers = getallheaders();
    if (isset($headers['Authorization'])) {
        $auth = $headers['Authorization'];
        if (preg_match('/Bearer\s+(.*)$/i', $auth, $matches)) {
            return $matches[1];
        }
    }
    return null;
}

function verifyToken($token) {
    if (!$token) {
        return null;
    }

    try {
        $database = new Database();
        $db = $database->getConnection();

        $parts = explode('.', $token);
        if (count($parts) !== 3) {
            return null;
        }

        $payload = json_decode(base64_decode($parts[1]), true);
        if (!$payload || !isset($payload['user_id'])) {
            return null;
        }

        $query = "SELECT id, email, full_name, role, vip_level, balance, referral_code
                  FROM users WHERE id = :id LIMIT 1";
        $stmt = $db->prepare($query);
        $stmt->bindParam(':id', $payload['user_id']);
        $stmt->execute();

        return $stmt->fetch();
    } catch (Exception $e) {
        error_log("Token verification error: " . $e->getMessage());
        return null;
    }
}

function createToken($userId) {
    $header = base64_encode(json_encode(['alg' => 'HS256', 'typ' => 'JWT']));
    $payload = base64_encode(json_encode([
        'user_id' => $userId,
        'iat' => time(),
        'exp' => time() + (7 * 24 * 60 * 60)
    ]));
    $secret = getenv('JWT_SECRET') ?: 'your-secret-key-change-in-production';
    $signature = base64_encode(hash_hmac('sha256', "$header.$payload", $secret, true));

    return "$header.$payload.$signature";
}

function requireAuth() {
    $token = getAuthToken();
    $user = verifyToken($token);

    if (!$user) {
        sendError('Unauthorized', 401);
    }

    return $user;
}

function requireAdmin() {
    $user = requireAuth();

    if ($user['role'] !== 'admin') {
        sendError('Forbidden: Admin access required', 403);
    }

    return $user;
}
?>
