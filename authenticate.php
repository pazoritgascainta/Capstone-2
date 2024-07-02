<?php
session_start();

// Database connection details
$servername = "localhost";
$username = "root";
$password = "";
$database = "homeowner";

// Establish connection
$conn = new mysqli($servername, $username, $password, $database);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Retrieve login form data
$email = $_POST['email'];
$password = $_POST['password'];

// Validate user credentials
$sql = "SELECT id, name FROM homeowners WHERE email = '$email' AND password = '$password' AND status = 'active'";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    // Found a matching user
    $row = $result->fetch_assoc();

    // Start session and store user ID
    $_SESSION['homeowner_id'] = $row['id'];

    // Redirect to dashboard or homepage
    header("Location: usercomplaint.php"); // Redirect to user dashboard or complaints page
} else {
    // No matching user found
    echo "Invalid email or password. <a href='login.php'>Try again</a>";
}

$conn->close();
?>
