<?php
session_start();
require_once 'includes/config.php';

if (!isset($_SESSION['id'])) {
    echo '<p>Please <a href="user_login.php">log in</a>.</p>';
    exit;
}
?>
<div class="d-flex justify-content-between align-items-center mb-3">
    <h2 class="mb-0">Apply for Leave</h2>
    <a href="user_dashboard.php" class="otms-btn otms-btn-ghost" style="font-size:0.9rem;">← Dashboard</a>
</div>

<div class="otms-card">
    <form method="post" action="user_dashboard.php">
        <div class="otms-field">
            <label for="subject">Subject *</label>
            <input type="text" name="subject" id="subject" required placeholder="e.g. Sick leave">
        </div>
        <div class="otms-field">
            <label for="message">Message *</label>
            <textarea name="message" id="message" required placeholder="Describe your leave request..."></textarea>
        </div>
        <button type="submit" name="submit_leave" class="otms-btn otms-btn-primary">Submit</button>
    </form>
</div>
