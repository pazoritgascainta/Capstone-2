<?php
// Database connection
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "homeowner";

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die(json_encode(['error' => "Connection failed: " . $conn->connect_error]));
}

// Fetch all pending appointments
$sql = "
    SELECT a.id, a.date, a.name, a.email, a.purpose, a.status, a.homeowner_id, 
           am.name AS amenity, t.time_start, t.time_end
    FROM appointments a
    JOIN timeslots t ON a.timeslot_id = t.id
    JOIN amenities am ON t.amenity_id = am.id
    WHERE a.status = 'Pending' AND a.date >= CURDATE()
";
$result = $conn->query($sql);

if (!$result) {
    echo json_encode(['error' => "Query failed: " . $conn->error]);
    exit;
}

$appointments = [];
while ($row = $result->fetch_assoc()) {
    $appointments[] = $row;
}

echo json_encode($appointments);

$conn->close();
?>
