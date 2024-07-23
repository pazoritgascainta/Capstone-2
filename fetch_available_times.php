<?php
// Database connection
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "homeowner";

$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Function to sanitize input data
function sanitize_input($data) {
    global $conn;
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $conn->real_escape_string($data);
}

// Handling date parameter
if (isset($_GET['date'])) {
    $date = sanitize_input($_GET['date']);

    // SQL to fetch time slots for the given date
    $sql = "SELECT start_time, end_time FROM timeslots WHERE date = ? AND available = 1";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $date);
    $stmt->execute();
    $result = $stmt->get_result();

    $timeslots = [];
    while ($row = $result->fetch_assoc()) {
        $timeslots[] = $row;
    }

    // Return JSON response with available time slots
    header('Content-Type: application/json');
    echo json_encode($timeslots);
    
    $stmt->close();
}

$conn->close();
?>
