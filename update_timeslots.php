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

// Function to sanitize input data
function sanitize_input($data) {
    global $conn;
    return $conn->real_escape_string(trim(htmlspecialchars($data)));
}

// Assume $timeslot_id is obtained from form submission or other sources
$timeslot_id = isset($_POST['timeslot_id']) ? sanitize_input($_POST['timeslot_id']) : '';

if (!empty($timeslot_id)) {
    // Prepare the SQL statement with a placeholder
    $sql_update = "UPDATE timeslots SET is_available = FALSE WHERE id = ?";

    // Prepare the statement
    if ($stmt_update = $conn->prepare($sql_update)) {
        // Bind the parameter to the placeholder
        $stmt_update->bind_param("i", $timeslot_id); // "i" for integer

        // Execute the statement
        if ($stmt_update->execute()) {
            echo "Timeslot updated successfully.";
        } else {
            echo "Error updating timeslot: " . $stmt_update->error;
        }

        // Close the statement
        $stmt_update->close();
    } else {
        echo "Prepare failed: " . $conn->error;
    }
} else {
    echo "Timeslot ID is not set.";
}

// Close the connection
$conn->close();
?>
