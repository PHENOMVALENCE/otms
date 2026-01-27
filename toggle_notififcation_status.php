<?php
include('includes/config.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['notification_id'])) {
    $notification_id = intval($_POST['notification_id']);
    $new_status = isset($_POST['mark_read']) ? 1 : 0;

    $stmt = $connection->prepare("UPDATE notifications SET is_read = ? WHERE id = ?");
    $stmt->bind_param("ii", $new_status, $notification_id);
    $stmt->execute();
    $stmt->close();

    // Redirect back to where the request came from
    header("Location: " . $_SERVER['HTTP_REFERER']);
    exit();
} else {
    die("Invalid request.");
}
?>
