<?php
session_start();
require_once __DIR__ . '/../includes/config.php';

if (!isset($_SESSION['admin_id'])) {
    header('Location: admin_login.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['notification_id'])) {
    $nid = (int) $_POST['notification_id'];
    $stmt = $connection->prepare("DELETE FROM notifications WHERE id = ? AND user_role = 'admin'");
    $stmt->bind_param("i", $nid);
    $stmt->execute();
    $stmt->close();
}

header('Location: admin_dashboard.php');
exit;
