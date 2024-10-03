<?php
session_name('admin_session'); 
session_start();

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "homeowner";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$admin_id = $_SESSION['id']; // Assuming you have stored admin_id in session
$sql = "SELECT COUNT(*) AS newMessages FROM admin_inbox WHERE id = '$admin_id' AND seen = 0";
$result = $conn->query($sql);
$row = $result->fetch_assoc();

echo json_encode(['newMessages' => $row['newMessages'] > 0]);

$conn->close();
?>