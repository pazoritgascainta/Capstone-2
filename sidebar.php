<?php
// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Database connection variables
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "homeowner";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Ensure admin_id is set in the session
if (!isset($_SESSION['admin_id'])) {
    // Redirect to the admin login page
    header("Location: admin_login.php");
    exit;  // Terminate script execution after the redirect
}
$admin_id = $_SESSION['admin_id'];

// Fetch the admin's current information
$sql = "SELECT * FROM admin WHERE id = ?";
$stmt = $conn->prepare($sql);

if ($stmt === false) {
    error_log("Failed to prepare SQL statement: " . $conn->error);
    return;  // Avoid using exit here to allow the rest of the page to render
}

$stmt->bind_param("i", $admin_id);
$stmt->execute();
$result = $stmt->get_result();

// Fetch the admin data if it exists
if ($result->num_rows > 0) {
    $admin = $result->fetch_assoc();
} else {
    error_log("Admin not found for ID " . $admin_id);
    return;  // Avoid using exit here to allow the rest of the page to render
}

$stmt->close();

// Default profile image handling
$default_image = 'profile.png';
$profile_image = isset($admin['profile_image']) && !empty($admin['profile_image']) ? $admin['profile_image'] : $default_image;

// Do not close the connection here if it is needed in another script

?>


<!-- Your HTML and rest of the code here -->

<link rel="stylesheet" href="dashbcss.css">
<link rel="stylesheet" href="sidebar.css">
<div class="headnavbar">
    <nav>
        <a href="dashadmin.php">
            <img src="monique logo.png" alt="logo" id="logo-img">
        </a>
        <div class="nav-links-wrapper">
            <ul>
                <li><a href="dashadmin.php" class="nav-link home-link">Home</a></li>
                <li>
        <a href="#" class="nav-link notifications-link">Notifications 
        <div class="notification-dot" id="inboxNotificationDot" style="display: none;"></div> <!-- Notification dot -->
        </a>
        <div class="sub-menu-wrap" id="notificationsMenu">
            <div class="sub-menu">
                <a href="inbox.php" class="sub-menu-link">
                    <img src="inbox.png" alt="">
                    <p>Inbox</p>
                    <span>></span>
                </a>
            </div>
        </div>
    </li>
    <audio id="notificationSound" src="Notificationsound.mp3" preload="auto"></audio>

            </ul>
            <img src="<?php echo htmlspecialchars($admin['profile_image'] ?? 'profile.png'); ?>" class="user-pic" onclick="toggleProfileMenu()" alt="profile picture">
            <div class="sub-menu-wrap" id="profileMenu">
                <div class="sub-menu">
                    <img src="<?php echo htmlspecialchars($admin['profile_image'] ?? 'profile.png'); ?>" alt="Profile Image" class="profile-img" id="sidebarProfileImage">
                    <div>
                        <p class="user-names"><?php echo htmlspecialchars($admin['username'] ?? 'Admin Name'); ?></p>
                        <p>Admin</p>
                    </div>
                    <a href="settingsadmin.php" class="sub-menu-link">
                        <img src="settings.png" alt="">
                        <p>Settings</p>
                        <span>></span>
                    </a>
                    <a href="adminlogout.php" class="sub-menu-link">
                        <img src="logawt.png" alt="">
                        <p>Logout</p>
                        <span>></span>
                    </a>
                </div>
            </div>
        </div>
    </nav>
</div>

<!-- Sidebar -->
<div class="sidebar">
    <div class="top">
        <div class="logo">
            <img src="monique logo.png" width="170" height="80" alt="monique" class="mnq-img">
        </div>
        <img src="menu.png" alt="menu" class="menu-img" id="btn">
    </div>
    <div class="user">
        <img src="<?php echo htmlspecialchars($admin['profile_image'] ?? 'profile.png'); ?>" alt="profile picture" class="profile-img">
        <div>
            <p class="user-names"><?php echo htmlspecialchars($admin['username'] ?? 'Admin Name'); ?></p> <!-- Dynamic admin name -->
            <p>Admin</p>
        </div>
    </div>
    <hr>

    <ul>
        <li>
            <a href="dashadmin.php">
                <img src="dashboard.png" alt="dashboard" class="sideimg">
                <span class="nav-item">Dashboard</span>
            </a>
            <span class="tooltip">Dashboard</span>
        </li>
        <li>
            <a href="homeowneradmin.php">
                <img src="homeowner.png" alt="homeowner" class="sideimg">
                <span class="nav-item">Homeowner</span>
            </a>
            <span class="tooltip">Homeowner</span>
        </li>
        <li>
            <a href="admincomplaint.php">
                <img src="complaint.png" alt="complaints" class="sideimg">
                <span class="nav-item">Complaints</span>
            </a>
            <span class="tooltip">Complaints</span>
        </li>
        <li>
            <a href="billingadmin.php">
                <img src="bill.png" alt="billing" class="sideimg">
                <span class="nav-item">Billing</span>
            </a>
            <span class="tooltip">Billing</span>
        </li>
        <li>
            <a href="recordingadmin.php">
                <img src="record.png" alt="recording" class="sideimg">
                <span class="nav-item">Recording</span>
            </a>
            <span class="tooltip">Recording</span>
        </li>
        <li>
            <a href="admin_approval.php">
                <img src="schedule.png" alt="schedule" class="sideimg">
                <span class="nav-item">Appointment</span>
            </a>
            <span class="tooltip">Appointment</span>
        </li>
        <li>
            <a href="serviceadmin.php">
                <img src="service.png" alt="service" class="sideimg">
                <span class="nav-item">Service</span>
            </a>
            <span class="tooltip">Service Requests</span>
        </li>
        <li>
            <a href="reportadmin.php">
                <img src="report.png" alt="report" class="sideimg">
                <span class="nav-item">Report</span>
            </a>
            <span class="tooltip">Report</span>
        </li>
    </ul>
</div>

<script src="sidebar.js"></script>
