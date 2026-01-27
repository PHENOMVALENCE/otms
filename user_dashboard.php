<?php
session_start();
include('includes/config.php');

if (!isset($_SESSION['email']) || !isset($_SESSION['username'])) {
    header('Location: user_login.php');
    exit();
}

$email = $_SESSION['email'];
$username = $_SESSION['username'];
$user_id = $_SESSION['id'];
$user_role = 'user';

include('notification_system/send_notification.php');

if (isset($_POST['submit_leave'])) {
    $stmt = $connection->prepare("INSERT INTO leaves (id, user_id, subject, message, status) VALUES (NULL, ?, ?, ?, 'No Action')");
    $stmt->bind_param("iss", $_SESSION['id'], $_POST['subject'], $_POST['message']);
    if ($stmt->execute()) {
        echo "<script>alert('Form submitted successfully...'); window.location.href = 'user_dashboard.php';</script>";
    } else {
        echo "<script>alert('Error Please Try Again!!'); window.location.href = 'user_dashboard.php';</script>";
    }
    $stmt->close();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>User Dashboard</title>
    <script src="includes/jquery_latest.js"></script>
    <link rel="stylesheet" href="bootstrap/css/bootstrap.min.css">
    <script src="bootstrap/js/bootstrap.min.js"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.18.0/font/bootstrap-icons.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <script>
        $(document).ready(function () {
            $("#manage_task").click(function () {
                $("#right_sidebar").load("task.php");
            });
            $("#apply_leave").click(function () {
                $("#right_sidebar").load("leaveForm.php");
            });
            $("#leave_status").click(function () {
                $("#right_sidebar").load("leave_status.php");
            });

            $('.notification-bell').click(function () {
                $(this).find('.dropdown').toggle();
            });
        });
    </script>

    <style>
        body {
            font-family: Arial, sans-serif;
            background: url('/OTMS PROJECT/OTMS PROJECT/background/image5.gif') no-repeat center center fixed;
            background-size: cover;
            background-color: #355764;
        }

        #header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 10px 20px;
            background-color: rgba(233, 150, 122, 0.5);
        }

        .notification-bell {
            position: relative;
            display: inline-block;
            cursor: pointer;
            margin-left: 30px;
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
        }

        .notification-bell .dropdown {
            background: white;
            position: absolute;
            top: 30px;
            right: 0;
            z-index: 9999;
            display: none;
            width: 320px;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
        }

        .notification-bell .dropdown div {
            padding: 10px;
            border-bottom: 1px solid #ccc;
        }

        .notification-bell .dropdown div a {
            color: black;
            text-decoration: none;
            display: block;
        }

        .unread {
            background-color: white;
        }

        .read {
            background-color: #f0f0f0;
        }

        #header h1 {
            margin: 0;
        }

        .header-right {
            text-align: right;
        }

        .header-right b {
            margin-right: 10px;
        }

        .header-right span {
            margin-left: 20px;
        }

        .header-right img {
            vertical-align: middle;
            margin-right: 7px;
        }

        .btn-link {
            text-decoration: none;
            color: white;
            display: block;
            padding: 10px;
            text-align: center;
        }

        .btn-link:hover {
            background-color: #DAF6CC;
            color: black;
        }

        #left_sidebar {
            margin-top: 50px;
            width: 20%;
            display: inline-block;
        }

        #left_sidebar table tr {
            background-color: rgba(0, 150, 255, 0.8);
            color: white;
            font-size: 18px;
        }

        #left_sidebar table tr:hover {
            background-color: #DAF6CC;
            font-size: 20px;
            color: black;
        }

        #right_sidebar {
            padding: 12px;
            width: 75%;
            display: inline-block;
            color: white;
            vertical-align: top;
        }

        #right_sidebar ul li {
            color: lightskyblue;
        }

        h2 {
            color: lightskyblue;
        }

        .logo-image {
            width: 70px;
            height: auto;
            margin-right: 10px;
            vertical-align: middle;
        }
    </style>
</head>
<body>
    <div id="header">
        <h1>
            <img src="/OTMS PROJECT/OTMS PROJECT/logo/logo33.png" alt="Logo" class="logo-image">
            Online Task Management
        </h1>
        <div class="header-right">
            <b><img src="envelope.svg" height="32" width="32"> Email: </b><?php echo htmlspecialchars($email); ?>
            <span><b><img src="person.svg" height="32" width="32"> Username: </b><?php echo htmlspecialchars($username); ?></span>

            <!-- 🔔 Notifications -->
            <?php
            $stmt = $connection->prepare("SELECT * FROM notifications WHERE user_id = ? AND user_role = ? ORDER BY created_at DESC LIMIT 10");
            $stmt->bind_param("is", $user_id, $user_role);
            $stmt->execute();
            $notifications = $stmt->get_result();
            $unread = 0;
            $notif_list = [];
            while ($notif = $notifications->fetch_assoc()) {
                if (!$notif['is_read']) $unread++;
                $notif_list[] = $notif;
            }
            ?>
            <div class="notification-bell">
                <img src="admin/bell.svg" alt="Notifications" height="30" style="vertical-align: middle;">
                <?php if ($unread > 0): ?>
                    <span class="badge"><?= $unread ?></span>
                <?php endif; ?>
                <div class="dropdown">
                   <?php foreach ($notif_list as $notif): ?>
                    <div class="<?= $notif['is_read'] == 0 ? 'unread' : 'read' ?>">
                        <?= htmlspecialchars($notif['message']) ?><br>
                        <small><?= date("d M Y H:i", strtotime($notif['created_at'])) ?></small><br>

                        <?php if ($notif['is_read'] == 0): ?>
                            <form method="POST" action="mark_notification_read.php" style="display:inline;">
                                <input type="hidden" name="notif_id" value="<?= $notif['id'] ?>">
                                <button type="submit" class="btn btn-sm btn-primary">Mark as Read</button>
                            </form>
                        <?php endif; ?>

                        <?php if ($notif['is_read'] == 1): ?>
                            <form method="POST" action="delete_notification.php" style="display:inline;">
                                <input type="hidden" name="notif_id" value="<?= $notif['id'] ?>">
                                <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure you want to delete this notification?');">Delete</button>
                            </form>
                        <?php endif; ?>
                    </div>
                <?php endforeach; ?>
                </div>
            </div>
        </div>
    </div>

    <?php if (isset($_GET['status']) && $_GET['status'] == 'read'): ?>
        <div class="alert alert-success text-center">Notification marked as read.</div>
    <?php endif; ?>

    <div class="row">
        <div class="col-md-2" id="left_sidebar">
            <table class="table">
                <tr><td><a href="user_dashboard.php" class="btn-link">Dashboard</a></td></tr>
                <tr><td><a class="btn-link" id="manage_task">Update Task</a></td></tr>
                <tr><td><a class="btn-link" id="apply_leave">Apply Leave</a></td></tr>
                <tr><td><a class="btn-link" id="leave_status">Leave Status</a></td></tr>
                <tr><td><a href="logout.php" class="btn-link">Logout</a></td></tr>
            </table>
        </div>
        <div class="col-md-10" id="right_sidebar">
            <h2>Instructions For Students</h2>
            <ul style="line-height: 3em;font-size: 1.2em;list-style-type: none;">
                <li>1. All students should mark their attendance daily.</li>
                <li>2. Everyone must complete their assigned task.</li>
                <li>3. Be on time when completing tasks.</li>
                <li>4. Regularly update progress on your tasks.</li>
            </ul>
        </div>
    </div>
</body>
</html>
