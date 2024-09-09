<?php
<<<<<<< HEAD
session_name('user_session'); 
session_start();
=======
session_name('user_session'); // Set a unique session name for admins
session_start();

// Check if admin is logged in
if (!isset($_SESSION['homeowner_id'])) {
    header("Location: login.php");
    exit;
}
?>
>>>>>>> 669e9bb6dd43ac9d1a9f58599b318159d8e799f5


if (!isset($_SESSION['homeowner_id'])) {
    header("Location: login.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <title>Dashboard</title>
    <link rel="stylesheet" href="usersidebar.css">
    <link rel="stylesheet" href="userService.css">
</head>
<body>
    <?php include 'usersidebar.php'; ?>

    <div class="main-content">
        <div class="Container">
            <h1>St. Monique User Service Requests</h1>
        </div>
        <div class="service-request-form">
        <div class="form-container">
            <div class="button-group">
                <button class="btn active" id="maintenance">Maintenance</button>
                <button class="btn" id="road-repair">Road Repair</button>
                <button class="btn" id="lawn-cleanup">Lawn Cleanup</button>
                <button class="btn" id="general-cleaning">General Cleaning</button>
                <button class="btn" id="other">Other</button>
            </div>
            <form id="serviceForm">
                <label for="email">Email</label>
                <input type="email" id="email" name="email" required>

                <label for="name">Home Address</label>
                        <input type="text" id="name" name="name" required>
                
                <label for="message">Message</label>
                <textarea id="message" name="message" rows="4" required></textarea>
                
                <button type="submit">Submit Form</button>
            </form>
        </div>
    </div>
    </div>

    <script src="Userservice.js"></script>
</body>
</html>
