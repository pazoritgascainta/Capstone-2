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
$sql = "SELECT id, message, date FROM inbox WHERE homeowner_id = '$user_id' ORDER BY date DESC";
$result = $conn->query($sql);

$announcements = [];
while ($row = $result->fetch_assoc()) {
    $announcements[] = $row;
}

echo json_encode($announcements);

$conn->close();
?>
