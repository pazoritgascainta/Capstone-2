<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin Login</title>
    <!-- <link rel="stylesheet" href="styles.css">  -->
</head>
<body>
    <div class="login-container">
        <h2>Admin Login</h2>
        <form action="admin_login_process.php" method="POST">
            <label for="username">Username</label>
            <input type="text" id="username" name="username" required>
            <br>
            <label for="password">Password</label>
            <input type="password" id="password" name="password" required>
            <br>
            <button type="submit">Login</button>
        </form>
        <p>Don't have an account? <a href="register_admin.php">Register</a></p>
    </div>
</body>
</html>
