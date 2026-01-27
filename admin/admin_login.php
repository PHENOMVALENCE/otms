<?php
session_start();
require_once __DIR__ . '/../includes/config.php';

if (isset($_SESSION['admin_id'])) {
    header('Location: admin_dashboard.php');
    exit;
}

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';

    if (empty($username) || empty($password)) {
        $error = 'Please enter username and password.';
    } else {
        $stmt = $connection->prepare("SELECT id, username, email FROM admins WHERE username = ? AND password = ?");
        $stmt->bind_param("ss", $username, $password);
        $stmt->execute();
        $res = $stmt->get_result();
        if ($res->num_rows === 0) {
            $error = 'Invalid username or password.';
        } else {
            $row = $res->fetch_assoc();
            $_SESSION['admin_id'] = (int) $row['id'];
            $_SESSION['admin_username'] = $row['username'];
            $_SESSION['admin_email'] = $row['email'];
            header('Location: admin_dashboard.php');
            exit;
        }
        $stmt->close();
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login – OTMS</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Sora:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>
    <div class="otms-center">
        <div class="otms-card">
            <h1>Admin Login</h1>
            <?php if ($error): ?>
                <div class="otms-alert otms-alert-danger"><?= htmlspecialchars($error) ?></div>
            <?php endif; ?>
            <form method="post" action="">
                <div class="otms-field">
                    <label for="username">Username</label>
                    <input type="text" name="username" id="username" required placeholder="Enter your username" value="<?= htmlspecialchars($_POST['username'] ?? '') ?>" autocomplete="username">
                </div>
                <div class="otms-field">
                    <label for="password">Password</label>
                    <input type="password" name="password" id="password" required placeholder="Enter password" autocomplete="current-password">
                </div>
                <button type="submit" class="otms-btn otms-btn-primary otms-btn-block">Login</button>
            </form>
            <div class="text-center mt-2">
                <a href="../index.php" class="otms-btn otms-btn-ghost">Back to Home</a>
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
