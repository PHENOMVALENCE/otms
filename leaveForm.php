<?php
include('includes/config.php');
session_start();

if (isset($_POST['submit_leave'])) {
    $subject = mysqli_real_escape_string($connection, $_POST['subject']);
    $message = mysqli_real_escape_string($connection, $_POST['message']);
    $user_id = $_SESSION['user_id'];
    $username = $_SESSION['username'];

    if (!empty($subject) && !empty($message)) {
        $query = "INSERT INTO leaves (user_id, subject, message, status, applied_at) 
                  VALUES ('$user_id', '$subject', '$message', 'Pending', NOW())";
        $run = mysqli_query($connection, $query);

        if ($run) {
            // Insert a notification for admin
            $notif = "Leave application submitted by $username.";
            $notif_query = "INSERT INTO notifications (user_id, user_role, message, status, created_at)
                            VALUES ('$user_id', 'admin', '$notif', 'unread', NOW())";
            mysqli_query($connection, $notif_query);

            echo "<script>alert('Leave submitted successfully.');</script>";
        } else {
            echo "Error: " . mysqli_error($connection);
        }
    } else {
        echo "All fields are required.";
    }
}
?>


<html>

<head>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 20px;
        }

        h2 {
            margin-bottom: 20px;
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
            width: 50%;
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
            width: 10%;

        }

        .btn-warning:hover {
            background-color: gold;
        }

        h3 {
            color: white;
        }

        .form-group label {
            color: lightskyblue;
        }
    </style>
</head>

<body>
    <b>
        <h3>APPLY LEAVE</h3><br>
    </b>
    <div class="row">
        <div class="col-md-6">
            <form action="" method="post">
                <div class="form-group">
                    <label for="subject">Subject</label>
                    <input type="text" class="form-group" name="subject" placeholder="leave Subject goes here.....">
                </div>
                <div class="form-group">
                    <label for="message">Message</label>
                    <textarea id="message" row="3" cols="40" name="message" placeholder="Type Message...">
                    </textarea>
                </div>
                <input type="submit" class="btn btn-warning" name="submit_leave" value="submit">

            </form>
        </div>
    </div>
</body>

</html>