<?php
session_start();
$servername = "localhost";
$username = "root";
$dbpassword = "";
$database = "homeowner";

$conn = new mysqli($servername, $username, $dbpassword, $database);

// Check if the reset email is stored in session
if (!isset($_SESSION['reset_email'])) {
    die("Unauthorized access.");
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_SESSION['reset_email'];
    $current_password = $_POST['current_password'];
    $new_password = $_POST['new_password'];
    $confirm_password = $_POST['confirm_password'];

    // Check if passwords match
    if ($new_password !== $confirm_password) {
        $error = "New passwords do not match!";
    } else {
        // Verify current password in the database
        $sql = "SELECT password FROM homeowners WHERE email = ?";
        if ($stmt = $conn->prepare($sql)) {
            $stmt->bind_param("s", $email);
            $stmt->execute();
            $stmt->bind_result($hashed_password);
            $stmt->fetch();

            if (password_verify($current_password, $hashed_password)) {
                // Hash the new password and update the database
                $new_hashed_password = password_hash($new_password, PASSWORD_DEFAULT);

                // Close the previous statement before preparing a new one
                $stmt->close();

                $update_sql = "UPDATE homeowners SET password = ? WHERE email = ?";
                if ($update_stmt = $conn->prepare($update_sql)) {
                    $update_stmt->bind_param("ss", $new_hashed_password, $email);
                    $update_stmt->execute();

                    echo "
                    <div class='success-message'>
                        <p>Password has been updated successfully!</p>
                        <p>You will be redirected to the homepage shortly.</p>
                    </div>
                    <script>
                        setTimeout(function() {
                            window.location.href = 'homepage.php';
                        }, 3000); // Redirect after 3 seconds
                    </script>
                ";
                    session_destroy(); // Optional: Clear session
                    exit();
                } else {
                    $error = "Error preparing the update statement.";
                }
            } else {
                $error = "Current password is incorrect!";
            }

            $stmt->close(); // Ensure the statement is closed
        } else {
            $error = "Error preparing the select statement.";
        }
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Reset Password</title>
    <link rel="stylesheet" href="reset_password.css">
</head>
<body>
    <div class="container">
        <h2>Reset Password</h2>
        <?php if (!empty($error)) { echo "<p class='error-message'>$error</p>"; } ?>
        <form method="POST" action="">
            <label for="current_password">Current Password</label>
            <input type="password" name="current_password" required>
            <label for="new_password">New Password</label>
            <input type="password" name="new_password" required>
            <label for="confirm_password">Confirm New Password</label>
            <input type="password" name="confirm_password" required>
            <button type="submit">Reset Password</button>
        </form>
    </div>
</body>
</html>
