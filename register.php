<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>OTMS | Registration Page</title>
    <!-- jQuery file -->
    <script src="includes/jquery_latest.js"></script>
    <!-- Bootstrap files -->
    <link rel="stylesheet" type="text/css" href="booystrap/css/bootstrap.min.css">
    <script src="bootstrap/js/bootstrap.min.js"></script>
    <!-- Externam CSS file -->
    <link rel="stylesheet" type="text/css" href="css/style.css">

</head>

<body>
    <div class="center-content">
        <div class="col-md-6 form-container">
            <center>
                <h1 class="text-center">REGISTRATION</h1>
            </center>
            <?php
            // Database connection settings
            $servername = "localhost";
            $username = "root"; // Replace with your MySQL username
            $password = ""; // Replace with your MySQL password
            $dbname = "otms";

            // Create connection
            $conn = new mysqli($servername, $username, $password, $dbname);

            // Check connection
            if ($conn->connect_error) {
                die("Connection failed: " . $conn->connect_error);
            }
            

            if ($_SERVER["REQUEST_METHOD"] == "POST") {

                $check_stmt = $conn->prepare("SELECT * FROM users WHERE username = ? OR email = ?");
                $check_stmt->bind_param("ss", $username, $email);

                // Set parameters and execute
                $username = $_POST['username'];
                $email = $_POST['email'];

                $check_stmt->execute();
                $result = $check_stmt->get_result();

                if ($result->num_rows > 0) {
                    // Username or email already exists
                    echo "Username or email already exists.";
                } else {
                // Prepare and bind parameters
                $stmt = $conn->prepare("INSERT INTO users (firstname, middlename, lastname, username, email, password, phone) VALUES (?, ?, ?, ?, ?, ?, ?)");
                $stmt->bind_param("sssssss", $firstname, $middlename, $lastname, $username, $email, $password, $phone);

                // Set parameters and execute
                $firstname = $_POST['firstname'];
                $middlename = $_POST['middlename'];
                $lastname = $_POST['lastname'];
                $username = $_POST['username'];
                $email = $_POST['email'];
                $password = $_POST['password'];
                $phone = $_POST['phone'];

                if ($stmt->execute()) {
                    header("Location: success.php");
                    exit();
                } else {
                    echo "Error: " . $stmt->error;
                }

                // Close statement
                $stmt->close();
            }
                $check_stmt->close();
            }

            // Close connection
            $conn->close();
            ?>
            <form action="" method="post">
                <div class="field input">
                    <label for="firstname">Firstname</label>
                    <input type="text" name="firstname" id="firstname" placeholder="Enter First Name" required>
                </div>

                <div class="field input">
                    <label for="middlename">Middlename</label>
                    <input type="text" name="middlename" id="middlename" autocomplete="off" placeholder="Enter Middle Name" required>
                </div>

                <div class="field input">
                    <label for="lastname">Lastname</label>
                    <input type="text" name="lastname" id="lastname" autocomplete="off" placeholder="Enter Last Name" required>
                </div>

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
                    <input type="text" name="phone" id="phone" autocomplete="off" placeholder="Enter Phone No." required>
                </div>

                <div class="form-group">
                    <center><input type="submit" name="userlogin" value="Register" class="btn btn-warning btn-block"></center>
                </div>
                <div class="text-center">
                    <p>Already have have an account? <a href="user_login.php">login</a></p>
                </div>
            </form>
            <center><a href="index.php" class="btn btn-danger btn-red btn-block">Go To Home</a></center>
        </div>
    </div>
</body>

</html>