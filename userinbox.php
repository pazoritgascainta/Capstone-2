<?php
session_name('user_session'); 
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Connect to the database
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "homeowner";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Get the current page number from query parameters, default to 1 if not set
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$limit = 20;  // Number of messages per page
$offset = ($page - 1) * $limit;  // Offset for SQL query

// Get the total number of messages for the user
$homeowner_id = $_SESSION['homeowner_id']; // Assuming the homeowner ID is stored in session
$resultCount = $conn->query("SELECT COUNT(*) AS total FROM inbox WHERE homeowner_id = '$homeowner_id'");
$rowCount = $resultCount->fetch_assoc();
$totalMessages = $rowCount['total'];
$totalPages = ceil($totalMessages / $limit);

// Fetch messages for the current page
$sql = "SELECT * FROM inbox WHERE homeowner_id = '$homeowner_id' ORDER BY date DESC LIMIT $limit OFFSET $offset";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Dashboard</title>
    <link rel="stylesheet" href="usersidebar.css">
    <link rel="stylesheet" href="userinbox.css">
</head>
<body>

    <?php include 'usersidebar.php'; ?>
    <div class="main-content">
        <h1>User Inbox</h1>

        <div class="inbox-container">
            <div class="inbox-list" id="inbox-list">
                <!-- Display Messages -->
                <?php if ($result->num_rows > 0): ?>
                    <?php while ($row = $result->fetch_assoc()): ?>
                        <div class="inbox-message">
                            <p><strong>Date:</strong> <?php echo date('F d, Y H:i', strtotime($row['date'])); ?></p>
                            <p><?php echo htmlspecialchars($row['message']); ?></p>
                        </div>
                    <?php endwhile; ?>
                <?php else: ?>
                    <p>No messages found.</p>
                <?php endif; ?>
            </div>

            <!-- Pagination Links -->
            <div class="pagination">
                <?php if ($page > 1): ?>
                    <a href="?page=<?php echo $page - 1; ?>">Previous</a>
                <?php endif; ?>
                
                <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                    <a href="?page=<?php echo $i; ?>" class="<?php echo ($i == $page) ? 'active' : ''; ?>">
                        <?php echo $i; ?>
                    </a>
                <?php endfor; ?>
                
                <?php if ($page < $totalPages): ?>
                    <a href="?page=<?php echo $page + 1; ?>">Next</a>
                <?php endif; ?>
            </div>
        </div>

        <!-- Modal for message details (if necessary) -->
        <div id="message-modal" class="modal">
            <div class="modal-content">
                <span class="close">&times;</span>
                <div id="message-details"></div>
            </div>
        </div>

        <script src="userinbox.js"></script>
    </div>
</body>
</html>
