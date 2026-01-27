<?php
session_start();
require_once __DIR__ . '/../includes/config.php';

if (!isset($_SESSION['admin_id'])) {
    echo '<p>Please <a href="admin_login.php">log in</a>.</p>';
    exit;
}

$tasks = [];
$r = $connection->query("
    SELECT t.id, t.title, t.description, t.start_date, t.end_date, t.status, t.priority,
           GROUP_CONCAT(u.username ORDER BY u.username SEPARATOR ', ') AS usernames
    FROM tasks t
    LEFT JOIN task_assignments a ON a.task_id = t.id
    LEFT JOIN users u ON u.id = a.user_id
    GROUP BY t.id, t.title, t.description, t.start_date, t.end_date, t.status, t.priority
    ORDER BY t.end_date ASC, t.id DESC
");
if ($r) $tasks = $r->fetch_all(MYSQLI_ASSOC);
?>
<div class="d-flex justify-content-between align-items-center mb-3">
    <h2 class="mb-0">Manage Tasks</h2>
    <a href="admin_dashboard.php" class="otms-btn otms-btn-ghost" style="font-size:0.9rem;">← Dashboard</a>
</div>

<?php if (empty($tasks)): ?>
    <div class="otms-card">
        <p class="mb-0" style="color:var(--text-muted);">No tasks yet.</p>
    </div>
<?php else: ?>
    <div class="otms-table-wrap">
        <table class="otms-table">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Task</th>
                    <th>Assigned to</th>
                    <th>Start</th>
                    <th>End</th>
                    <th>Priority</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($tasks as $i => $t): ?>
                    <?php
                    $status = $t['status'] ?? 'Not Started';
                    $badge = 'badge-not-started';
                    if (stripos($status, 'Complete') !== false) $badge = 'badge-complete';
                    elseif (stripos($status, 'progress') !== false) $badge = 'badge-in-progress';
                    ?>
                    <tr>
                        <td><?= $i + 1 ?></td>
                        <td>
                            <?= htmlspecialchars($t['title'] ?: $t['description']) ?>
                            <?php if (!empty($t['title']) && !empty($t['description'])): ?>
                                <br><small style="color:var(--text-muted);"><?= htmlspecialchars(mb_strlen($t['description']) > 60 ? mb_substr($t['description'], 0, 60) . '…' : $t['description']) ?></small>
                            <?php endif; ?>
                        </td>
                        <td><?= htmlspecialchars($t['usernames'] ?? '—') ?></td>
                        <td><?= htmlspecialchars($t['start_date'] ?? '-') ?></td>
                        <td><?= htmlspecialchars($t['end_date'] ?? '-') ?></td>
                        <td><?= htmlspecialchars($t['priority'] ?? 'Medium') ?></td>
                        <td><span class="badge <?= $badge ?>"><?= htmlspecialchars($status) ?></span></td>
                        <td>
                            <a href="edit_task.php?id=<?= (int)$t['id'] ?>" class="otms-btn otms-btn-primary" style="padding:0.35rem 0.75rem;font-size:0.85rem;">Edit</a>
                            <a href="delete_task.php?id=<?= (int)$t['id'] ?>" class="otms-btn otms-btn-danger" style="padding:0.35rem 0.75rem;font-size:0.85rem;" onclick="return confirm('Delete this task?');">Delete</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
<?php endif; ?>
