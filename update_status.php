<?php
include('includes/config.php');
if(isset($_POST['update'])){
    $query = "update tasks set status = '$_POST[status]' where id = $_GET[id]";
$query_run = mysqli_query($connection,$query);
if($query_run){
        echo "<script type='text/javascript'>
                alert('Status updated successfully...');
                window.location.href = 'user_dashboard.php';
              </script>";
    } 
    else {
        echo "<script type='text/javascript'>
                alert('Error...please try again!!!');
                window.location.href = 'user_dashboard.php';
              </script>";
    }


}     
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Update status</title>
    <script src="includes/jquery_latest.js"></script>
    <!-- Bootstrap files -->
    <link rel="stylesheet" type="text/css" href="bootstrap/css/bootstrap.min.css">
    <script src="bootstrap/js/bootstrap.min.js"></script>

    <style>
        body {
            margin: 0;
            font-family: Arial, sans-serif;
            background: url('/OTMS PROJECT/background/image5.gif') no-repeat center center fixed;
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
            width: 200%;
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
            width: 60%;

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
                <h3>Update the task</h3><br>
            </center>
            <div class="center-content">
                <?php
                $query = "select * from tasks where id = $_GET[id]";
                $query_run = mysqli_query($connection,$query);
                while($row = mysqli_fetch_assoc($query_run)){
                ?>
                    <form action="" method="post">
                        <div class="form-group">
                            <input type="hidden" name="id" class="form-control" value="<?php echo $row['id']; ?>" required>
                        </div>
                        <div class="form-group" center-content>
                            <select class="form-control" name="status">
                                <option>-select-</option>
                                <option>In-progress</option>
                                <option>Complete</option>
                            </select>
                        </div>
                        <input type="submit" class="btn btn-warning" name="update" value="Update">
                    </form>
                <?php
                }
                ?>

            </div>
        </div>
    </div>
</body>

</html>