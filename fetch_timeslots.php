<?php
header('Content-Type: application/json');

// Database connection
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "homeowner";

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Function to sanitize input data
function sanitize_input($data) {
    global $conn;
    return $conn->real_escape_string(trim(htmlspecialchars($data)));
}

// Check for parameters and fetch timeslots
if (isset($_GET['date']) && isset($_GET['amenity_id'])) {
    $date = sanitize_input($_GET['date']);
    $amenity_id = sanitize_input($_GET['amenity_id']);

    $sql = "SELECT * FROM timeslots WHERE amenity_id = ? AND date = ? AND is_available = TRUE";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("is", $amenity_id, $date);
    $stmt->execute();
    $result = $stmt->get_result();

    $timeslots = [];
    while ($row = $result->fetch_assoc()) {
        $timeslots[] = $row;
    }

    // Debugging line to check query results
    // echo "<pre>" . print_r($timeslots, true) . "</pre>";
    
    echo json_encode($timeslots);
} else {
    echo json_encode([]);
}

$conn->close();
?>
