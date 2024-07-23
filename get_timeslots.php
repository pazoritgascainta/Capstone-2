<?php
header('Content-Type: application/json');

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "homeowner";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$date = $_GET['date'];
$amenity_id = $_GET['amenity_id'];

$sql = "SELECT * FROM timeslots WHERE amenity_id = ? AND date = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("is", $amenity_id, $date);
$stmt->execute();
$result = $stmt->get_result();

$timeslots = [];
while ($row = $result->fetch_assoc()) {
    $timeslots[] = $row;
}

echo json_encode($timeslots);

$conn->close();
?>
