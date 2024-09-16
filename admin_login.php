<?php
session_name('admin_session'); // Set a unique session name for admins
session_start();

// Initialize error message variable
$error_message = "";

// Check if the form has been submitted
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    // Database connection
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

    // Retrieve form data
    $admin_username = $_POST['username'];
    $admin_password = $_POST['password'];

    // Query to check if admin exists
    $sql = "SELECT id, username, password FROM admin WHERE username = ?";
    $stmt = $conn->prepare($sql);

    if ($stmt === false) {
        die("Failed to prepare SQL statement: " . $conn->error);
    }

    $stmt->bind_param("s", $admin_username); // "s" stands for string
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $admin = $result->fetch_assoc();

        // Verify password
        if (password_verify($admin_password, $admin['password'])) {
            // Set session variables
            $_SESSION['admin_id'] = $admin['id'];

            // Check if redirect parameter is set
            if (isset($_GET['redirect'])) {
                // Redirect to the original page the user was trying to access
                $redirect_url = urldecode($_GET['redirect']);
                header("Location: " . $redirect_url);
            } else {
                // Redirect to default admin dashboard if no redirect URL is set
                header("Location: dashadmin.php");
            }
            exit();
        } else {
            // Password is incorrect
            $error_message = "Invalid password.";
        }
    } else {
        // Admin not found
        $error_message = "Invalid username.";
    }

    $stmt->close();
    $conn->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin Login</title>
    <link rel="stylesheet" href="admin_login.css">
    
    <script>
        function updateDateTime() {
            const now = new Date();
            const dateTimeString = now.toLocaleString();
            document.getElementById('date-time').textContent = dateTimeString;
        }

        window.onload = function() {
            updateDateTime();
            setInterval(updateDateTime, 1000); // Update every second
        }
    </script>

</head>
<body>
    <div class="date-time-container">
        <p id="date-time"></p>
    </div>

    <div class="login-container">
        <h2>Admin Login</h2>
        <h3>Welcome to St.Monique Management System</h3>
        <?php if (!empty($error_message)) { ?>
            <p style="color: red;"><?php echo htmlspecialchars($error_message); ?></p>
        <?php } ?>
        <form action="admin_login.php<?php echo isset($_GET['redirect']) ? '?redirect=' . urlencode($_GET['redirect']) : ''; ?>" method="POST">
            <label for="username">Username</label>
            <input type="text" id="username" name="username" required>
            <br>
            <label for="password">Password</label>
            <input type="password" id="password" name="password" required>
            <br>
            <button type="submit">Login</button>
        </form>
    </div>
</body>
</html>
