<?php
include('../includes/config.php');
session_start();

if (!isset($_SESSION['admin_username']) || !isset($_SESSION['admin_email'])) {
    header("Location: admin_login.php");
    exit();
}

$admin_username = $_SESSION['admin_username'];
$admin_email = $_SESSION['admin_email'];

$notifications = [];
$unread_count = 0;

$notif_query = "SELECT * FROM notifications WHERE user_role = 'admin' ORDER BY created_at DESC";
$notif_result = mysqli_query($connection, $notif_query);

if ($notif_result) {
    while ($row = mysqli_fetch_assoc($notif_result)) {
        $notifications[] = $row;
        if (isset($row['is_read']) && $row['is_read'] == 0) {
            $unread_count++;
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Admin Dashboard</title>
    <script src="../includes/jquery_latest.js"></script>
    <link rel="stylesheet" href="../bootstrap/css/bootstrap.min.css" />
    <script src="../bootstrap/js/bootstrap.min.js"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.18.0/font/bootstrap-icons.css" />
    <style>
        body {
            margin: 0;
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
            position: relative;
        }
        .notification-icon {
            position: relative;
            cursor: pointer;
            margin-right: 15px;
        }
        .notif-count {
            position: absolute;
            top: -5px;
            right: -10px;
            background: red;
            color: white;
            border-radius: 50%;
            padding: 2px 6px;
            font-size: 12px;
        }
        .notif-dropdown {
            display: none;
            position: absolute;
            right: 20px;
            top: 60px;
            background: white;
            color: black;
            width: 350px;
            max-height: 300px;
            overflow-y: auto;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.2);
            border-radius: 5px;
            z-index: 1000;
        }
        .notif-dropdown div {
            padding: 10px;
            border-bottom: 1px solid #ccc;
        }
        .notif-dropdown div:last-child {
            border-bottom: none;
        }
        .notif-dropdown .unread {
            background-color: #ffffff;
        }
        .notif-dropdown .read {
            background-color: #f0f0f0;
        }
        .notif-dropdown form {
            margin-top: 5px;
        }
        .delete-btn {
            margin-top: 5px;
            font-size: 12px;
            background: red;
            color: white;
            border: none;
            padding: 3px 6px;
            border-radius: 3px;
            cursor: pointer;
        }
        #header h1 {
            margin: 0;
        }
        .header-right {
            text-align: right;
            display: flex;
            align-items: center;
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
            background-color: #daf6cc;
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
            background-color: #daf6cc;
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
        #content {
            display: flex;
        }
        .logo-image {
            width: 70px;
            height: auto;
            margin-right: 10px;
            vertical-align: middle;
        }
    </style>
    <script>
        $(document).ready(function () {
            $("#create_task").click(function () {
                $("#right_sidebar").load("create_task.php");
            });
            $("#manage_task").click(function () {
                $("#right_sidebar").load("manage_task.php");
            });
            $("#view_leave").click(function () {
                $("#right_sidebar").load("view_leave.php");
            });

            $(".notification-icon").click(function (e) {
                e.stopPropagation();
                $(".notif-dropdown").toggle();

                // Mark all admin notifications as read on dropdown open
                if ($(".notif-dropdown").is(":visible")) {
                    $.ajax({
                        url: 'mark_notifications_read.php',
                        method: 'POST',
                        data: { user_role: 'admin' },
                        success: function () {
                            $(".notif-count").remove();
                            $(".notif-dropdown div.unread").removeClass("unread").addClass("read");
                            $(".notif-dropdown span").filter(function () {
                                return $(this).text().trim() === "Unread";
                            }).text("Read").css("color", "green");
                        },
                        error: function () {
                            console.log("Failed to mark notifications as read.");
                        }
                    });
                }
            });

            $(document).click(function () {
                $(".notif-dropdown").hide();
            });

            $(".notif-dropdown").click(function (e) {
                e.stopPropagation();
            });
        });
    </script>
</head>
<body>
    <div id="header">
        <h1>
            <img src="/OTMS PROJECT/OTMS PROJECT/logo/logo33.png" alt="Logo" class="logo-image" />
            Online Task Management
        </h1>
        <div class="header-right">
            <div class="notification">
                <div class="notification-icon">
                    <img src="bell.svg" alt="Notifications" height="30" />
                    <?php if ($unread_count > 0): ?>
                        <span class="notif-count"><?= $unread_count ?></span>
                    <?php endif; ?>
                </div>
                <div class="notif-dropdown">
                    <?php if (!empty($notifications)): ?>
                        <?php foreach ($notifications as $notif): ?>
                            <div class="<?= $notif['is_read'] == 0 ? 'unread' : 'read' ?>">
                                <?= htmlspecialchars($notif['message']) ?><br />
                                <small><?= date("d M Y H:i", strtotime($notif['created_at'])) ?></small><br />
                                <span style="font-size: 12px; font-style: italic; color: <?= $notif['is_read'] == 0 ? 'red' : 'green' ?>;">
                                    <?= $notif['is_read'] == 0 ? 'Unread' : 'Read' ?>
                                </span>
                                <form method="POST" action="delete_admin_notification.php" onsubmit="return confirm('Delete this notification?');">
                                    <input type="hidden" name="notification_id" value="<?= $notif['id'] ?>" />
                                    <button type="submit" class="delete-btn">Delete</button>
                                </form>
                            </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <div>No notifications found</div>
                    <?php endif; ?>
                </div>
            </div>
            <b><img src="envelope.svg" height="32" width="32" /> Email:</b> <?= htmlspecialchars($admin_email) ?>
            <span>
                <b><img src="person.svg" height="32" width="32" /> Username:</b> <?= htmlspecialchars($admin_username) ?>
            </span>
        </div>
    </div>

    <div id="content" class="row">
        <div class="col-md-2" id="left_sidebar">
            <table class="table">
                <tr><td style="text-align: center"><a href="admin_dashboard.php" class="btn-link">Dashboard</a></td></tr>
                <tr><td style="text-align: center"><a href="javascript:void(0);" class="btn-link" id="create_task">Create Task</a></td></tr>
                <tr><td style="text-align: center"><a href="javascript:void(0);" class="btn-link" id="manage_task">Manage Task</a></td></tr>
                <tr><td style="text-align: center"><a href="javascript:void(0);" class="btn-link" id="view_leave">Leave Applications</a></td></tr>
                <tr><td style="text-align: center"><a href="../logout.php" class="btn-link">Logout</a></td></tr>
            </table>
        </div>
        <div class="col-md-10" id="right_sidebar">
            <h2>Instructions For Admin</h2>
            <ul style="line-height: 3em; font-size: 1.2em; list-style-type: none;">
                <li>1. All the students should mark their attendance daily.</li>
                <li>2. Everyone must complete the assigned task.</li>
                <li>3. Make sure you are on time when completing the task.</li>
                <li>4. Make sure to update on the progress of your work.</li>
            </ul>
        </div>
    </div>
</body>
</html>
