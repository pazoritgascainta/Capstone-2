<?php
session_name('user_session');
session_start();

// Redirect to login if homeowner is not logged in
if (!isset($_SESSION['homeowner_id'])) {
    header('Location: login.php');
    exit();
}

// Retrieve the homeowner ID from the session
$homeowner_id = $_SESSION['homeowner_id'];

// Database connection
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "homeowner";

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Pagination settings
$limit = 10; // Number of service requests per page
$current_page = isset($_GET['page']) ? intval($_GET['page']) : 1;
$offset = ($current_page - 1) * $limit;

// Fetch total number of service requests
$sql_count = "SELECT COUNT(*) as total FROM serreq WHERE homeowner_id = ?";
$stmt_count = $conn->prepare($sql_count);
$stmt_count->bind_param("i", $homeowner_id);
$stmt_count->execute();
$result_count = $stmt_count->get_result();
$row_count = $result_count->fetch_assoc();
$total_service_requests = $row_count['total'];
$total_pages = max(ceil($total_service_requests / $limit), 1);

// Fetch service requests with pagination
$sql_requests = "SELECT * FROM serreq WHERE homeowner_id = ? LIMIT ?, ?";
$stmt_requests = $conn->prepare($sql_requests);
$stmt_requests->bind_param("iii", $homeowner_id, $offset, $limit);
$stmt_requests->execute();
$result_requests = $stmt_requests->get_result();
$service_requests = $result_requests->fetch_all(MYSQLI_ASSOC);

// Handle cancellation
if (isset($_POST['cancel_request_id'])) {
    $request_id_to_cancel = $_POST['cancel_request_id'];

    // Prepare cancellation query
    $sql_cancel = "DELETE FROM serreq WHERE service_req_id = ? AND homeowner_id = ?";
    $stmt_cancel = $conn->prepare($sql_cancel);
    $stmt_cancel->bind_param("ii", $request_id_to_cancel, $homeowner_id);
    $stmt_cancel->execute();

    // Redirect to avoid resubmission
    header("Location: " . $_SERVER['PHP_SELF']);
    exit();
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Your Service Requests</title>
    <link rel="stylesheet" href="usersidebar.css">
    <link rel="stylesheet" href="view_service_requests.css">
</head>
<body>
<?php include 'usersidebar.php'; ?>

<div class="main-content">
    <div class="container">
        <h2>Your Service Requests</h2>

        <?php if (count($service_requests) > 0): ?>
            <table class="service-requests-table">
    <thead>
        <tr>
            <th>Service Request ID</th>
            <th>Details</th>
            <th>Urgency</th>
            <th>Type</th>
            <th>Status</th>
            <th>Actions</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($service_requests as $request): ?>
            <tr>
                <td><?php echo htmlspecialchars($request['service_req_id']); ?></td>
                <td><?php echo htmlspecialchars($request['details']); ?></td>
                <td><?php echo htmlspecialchars($request['urgency']); ?></td>
                <td><?php echo htmlspecialchars($request['type']); ?></td>
                <td><?php echo htmlspecialchars($request['status']); ?></td>
                <td>
                    <form method="POST" style="display:inline;">
                        <input type="hidden" name="cancel_request_id" value="<?php echo htmlspecialchars($request['service_req_id']); ?>">
                        <button type="submit" class="cancel-btn" onclick="return confirm('Are you sure you want to cancel this request?');">Cancel</button>
                    </form>
                </td>
            </tr>
        <?php endforeach; ?>
    </tbody>
</table>


<!-- Pagination controls -->
<div class="pagination">
    <?php if ($current_page > 1): ?>
        <a href="?page=<?php echo $current_page - 1; ?>" class="pagination-link">Previous</a>
    <?php endif; ?>
    
    <span class="pagination-info">Page <?php echo $current_page; ?> of <?php echo $total_pages; ?></span>
    
    <?php if ($current_page < $total_pages): ?>
        <a href="?page=<?php echo $current_page + 1; ?>" class="pagination-link">Next</a>
    <?php endif; ?>
</div>

<?php else: ?>
    <p>No service requests found.</p>
<?php endif; ?>

<a href="serviceuser.php" class="submit-link">Submit a Service Request</a>

</div>
</body>
</html>
