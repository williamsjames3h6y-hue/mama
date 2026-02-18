<?php

class Auth {
    private $db;

    public function __construct(Database $db) {
        $this->db = $db;
    }

    public function register($email, $password, $fullName) {
        $existingUser = $this->db->select('users', ['email' => $email]);

        if (!empty($existingUser)) {
            return ['success' => false, 'message' => 'Email already exists'];
        }

        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
        $referralCode = $this->generateReferralCode();
        $userId = $this->generateUUID();

        $userData = [
            'id' => $userId,
            'email' => $email,
            'password' => $hashedPassword,
            'full_name' => $fullName,
            'referral_code' => $referralCode,
            'role' => 'user',
            'vip_level' => 1
        ];

        $result = $this->db->insert('users', $userData);

        if ($result) {
            return ['success' => true, 'message' => 'Registration successful'];
        }

        return ['success' => false, 'message' => 'Registration failed'];
    }

    public function login($email, $password) {
        $users = $this->db->select('users', ['email' => $email]);

        if (empty($users)) {
            return ['success' => false, 'message' => 'Invalid credentials'];
        }

        $user = $users[0];

        if (password_verify($password, $user['password'])) {
            Session::set('user_id', $user['id']);
            Session::set('user_email', $user['email']);
            Session::set('user_name', $user['full_name']);
            Session::set('user_role', $user['role']);

            return ['success' => true, 'message' => 'Login successful', 'user' => $user];
        }

        return ['success' => false, 'message' => 'Invalid credentials'];
    }

    public function logout() {
        Session::destroy();
    }

    private function generateReferralCode() {
        return strtoupper(substr(md5(uniqid(rand(), true)), 0, 8));
    }

    private function generateUUID() {
        return sprintf('%04x%04x-%04x-%04x-%04x-%04x%04x%04x',
            mt_rand(0, 0xffff), mt_rand(0, 0xffff),
            mt_rand(0, 0xffff),
            mt_rand(0, 0x0fff) | 0x4000,
            mt_rand(0, 0x3fff) | 0x8000,
            mt_rand(0, 0xffff), mt_rand(0, 0xffff), mt_rand(0, 0xffff)
        );
    }

    public function requireLogin() {
        if (!Session::isLoggedIn()) {
            header('Location: /login');
            exit;
        }
    }

    public function requireAdmin() {
        $this->requireLogin();
        if (!Session::isAdmin()) {
            header('Location: /home');
            exit;
        }
    }
}
