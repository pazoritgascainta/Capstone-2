<?php
session_name('admin_session'); // Set a unique session name for admins
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Database configuration
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "homeowner";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die('Database connection failed: ' . $conn->connect_error);
}

// Fetch the admin's ID from the session
$admin_id = $_SESSION['admin_id'] ?? null;

if (!$admin_id) {
    die('No admin ID found in session.');
}

// Initialize message variables
$errorMessage = '';
$successMessage = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $current_password = $_POST['current_password'] ?? '';
    $new_password = $_POST['new_password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';

    if (empty($current_password) || empty($new_password) || empty($confirm_password)) {
        $errorMessage = 'All fields are required.';
    } elseif ($new_password !== $confirm_password) {
        $errorMessage = 'New passwords do not match.';
    } else {
        // Fetch the current password hash from the database
        $sql = "SELECT password FROM admin WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $admin_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();
        $current_password_hash = $row['password'];

        // Verify the current password
        if (!password_verify($current_password, $current_password_hash)) {
            $errorMessage = 'Current password is incorrect.';
        } else {
            // Hash the new password
            $new_password_hash = password_hash($new_password, PASSWORD_DEFAULT);

            // Update the password in the database
            $sql = "UPDATE admin SET password = ? WHERE id = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("si", $new_password_hash, $admin_id);

            if ($stmt->execute()) {
                $successMessage = 'Password updated successfully.';
            } else {
                $errorMessage = 'Error updating password.';
            }
        }
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Change Admin Password</title>
    <link rel="stylesheet" href="editpro.css">
</head>
<body>

<?php include 'sidebar.php'; ?>
<div class="main-content">
    <h1>Change Password</h1>
    <div class="container">
        <?php if (!empty($errorMessage)): ?>
            <div style="color: red;"><?php echo htmlspecialchars($errorMessage); ?></div>
        <?php endif; ?>

        <?php if (!empty($successMessage)): ?>
            <div style="color: green;"><?php echo htmlspecialchars($successMessage); ?></div>
        <?php endif; ?>

        <form method="post" action="">
            <div>
                <label for="current_password">Current Password:</label>
                <input type="password" id="current_password" name="current_password" required>
            </div><br>

            <div>
                <label for="new_password">New Password:</label>
                <input type="password" id="new_password" name="new_password" required>
            </div><br>

            <div>
                <label for="confirm_password">Confirm New Password:</label>
                <input type="password" id="confirm_password" name="confirm_password" required>
            </div><br>

            <input type="submit" value="Change Password">
        </form>
    </div>
</div>

<script src="editpro.js"></script>
</body>
</html>
