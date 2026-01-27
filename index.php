<?php
session_start();
require_once 'includes/config.php';
$is_user = isset($_SESSION['id']) && isset($_SESSION['email']);
$is_admin = isset($_SESSION['admin_id']) && isset($_SESSION['admin_username']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>OTMS – Online Task Management System</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Sora:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <div class="otms-center">
        <div class="otms-container text-center">
            <img src="logo/logo33.png" alt="OTMS" class="mb-3" style="width:88px;height:auto;border-radius:16px;box-shadow:0 4px 20px rgba(0,0,0,0.08);">
            <div class="otms-hero">
                <h1 class="mb-2">Welcome to OTMS</h1>
                <p class="lead">
                    Whether you’re a <strong>team member</strong> managing tasks and leave, or an <strong>admin</strong> with full system control — you’re in the right place.
                </p>
            </div>

            <?php if (!empty($_SESSION['feedback_success'])): ?>
                <div class="otms-alert otms-alert-success mb-3"><?= htmlspecialchars($_SESSION['feedback_success']) ?></div>
                <?php unset($_SESSION['feedback_success']); ?>
            <?php endif; ?>
            <?php if (!empty($_SESSION['feedback_error'])): ?>
                <div class="otms-alert otms-alert-danger mb-3"><?= htmlspecialchars($_SESSION['feedback_error']) ?></div>
                <?php unset($_SESSION['feedback_error']); ?>
            <?php endif; ?>

            <div class="otms-role-grid">
                <a href="<?= $is_user ? 'user_dashboard.php' : 'user_login.php' ?>" class="otms-role-card">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                    </svg>
                    <?= $is_user ? 'Go to Dashboard' : 'User Login' ?>
                    <span class="sub">Team members: tasks & leave</span>
                </a>
                <?php if (!$is_user): ?>
                <a href="register.php" class="otms-role-card">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z" />
                    </svg>
                    Register
                    <span class="sub">Create your account</span>
                </a>
                <?php endif; ?>
                <a href="<?= $is_admin ? 'admin/admin_dashboard.php' : 'admin/admin_login.php' ?>" class="otms-role-card">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                    </svg>
                    <?= $is_admin ? 'Admin Dashboard' : 'Admin Login' ?>
                    <span class="sub">Full system control & privileges</span>
                </a>
            </div>

            <div class="otms-features">
                <div class="otms-feature">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4" /></svg>
                    <span>Tasks & progress</span>
                </div>
                <div class="otms-feature">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" /></svg>
                    <span>Leave management</span>
                </div>
                <div class="otms-feature">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" /></svg>
                    <span>Notifications</span>
                </div>
            </div>

            <div class="mt-4 d-flex flex-wrap justify-content-center gap-2">
                <button type="button" class="otms-btn otms-btn-ghost" data-bs-toggle="modal" data-bs-target="#contactModal">Contact</button>
                <button type="button" class="otms-btn otms-btn-ghost" data-bs-toggle="modal" data-bs-target="#feedbackModal">Feedback</button>
            </div>
        </div>
    </div>

    <div class="modal fade" id="contactModal" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Contact</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p class="mb-2"><strong>Email:</strong> Jessemallya@gmail.com</p>
                    <p class="mb-2"><strong>Phone:</strong> +255653294241</p>
                    <p class="mb-0"><strong>Instagram:</strong> _.firminjr03</p>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="feedbackModal" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Submit Feedback</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="feedbackForm" action="submit_feedback.php" method="post">
                    <div class="modal-body">
                        <div class="otms-field">
                            <label for="fbType">Type</label>
                            <select class="form-select" name="type" id="fbType" required>
                                <option value="Bug Report">Bug Report</option>
                                <option value="Feature Request">Feature Request</option>
                                <option value="General Feedback">General Feedback</option>
                            </select>
                        </div>
                        <div class="otms-field">
                            <label for="fbDesc">Description</label>
                            <textarea name="description" id="fbDesc" rows="3" required placeholder="Your feedback..."></textarea>
                        </div>
                        <div class="otms-field">
                            <label for="fbPriority">Priority</label>
                            <select class="form-select" name="priority" id="fbPriority">
                                <option value="Low">Low</option>
                                <option value="Medium">Medium</option>
                                <option value="High">High</option>
                            </select>
                        </div>
                        <div class="otms-field">
                            <label for="fbContact">Contact (optional)</label>
                            <input type="text" name="contact" id="fbContact" placeholder="Email or phone">
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="otms-btn otms-btn-ghost" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="otms-btn otms-btn-primary">Submit</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
