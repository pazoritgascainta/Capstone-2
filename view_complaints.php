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

// Function to sanitize input data
function sanitize_input($data) {
    global $conn;
    return $conn->real_escape_string(trim(htmlspecialchars($data)));
}

// Pagination settings
$limit = 10; // Number of complaints per page
$current_page = isset($_GET['page']) ? intval($_GET['page']) : 1;
$offset = ($current_page - 1) * $limit;

// Fetch total number of complaints
$sql_count = "SELECT COUNT(*) as total FROM complaints WHERE homeowner_id = ?";
$stmt_count = $conn->prepare($sql_count);
$stmt_count->bind_param("i", $homeowner_id);
$stmt_count->execute();
$result_count = $stmt_count->get_result();
$row_count = $result_count->fetch_assoc();
$total_complaints = $row_count['total'];
$total_pages = max(ceil($total_complaints / $limit), 1);

// Fetch complaints with pagination
$sql_complaints = "SELECT * FROM complaints WHERE homeowner_id = ? LIMIT ?, ?";
$stmt_complaints = $conn->prepare($sql_complaints);
$stmt_complaints->bind_param("iii", $homeowner_id, $offset, $limit);
$stmt_complaints->execute();
$result_complaints = $stmt_complaints->get_result();
$complaints = $result_complaints->fetch_all(MYSQLI_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>View Complaints</title>
    <link rel="stylesheet" href="usersidebar.css">
    <link rel="stylesheet" href="view_complaints.css">
</head>
<body>
<?php include 'usersidebar.php'; ?>

<div class="main-content">
    <div class="container">
        <h2>View Your Complaints</h2>

        <?php if (count($complaints) > 0) : ?>
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Subject</th>
                        <th>Description</th>
                        <th>Status</th>
                        <th>Created At</th>
                        <th>Updated At</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($complaints as $complaint): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($complaint['complaint_id']); ?></td>
                            <td><?php echo htmlspecialchars($complaint['subject']); ?></td>
                            <td><?php echo htmlspecialchars($complaint['description']); ?></td>
                            <td><?php echo htmlspecialchars($complaint['status']); ?></td>
                            <td><?php echo htmlspecialchars($complaint['created_at']); ?></td>
                            <td><?php echo htmlspecialchars($complaint['updated_at']); ?></td>
                            <td>
                                <a href="edit_complaint.php?id=<?php echo $complaint['complaint_id']; ?>" class="action-buttons">Edit</a>
                                <a href="cancel_complaint.php?id=<?php echo $complaint['complaint_id']; ?>" class="action-buttons">Cancel</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>

            <!-- Pagination controls -->
            <div class="pagination">
                <?php if ($current_page > 1): ?>
                    <a href="?page=<?php echo $current_page - 1; ?>">Previous</a>
                <?php endif; ?>
                <span>Page <?php echo $current_page; ?> of <?php echo $total_pages; ?></span>
                <?php if ($current_page < $total_pages): ?>
                    <a href="?page=<?php echo $current_page + 1; ?>">Next</a>
                <?php endif; ?>
            </div>

        <?php else : ?>
            <p>No complaints found.</p>
        <?php endif; ?>

        <a href="usercomplaint.php">Submit a Complaint</a>
    </div>
</div>
</body>
</html>
