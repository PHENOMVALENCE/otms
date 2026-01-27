<?php
session_start();
require_once 'includes/config.php';

if (!isset($_SESSION['id']) || !isset($_SESSION['email'])) {
    header('Location: user_login.php');
    exit;
}

$user_id = (int) $_SESSION['id'];
$email = $_SESSION['email'];
$username = $_SESSION['username'];
$user_role = 'user';

if (isset($_POST['submit_leave'])) {
    $subject = trim($_POST['subject'] ?? '');
    $message = trim($_POST['message'] ?? '');
    if ($subject !== '' && $message !== '') {
        $stmt = $connection->prepare("INSERT INTO leaves (user_id, subject, message, status, applied_at) VALUES (?, ?, ?, 'Pending', NOW())");
        $stmt->bind_param("iss", $user_id, $subject, $message);
        if ($stmt->execute()) {
            $msg = 'Leave application submitted successfully.';
            $notif_msg = "Leave application from " . $username . ": " . $subject;
            $ins = $connection->prepare("INSERT INTO notifications (user_id, user_role, message, is_read, created_at) VALUES (?, 'admin', ?, 0, NOW())");
            $ins->bind_param("is", $user_id, $notif_msg);
            $ins->execute();
            $ins->close();
        } else {
            $msg = 'Error submitting leave. Please try again.';
        }
        $stmt->close();
    } else {
        $msg = 'Subject and message are required.';
    }
    echo "<script>alert(" . json_encode($msg) . "); window.location.href='user_dashboard.php';</script>";
    exit;
}

$tasks_res = $connection->prepare("
    SELECT t.status, COUNT(*) c FROM tasks t
    INNER JOIN task_assignments a ON a.task_id = t.id AND a.user_id = ?
    GROUP BY t.status
");
$tasks_res->bind_param("i", $user_id);
$tasks_res->execute();
$tasks_res = $tasks_res->get_result();
$task_counts = ['Not Started' => 0, 'In-progress' => 0, 'Complete' => 0];
while ($r = $tasks_res->fetch_assoc()) {
    $task_counts[$r['status']] = (int) $r['c'];
}
$total_tasks = array_sum($task_counts);

$leaves_res = $connection->query("SELECT status, COUNT(*) c FROM leaves WHERE user_id = $user_id GROUP BY status");
$leave_counts = ['Pending' => 0, 'Approved' => 0, 'Rejected' => 0, 'No Action' => 0, 'Not Action' => 0];
while ($r = $leaves_res->fetch_assoc()) {
    $leave_counts[$r['status']] = (int) $r['c'];
}
$pending_leaves = ($leave_counts['Pending'] ?? 0) + ($leave_counts['No Action'] ?? 0) + ($leave_counts['Not Action'] ?? 0);

$stmt = $connection->prepare("
    SELECT t.id, t.title, t.description, t.start_date, t.end_date, t.status, t.priority
    FROM tasks t
    INNER JOIN task_assignments a ON a.task_id = t.id AND a.user_id = ?
    ORDER BY t.end_date ASC, t.id DESC
");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$all_tasks = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
$stmt->close();

$focus_tasks = array_filter($all_tasks, function ($t) {
    $s = $t['status'] ?? '';
    return $s === 'In-progress' || (($s === 'Not Started') && !empty($t['end_date']));
});
$focus_tasks = array_slice(array_values($focus_tasks), 0, 5);

$notif_stmt = $connection->prepare("SELECT * FROM notifications WHERE user_id = ? AND user_role = ? ORDER BY created_at DESC LIMIT 10");
$notif_stmt->bind_param("is", $user_id, $user_role);
$notif_stmt->execute();
$notifications = $notif_stmt->get_result()->fetch_all(MYSQLI_ASSOC);
$notif_stmt->close();
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
    <title>Dashboard – OTMS</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Sora:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <div class="otms-dash">
        <aside class="otms-sidebar">
            <div class="brand">
                <img src="logo/logo33.png" alt="">
                <span>OTMS</span>
            </div>
            <nav>
                <a href="javascript:void(0)" id="nav_home" class="active">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" /></svg>
                    Dashboard
                </a>
                <a href="javascript:void(0)" id="nav_tasks">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4" /></svg>
                    My Tasks
                </a>
                <a href="javascript:void(0)" id="nav_leave">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" /></svg>
                    Apply Leave
                </a>
                <a href="javascript:void(0)" id="nav_leave_status">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                    Leave Status
                </a>
                <a href="index.php">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" /></svg>
                    Home
                </a>
                <a href="logout.php">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" /></svg>
                    Logout
                </a>
            </nav>
        </aside>

        <div class="otms-main">
            <header class="otms-header">
                <h1>
                    <img src="logo/logo33.png" alt="" class="logo-img">
                    Task Management
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
                                        <?php if (!($n['is_read'] ?? 0)): ?>
                                            <form method="post" action="mark_notification_read.php" class="d-inline mt-1">
                                                <input type="hidden" name="notif_id" value="<?= (int)$n['id'] ?>">
                                                <button type="submit" class="otms-btn otms-btn-primary" style="padding:0.25rem 0.5rem;font-size:0.8rem;">Mark read</button>
                                            </form>
                                        <?php else: ?>
                                            <form method="post" action="delete_notification.php" class="d-inline mt-1" onsubmit="return confirm('Delete this notification?');">
                                                <input type="hidden" name="notif_id" value="<?= (int)$n['id'] ?>">
                                                <button type="submit" class="otms-btn otms-btn-danger" style="padding:0.25rem 0.5rem;font-size:0.8rem;">Delete</button>
                                            </form>
                                        <?php endif; ?>
                                    </div>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </div>
                    </div>
                    <span class="text-muted"><?= htmlspecialchars($email) ?></span>
                    <span class="text-muted">@<?= htmlspecialchars($username) ?></span>
                </div>
            </header>

            <div class="otms-container" style="flex:1;">
                <div id="dashboardHome">
                    <h2 class="mb-1">Welcome, <?= htmlspecialchars($username) ?></h2>
                    <p class="otms-section sub mb-3">Manage your tasks and leave in one place.</p>

                    <div class="otms-stats">
                        <div class="otms-stat">
                            <div class="value"><?= $total_tasks ?></div>
                            <div class="label">Total tasks</div>
                        </div>
                        <div class="otms-stat">
                            <div class="value"><?= $task_counts['In-progress'] ?? 0 ?></div>
                            <div class="label">In progress</div>
                        </div>
                        <div class="otms-stat">
                            <div class="value"><?= $task_counts['Complete'] ?? 0 ?></div>
                            <div class="label">Completed</div>
                        </div>
                        <div class="otms-stat">
                            <div class="value"><?= $pending_leaves ?></div>
                            <div class="label">Pending leave</div>
                        </div>
                    </div>

                    <div class="otms-quick-actions mb-4">
                        <a href="javascript:void(0)" id="link_tasks">
                            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4" /></svg>
                            View & update tasks
                        </a>
                        <a href="javascript:void(0)" id="link_leave">
                            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" /></svg>
                            Apply for leave
                        </a>
                        <a href="javascript:void(0)" id="link_leave_status">
                            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                            Check leave status
                        </a>
                    </div>

                    <?php if (!empty($focus_tasks)): ?>
                    <div class="otms-section">
                        <h2>Focus</h2>
                        <p class="sub">In progress or coming up — update status to keep things moving.</p>
                        <div class="otms-task-list">
                            <?php foreach ($focus_tasks as $t): ?>
                                <?php
                                $status = $t['status'] ?? 'Not Started';
                                $badge = 'badge-not-started';
                                if (stripos($status, 'Complete') !== false) $badge = 'badge-complete';
                                elseif ($status === 'In-progress') $badge = 'badge-in-progress';
                                ?>
                                <div class="otms-task-card">
                                    <div>
                                        <div class="title"><?= htmlspecialchars($t['title'] ?: $t['description']) ?></div>
                                        <div class="meta"><?= htmlspecialchars($t['end_date'] ?? 'No due date') ?> · <?= htmlspecialchars($t['priority'] ?? 'Medium') ?></div>
                                    </div>
                                    <div class="actions">
                                        <span class="badge <?= $badge ?>"><?= htmlspecialchars($status) ?></span>
                                        <a href="update_status.php?id=<?= (int)$t['id'] ?>" class="otms-btn otms-btn-primary" style="padding:0.35rem 0.75rem;font-size:0.85rem;">Update</a>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                    <?php endif; ?>
                </div>
                <div id="ajaxPanel" style="display:none;"></div>
            </div>
        </div>
    </div>

    <script src="includes/jquery_latest.js"></script>
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
            $('#nav_tasks, #link_tasks').on('click', function() { load('task.php'); setActive('#nav_tasks'); });
            $('#nav_leave, #link_leave').on('click', function() { load('leaveForm.php'); setActive('#nav_leave'); });
            $('#nav_leave_status, #link_leave_status').on('click', function() { load('leave_status.php'); setActive('#nav_leave_status'); });
            $('#notifToggle').on('click', function(e) {
                e.stopPropagation();
                $('#notifDropdown').toggleClass('show');
            });
            $(document).on('click', function() { $('#notifDropdown').removeClass('show'); });
            $('#notifDropdown').on('click', function(e) { e.stopPropagation(); });
        })();
    </script>
</body>
</html>
