<?php
include 'includes/config.php'; // Make sure path is correct

if (isset($_GET['id']) && isset($_GET['link'])) {
    $id = intval($_GET['id']);
    $link = $_GET['link'];

    // Allow clean relative URLs and prevent injection
    if (!preg_match('/^[a-zA-Z0-9_\-\/.]+$/', $link)) {
        die("Invalid redirect URL.");
    }

    $stmt = $connection->prepare("UPDATE notifications SET is_read = 1 WHERE id = ?");
    if ($stmt) {
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $stmt->close();
    } else {
        die("Database error: " . $connection->error);
    }

    header("Location: " . $link);
    exit();
} else {
    die("Required parameters missing.");
}
