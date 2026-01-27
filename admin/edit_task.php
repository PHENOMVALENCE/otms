<?php
include('../includes/config.php');

if (isset($_POST['edit_task'])) {
    $id = mysqli_real_escape_string($connection, $_POST['id']);
    $description = mysqli_real_escape_string($connection, $_POST['description']);
    $start_date = mysqli_real_escape_string($connection, $_POST['start_date']);
    $end_date = mysqli_real_escape_string($connection, $_POST['end_date']);


    $query = "UPDATE tasks SET description = '$description', start_date = '$start_date', end_date = '$end_date' WHERE id = '$id'";
    $query_run = mysqli_query($connection, $query);

    if ($query_run) {
        echo "<script type='text/javascript'>
                alert('Task updated successfully...');
                window.location.href = 'admin_dashboard.php';
              </script>";
    } else {
        echo "<script type='text/javascript'>
                alert('Error...please try again!!!');
                window.location.href = 'admin_dashboard.php';
              </script>";
    }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ETMS</title>
    <script src="../includes/jquery_latest.js"></script>
    <!-- Bootstrap files -->
    <link rel="stylesheet" type="text/css" href="../bootstrap/css/bootstrap.min.css">
    <script src="../bootstrap/js/bootstrap.min.js"></script>

    <style>
        body {
            margin: 0;
            font-family: Arial, sans-serif;
            background: url('/OTMS PROJECT/background/sad.gif') no-repeat center center fixed;
            background-size: cover;
            background-color: #355764;
        }

        #header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 10px 20px;
            background-color: darksalmon;
            border-bottom: 1px solid #dee2e6;
        }

        .form-group {
            margin-bottom: 15px;
        }

        label {
            display: block;
            margin-bottom: 5px;
        }

        input,
        select,
        textarea {
            width: 110%;
            padding: 8px;
            box-sizing: border-box;
        }

        .btn-warning {
            background-color: yellow;
            border: none;
            color: black;
            padding: 5px 10px;
            text-align: center;
            text-decoration: none;
            display: inline-block;
            font-size: 12px;
            margin: 4px 2px;
            cursor: pointer;
            border-radius: 4px;
            width: 30%;

        }

        .btn-warning:hover {
            background-color: gold;
        }

        .center-content {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            height: 100%;
        }

        h3 {
            color: salmon;
        }

        .form-group label {
            color: salmon;
        }
    </style>
</head>

<body>
    <!-- Header code starts here -->
    <div class="row" id="header">
        <div class="col-md-12">
            <h2><i class="fa fa-solid fa-list" style="padding-right: 15px;"></i>Online Task Management System</h2>
        </div>
    </div>
    <div class="row">
        <div class="col-md-4 m-auto" style="color:white;"><br>
            <center>
                <h3>Edit the task</h3><br>
            </center>
            <div class="center-content">
                <?php
                $query = "select * from tasks where id = $_GET[id]";
                $query_run = mysqli_query($connection, $query);
                while ($row = mysqli_fetch_assoc($query_run)) {
                ?>
                    <form action="" method="post">
                        <div class="form-group">
                            <input type="hidden" name="id" class="form-control" value="<?php echo $row['id']; ?>" required>
                        </div>
                        <div class="form-group" center-content>
                            <label>Select user:</label>
                            <select class="form-control" name="user_id" required>
                                <option>-select-</option>
                                <?php

                                $query1 = "SELECT id, username FROM users";
                                $query_run1 = mysqli_query($connection, $query1);
                                if (mysqli_num_rows($query_run1)) {
                                    while ($row1 = mysqli_fetch_assoc($query_run1)) {
                                ?>
                                        <option value="<?php echo $row1['id']; ?>"><?php echo $row1['username']; ?></option>
                                <?php
                                    }
                                }
                                ?>
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="description">Description</label>
                            <textarea class="form-control" id="description" name="description" rows="3" cols="40" required><?php echo $row['description']; ?></textarea>
                        </div>
                        <div class="form-group">
                            <label for="start_date">Start Date</label>
                            <input type="date" class="form-control" id="start_date" name="start_date" value="<?php echo $row['start_date']; ?>" required>
                        </div>

                        <div class="form-group">
                            <label for="end-date">End Date</label>
                            <input type="date" class="form-control" id="end_date" name="end_date" value="<?php echo $row['end_date']; ?>" required>
                        </div>
                        <input type="submit" class="btn btn-warning" name="edit_task" value="Update">
                    </form>
                <?php
                }
                ?>

            </div>
        </div>
    </div>
</body>

</html>