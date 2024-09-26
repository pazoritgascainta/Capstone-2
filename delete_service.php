<?php
session_name('admin_session');
session_start();

// Check if admin is logged in
if (!isset($_SESSION['admin_id'])) {
    header("Location: admin_login.php");
    exit;
}

// Database connection
$servername = "localhost"; // Your server name
$username = "root"; // Your database username
$password = ""; // Your database password
$dbname = "homeowner"; // Your database name

$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Check if service_req_id is set and is a valid integer
if (isset($_POST['service_req_id']) && is_numeric($_POST['service_req_id'])) {
    $service_req_id = intval($_POST['service_req_id']);

    // Prepare the delete statement
    $stmt = $conn->prepare("DELETE FROM serreq WHERE service_req_id = ?");
    $stmt->bind_param("i", $service_req_id);

    // Execute the statement
    if ($stmt->execute()) {
        // Redirect back to serviceadmin.php with a success message
        header("Location: serviceadmin.php?message=Request deleted successfully.");
    } else {
        // Redirect back with an error message
        header("Location: serviceadmin.php?message=Error deleting request.");
    }

    $stmt->close();
} else {
    header("Location: serviceadmin.php?message=Invalid request.");
}

$conn->close();
?>
