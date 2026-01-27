<?php
function sendNotification($conn, $user_id, $user_role, $message, $link = null, $auto_read = false) {
    // Input validation
    if (empty($user_role) || empty($message)) {
        error_log("Notification Error: Missing user_role or message.");
        return false;
    }

    $user_role = strtolower(trim($user_role));
    $is_read = $auto_read ? 1 : 0;

    // Case 1: Specific user ID provided
    if (!empty($user_id)) {
        return insertNotification($conn, $user_id, $user_role, $message, $link, $is_read);
    }

    // Case 2: Broadcast to all admins
    if ($user_role === 'admin') {
        $admin_query = $conn->query("SELECT id FROM admins");
        if (!$admin_query) {
            error_log("Notification Error: Failed to fetch admins - " . $conn->error);
            return false;
        }

        $success = true;
        while ($admin = $admin_query->fetch_assoc()) {
            $inserted = insertNotification($conn, $admin['id'], 'admin', $message, $link, $is_read);
            if (!$inserted) {
                $success = false;
                error_log("Notification Error: Failed to insert for admin ID {$admin['id']}");
            }
        }
        return $success;
    }

    // Unknown role or bad request
    error_log("Notification Error: Unknown scenario. user_id=$user_id, user_role=$user_role");
    return false;
}

function insertNotification($conn, $user_id, $user_role, $message, $link = null, $is_read = 0) {
    $stmt = $conn->prepare("
        INSERT INTO notifications (user_id, user_role, message, link, is_read, created_at)
        VALUES (?, ?, ?, ?, ?, NOW())
    ");

    if (!$stmt) {
        error_log("Notification Error: Prepare failed - " . $conn->error);
        return false;
    }

    $stmt->bind_param("isssi", $user_id, $user_role, $message, $link, $is_read);
    $success = $stmt->execute();

    if (!$success) {
        error_log("Notification Error: Execute failed - " . $stmt->error);
    }

    $stmt->close();
    return $success;
}
?>
