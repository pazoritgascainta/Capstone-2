<?php
session_name('user_session'); // Set a unique session name for admins
session_start();

// Check if admin is logged in
if (!isset($_SESSION['homeowner_id'])) {
    header("Location: login.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Dashboard</title>
    <link rel="stylesheet" href="usersidebar.css">
    <link rel="stylesheet" href="usersettings.css">


</head>
<body>

        <?php include 'usersidebar.php'; ?>
        <div class="main-content">
<div>
  <!-- dito ilagay ang contents -->
            <div class="container">   
              <h1>USER SETTINGS</h1>
              <a href="useredit.php">Edit User Profile</a>
              <br>
              <a href="change_pass.php">Change Password</a>


         
</div>
</div>
</div>

</body>
</html>
