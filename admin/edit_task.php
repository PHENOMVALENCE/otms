<?php
session_start();
require_once __DIR__ . '/../includes/config.php';

if (!isset($_SESSION['admin_id'])) {
    header('Location: admin_login.php');
    exit;
}

$id = isset($_GET['id']) ? (int) $_GET['id'] : 0;
if ($id <= 0) {
    header('Location: admin_dashboard.php');
    exit;
}

$stmt = $connection->prepare("SELECT id, title, description, start_date, end_date, status, priority FROM tasks WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$task = $stmt->get_result()->fetch_assoc();
$stmt->close();

if (!$task) {
    header('Location: admin_dashboard.php');
    exit;
}

$assigned_ids = [];
$ar = $connection->prepare("SELECT user_id FROM task_assignments WHERE task_id = ?");
$ar->bind_param("i", $id);
$ar->execute();
$res = $ar->get_result();
while ($row = $res->fetch_assoc()) $assigned_ids[] = (int) $row['user_id'];
$ar->close();

$error = '';
$updated = false;
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['edit_task'])) {
    $user_ids = isset($_POST['user_ids']) && is_array($_POST['user_ids']) ? $_POST['user_ids'] : [];
    $user_ids = array_unique(array_filter(array_map('intval', $user_ids)));
    $title = trim($_POST['title'] ?? '');
    $description = trim($_POST['description'] ?? '');
    $start_date = trim($_POST['start_date'] ?? '');
    $end_date = trim($_POST['end_date'] ?? '');
    $priority = trim($_POST['priority'] ?? 'Medium');

    if (count($user_ids) === 0 || $description === '') {
        $error = 'Select at least one user and enter a description.';
    } else {
        $up = $connection->prepare("UPDATE tasks SET title = ?, description = ?, start_date = NULLIF(?, ''), end_date = NULLIF(?, ''), priority = ? WHERE id = ?");
        $up->bind_param("sssssi", $title, $description, $start_date, $end_date, $priority, $id);
        if ($up->execute()) {
            $up->close();
            $del = $connection->prepare("DELETE FROM task_assignments WHERE task_id = ?");
            $del->bind_param("i", $id);
            $del->execute();
            $del->close();
            $ins = $connection->prepare("INSERT INTO task_assignments (task_id, user_id) VALUES (?, ?)");
            foreach ($user_ids as $uid) {
                if ($uid > 0) {
                    $ins->bind_param("ii", $id, $uid);
                    $ins->execute();
                }
            }
            $ins->close();
            $updated = true;
        } else {
            $error = 'Update failed. Please try again.';
            $up->close();
        }
    }
}

if ($updated) {
    echo "<script>alert('Task updated successfully.'); window.location.href='admin_dashboard.php';</script>";
    exit;
}

$users = [];
$r = $connection->query("SELECT id, username FROM users ORDER BY username");
if ($r) while ($row = $r->fetch_assoc()) $users[] = $row;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Task – OTMS</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Sora:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>
    <div class="otms-center">
        <div class="otms-card" style="max-width:520px;">
            <h1>Edit task</h1>
            <?php if ($error): ?>
                <div class="otms-alert otms-alert-danger"><?= htmlspecialchars($error) ?></div>
            <?php endif; ?>
            <form method="post" action="">
                <input type="hidden" name="id" value="<?= (int)$task['id'] ?>">
                <div class="otms-field">
                    <label for="user_ids">Assign to user(s) *</label>
                    <select name="user_ids[]" id="user_ids" class="form-select" multiple size="6">
                        <?php foreach ($users as $u): ?>
                            <option value="<?= (int)$u['id'] ?>" <?= in_array((int)$u['id'], $assigned_ids, true) ? 'selected' : '' ?>><?= htmlspecialchars($u['username']) ?></option>
                        <?php endforeach; ?>
                    </select>
                    <small class="text-muted">Hold Ctrl (Windows) or Cmd (Mac) to select multiple users.</small>
                </div>
                <div class="otms-field">
                    <label for="title">Title (optional)</label>
                    <input type="text" name="title" id="title" placeholder="Title" value="<?= htmlspecialchars($task['title'] ?? '') ?>">
                </div>
                <div class="otms-field">
                    <label for="description">Description *</label>
                    <textarea name="description" id="description" required><?= htmlspecialchars($task['description'] ?? '') ?></textarea>
                </div>
                <div class="row g-2">
                    <div class="col-md-6">
                        <div class="otms-field">
                            <label for="start_date">Start date</label>
                            <input type="date" name="start_date" id="start_date" value="<?= htmlspecialchars($task['start_date'] ?? '') ?>">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="otms-field">
                            <label for="end_date">End date</label>
                            <input type="date" name="end_date" id="end_date" value="<?= htmlspecialchars($task['end_date'] ?? '') ?>">
                        </div>
                    </div>
                </div>
                <div class="otms-field">
                    <label for="priority">Priority</label>
                    <select name="priority" id="priority" class="form-select">
                        <option value="Low" <?= ($task['priority'] ?? '') === 'Low' ? 'selected' : '' ?>>Low</option>
                        <option value="Medium" <?= ($task['priority'] ?? 'Medium') === 'Medium' ? 'selected' : '' ?>>Medium</option>
                        <option value="High" <?= ($task['priority'] ?? '') === 'High' ? 'selected' : '' ?>>High</option>
                    </select>
                </div>
                <button type="submit" name="edit_task" class="otms-btn otms-btn-primary">Update</button>
                <a href="admin_dashboard.php" class="otms-btn otms-btn-ghost ms-2">Cancel</a>
            </form>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
