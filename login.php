<?php
session_name('user_session'); 
session_start();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);
    $password = $_POST['password'];

    $servername = "localhost";
    $username = "root";
    $dbpassword = "";
    $database = "homeowner";

    // Establish connection
    $conn = new mysqli($servername, $username, $dbpassword, $database);

    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error); 
    }

    $sql = "SELECT id, name, password, status FROM homeowners WHERE email = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->store_result();
    $stmt->bind_result($id, $name, $hashed_password, $status);

    if ($stmt->num_rows > 0) {
        $stmt->fetch();
        if ($status === 'archived') {
            $error = "Your account has been archived and cannot be accessed.";
        } elseif (password_verify($password, $hashed_password)) {
            $_SESSION['homeowner_id'] = $id;
            $_SESSION['homeowner_name'] = $name;
            header("Location: dashuser.php");
            exit;
        } else {
            $error = "Invalid email or password.";
        }
    } else {
        $error = "Invalid email or password.";
    }

    $stmt->close();
    $conn->close();
}

// Display logout message if redirected from logout
$logout_message = isset($_GET['message']) && $_GET['message'] == 'loggedout' ? "You have been logged out successfully." : '';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link rel="stylesheet" href="login.css">
</head>
<body>
    <div class="container my-5">
        <h2>Login</h2>
        <?php if (!empty($error)): ?>
            <div style="color: red;"><?= htmlspecialchars($error); ?></div>
        <?php endif; ?>
        <?php if (!empty($logout_message)): ?>
            <div style="color: green;"><?= htmlspecialchars($logout_message); ?></div>
        <?php endif; ?>
        <form method="post">
            <div class="row">
                <label class="col-form-label" for="email">Email</label>
                <div class="col">
                    <input type="email" id="email" class="form-control" name="email" required>
                </div>
            </div>
            <div class="row">
                <label class="col-form-label" for="password">Password</label>
                <div class="col">
                    <input type="password" id="password" class="form-control" name="password" required>
                </div>
            </div>
            <div class="row mt-3">
                <div class="col">
                    <button type="submit" class="btn btn-primary">Login</button>
                </div>
            </div>
        </form>
    </div>
</body>
</html>
