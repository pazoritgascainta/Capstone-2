<?php
session_name('admin_session'); 
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

$messages = [];
while ($row = $result->fetch_assoc()) {
    $messages[] = $row;
}

// Return the messages, total pages, and current page
echo json_encode([
    'messages' => $messages, 
    'totalPages' => $totalPages, 
    'currentPage' => $page
]);

$conn->close();
?>
