<?php
session_start(); 

$servername = "localhost";
$username = "root"; 
$password = ""; 
$dbname = "otms";


$conn = new mysqli($servername, $username, $password, $dbname);


if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    $email = $_POST['email'];
    $password = $_POST['password'];

    $query = "SELECT * FROM users WHERE email = ? AND password = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("ss", $email, $password);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        
        $row = $result->fetch_assoc();
        $_SESSION['id'] = $row['id'];
        $_SESSION['email'] = $row['email']; 
        $_SESSION['username'] = $row['username'];
        header("Location: user_dashboard.php");
        exit();
    } else {
       
        echo "<script>alert('Invalid email or password');</script>";
    }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>OTMS | Login Page</title>
    <!-- jQuery file -->
    <script src="includes/jquery_latest.js"></script>
    <!-- Bootstrap files -->
    <link rel="stylesheet" type="text/css" href="booystrap/css/bootstrap.min.css">
    <script src="bootstrap/js/bootstrap.min.js"></script>
    
    <link rel="stylesheet" type="text/css" href="css/style.css">

</head>

<body>
    <div class="center-content">
        <div class="col-md-6 form-container">
            <center>
                <h1 class="text-center">USER LOGIN</h1>
            </center>

            <form action="" method="post">
                <div class="form-group">
                    <div class="field input">
                        <label for="email">Email</label>
                        <input type="email" name="email" id="email" autocomplete="off" placeholder="Enter Email" required>
                    </div>
                    <div class="field input">
                        <label for="password">Password</label>
                        <input type="password" name="password" id="password" autocomplete="off" placeholder="Enter Password" required>
                    </div>
                    <div class="form-group">
                        <center><input type="submit" name="userlogin" value="Login" class="btn btn-warning btn-block"></center>
                    </div>
                    <div class="text-center">
                        <p>Don't have an account? <a href="register.php">Register</a></p>
                    </div>
            </form>
            <center><a href="index.php" class="btn btn-danger btn-red btn-block">Go To Home</a></center>
        </div>
    </div>
</body>

</html>