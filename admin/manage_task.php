<?php
include('../includes/config.php');
?>
<html>

<body>
    <style>
        th {
            color: black;
        }

        td {
            color: black;
        }

        h3 {
            color: white;
        }
    </style>
    <center>
        <b>
            <h3>ALL ASSIGNED TASKS</h3>
        </b><br>
    </center>
    <table class="table" style="background-color:rgba(233, 150, 122, 0.8); width: 75vw; height: 25vh; border-collapse: collapse; border: 2px solid #333;">
        <tr>
            <th style="padding: 10px;text-align:center;border:1px solid #ddd;color:white;">s.No</th>
            <th style="padding: 10px;text-align:center;border:1px solid #ddd;color:white;">Task ID</th>
            <th style="padding: 10px;text-align:center;border:1px solid #ddd;color:white;">Description</th>
            <th style="padding: 10px;text-align:center;border:1px solid #ddd;color:white;">Start Date</th>
            <th style="padding: 10px;text-align:center;border:1px solid #ddd;color:white;">End Date</th>
            <th style="padding: 10px;text-align:center;border:1px solid #ddd;color:white;">Status</th>
            <th style="padding: 10px;text-align:center;border:1px solid #ddd;color:white;">Action</th>
        </tr>
        <?php
        $sno = 1;
        $query = "select * from tasks";
        $query_run = mysqli_query($connection, $query);
        while ($row = mysqli_fetch_assoc($query_run)) {
        ?>
            <tr>
                <td style="padding: 10px;text-align:center;border:1px solid #ddd;color:black;"><?php echo $sno; ?></td>
                <td style="padding: 10px;text-align:center;border:1px solid #ddd;color:black;"><?php echo $row['id']; ?></td>
                <td style="padding: 10px;text-align:center;border:1px solid #ddd;color:black;"><?php echo $row['description']; ?></td>
                <td style="padding: 10px;text-align:center;border:1px solid #ddd;color:black;"><?php echo $row['start_date']; ?></td>
                <td style="padding: 10px;text-align:center;border:1px solid #ddd;color:black;"><?php echo $row['end_date']; ?></td>
                <td style="padding: 10px;text-align:center;border:1px solid #ddd;color:black;"><?php echo $row['status']; ?></td>
                <td style="padding: 10px;text-align:center;border:1px solid #ddd;color:black;"><a href="edit_task.php?id=<?php echo $row['id']; ?>">Edit</a> | <a href="delete_task.php?id=<?php echo $row['id']; ?>">Delete</a></td>
            </tr>
        <?php
            $sno = $sno + 1;
        }
        ?>
    </table>
</body>

</html>