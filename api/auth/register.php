<?php
require_once '../config/database.php';

$database = new Database();
$db = $database->getConnection();

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

    $query = "INSERT INTO users (email, password_hash, full_name, role, vip_level, balance, referral_code, referred_by)
              VALUES (:email, :password_hash, :full_name, 'user', 1, 0, :referral_code, :referred_by)";
    $stmt = $db->prepare($query);
    $stmt->bindParam(':email', $data->email);
    $stmt->bindParam(':password_hash', $passwordHash);
    $stmt->bindParam(':full_name', $data->full_name);
    $stmt->bindParam(':referral_code', $referralCode);

    $referredBy = isset($data->referral_code) ? $data->referral_code : null;
    $stmt->bindParam(':referred_by', $referredBy);

    $stmt->execute();

    $userId = $db->lastInsertId();

    if ($referredBy) {
        $query = "SELECT id FROM users WHERE referral_code = :code LIMIT 1";
        $stmt = $db->prepare($query);
        $stmt->bindParam(':code', $referredBy);
        $stmt->execute();
        $referrer = $stmt->fetch();

        if ($referrer) {
            $query = "SELECT value FROM site_settings WHERE setting_key = 'referral_bonus' LIMIT 1";
            $stmt = $db->prepare($query);
            $stmt->execute();
            $bonusResult = $stmt->fetch();
            $bonus = $bonusResult ? floatval($bonusResult['value']) : 300;

            $query = "UPDATE users SET balance = balance + :bonus WHERE id = :id";
            $stmt = $db->prepare($query);
            $stmt->bindParam(':bonus', $bonus);
            $stmt->bindParam(':id', $referrer['id']);
            $stmt->execute();

            $query = "INSERT INTO payments (user_id, amount, type, status, description)
                      VALUES (:user_id, :amount, 'bonus', 'completed', 'Referral bonus')";
            $stmt = $db->prepare($query);
            $stmt->bindParam(':user_id', $referrer['id']);
            $stmt->bindParam(':amount', $bonus);
            $stmt->execute();
        }
    }

    $token = createToken($userId);

    $query = "SELECT id, email, full_name, role, vip_level, balance, referral_code
              FROM users WHERE id = :id LIMIT 1";
    $stmt = $db->prepare($query);
    $stmt->bindParam(':id', $userId);
    $stmt->execute();
    $user = $stmt->fetch();

    sendResponse([
        'token' => $token,
        'user' => $user
    ]);
} catch (Exception $e) {
    error_log("Registration error: " . $e->getMessage());
    sendError('Registration failed', 500);
}
?>
