<?php
include('../includes/config.php');
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['notification_id'])) {
    $notif_id = intval($_POST['notification_id']);

    $delete_query = "DELETE FROM notifications WHERE id = $notif_id AND user_role = 'admin'";
    mysqli_query($connection, $delete_query);
}

// Redirect back to admin dashboard
header("Location: admin_dashboard.php");
exit();
?>
