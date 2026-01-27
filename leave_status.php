<?php
session_start();
require_once 'includes/config.php';

if (!isset($_SESSION['id'])) {
    echo '<p>Please <a href="user_login.php">log in</a>.</p>';
    exit;
}

$user_id = (int) $_SESSION['id'];
$stmt = $connection->prepare("SELECT id, subject, message, status, applied_at FROM leaves WHERE user_id = ? ORDER BY applied_at DESC, id DESC");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$leaves = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
$stmt->close();
?>
<div class="d-flex justify-content-between align-items-center mb-3">
    <h2 class="mb-0">Leave Status</h2>
    <a href="user_dashboard.php" class="otms-btn otms-btn-ghost" style="font-size:0.9rem;">← Dashboard</a>
</div>

<?php if (empty($leaves)): ?>
    <div class="otms-card">
        <p class="mb-0" style="color:var(--text-muted);">No leave applications yet.</p>
    </div>
<?php else: ?>
    <div class="otms-table-wrap">
        <table class="otms-table">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Subject</th>
                    <th>Message</th>
                    <th>Applied</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($leaves as $i => $l): ?>
                    <?php
                    $s = $l['status'] ?? '';
                    $badge = 'badge-pending';
                    if (stripos($s, 'Approved') !== false) $badge = 'badge-approved';
                    elseif (stripos($s, 'Reject') !== false) $badge = 'badge-rejected';
                    elseif (stripos($s, 'Action') !== false || stripos($s, 'Pending') !== false) $badge = 'badge-pending';
                    ?>
                    <tr>
                        <td><?= $i + 1 ?></td>
                        <td><?= htmlspecialchars($l['subject'] ?? '-') ?></td>
                        <td><?= htmlspecialchars($l['message'] ?? '-') ?></td>
                        <td><?= $l['applied_at'] ? date('d M Y', strtotime($l['applied_at'])) : '-' ?></td>
                        <td><span class="badge <?= $badge ?>"><?= htmlspecialchars($s) ?></span></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
<?php endif; ?>
