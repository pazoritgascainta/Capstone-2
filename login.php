<?php
session_start();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST['email'];
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

    $sql = "SELECT id, name, password FROM homeowners WHERE email = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->store_result();
    $stmt->bind_result($id, $name, $hashed_password);

    if ($stmt->num_rows > 0) {
        $stmt->fetch();
        if (password_verify($password, $hashed_password)) {
            $_SESSION['homeowner_id'] = $id;
            $_SESSION['homeowner_name'] = $name;
            header("location: dashuser.php");
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
if (isset($_GET['message']) && $_GET['message'] == 'loggedout') {
    $logout_message = "You have been logged out successfully.";
}
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
        <?php
        if (!empty($error)) {
            echo "<div style='color: red;'>$error</div>";
        }
        if (!empty($logout_message)) {
            echo "<div style='color: green;'>$logout_message</div>";
        }
        ?>
        <form method="post">
            <div class="row">
                <label class="col-form-label">Email</label>
                <div class="col">
                    <input type="text" class="form-control" name="email">
                </div>
            </div>
            <div class="row">
                <label class="col-form-label">Password</label>
                <div class="col">
                    <input type="password" class="form-control" name="password">
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
