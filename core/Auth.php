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

        $userData = [
            'email' => $email,
            'password' => $hashedPassword,
            'full_name' => $fullName,
            'referral_code' => $referralCode,
            'role' => 'user'
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
