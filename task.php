<?php
session_start();
require_once 'includes/config.php';

if (!isset($_SESSION['id'])) {
    echo '<p>Please <a href="user_login.php">log in</a>.</p>';
    exit;
}

$user_id = (int) $_SESSION['id'];
$stmt = $connection->prepare("
    SELECT t.id, t.title, t.description, t.start_date, t.end_date, t.status, t.priority
    FROM tasks t
    INNER JOIN task_assignments a ON a.task_id = t.id AND a.user_id = ?
    ORDER BY t.end_date ASC, t.id DESC
");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$tasks = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
$stmt->close();
?>
<div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-3">
    <h2 class="mb-0">My Tasks</h2>
    <a href="user_dashboard.php" class="otms-btn otms-btn-ghost" style="font-size:0.9rem;">← Dashboard</a>
</div>

<?php if (empty($tasks)): ?>
    <div class="otms-card">
        <p class="mb-0" style="color:var(--text-muted);">No tasks assigned yet.</p>
    </div>
<?php else: ?>
    <div class="otms-filters mb-3" id="taskFilters">
        <button type="button" class="otms-btn otms-btn-ghost active" data-filter="all">All</button>
        <button type="button" class="otms-btn otms-btn-ghost" data-filter="Not Started">Not started</button>
        <button type="button" class="otms-btn otms-btn-ghost" data-filter="In-progress">In progress</button>
        <button type="button" class="otms-btn otms-btn-ghost" data-filter="Complete">Complete</button>
    </div>
    <div class="otms-table-wrap">
        <table class="otms-table" id="taskTable">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Task</th>
                    <th>Start</th>
                    <th>End</th>
                    <th>Priority</th>
                    <th>Status</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($tasks as $i => $t): ?>
                    <?php
                    $status = $t['status'] ?? 'Not Started';
                    $badge = 'badge-not-started';
                    if (strpos($status, 'Complete') !== false) $badge = 'badge-complete';
                    elseif (strpos($status, 'progress') !== false || $status === 'In-progress') $badge = 'badge-in-progress';
                    $pri = $t['priority'] ?? 'Medium';
                    $priClass = $pri === 'High' ? 'badge-high' : ($pri === 'Low' ? 'badge-low' : '');
                    ?>
                    <tr data-status="<?= htmlspecialchars($status) ?>">
                        <td><?= $i + 1 ?></td>
                        <td>
                            <span class="title"><?= htmlspecialchars($t['title'] ?: $t['description']) ?></span>
                            <?php if (!empty($t['title']) && !empty($t['description'])): ?>
                                <br><small style="color:var(--text-muted);"><?= htmlspecialchars(mb_strlen($t['description']) > 80 ? mb_substr($t['description'], 0, 80) . '…' : $t['description']) ?></small>
                            <?php endif; ?>
                        </td>
                        <td><?= htmlspecialchars($t['start_date'] ?? '-') ?></td>
                        <td><?= htmlspecialchars($t['end_date'] ?? '-') ?></td>
                        <td><span class="badge <?= $priClass ?>"><?= htmlspecialchars($pri) ?></span></td>
                        <td><span class="badge <?= $badge ?>"><?= htmlspecialchars($status) ?></span></td>
                        <td><a href="update_status.php?id=<?= (int)$t['id'] ?>" class="otms-btn otms-btn-primary" style="padding:0.35rem 0.75rem;font-size:0.85rem;">Update</a></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    <script>
        (function() {
            var t = document.getElementById('taskTable');
            if (!t) return;
            var rows = t.querySelectorAll('tbody tr');
            var btns = document.querySelectorAll('#taskFilters [data-filter]');
            btns.forEach(function(b) {
                b.addEventListener('click', function() {
                    btns.forEach(function(x) { x.classList.remove('active'); });
                    b.classList.add('active');
                    var f = b.getAttribute('data-filter');
                    rows.forEach(function(r) {
                        var s = r.getAttribute('data-status');
                        var show = f === 'all' || s === f;
                        r.style.display = show ? '' : 'none';
                    });
                });
            });
        })();
    </script>
<?php endif; ?>
