<?php
session_start();

// Include database connection
include 'config.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Collect form data
    $username = $_POST['username'];
    $password = $_POST['password']; // Plain text password from form

    // Hash the password
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

    // Prepare SQL statement to insert into database
    $sql = "INSERT INTO admin (username, password) VALUES (?, ?)";
    $stmt = $conn->prepare($sql);

    if (!$stmt) {
        // Handle error if prepare fails
        echo "Prepare failed: (" . $conn->errno . ") " . $conn->error;
    } else {
        // Bind parameters and execute statement
        $stmt->bind_param("ss", $username, $hashed_password);

        if ($stmt->execute()) {
            // Registration successful
            $_SESSION['registration_success'] = true; // Optional: Set a session variable to show a success message on login page
            header("Location: admin_login.php");
            exit; // Make sure to exit after redirecting
        } else {
            // Registration failed
            echo "Error registering admin: " . $stmt->error;
        }

        // Close statement
        $stmt->close();
    }
}

// Close database connection
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin Registration</title>
</head>
<body>
    <h2>Admin Registration</h2>
    <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
        <label for="username">Username:</label>
        <input type="text" id="username" name="username" required><br><br>
        
        <label for="password">Password:</label>
        <input type="password" id="password" name="password" required><br><br>
        
        <button type="submit">Register</button>
    </form>
    
    <p>Already registered? <a href="admin_login.php">Go to Admin Login</a></p>
</body>
</html>

