<?php
session_start();
require_once 'includes/config.php';

if (isset($_SESSION['id'])) {
    header('Location: user_dashboard.php');
    exit;
}

$error = '';
$success = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $firstname = trim($_POST['firstname'] ?? '');
    $middlename = trim($_POST['middlename'] ?? '');
    $lastname = trim($_POST['lastname'] ?? '');
    $username = trim($_POST['username'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $phone = trim($_POST['phone'] ?? '');

    if (empty($firstname) || empty($lastname) || empty($username) || empty($email) || empty($password) || empty($phone)) {
        $error = 'Please fill all required fields.';
    } else {
        $chk = $connection->prepare("SELECT id FROM users WHERE username = ? OR email = ?");
        $chk->bind_param("ss", $username, $email);
        $chk->execute();
        $r = $chk->get_result();
        if ($r->num_rows > 0) {
            $error = 'Username or email already exists.';
        } else {
            $ins = $connection->prepare("INSERT INTO users (firstname, middlename, lastname, username, email, password, phone) VALUES (?, ?, ?, ?, ?, ?, ?)");
            $ins->bind_param("sssssss", $firstname, $middlename, $lastname, $username, $email, $password, $phone);
            if ($ins->execute()) {
                header('Location: success.php');
                exit;
            }
            $error = 'Registration failed. Please try again.';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register – OTMS</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Sora:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <div class="otms-center">
        <div class="otms-card" style="max-width:480px;">
            <h1>Register</h1>
            <?php if ($error): ?>
                <div class="otms-alert otms-alert-danger"><?= htmlspecialchars($error) ?></div>
            <?php endif; ?>
            <form method="post" action="">
                <div class="row g-2">
                    <div class="col-md-6">
                        <div class="otms-field">
                            <label for="firstname">First name *</label>
                            <input type="text" name="firstname" id="firstname" required placeholder="First name" value="<?= htmlspecialchars($_POST['firstname'] ?? '') ?>">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="otms-field">
                            <label for="lastname">Last name *</label>
                            <input type="text" name="lastname" id="lastname" required placeholder="Last name" value="<?= htmlspecialchars($_POST['lastname'] ?? '') ?>">
                        </div>
                    </div>
                </div>
                <div class="otms-field">
                    <label for="middlename">Middle name (optional)</label>
                    <input type="text" name="middlename" id="middlename" placeholder="Middle name" value="<?= htmlspecialchars($_POST['middlename'] ?? '') ?>">
                </div>
                <div class="otms-field">
                    <label for="username">Username *</label>
                    <input type="text" name="username" id="username" required placeholder="Username" value="<?= htmlspecialchars($_POST['username'] ?? '') ?>">
                </div>
                <div class="otms-field">
                    <label for="email">Email *</label>
                    <input type="email" name="email" id="email" required placeholder="Email" value="<?= htmlspecialchars($_POST['email'] ?? '') ?>">
                </div>
                <div class="otms-field">
                    <label for="password">Password *</label>
                    <input type="password" name="password" id="password" required placeholder="Password">
                </div>
                <div class="otms-field">
                    <label for="phone">Phone *</label>
                    <input type="text" name="phone" id="phone" required placeholder="Phone" value="<?= htmlspecialchars($_POST['phone'] ?? '') ?>">
                </div>
                <button type="submit" class="otms-btn otms-btn-primary otms-btn-block">Register</button>
            </form>
            <p class="text-center mt-2 mb-0" style="color:var(--text-muted);font-size:0.9rem;">
                Already have an account? <a href="user_login.php">Login</a>
            </p>
            <div class="text-center mt-2">
                <a href="index.php" class="otms-btn otms-btn-ghost">Back to Home</a>
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
