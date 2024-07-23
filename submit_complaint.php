<?php
session_start();


if (!isset($_SESSION['homeowner_id'])) {
    header("Location: usercomplaint.php");
    exit;
}


$homeowner_id = $_SESSION['homeowner_id'];
$subject = $_POST['subject']; 
$description = $_POST['description'];

$servername = "localhost";
$username = "root";
$password = "";
$database = "homeowner";

$conn = new mysqli($servername, $username, $password, $database);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}


$sql = "INSERT INTO complaints (homeowner_id, subject, description, status, created_at) VALUES (?, ?, ?, 'Pending', NOW())";
$stmt = $conn->prepare($sql);

$stmt->bind_param("iss", $homeowner_id, $subject, $description);

if ($stmt->execute()) {

    header("Location: view_complaints.php");
    exit;
} else {

    echo "Error: " . $stmt->error;
}

$stmt->close();
$conn->close();
?>
