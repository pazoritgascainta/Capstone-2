<?php
session_name('user_session'); 
session_start();

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "homeowner";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$user_id = $_SESSION['homeowner_id']; // Assuming you have stored homeowner_id in session
$sql = "SELECT COUNT(*) AS newMessages FROM inbox WHERE homeowner_id = '$user_id' AND seen = 0";
$result = $conn->query($sql);
$row = $result->fetch_assoc();

echo json_encode(['newMessages' => $row['newMessages'] > 0]);

$conn->close();
?>
