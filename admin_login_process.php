<?php
session_start();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Include database connection
    include 'config.php';

    // Collect form data
    $username = $conn->real_escape_string($_POST['username']);
    $password = $_POST['password'];

    // Query to fetch admin from database
    $query = "SELECT * FROM admin WHERE username='$username'";
    $result = $conn->query($query);

    if ($result->num_rows == 1) {
        // Admin found, verify password
        $admin = $result->fetch_assoc();
        if (password_verify($password, $admin['password'])) {
            // Password is correct, set session variables
            $_SESSION['admin_logged_in'] = true;
            $_SESSION['admin_username'] = $username;

            // Redirect to admin dashboard or desired page
            header("Location: dashadmin.php");
            exit;
        } else {
            // Password incorrect, redirect back to login with error
            header("Location: admin_login.php?error=invalid_credentials");
            exit;
        }
    } else {
        // Admin not found, redirect back to login with error
        header("Location: admin_login.php?error=invalid_credentials");
        exit;
    }

    // Close database connection
    $conn->close();
}
?>
