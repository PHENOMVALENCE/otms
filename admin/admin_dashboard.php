<?php
session_start();
require_once __DIR__ . '/../includes/config.php';

if (!isset($_SESSION['admin_id']) || !isset($_SESSION['admin_username'])) {
    header('Location: admin_login.php');
    exit;
}

$admin_username = $_SESSION['admin_username'];
$admin_email = $_SESSION['admin_email'];

if (isset($_POST['create_task'])) {
    $user_ids = isset($_POST['user_ids']) && is_array($_POST['user_ids']) ? $_POST['user_ids'] : [];
    $user_ids = array_filter(array_map('intval', $user_ids));
    $user_ids = array_unique($user_ids);
    $title = trim($_POST['title'] ?? '');
    $description = trim($_POST['description'] ?? '');
    $start_date = trim($_POST['start_date'] ?? '');
    $end_date = trim($_POST['end_date'] ?? '');
    $priority = trim($_POST['priority'] ?? 'Medium');
    if (count($user_ids) > 0 && $description !== '') {
        $stmt = $connection->prepare("INSERT INTO tasks (title, description, start_date, end_date, priority, status) VALUES (?, ?, NULLIF(?, ''), NULLIF(?, ''), ?, 'Not Started')");
        $stmt->bind_param("sssss", $title, $description, $start_date, $end_date, $priority);
        if ($stmt->execute()) {
            $task_id = (int) $connection->insert_id;
            $stmt->close();
            $ins = $connection->prepare("INSERT INTO task_assignments (task_id, user_id) VALUES (?, ?)");
            foreach ($user_ids as $uid) {
                if ($uid > 0) {
                    $ins->bind_param("ii", $task_id, $uid);
                    $ins->execute();
                }
            }
            $ins->close();
            $msg = 'Task created successfully.';
        } else {
            $msg = 'Error creating task. Please try again.';
            $stmt->close();
        }
    } else {
        $msg = 'Select at least one user and enter a description.';
    }
    echo "<script>alert(" . json_encode($msg) . "); window.location.href='admin_dashboard.php';</script>";
    exit;
}

$user_count = 0;
$uc = $connection->query("SELECT COUNT(*) c FROM users");
if ($uc && $row = $uc->fetch_assoc()) $user_count = (int) $row['c'];

$tasks_res = $connection->query("SELECT status, COUNT(*) c FROM tasks GROUP BY status");
$task_counts = ['Not Started' => 0, 'In-progress' => 0, 'Complete' => 0];
while ($r = $tasks_res->fetch_assoc()) {
    $task_counts[$r['status']] = (int) $r['c'];
}
$total_tasks = array_sum($task_counts);

$leaves_res = $connection->query("SELECT status, COUNT(*) c FROM leaves GROUP BY status");
$leave_counts = [];
while ($r = $leaves_res->fetch_assoc()) {
    $leave_counts[$r['status']] = (int) $r['c'];
}
$total_leaves = array_sum($leave_counts);
$pending_leaves = ($leave_counts['Pending'] ?? 0) + ($leave_counts['No Action'] ?? 0) + ($leave_counts['Not Action'] ?? 0);

$notif_result = $connection->query("SELECT * FROM notifications WHERE user_role = 'admin' ORDER BY created_at DESC LIMIT 15");
$notifications = $notif_result ? $notif_result->fetch_all(MYSQLI_ASSOC) : [];
$unread_count = 0;
foreach ($notifications as $n) {
    if (!($n['is_read'] ?? 0)) $unread_count++;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard – OTMS</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Sora:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>
    <div class="otms-dash">
        <aside class="otms-sidebar">
            <div class="brand">
                <img src="../logo/logo33.png" alt="">
                <span>OTMS Admin</span>
            </div>
            <nav>
                <a href="javascript:void(0)" id="nav_home" class="active">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z" /></svg>
                    Dashboard
                </a>
                <a href="javascript:void(0)" id="nav_manage_users">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" /></svg>
                    Manage Users
                    <span class="admin-badge">Admin</span>
                </a>
                <a href="javascript:void(0)" id="nav_create_task">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" /></svg>
                    Create Task
                </a>
                <a href="javascript:void(0)" id="nav_manage_task">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4" /></svg>
                    Manage Tasks
                </a>
                <a href="javascript:void(0)" id="nav_view_leave">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" /></svg>
                    Leave Applications
                </a>
                <a href="../index.php">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" /></svg>
                    Home
                </a>
                <a href="../logout.php">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" /></svg>
                    Logout
                </a>
            </nav>
        </aside>

        <div class="otms-main">
            <header class="otms-header">
                <h1>
                    <img src="../logo/logo33.png" alt="" class="logo-img">
                    Admin Dashboard
                </h1>
                <div class="otms-header-right">
                    <div class="otms-notif-wrap">
                        <button type="button" class="otms-btn otms-btn-ghost" id="notifToggle" style="padding:0.4rem 0.6rem;">
                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" /></svg>
                            <?php if ($unread_count > 0): ?>
                                <span class="otms-notif-badge"><?= $unread_count ?></span>
                            <?php endif; ?>
                        </button>
                        <div class="otms-notif-dropdown" id="notifDropdown">
                            <?php if (empty($notifications)): ?>
                                <div class="otms-notif-item">No notifications</div>
                            <?php else: ?>
                                <?php foreach ($notifications as $n): ?>
                                    <div class="otms-notif-item <?= ($n['is_read'] ?? 0) ? '' : 'unread' ?>">
                                        <?= htmlspecialchars($n['message']) ?>
                                        <br><small><?= date('d M Y H:i', strtotime($n['created_at'])) ?></small>
                                        <form method="post" action="delete_admin_notification.php" class="d-inline mt-1" onsubmit="return confirm('Delete this notification?');">
                                            <input type="hidden" name="notification_id" value="<?= (int)$n['id'] ?>">
                                            <button type="submit" class="otms-btn otms-btn-danger" style="padding:0.25rem 0.5rem;font-size:0.8rem;">Delete</button>
                                        </form>
                                    </div>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </div>
                    </div>
                    <span class="text-muted"><?= htmlspecialchars($admin_email) ?></span>
                    <span class="text-muted">@<?= htmlspecialchars($admin_username) ?></span>
                </div>
            </header>

            <div class="otms-container" style="flex:1;">
                <div class="otms-admin-banner">
                    <span class="badge">Admin</span>
                    <span>You have full system control: manage users, create and assign tasks, approve or reject leave applications, and view all notifications.</span>
                </div>

                <div id="dashboardHome">
                    <h2 class="mb-1">System overview</h2>
                    <p class="otms-section sub mb-3">Monitor users, tasks, and leave at a glance.</p>

                    <div class="otms-stats">
                        <div class="otms-stat">
                            <div class="value"><?= $user_count ?></div>
                            <div class="label">Users</div>
                        </div>
                        <div class="otms-stat">
                            <div class="value"><?= $total_tasks ?></div>
                            <div class="label">Total tasks</div>
                        </div>
                        <div class="otms-stat">
                            <div class="value"><?= $task_counts['In-progress'] ?? 0 ?></div>
                            <div class="label">In progress</div>
                        </div>
                        <div class="otms-stat">
                            <div class="value"><?= $pending_leaves ?></div>
                            <div class="label">Pending leave</div>
                        </div>
                        <div class="otms-stat">
                            <div class="value"><?= $total_leaves ?></div>
                            <div class="label">Total leave requests</div>
                        </div>
                    </div>

                    <div class="otms-quick-actions mb-4">
                        <a href="javascript:void(0)" id="link_manage_users">
                            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" /></svg>
                            Manage users
                        </a>
                        <a href="javascript:void(0)" id="link_create_task">
                            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" /></svg>
                            Create task
                        </a>
                        <a href="javascript:void(0)" id="link_manage_task">
                            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4" /></svg>
                            Manage tasks
                        </a>
                        <a href="javascript:void(0)" id="link_view_leave">
                            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" /></svg>
                            Review leave
                        </a>
                    </div>
                </div>
                <div id="ajaxPanel" style="display:none;"></div>
            </div>
        </div>
    </div>

    <script src="../includes/jquery_latest.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        (function() {
            var home = $('#dashboardHome');
            var panel = $('#ajaxPanel');
            function setActive(id) {
                $('.otms-sidebar nav a').removeClass('active');
                var $a = $(id);
                if ($a.length) $a.addClass('active');
            }
            function load(url) {
                home.hide();
                panel.show().empty().load(url);
            }
            function showHome() {
                panel.hide().empty();
                home.show();
                setActive('#nav_home');
            }
            $('#nav_home').on('click', function() { showHome(); });
            $('#nav_manage_users, #link_manage_users').on('click', function() { load('manage_users.php'); setActive('#nav_manage_users'); });
            $('#nav_create_task, #link_create_task').on('click', function() { load('create_task.php'); setActive('#nav_create_task'); });
            $('#nav_manage_task, #link_manage_task').on('click', function() { load('manage_task.php'); setActive('#nav_manage_task'); });
            $('#nav_view_leave, #link_view_leave').on('click', function() { load('view_leave.php'); setActive('#nav_view_leave'); });

            $('#notifToggle').on('click', function(e) {
                e.stopPropagation();
                var dd = $('#notifDropdown');
                dd.toggleClass('show');
                if (dd.hasClass('show')) {
                    $.post('mark_notifications_read.php', { user_role: 'admin' }, function() {
                        $('.otms-notif-badge').remove();
                        $('#notifDropdown .otms-notif-item').removeClass('unread');
                    });
                }
            });
            $(document).on('click', function() { $('#notifDropdown').removeClass('show'); });
            $('#notifDropdown').on('click', function(e) { e.stopPropagation(); });
        })();
    </script>
</body>
</html>
