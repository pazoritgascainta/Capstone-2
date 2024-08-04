<?php
header('Content-Type: application/json');

// Database connection
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "homeowner";

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    echo json_encode(['success' => false, 'message' => 'Database connection failed.']);
    exit();
}

$date = $_GET['date'] ?? '';

if (empty($date)) {
    echo json_encode(['success' => false, 'message' => 'No date provided.']);
    exit();
}

$sql = "SELECT id, name, email, purpose FROM appointments WHERE date = ?";
$stmt = $conn->prepare($sql);
if (!$stmt) {
    echo json_encode(['success' => false, 'message' => 'Prepare statement failed.']);
    exit();
}

$stmt->bind_param("s", $date);
$stmt->execute();
$result = $stmt->get_result();

$bookings = [];
while ($row = $result->fetch_assoc()) {
    $bookings[] = $row;
}

echo json_encode(['success' => true, 'bookings' => $bookings]);

$stmt->close();
$conn->close();
?>
