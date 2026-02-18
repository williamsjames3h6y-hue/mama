<?php

echo "Testing Laravel-style API Structure\n\n";

$_SERVER['REQUEST_METHOD'] = 'GET';
$_SERVER['REQUEST_URI'] = '/api/health';

echo "Testing Health Endpoint...\n";
ob_start();
include __DIR__ . '/public/index.php';
$output = ob_get_clean();
echo "Response: " . $output . "\n\n";

preg_match('/\{.*\}/', $output, $matches);
if (!empty($matches)) {
    $response = json_decode($matches[0], true);
    if (isset($response['status']) && $response['status'] === 'ok') {
        echo "✓ Health check passed!\n";
        echo "✓ Timestamp: " . $response['timestamp'] . "\n";
    } else {
        echo "✗ Health check failed!\n";
    }
} else {
    echo "✗ Could not parse response!\n";
}

echo "\n";
echo "API Structure Tests Complete!\n";
echo "\n";
echo "Available Endpoints:\n";
echo "- POST /api/auth/register\n";
echo "- POST /api/auth/login\n";
echo "- GET /api/auth/me\n";
echo "- GET /api/projects\n";
echo "- GET /api/users (admin)\n";
echo "- GET /api/user-projects\n";
echo "- GET /api/withdrawals\n";
echo "- GET /api/payments\n";
echo "- GET /api/settings\n";
echo "- GET /api/health\n";
