<?php

session_start();


include('../includes/config.php');
function verify_admin_credentials($username, $email, $password, $phone)
{
    return true;
}
if (isset($_POST['adminlogin'])) {

    $username = $_POST['username'];
    $email = $_POST['email'];
    $password = $_POST['password'];
    $phone = $_POST['phone'];


    if (verify_admin_credentials($username, $email, $password, $phone)) {

        $_SESSION['admin_username'] = $username;
        $_SESSION['admin_email'] = $email;
        header("Location: admin_dashboard.php");
        exit();
    } else {

        echo "Invalid credentials. Please try again.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>OTMS | Admin Page</title>
    <!-- jQuery file -->
    <script src="../includes/jquery_latest.js"></script>
    <!-- Bootstrap files -->
    <link rel="stylesheet" type="text/css" href="../bootstrap/css/bootstrap.min.css">
    <script src="../bootstrap/js/bootstrap.min.js"></script>

    <link rel="stylesheet" type="text/css" href="../css/style.css">

</head>

<body>
    <div class="center-content">
        <div class="col-md-6 form-container">
            <center>
                <h1 class="text-center">ADMIN LOGIN</h1>
            </center>
            <form action="" method="post">
                <div class="field input">
                    <label for="username">Username</label>
                    <input type="text" name="username" id="username" autocomplete="off" placeholder="Enter User Name" required>
                </div>


                <div class="field input">
                    <label for="email">Email</label>
                    <input type="email" name="email" id="email" autocomplete="off" placeholder="Enter Email" required>
                </div>


                <div class="field input">
                    <label for="password">Password</label>
                    <input type="password" name="password" id="password" autocomplete="off" placeholder="Enter Password" required>
                </div>

                <div class="field input">
                    <label for="phone">Phone Number</label>
                    <input type="text" name="phone" id="phone" autocomplete="off" placeholder="Enter Phone No." required><br>
                </div>
                <div class="form-group">
                    <center><input type="submit" name="adminlogin" value="Login" class="btn btn-warning btn-block"></center><br><br>
                </div>
                <div class="text-center">
                <p>Don't have an account? <a href="admin_register.php">Register</a></p>
        </div>

        </form>
        <center><a href="../index.php" class="btn btn-danger btn-red btn-block">Go To Home</a></center>
    </div>
    </div>
</body>

</html>