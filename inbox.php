<?php
session_name('admin_session'); // Set a unique session name for admins
session_start();

if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
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

$admin_id = $_SESSION['admin_id'];
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$messagesPerPage = 20; // Number of messages per page
$offset = ($page - 1) * $messagesPerPage;

// Fetch messages for the admin with pagination
$sql = "SELECT id, message, date FROM admin_inbox WHERE admin_id = '$admin_id' ORDER BY date DESC LIMIT $offset, $messagesPerPage";
$result = $conn->query($sql);

// Calculate total pages
$totalMessagesResult = $conn->query("SELECT COUNT(*) AS totalMessages FROM admin_inbox WHERE admin_id = '$admin_id'");
$totalMessagesRow = $totalMessagesResult->fetch_assoc();
$totalPages = ceil($totalMessagesRow['totalMessages'] / $messagesPerPage);

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <link rel="stylesheet" href="dashbcss.css">
    <link rel="stylesheet" href="inbox.css">
</head>
<body>
    <?php include 'sidebar.php'; ?>
    <div class="main-content">
        <h1>ADMIN INBOX</h1>
        <div class="container">   
            <div class="inbox-container">
                <div class="inbox-list" id="inbox-list">
                    <!-- Messages will be dynamically added here -->
                </div>
            </div>

            <!-- Pagination Links -->
            <div class="pagination" id="pagination">
                <!-- Pagination will be dynamically added here -->
            </div>

            <!-- Modal for message details -->
            <div id="message-modal" class="modal">
                <div class="modal-content">
                    <span class="close">&times;</span>
                    <div id="message-details"></div>
                </div>
            </div>

            <!-- Include the audio element for notification sound -->
            <audio id="notificationSound" src="notification-sound.mp3" preload="auto"></audio>

            <script src="inbox.js"></script>
            </div>
    </div>
</body>
</html>

