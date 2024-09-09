<?php
session_name('admin_session'); // Set a unique session name for admins
session_start();

// Check if admin is logged in
if (!isset($_SESSION['admin_id'])) {
    header("Location: admin_login.php");
    exit;
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <link rel="stylesheet" href="dashbcss.css">
    <link rel="stylesheet" href="adminsettings.css">


</head>
<body>

        <?php include 'sidebar.php'; ?>
        <div class="main-content">
            <h1></h1>
<div>
  <!-- dito ilagay ang contents -->
            <div class="container">   
              <h1>ADMIN SETTINGS</h1>
             
              <a href="EditPro.php">Edit Admin Profile</a>
              <br>
              <a href="adminpw.php">Change Password</a>
         
</div>
</div>
</div>

</body>
</html>
