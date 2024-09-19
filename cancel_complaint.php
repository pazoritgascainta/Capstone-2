<?php
session_start();

if (!isset($_SESSION['homeowner_id'])) {
    header("Location: usercomplaint.php");
    exit;
}

$servername = "localhost";
$username = "root";
$password = "";
$database = "homeowner";

// Create connection
$conn = new mysqli($servername, $username, $password, $database);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Check if complaint ID is passed via GET and validate it
if (!isset($_GET['id']) || empty($_GET['id']) || !is_numeric($_GET['id'])) {
    echo "Error: Invalid or missing complaint ID.";
    exit;
}

$complaint_id = intval($_GET['id']);

// Prepare and execute the DELETE query
$delete_sql = "DELETE FROM complaints WHERE complaint_id = ?";
$stmt = $conn->prepare($delete_sql);

if ($stmt === false) {
    echo "Error preparing statement: " . $conn->error;
    exit;
}

$stmt->bind_param("i", $complaint_id);

// Execute the query and check for success
if ($stmt->execute()) {
    // Success: Redirect back to the complaints view page
    header("Location: view_complaints.php?status=success");
    exit;
} else {
    echo "Error deleting complaint: " . $stmt->error;
}

$stmt->close();
$conn->close();
?>
