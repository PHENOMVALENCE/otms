<?php
session_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registration Successful – OTMS</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Sora:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <div class="otms-center">
        <div class="otms-card">
            <div class="otms-alert otms-alert-success">
                <h4 class="mb-2">Registration successful</h4>
                <p class="mb-0">Your account has been created. You can now <a href="user_login.php">log in</a>.</p>
            </div>
            <div class="text-center">
                <a href="user_login.php" class="otms-btn otms-btn-primary">Go to Login</a>
                <a href="index.php" class="otms-btn otms-btn-ghost mt-1">Back to Home</a>
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
