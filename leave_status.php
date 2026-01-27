<?php
session_start();
include('includes/config.php');
?>
<html>

<head>
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
        <b>
            <h3>YOUR LEAVE APPLICATION STATUS</h3>
        </b>
    </center>
    <table class="table" style="background-color:rgba(233, 150, 122, 0.8); width: 75vw; height: 25vh; border-collapse: collapse; border: 2px solid #333;">
        <tr>
            <th style="padding: 10px;text-align:center;border:1px solid #ddd;">s.No</th>
            <th style="padding: 10px;text-align:center;border:1px solid #ddd;">Subject</th>
            <th style="padding: 10px;text-align:center;border:1px solid #ddd;">Message</th>
            <th style="padding: 10px;text-align:center;border:1px solid #ddd;">Status</th>
        </tr>
        <?php
        $sno = 1;
        $query = "select * from leaves where user_id = $_SESSION[id]";
        $query_run = mysqli_query($connection, $query);
        while ($row = mysqli_fetch_assoc($query_run)) {
        ?>
            <tr>
                <td style="padding: 10px;text-align:center;border:1px solid #ddd;color:black;"><?php echo $sno; ?></td>
                <td style="padding: 10px;text-align:center;border:1px solid #ddd;color:black;"><?php echo $row['subject']; ?></td>
                <td style="padding: 10px;text-align:center;border:1px solid #ddd;color:black;"><?php echo $row['message']; ?></td>
                <td style="padding: 10px;text-align:center;border:1px solid #ddd;color:black;"><?php echo $row['status']; ?></td>
            </tr>
        <?php
            $sno = $sno + 1;
        }
        ?>

    </table>

</html>