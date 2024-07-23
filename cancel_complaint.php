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

$conn = new mysqli($servername, $username, $password, $database);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if (!isset($_GET['id'])) {
    header("Location: view_complaints.php");
    exit;
}

$complaint_id = $_GET['id'];


$update_sql = "UPDATE complaints SET status = 'Cancelled' WHERE complaint_id = ?";
$stmt = $conn->prepare($update_sql);
$stmt->bind_param("i", $complaint_id);

if ($stmt->execute()) {
    header("Location: view_complaints.php");
    exit;
} else {
    echo "Error cancelling complaint: " . $stmt->error;
}

$stmt->close();
$conn->close();
?>
