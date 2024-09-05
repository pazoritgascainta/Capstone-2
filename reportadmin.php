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
    <title>Report</title>
    <link rel="stylesheet" href="dashbcss.css">
</head>
<body>
<?php include 'sidebar.php'; ?>
<div class="main-content">
    <div class="Container">
        <h1>St. Monique Report</h1>
    </div>
</div>

</body>




</html>