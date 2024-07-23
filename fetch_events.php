<?php
// Database connection (adjust with your credentials)
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "homeowner";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Fetch booked appointments from database
$sql = "SELECT id, date, time, purpose, 'booked' AS type FROM appointments WHERE approved = 'Approved'";
$result = $conn->query($sql);

$events = [];

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $events[] = [
            'id' => $row['id'],
            'title' => 'Booked - ' . $row['purpose'],
            'start' => $row['date'] . 'T' . $row['time'],
            'backgroundColor' => '#f0ad4e', // Customize color as needed
            'borderColor' => '#f0ad4e', // Customize border color as needed
        ];
    }
}

// Close connection
$conn->close();

// Output events in JSON format
header('Content-Type: application/json');
echo json_encode($events);
?>
