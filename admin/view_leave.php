<?php
session_start();
require_once __DIR__ . '/../includes/config.php';

if (!isset($_SESSION['admin_id'])) {
    echo '<p>Please <a href="admin_login.php">log in</a>.</p>';
    exit;
}

$leaves = [];
$r = $connection->query("SELECT l.id, l.user_id, l.subject, l.message, l.status, l.applied_at, u.username FROM leaves l LEFT JOIN users u ON l.user_id = u.id ORDER BY l.applied_at DESC, l.id DESC");
if ($r) $leaves = $r->fetch_all(MYSQLI_ASSOC);
?>
<div class="d-flex justify-content-between align-items-center mb-3">
    <h2 class="mb-0">Leave Applications</h2>
    <a href="admin_dashboard.php" class="otms-btn otms-btn-ghost" style="font-size:0.9rem;">← Dashboard</a>
</div>

<?php if (empty($leaves)): ?>
    <div class="otms-card">
        <p class="mb-0" style="color:var(--text-muted);">No leave applications.</p>
    </div>
<?php else: ?>
    <div class="otms-table-wrap">
        <table class="otms-table">
            <thead>
                <tr>
                    <th>#</th>
                    <th>User</th>
                    <th>Subject</th>
                    <th>Message</th>
                    <th>Applied</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($leaves as $i => $l): ?>
                    <?php
                    $s = $l['status'] ?? '';
                    $badge = 'badge-pending';
                    if (stripos($s, 'Approved') !== false) $badge = 'badge-approved';
                    elseif (stripos($s, 'Reject') !== false) $badge = 'badge-rejected';
                    else $badge = 'badge-pending';
                    ?>
                    <tr>
                        <td><?= $i + 1 ?></td>
                        <td><?= htmlspecialchars($l['username'] ?? '—') ?></td>
                        <td><?= htmlspecialchars($l['subject'] ?? '-') ?></td>
                        <td><?= htmlspecialchars($l['message'] ?? '-') ?></td>
                        <td><?= !empty($l['applied_at']) ? date('d M Y', strtotime($l['applied_at'])) : '-' ?></td>
                        <td><span class="badge <?= $badge ?>"><?= htmlspecialchars($s) ?></span></td>
                        <td>
                            <?php if (stripos($s, 'Approved') === false && stripos($s, 'Rejected') === false): ?>
                                <a href="approve_leave.php?id=<?= (int)$l['id'] ?>" class="otms-btn otms-btn-success" style="padding:0.35rem 0.75rem;font-size:0.85rem;">Approve</a>
                                <a href="reject_leave.php?id=<?= (int)$l['id'] ?>" class="otms-btn otms-btn-danger" style="padding:0.35rem 0.75rem;font-size:0.85rem;">Reject</a>
                            <?php else: ?>
                                —
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
<?php endif; ?>
