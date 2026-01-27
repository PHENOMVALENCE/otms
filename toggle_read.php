<?php
session_start();
include 'includes/config.php';
include 'notification_system/send_notification.php'; // Required to notify admin

// Ensure the user is logged in
if (!isset($_SESSION['id']) || !isset($_SESSION['username'])) {
    die("Unauthorized access.");
}

$user_id = $_SESSION['id'];
$username = $_SESSION['username'];
$user_role = 'user';

// Validate required GET parameters
if (isset($_GET['id']) && isset($_GET['status'])) {
    $id = intval($_GET['id']);
    $status = intval($_GET['status']); // 1 for read, 0 for unread

    // Update notification status (read/unread)
    $stmt = $connection->prepare("UPDATE notifications SET is_read = ? WHERE id = ? AND user_id = ?");
    if ($stmt) {
        $stmt->bind_param("iii", $status, $id, $user_id);
        $stmt->execute();
        $stmt->close();

        // If status changed to 'read', notify admin
        if ($status == 1) {
            $admin_id = 1; // You can adjust this if your admin ID is different
            $message = "$username has read a notification.";
            sendNotification($connection, $admin_id, 'admin', $message);
        }
    } else {
        die("Database error: " . $connection->error);
    }

    // Redirect back to user dashboard
    header("Location: user_dashboard.php");
    exit();
} else {
    die("Required parameters missing.");
}
