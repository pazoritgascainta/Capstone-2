<?php
session_start();

/* Check if user is logged in
if (!isset($_SESSION['homeowner_id'])) {
    header("Location: login.php");
    exit;
}
*/
// Retrieve user name from session
$user_name = $_SESSION['homeowner_name'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Welcome, <?php echo htmlspecialchars($user_name); ?></title>
    <link rel="stylesheet" href="usersidebar.css">
    <link rel="stylesheet" href="dashusercss.css">
</head>
<body>
    <?php include 'usersidebar.php'; ?>

    <div class="main-content">
        <div class="container">
            <h1>St. Monique User Dashboard</h1>
            <h2>Welcome, <?php echo htmlspecialchars($user_name); ?></h2>
            <!-- DITO dashboard content  -->
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        let btn = document.querySelector('#btn');
        let sidebar = document.querySelector('.sidebar');

        btn.onclick = function () {
            sidebar.classList.toggle('active');
        };
    </script>

</body>
</html>
