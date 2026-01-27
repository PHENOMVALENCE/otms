<?php
session_start();
include('includes/config.php');
include('notification_system/send_notification.php');

if (!isset($_SESSION['id']) || !isset($_SESSION['username'])) {
    http_response_code(401);
    echo "Unauthorized";
    exit();
}

$user_id = $_SESSION['id'];
$user_name = $_SESSION['username'];
$user_role = $_SESSION['role'] ?? 'user';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['notif_id'])) {
    $notif_id = intval($_POST['notif_id']);

    // Fetch the original notification
    $stmt = $connection->prepare("SELECT * FROM notifications WHERE id = ?");
    $stmt->bind_param("i", $notif_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $original_notif = $result->fetch_assoc();

    if ($original_notif && $original_notif['user_id'] == $user_id && $original_notif['is_read'] == 0) {
        // Step 1: Mark the user's notification as read
        $update_stmt = $connection->prepare("UPDATE notifications SET is_read = 1 WHERE id = ?");
        $update_stmt->bind_param("i", $notif_id);
        $update_stmt->execute();

        // Step 2: If the original notification is related to leave, notify admin
        if (strpos(strtolower($original_notif['message']), 'leave') !== false) {
            $admin_id = 1;

            // Step 3: Send notification to admin as auto-read (is_read = 1)
            $message = "User {$user_name} has read your response to their leave request.";
            sendNotification($connection, $admin_id, 'admin', $message, null, true);
        }
    }

    header("Location: user_dashboard.php");
    exit();
}
?>
