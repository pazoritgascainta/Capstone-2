<?php
session_start();

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

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['book_appointment'])) {
    $selected_timeslots = isset($_POST['timeslot_ids']) ? $_POST['timeslot_ids'] : [];
    $name = isset($_POST['name']) ? sanitize_input($_POST['name']) : '';
    $email = isset($_POST['email']) ? sanitize_input($_POST['email']) : '';
    $purpose = isset($_POST['purpose']) ? sanitize_input($_POST['purpose']) : '';

    if (!empty($selected_timeslots)) {
        foreach ($selected_timeslots as $timeslot_id) {
            // Sanitize the timeslot ID
            $timeslot_id = sanitize_input($timeslot_id);

            // Insert booking details into the database
            $sql = "INSERT INTO bookings (timeslot_id, name, email, purpose) VALUES (?, ?, ?, ?)";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("isss", $timeslot_id, $name, $email, $purpose);
            if ($stmt->execute()) {
                echo "<p>Appointment booked successfully for timeslot ID $timeslot_id!</p>";
            } else {
                echo "<p>Error: " . $stmt->error . "</p>";
            }
        }
    } else {
        echo "<p>No timeslots selected.</p>";
    }
}

// Close the database connection
$conn->close();
?>
