<?php
session_name('admin_session'); // Set a unique session name for admins
session_start();


// Include database connection
$servername = "localhost";
$username = "root";
$password = "";
$database = "homeowner";

$conn = new mysqli($servername, $username, $password, $database);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Check if 'id' parameter is present in URL
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header("Location: admincomplaint.php?error=missing_id");
    exit;
}

// Get 'id' parameter from URL and sanitize it
$id = intval($_GET['id']);

// Fetch complaint details using prepared statements
$sql = "SELECT * FROM complaints WHERE complaint_id = ?";
$stmt = $conn->prepare($sql);
if ($stmt === false) {
    die("Error preparing statement: " . $conn->error);
}
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows == 1) {
    $complaint = $result->fetch_assoc();
} else {
    header("Location: admincomplaint.php?error=complaint_not_found");
    exit;
}

// Close the result set
$result->close();

// Handle form submission to update complaint status
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['status'])) {
    $status = $_POST['status'];

    // Update complaint status using prepared statements
    $update_sql = "UPDATE complaints SET status = ? WHERE complaint_id = ?";
    $update_stmt = $conn->prepare($update_sql);
    if ($update_stmt === false) {
        die("Error preparing update statement: " . $conn->error);
    }
    $update_stmt->bind_param("si", $status, $id);

    if ($update_stmt->execute()) {
        // Successful update message
        $_SESSION['update_success'] = "Complaint status updated successfully.";
        header("Location: admincomplaint.php"); // Redirect back to complaints list
        exit;
    } else {
        // Error handling if update fails
        $_SESSION['update_error'] = "Error updating record: " . $update_stmt->error;
        header("Location: admincomplaint.php?error=update_failed");
        exit;
    }
} else {
    // Handle case where status is not set in $_POST
    $_SESSION['update_error'] = "Error: Status not provided.";
    header("Location: admincomplaint.php?error=status_not_provided");
    exit;
}

// Close database connection
$conn->close();
?>
