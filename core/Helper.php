<?php

class Helper {
    public static function redirect($url) {
        header('Location: ' . $url);
        exit;
    }

    public static function escape($value) {
        return htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
    }

    public static function formatMoney($amount, $symbol = '$') {
        return $symbol . number_format($amount, 2);
    }

    public static function formatDate($date) {
        return date('M d, Y', strtotime($date));
    }

    public static function formatDateTime($date) {
        return date('M d, Y H:i', strtotime($date));
    }

    public static function generateToken() {
        return bin2hex(random_bytes(32));
    }

    public static function validateEmail($email) {
        return filter_var($email, FILTER_VALIDATE_EMAIL);
    }

    public static function isPost() {
        return $_SERVER['REQUEST_METHOD'] === 'POST';
    }

    public static function post($key, $default = null) {
        return $_POST[$key] ?? $default;
    }

    public static function get($key, $default = null) {
        return $_GET[$key] ?? $default;
    }
}
