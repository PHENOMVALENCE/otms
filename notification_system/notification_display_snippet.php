<?php
include 'notification_system/send_notification.php';

// Example session values
$user_id = $_SESSION['user_id'];
$user_role = $_SESSION['role'];

$stmt = $conn->prepare("SELECT * FROM notifications WHERE user_id = ? AND user_role = ? ORDER BY created_at DESC LIMIT 10");
$stmt->bind_param("is", $user_id, $user_role);
$stmt->execute();
$notifications = $stmt->get_result();
?>

<div class="notification-bell">
    <i class="fa fa-bell"></i>
    <?php
    $unread = 0;
    foreach ($notifications as $notif) {
        if (!$notif['is_read']) $unread++;
    }
    ?>
    <?php if ($unread > 0): ?>
        <span class="badge"><?= $unread ?></span>
    <?php endif; ?>
    <div class="dropdown">
        <?php foreach ($notifications as $notif): ?>
            <div class="<?= $notif['is_read'] ? 'read' : 'unread' ?>">
                <a href="<?= $notif['link'] ?: '#' ?>">
                    <?= htmlspecialchars($notif['message']) ?>
                    <small><?= $notif['created_at'] ?></small>
                </a>
            </div>
        <?php endforeach; ?>
    </div>
</div>
