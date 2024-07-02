<?php
session_start();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST['email'];
    $password = $_POST['password'];

    // Database connection details
    $servername = "localhost";
    $username = "root";
    $password = "";  // Your MySQL password here, if any
    $database = "homeowner";

    // Create connection
    $conn = new mysqli($servername, $username, $password, $database);

    // Check connection
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    // Prepare SQL statement to fetch user details based on email
    $sql = "SELECT id, name, email, password FROM homeowners WHERE email=? AND status='active'";
    $stmt = $conn->prepare($sql);

    // Bind parameters and execute the statement
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    // Check if user exists
    if ($result->num_rows == 1) {
        // User found, verify password
        $row = $result->fetch_assoc();
        $stored_password = $row['password'];

        // Verify password (assuming passwords are stored in plain text for simplicity)
        if ($password == $stored_password) {
            // Passwords match, set session variables
            $_SESSION['user_id'] = $row['id'];
            $_SESSION['user_name'] = $row['name'];

            // Redirect to dashuser.php after successful login
            header("Location: dashuser.php");
            exit();
        } else {
            // Passwords do not match
            echo "Invalid password.";
        }
    } else {
        // User not found
        echo "User not found.";
    }

    // Close statement and connection
    $stmt->close();
    $conn->close();
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <!-- Include your CSS files here -->
</head>
<body>
    <h2>Login</h2>
    <form action="login.php" method="post">
        <label for="email">Email:</label><br>
        <input type="email" id="email" name="email" required><br><br>

        <label for="password">Password:</label><br>
        <input type="password" id="password" name="password" required><br><br>

        <button type="submit">Login</button>
    </form>
</body>
</html>
