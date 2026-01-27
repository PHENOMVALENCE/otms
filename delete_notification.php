<?php
include('includes/config.php');
session_start();

if (!isset($_SESSION['id'])) {
    header("Location: user_login.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['notif_id'])) {
    $notif_id = intval($_POST['notif_id']);
    $user_id = $_SESSION['id'];

    // Only allow deletion if notification is already read
    $sql = "DELETE FROM notifications WHERE id = ? AND user_id = ? AND is_read = 1";
    $stmt = $connection->prepare($sql);
    $stmt->bind_param("ii", $notif_id, $user_id);
    $stmt->execute();
}

header("Location: user_dashboard.php");
exit();
?>
