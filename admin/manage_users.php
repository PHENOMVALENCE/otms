<?php
session_start();
require_once __DIR__ . '/../includes/config.php';

if (!isset($_SESSION['admin_id'])) {
    echo '<p>Please <a href="admin_login.php">log in</a>.</p>';
    exit;
}

$users = [];
$r = $connection->query("SELECT id, firstname, lastname, username, email, phone FROM users ORDER BY username");
if ($r) $users = $r->fetch_all(MYSQLI_ASSOC);

$task_counts = [];
$tr = $connection->query("SELECT user_id, COUNT(*) c FROM task_assignments GROUP BY user_id");
if ($tr) while ($row = $tr->fetch_assoc()) $task_counts[(int)$row['user_id']] = (int) $row['c'];
?>
<div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-3">
    <h2 class="mb-0">Manage Users</h2>
    <a href="admin_dashboard.php" class="otms-btn otms-btn-ghost" style="font-size:0.9rem;">← Dashboard</a>
</div>
<p class="text-muted mb-3">View all registered users. Assign tasks from Create Task or Manage Tasks.</p>

<?php if (empty($users)): ?>
    <div class="otms-card">
        <p class="mb-0" style="color:var(--text-muted);">No users registered yet.</p>
    </div>
<?php else: ?>
    <div class="otms-table-wrap">
        <table class="otms-table">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Name</th>
                    <th>Username</th>
                    <th>Email</th>
                    <th>Phone</th>
                    <th>Tasks assigned</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($users as $i => $u): ?>
                    <tr>
                        <td><?= $i + 1 ?></td>
                        <td><?= htmlspecialchars(trim(($u['firstname'] ?? '') . ' ' . ($u['lastname'] ?? ''))) ?: '—' ?></td>
                        <td><?= htmlspecialchars($u['username'] ?? '—') ?></td>
                        <td><?= htmlspecialchars($u['email'] ?? '—') ?></td>
                        <td><?= htmlspecialchars($u['phone'] ?? '—') ?></td>
                        <td><?= $task_counts[(int)($u['id'])] ?? 0 ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
<?php endif; ?>
