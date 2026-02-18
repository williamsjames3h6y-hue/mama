<?php

namespace App\Controllers;

class Controller
{
    protected function json($data, $statusCode = 200)
    {
        http_response_code($statusCode);
        header('Content-Type: application/json');
        echo json_encode($data);
        exit();
    }

    protected function error($message, $statusCode = 400)
    {
        http_response_code($statusCode);
        header('Content-Type: application/json');
        echo json_encode(['error' => $message]);
        exit();
    }

    protected function getJsonInput()
    {
        return json_decode(file_get_contents("php://input"), true);
    }

    protected function getQueryParam($key, $default = null)
    {
        return $_GET[$key] ?? $default;
    }
}
