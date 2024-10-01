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

$data = json_decode(file_get_contents('php://input'), true);
$messageId = $data['messageId'];
$user_id = $_SESSION['homeowner_id'];

$sql = "UPDATE inbox SET seen = 1 WHERE id = '$messageId' AND homeowner_id = '$user_id'";
if ($conn->query($sql) === TRUE) {
    echo json_encode(['success' => true]);
} else {
    echo json_encode(['success' => false]);
}

$conn->close();
?>
