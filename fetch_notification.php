<?php
session_start();
include('includes/config.php');

if (!isset($_SESSION['id']) || !isset($_SESSION['email'])) {
    http_response_code(401);
    echo json_encode(['error' => 'Unauthorized']);
    exit();
}

$user_id = $_SESSION['id'];
$user_role = $_SESSION['role'] ?? 'user'; // Uses session role if set, default 'user'

$stmt = $connection->prepare("SELECT id, message, is_read, created_at FROM notifications WHERE user_id = ? AND user_role = ? ORDER BY created_at DESC LIMIT 10");
$stmt->bind_param("is", $user_id, $user_role);
$stmt->execute();
$result = $stmt->get_result();

$notifications = [];
$unread_count = 0;
while ($row = $result->fetch_assoc()) {
    $notifications[] = $row;
    if ($row['is_read'] == 0) {
        $unread_count++;
    }
}
$stmt->close();

header('Content-Type: application/json');
echo json_encode([
    'unread_count' => $unread_count,
    'notifications' => $notifications,
]);
?>

<!-- Real-Time Notification Loader -->
<script src="includes/jquery_latest.js"></script>
<script>
$(document).ready(function() {
    function loadNotifications() {
        $.ajax({
            url: 'fetch_notification.php',
            method: 'GET',
            dataType: 'json',
            success: function(data) {
                if (data.error) {
                    console.error(data.error);
                    return;
                }

                // Unread count badge
                if (data.unread_count > 0) {
                    $('.notification-bell .badge').text(data.unread_count).show();
                } else {
                    $('.notification-bell .badge').hide();
                }

                // List of notifications
                let notifHtml = '';
                if (data.notifications.length > 0) {
                    data.notifications.forEach(function(notif) {
                        notifHtml += `<div class="notification-item ${notif.is_read == 0 ? 'notification-unread' : 'notification-read'}">
                            <p>${notif.message}</p>
                            <small>${new Date(notif.created_at).toLocaleString()}</small>
                            ${notif.is_read == 0 ? `<button class="mark-read-btn" data-id="${notif.id}">Mark as Read</button>` : ''}
                        </div>`;
                    });
                } else {
                    notifHtml = '<div>No notifications found.</div>';
                }

                $('.notification-bell .dropdown').html(notifHtml);
            },
            error: function(xhr, status, error) {
                console.error('Fetch error:', error);
            }
        });
    }

    // Initial + periodic call
    loadNotifications();
    setInterval(loadNotifications, 10000);

    // Dropdown toggle
    $('.notification-bell').off('click').on('click', function() {
        $(this).find('.dropdown').toggle();
    });

    // Mark as read button
    $(document).off('click', '.mark-read-btn').on('click', '.mark-read-btn', function() {
        let notifId = $(this).data('id');
        $.ajax({
            url: 'mark_notification_read.php',
            method: 'POST',
            data: { notif_id: notifId },
            success: function() {
                loadNotifications();
            },
            error: function() {
                alert('Error marking as read.');
            }
        });
    });
});
</script>

<!-- Notification Style -->
<style>
.notification-bell {
    position: relative;
    cursor: pointer;
    display: inline-block;
}
.notification-bell .badge {
    position: absolute;
    top: -8px;
    right: -8px;
    background: red;
    color: white;
    border-radius: 50%;
    padding: 2px 6px;
    font-size: 12px;
    display: none;
}
.notification-bell .dropdown {
    display: none;
    position: absolute;
    right: 0;
    background: white;
    width: 300px;
    max-height: 350px;
    overflow-y: auto;
    box-shadow: 0 0 10px rgba(0,0,0,0.2);
    border-radius: 5px;
    z-index: 10000;
}
.notification-item {
    border-bottom: 1px solid #ddd;
    padding: 10px;
}
.notification-unread {
    background-color: #eaf3ff;
}
.notification-read {
    background-color: #f8f8f8;
}
.notification-item p {
    margin: 0 0 5px 0;
}
.notification-item small {
    color: #888;
}
.mark-read-btn {
    background: #4CAF50;
    color: white;
    border: none;
    padding: 5px 8px;
    font-size: 12px;
    cursor: pointer;
    border-radius: 3px;
}
.mark-read-btn:hover {
    background: #45a049;
}
</style>
