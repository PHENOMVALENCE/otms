<?php
session_start();
require_once __DIR__ . '/../includes/config.php';

if (!isset($_SESSION['admin_id'])) {
    echo '<p>Please <a href="admin_login.php">log in</a>.</p>';
    exit;
}

$users = [];
$r = $connection->query("SELECT id, username FROM users ORDER BY username");
if ($r) while ($row = $r->fetch_assoc()) $users[] = $row;
?>
<div class="d-flex justify-content-between align-items-center mb-3">
    <h2 class="mb-0">Create Task</h2>
    <a href="admin_dashboard.php" class="otms-btn otms-btn-ghost" style="font-size:0.9rem;">← Dashboard</a>
</div>

<div class="otms-card">
    <form method="post" action="admin_dashboard.php">
        <div class="otms-field">
            <label for="user_ids">Assign to user(s) *</label>
            <select name="user_ids[]" id="user_ids" class="form-select" multiple size="6">
                <?php foreach ($users as $u): ?>
                    <option value="<?= (int)$u['id'] ?>"><?= htmlspecialchars($u['username']) ?></option>
                <?php endforeach; ?>
            </select>
            <small class="text-muted">Hold Ctrl (Windows) or Cmd (Mac) to select multiple users.</small>
        </div>
        <div class="otms-field">
            <label for="title">Title (optional)</label>
            <input type="text" name="title" id="title" placeholder="e.g. Complete report">
        </div>
        <div class="otms-field">
            <label for="description">Description *</label>
            <textarea name="description" id="description" required placeholder="Task description..."></textarea>
        </div>
        <div class="row g-2">
            <div class="col-md-6">
                <div class="otms-field">
                    <label for="start_date">Start date</label>
                    <input type="date" name="start_date" id="start_date">
                </div>
            </div>
            <div class="col-md-6">
                <div class="otms-field">
                    <label for="end_date">End date</label>
                    <input type="date" name="end_date" id="end_date">
                </div>
            </div>
        </div>
        <div class="otms-field">
            <label for="priority">Priority</label>
            <select name="priority" id="priority" class="form-select">
                <option value="Low">Low</option>
                <option value="Medium" selected>Medium</option>
                <option value="High">High</option>
            </select>
        </div>
        <button type="submit" name="create_task" class="otms-btn otms-btn-primary">Create task</button>
    </form>
</div>
