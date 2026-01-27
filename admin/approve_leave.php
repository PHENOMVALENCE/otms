<?php
session_start();
require_once __DIR__ . '/../includes/config.php';

if (!isset($_SESSION['admin_id'])) {
    header('Location: admin_login.php');
    exit;
}

$id = isset($_GET['id']) ? (int) $_GET['id'] : 0;
if ($id <= 0) {
    header('Location: admin_dashboard.php');
    exit;
}

$stmt = $connection->prepare("SELECT user_id FROM leaves WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$row = $stmt->get_result()->fetch_assoc();
$stmt->close();
if (!$row) {
    header('Location: admin_dashboard.php');
    exit;
}

$user_id = (int) $row['user_id'];

$up = $connection->prepare("UPDATE leaves SET status = 'Approved' WHERE id = ?");
$up->bind_param("i", $id);
$up->execute();
$up->close();

$msg = "Your leave request has been approved.";
$ins = $connection->prepare("INSERT INTO notifications (user_id, user_role, message, is_read, created_at) VALUES (?, 'user', ?, 0, NOW())");
$ins->bind_param("is", $user_id, $msg);
$ins->execute();
$ins->close();

echo "<script>alert('Leave approved.'); window.location.href='admin_dashboard.php';</script>";
exit;
