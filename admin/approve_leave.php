<?php
include('../includes/config.php');

$leave_id = $_GET['id'];
// Get user_id from leave before update
$get_user = mysqli_query($connection, "SELECT user_id FROM leaves WHERE id = '$leave_id'");
$row = mysqli_fetch_assoc($get_user);
$user_id = $row['user_id'];

$query = "UPDATE leaves SET status = 'Approved' WHERE id = '$leave_id'";
$query_run = mysqli_query($connection, $query);

if ($query_run) {
    // Insert notification for user
    $notif = "Your leave request has been approved.";
    $notif_query = "INSERT INTO notifications (user_id, user_role, message, is_read, created_at)
                    VALUES ('$user_id', 'user', '$notif', 0, NOW())";
    mysqli_query($connection, $notif_query);

    echo "<script>
        alert('Leave approved successfully.');
        window.location.href = 'admin_dashboard.php';
    </script>";
}

?>
