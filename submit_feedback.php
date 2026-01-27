<?php
// Database configuration
$servername = "localhost";
$username = "root"; // Replace with your database username
$password = ""; // Replace with your database password
$dbname = "feedback_db"; // Replace with your database name

// Create a connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check the connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Get feedback data from the POST request
$type = $_POST['type'];
$description = $_POST['description'];
$priority = $_POST['priority'];
$contact_info = $_POST['contact'];

// Prepare and bind
$stmt = $conn->prepare("INSERT INTO feedback (type, description, priority, contact_info) VALUES (?, ?, ?, ?)");
$stmt->bind_param("ssss", $type, $description, $priority, $contact_info);

// Execute the statement
if ($stmt->execute()) {
    echo "Feedback submitted successfully";
} else {
    echo "Error: " . $stmt->error;
}

// Close the statement and connection
$stmt->close();
$conn->close();
?>