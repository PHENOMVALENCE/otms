<?php
session_start();

include('includes/config.php');

if (!isset($_SESSION['email']) || !isset($_SESSION['username']) || !isset($_SESSION['id'])) {
    header('Location: user_login.php');
    exit();
}
$user_id = $_SESSION['id'];
$query = "SELECT * FROM tasks WHERE user_id = ?";
$stmt = mysqli_prepare($connection, $query);
mysqli_stmt_bind_param($stmt, 'i', $user_id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>OTMS | Task</title>
    <script src="includes/jquery_latest.js"></script>
    <!-- Bootstrap files -->
    <link rel="stylesheet" type="text/css" href="bootstrap/css/bootstrap.min.css">
    <script src="bootstrap/js/bootstrap.min.js"></script>
    <style>
        h3 {
            color: white;
            text-align: center;
            margin-top: 20px;
        }
    </style>
</head>

<body>
    <center>
        <h3>YOUR TASKS</h3>
    </center>
    <table class="table" style="background-color:rgba(233, 150, 122, 0.8); width: 75vw; height: 25vh; border-collapse: collapse; border: 2px solid #333;">
        <tr>
            <th style="padding: 10px;text-align:center;border:1px solid #ddd;">S.No</th>
            <th style="padding: 10px;text-align:center;border:1px solid #ddd;">Task ID</th>
            <th style="padding: 10px;text-align:center;border:1px solid #ddd;">Description</th>
            <th style="padding: 10px;text-align:center;border:1px solid #ddd;">Start Date</th>
            <th style="padding: 10px;text-align:center;border:1px solid #ddd;">End Date</th>
            <th style="padding: 10px;text-align:center;border:1px solid #ddd;">Status</th>
            <th style="padding: 10px;text-align:center;border:1px solid #ddd;">Action</th>
        </tr>
        <?php
        $sno = 1;
        while ($row = mysqli_fetch_assoc($result)) {
        ?>
            <tr>
                <td style="padding: 10px;text-align:center;border:1px solid #ddd;color:black;"><?php echo $sno; ?></td>
                <td style="padding: 10px;text-align:center;border:1px solid #ddd;color:black;"><?php echo $row['id']; ?></td>
                <td style="padding: 10px;text-align:center;border:1px solid #ddd;color:black;"><?php echo $row['description']; ?></td>
                <td style="padding: 10px;text-align:center;border:1px solid #ddd;color:black;"><?php echo $row['start_date']; ?></td>
                <td style="padding: 10px;text-align:center;border:1px solid #ddd;color:black;"><?php echo $row['end_date']; ?></td>
                <td style="padding: 10px;text-align:center;border:1px solid #ddd;color:black;"><?php echo $row['status']; ?></td>
                <td style="padding: 10px;text-align:center;border:1px solid #ddd;color:black;"><a href="update_status.php?id=<?php echo $row['id']; ?>">Update</a></td>
            </tr>
        <?php
            $sno++;
        }
        mysqli_stmt_close($stmt);
        mysqli_close($connection);
        ?>
    </table>
</body>

</html>