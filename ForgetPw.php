<?php
session_start();
$servername = "localhost";
$username = "root";
$dbpassword = "";
$database = "homeowner";

$conn = new mysqli($servername, $username, $dbpassword, $database);

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);
    
    // Check if email exists
    $sql = "SELECT id FROM homeowners WHERE email = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        // Create reset token (or use session for simplicity in local environment)
        $_SESSION['reset_email'] = $email;
        header("Location: reset_password.php"); // Redirect to reset password page
        exit();
    } else {
        $error = "No account found with that email.";
    }
    $stmt->close();
}
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <link rel="stylesheet" href="forgetpw.css">
    <title>St. Monique</title>
</head>

<body>
    <div class="container" id="container">
        <div class="form-container forgetpw">
        <form method="POST" action="">
        <h2>Forgot Password</h2>
    <?php if (!empty($error)) { echo "<p style='color: red;'>$error</p>"; } ?>
    <form method="POST" action="">
        <input type="email" name="email" placeholder="Enter your email" required>
        <button type="submit">Reset Password</button>
    </form>
</form>
        </div>
        <div class="toggle-container">
            <div class="toggle">
                <div class="toggle-panel toggle-right">
                    <h1>Find your St. Monique account</h1>
                    <div class="back-btn-container">
    <button id="exitBtn" class="exit-btn" onclick="window.location.href='homepage.php';">Back</button>
</div>
                    <p></p>
                </div>
            </div>
        </div>
    </div>

    <script src="forgetpw.js"></script>
</body>

</html>
