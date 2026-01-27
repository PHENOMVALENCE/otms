<?php
session_start();
require_once 'includes/config.php';

if (!isset($_SESSION['id'])) {
    header('Location: user_login.php');
    exit;
}

$user_id = (int) $_SESSION['id'];
$id = isset($_GET['id']) ? (int) $_GET['id'] : 0;

if ($id <= 0) {
    header('Location: user_dashboard.php');
    exit;
}

$stmt = $connection->prepare("SELECT t.id, t.title, t.description, t.status FROM tasks t INNER JOIN task_assignments a ON a.task_id = t.id AND a.user_id = ? WHERE t.id = ?");
$stmt->bind_param("ii", $user_id, $id);
$stmt->execute();
$task = $stmt->get_result()->fetch_assoc();
$stmt->close();

if (!$task) {
    header('Location: user_dashboard.php');
    exit;
}

$updated = false;
$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update'])) {
    $status = trim($_POST['status'] ?? '');
    $allowed = ['Not Started', 'In-progress', 'Complete'];
    if (!in_array($status, $allowed, true)) {
        $error = 'Invalid status.';
    } else {
        $up = $connection->prepare("UPDATE tasks SET status = ? WHERE id = ?");
        $up->bind_param("si", $status, $id);
        if ($up->execute()) {
            $updated = true;
        } else {
            $error = 'Update failed. Please try again.';
        }
        $up->close();
    }
}

if ($updated) {
    echo "<script>alert('Status updated successfully.'); window.location.href='user_dashboard.php';</script>";
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Update Task – OTMS</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Sora:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <div class="otms-center">
        <div class="otms-card">
            <h1>Update task status</h1>
            <p class="mb-3" style="color:var(--text-muted);"><?= htmlspecialchars($task['title'] ?: $task['description']) ?></p>
            <?php if ($error): ?>
                <div class="otms-alert otms-alert-danger"><?= htmlspecialchars($error) ?></div>
            <?php endif; ?>
            <form method="post" action="">
                <div class="otms-field">
                    <label for="status">Status</label>
                    <select name="status" id="status" class="form-select" required>
                        <option value="">— Select —</option>
                        <option value="Not Started" <?= ($task['status'] ?? '') === 'Not Started' ? 'selected' : '' ?>>Not Started</option>
                        <option value="In-progress" <?= ($task['status'] ?? '') === 'In-progress' ? 'selected' : '' ?>>In progress</option>
                        <option value="Complete" <?= ($task['status'] ?? '') === 'Complete' ? 'selected' : '' ?>>Complete</option>
                    </select>
                </div>
                <button type="submit" name="update" class="otms-btn otms-btn-primary">Update</button>
                <a href="user_dashboard.php" class="otms-btn otms-btn-ghost ms-2">Cancel</a>
            </form>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
