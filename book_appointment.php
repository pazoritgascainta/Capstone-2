<?php
session_start();

// Redirect to login if homeowner is not logged in
if (!isset($_SESSION['homeowner_id'])) {
    header('Location: login.php');
    exit();
}

// Retrieve the homeowner ID from the session
$homeowner_id = $_SESSION['homeowner_id'];

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

// Function to check if a timeslot is already booked by the same homeowner
function is_timeslot_booked($conn, $homeowner_id, $date, $timeslot_id, $amenity_id) {
    $sql_check = "SELECT COUNT(*) FROM appointments WHERE homeowner_id = ? AND date = ? AND timeslot_id = ? AND amenity_id = ?";
    $stmt_check = $conn->prepare($sql_check);
    $stmt_check->bind_param("issi", $homeowner_id, $date, $timeslot_id, $amenity_id);
    $stmt_check->execute();
    $stmt_check->bind_result($count);
    $stmt_check->fetch();
    $stmt_check->close();
    return $count > 0;
}

// Handle form submission to book an appointment
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['book_appointment'])) {
    $date = sanitize_input($_POST['date']);
    $name = sanitize_input($_POST['name']);
    $email = sanitize_input($_POST['email']);
    $purpose = sanitize_input($_POST['purpose']);
    $amenity_id = sanitize_input($_POST['amenity_id']);
    $timeslot_ids = isset($_POST['timeslot_ids']) ? $_POST['timeslot_ids'] : [];

    $homeowner_id = $_SESSION['homeowner_id'];
    $status = 'Pending';

    $errors = [];
    foreach ($timeslot_ids as $timeslot_id) {
        $timeslot_id = intval($timeslot_id);

        // Check if the timeslot is already booked
        if (is_timeslot_booked($conn, $homeowner_id, $date, $timeslot_id, $amenity_id)) {
            $errors[] = "Timeslot ID $timeslot_id is already booked for this amenity.";
            continue;
        }

        // Insert the new appointment
        $sql_insert = "INSERT INTO appointments (homeowner_id, date, name, email, purpose, status, timeslot_id, amenity_id) 
                       VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
        $stmt_insert = $conn->prepare($sql_insert);
        if (!$stmt_insert) {
            $errors[] = "Failed to prepare SQL statement: " . $conn->error;
            continue;
        }
        $stmt_insert->bind_param("isssssii", $homeowner_id, $date, $name, $email, $purpose, $status, $timeslot_id, $amenity_id);

        if (!$stmt_insert->execute()) {
            $errors[] = "Failed to execute SQL statement: " . $stmt_insert->error;
        }
    }

    // Prepare response and redirect
    if (empty($errors)) {
        $_SESSION['message'] = ['status' => 'success', 'message' => 'Appointment booked successfully.'];
    } else {
        $_SESSION['message'] = ['status' => 'error', 'message' => implode(', ', $errors)];
    }

    // Redirect to the booking page
    header('Location: amenity_booking.php');
    exit();
}