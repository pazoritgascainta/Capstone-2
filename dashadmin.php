<?php
session_name('admin_session'); // Set a unique session name for admins
session_start();

// Check if admin is logged in
if (!isset($_SESSION['admin_id'])) {
    header("Location: admin_login.php");
    exit;
}
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "homeowner";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Query to get the total number of homeowners
$sql = "SELECT COUNT(id) AS total_homeowners FROM homeowners";
$result = $conn->query($sql);
$total_homeowners = 0;

if ($result->num_rows > 0) {
    // Fetch the result
    $row = $result->fetch_assoc();
    $total_homeowners = $row['total_homeowners'];
}
$sql = "SELECT COUNT(*) AS total_complaints FROM complaints";
$result = $conn->query($sql);

$total_complaints = 0;
if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    $total_complaints = $row['total_complaints'];
}
$sql = "SELECT COUNT(*) AS total_billing FROM billing";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    // Fetch the row
    $row = $result->fetch_assoc();
    $total_billing = $row['total_billing'];
} else {
    $total_billing = 0;
}
$sql = "SELECT COUNT(*) AS total FROM accepted_appointments";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    $totalAppointments = $row['total'];
} else {
    $totalAppointments = 0;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dash Admin</title>

    <link rel="stylesheet" href="dashadmincss.css">
    <link rel="stylesheet" href="dashboardadmincss.css">


</head>
<body>
    <?php include 'sidebar.php'; ?>

    <div class="main-content">
        <div class="container">
            <h1>St. Monique Admin Dashboard</h1>
            <h2>Welcome,  <?php echo htmlspecialchars($admin['username'] ?? 'Admin Name'); ?></h2>
            <div class="dashboard">
            <div class="tiles">
					<article class="tile">
						<div class="tile-header">
							<i class="ph-lightning-light"></i>
							<h3>
								<span>Homeowners</span>
								<span>UrkEnergo LTD.</span>
							</h3>
						</div>
                        <div class="tile-content">
            <span><?php echo $total_homeowners; ?></span>
        </div>
						<a href="homeowneradmin.php">
							<span>Go to Homeowners</span>
							<span class="icon-button">
								<i class="ph-caret-right-bold"></i>
							</span>
						</a>
					</article>
					<article class="tile">
						<div class="tile-header">
							<i class="ph-fire-simple-light"></i>
							<h3>
								<span>Complaints</span>
								<span>Gazprom UA</span>
							</h3>
						</div>
                        <div class="tile-content">
                        <span><?php echo $total_complaints; ?></span>
                        </div>
						<a href="admincomplaint.php">
							<span>Go to Complaints</span>
							<span class="icon-button">
								<i class="ph-caret-right-bold"></i>
							</span>
						</a>
					</article>

					<article class="tile">
						<div class="tile-header">
							<i class="ph-file-light"></i>
							<h3>
								<span>Billing</span>
								<span>Kharkov 62 str.</span>
							</h3>
						</div>
                        <div class="tile-content">
                        <span><?php echo $total_billing; ?></span> 
                        </div>
						<a href="billingadmin.php">
							<span>Go to Billing</span>
							<span class="icon-button">
								<i class="ph-caret-right-bold"></i>
							</span>
						</a>
					</article>
                    <article class="tile">
						<div class="tile-header">
							<i class="ph-lightning-light"></i>
							<h3>
								<span>Recording</span>
								<span>UrkEnergo LTD.</span>
							</h3>
						</div>
						<a href="recordingadmin.php">
							<span>Go to Recording</span>
							<span class="icon-button">
								<i class="ph-caret-right-bold"></i>
							</span>
						</a>
					</article>
					<article class="tile">
						<div class="tile-header">
							<i class="ph-fire-simple-light"></i>
							<h3>
								<span>Appointment</span>
								<span>Gazprom UA</span>
							</h3>
						</div>
                        <div class="tile-content">
                        <span><?php echo $totalAppointments; ?></span> 
                        </div>

						<a href="admin_approval.php">
							<span>Go to Appointment</span>
							<span class="icon-button">
								<i class="ph-caret-right-bold"></i>
							</span>
						</a>
					</article>
					<article class="tile">
						<div class="tile-header">
							<i class="ph-file-light"></i>
							<h3>
								<span>Service Requests</span>
								<span>Kharkov 62 str.</span>
							</h3>
						</div>
						<a href="serviceadmin.php">
							<span>Go to Service Requests</span>
							<span class="icon-button">
								<i class="ph-caret-right-bold"></i>
							</span>
						</a>
					</article>
				</div>
                </div>

</body>
</html>
