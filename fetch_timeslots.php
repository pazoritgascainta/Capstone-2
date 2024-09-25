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

    // Debugging: Log input parameters
    error_log("Date: $date, Amenity ID: $amenity_id");

    // Prepare the SQL statement
    $sql = "SELECT * FROM timeslots WHERE amenity_id = ? AND date = ? AND is_available = 1";
    $stmt = $conn->prepare($sql);
    
    if ($stmt) {
        $stmt->bind_param("is", $amenity_id, $date);
        $stmt->execute();
        $result = $stmt->get_result();

        // Check if any timeslots were returned
        if ($result->num_rows > 0) {
            $timeslots = [];
            while ($row = $result->fetch_assoc()) {
                $timeslots[] = $row;
            }
            // Debugging: Log fetched timeslots
            error_log("Fetched timeslots: " . print_r($timeslots, true));
            echo json_encode($timeslots);
        } else {
            // No records found
            error_log("No available timeslots found for the given date and amenity.");
            echo json_encode([]);
        }
    } else {
        // SQL statement preparation error
        error_log("SQL statement preparation failed: " . $conn->error);
        echo json_encode([]);
    }
} else {
    echo json_encode([]);
}

$conn->close();
?>
