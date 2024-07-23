<?php
session_start();
if (!isset($_SESSION['homeowner_id'])) {
    die('Unauthorized access'); // Redirect or handle unauthorized access
}

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

// Get form data
$date = $_POST['date'];
$time = $_POST['time'];
$name = $_POST['name'];
$email = $_POST['email'];
$purpose = $_POST['purpose'];
$homeowner_id = $_SESSION['homeowner_id'];

// SQL to insert appointment
$sql = "INSERT INTO appointments (date, time, name, email, purpose, homeowner_id)
        VALUES ('$date', '$time', '$name', '$email', '$purpose', '$homeowner_id')";

if ($conn->query($sql) === TRUE) {
    echo "Success: Appointment booked successfully!";
} else {
    echo "Error: " . $sql . "<br>" . $conn->error;
}

$conn->close();
?>
