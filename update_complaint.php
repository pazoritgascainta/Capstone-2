<?php
session_start();

// Check if admin is logged in
if (!isset($_SESSION['admin_logged_in'])) {
    header("Location: admin_login.php?error=not_logged_in");
    exit;
}

// Include database connection
include 'config.php';

// Check if 'id' parameter is present in URL
if (!isset($_GET['id'])) {
    // Redirect back to complaints list or handle error
    header("Location: admincomplaint.php?error=missing_id");
    exit;
}

// Get 'id' parameter from URL
$id = $_GET['id'];

// Fetch complaint details
$sql = "SELECT * FROM complaints WHERE complaint_id = '$id'";
$result = $conn->query($sql);

if ($result->num_rows == 1) {
    $complaint = $result->fetch_assoc();
} else {
    // Redirect or handle error if complaint not found
    header("Location: admincomplaint.php?error=complaint_not_found");
    exit;
}

// Close the result set
$result->close();

// Handle form submission to update complaint status
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['status'])) {
    $status = $_POST['status'];

    // Update complaint status
    $update_sql = "UPDATE complaints SET status = '$status' WHERE complaint_id = '$id'";

    if ($conn->query($update_sql) === TRUE) {
        // Successful update message
        $_SESSION['update_success'] = "Complaint status updated successfully.";
        header("Location: admincomplaint.php"); // Redirect back to complaints list
        exit;
    } else {
        // Error handling if update fails
        $_SESSION['update_error'] = "Error updating record: " . $conn->error;
        header("Location: admincomplaint.php?error=update_failed");
        exit;
    }
} else {
    // Handle case where status is not set in $_POST (shouldn't normally happen with proper form setup)
    $_SESSION['update_error'] = "Error: Status not provided.";
    header("Location: admincomplaint.php?error=status_not_provided");
    exit;
}

// Close database connection
$conn->close();
?>
