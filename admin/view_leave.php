<?php
include('../includes/config.php');
?>
<html>

<body>
    <center>
        <b>
            <h3>ALL LEAVE APPLICATIONS</h3>
        </b>
    </center><br>
    <table class="table" style="background-color:rgba(233, 150, 122, 0.8); width: 75vw; height: 25vh; border-collapse: collapse; border: 2px solid #333;">
        <tr>
            <th style="padding: 10px;text-align:center;border:1px solid #ddd;">S.No</th>
            <th style="padding: 10px;text-align:center;border:1px solid #ddd;">User</th>
            <th style="padding: 10px;text-align:center;border:1px solid #ddd;">Subject</th>
            <th style="padding: 10px;text-align:center;border:1px solid #ddd;width:40%;">Message</th>
            <th style=" padding: 10px;text-align:center;border:1px solid #ddd;">Status</th>
            <th style="padding: 10px;text-align:center;border:1px solid #ddd;">Action</th>
        </tr>
        <?php
        $sno = 1;
        $query = "SELECT * FROM leaves";
        $query_run = mysqli_query($connection, $query);
        while ($row = mysqli_fetch_assoc($query_run)) {
        ?>
            <tr>
                <td style="padding: 10px;text-align:center;border:1px solid #ddd;color:black;"><?php echo $sno; ?></td>
                <?php
                $user_id = $row['user_id'];
                $query1 = "SELECT username FROM users WHERE id = ?";
                $stmt1 = $connection->prepare($query1);
                $stmt1->bind_param("i", $user_id);
                $stmt1->execute();
                $result1 = $stmt1->get_result();
                if ($row1 = $result1->fetch_assoc()) {
                ?>
                    <td style="padding: 10px;text-align:center;border:1px solid #ddd;color:black;"><?php echo htmlspecialchars($row1['username']); ?></td>
                <?php
                } else {
                ?>
                    <td style="padding: 10px;text-align:center;border:1px solid #ddd;color:black;">Unknown User</td>
                <?php
                }
                $stmt1->close();
                ?>
                <td style="padding: 10px;text-align:center;border:1px solid #ddd;color:black;"><?php echo htmlspecialchars($row['subject']); ?></td>
                <td style="padding: 10px;text-align:center;border:1px solid #ddd;color:black;"><?php echo htmlspecialchars($row['message']); ?></td>
                <td style="padding: 10px;text-align:center;border:1px solid #ddd;color:black;"><?php echo htmlspecialchars($row['status']); ?></td>
                <td style="padding: 10px;text-align:center;border:1px solid #ddd;color:black;"><a a href="approve_leave.php?id=<?php echo $row['id']; ?>">Approve</a> | <a href="reject_leave.php?id=<?php echo $row['id']; ?>">Reject</a></td>
            </tr>
        <?php
            $sno++;
        }
        ?>
    </table>
</body>

</html>