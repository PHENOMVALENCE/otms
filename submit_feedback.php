<?php
session_start();
require_once 'includes/config.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: index.php');
    exit;
}

$type = trim($_POST['type'] ?? '');
$description = trim($_POST['description'] ?? '');
$priority = trim($_POST['priority'] ?? 'Medium');
$contact = trim($_POST['contact'] ?? '');

if (empty($type) || empty($description)) {
    $_SESSION['feedback_error'] = 'Type and description are required.';
    header('Location: index.php');
    exit;
}

$stmt = $connection->prepare("INSERT INTO feedback (type, description, priority, contact_info) VALUES (?, ?, ?, ?)");
$stmt->bind_param("ssss", $type, $description, $priority, $contact);

if ($stmt->execute()) {
    $_SESSION['feedback_success'] = 'Thank you for your feedback.';
} else {
    $_SESSION['feedback_error'] = 'Failed to submit feedback. Please try again.';
}
$stmt->close();

header('Location: index.php');
exit;
