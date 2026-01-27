<?php
session_start();
require_once __DIR__ . '/../includes/config.php';

if (!isset($_SESSION['admin_id'])) {
    http_response_code(401);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $connection->query("UPDATE notifications SET is_read = 1 WHERE user_role = 'admin' AND is_read = 0");
}
header('Content-Type: application/json');
echo json_encode(['ok' => true]);
