<?php
session_name('admin_session'); // Set a unique session name for admins
session_start();

// Check if admin is logged in
$servername = "localhost";
$username = "root";
$password = "";
$database = "homeowner";

$conn = new mysqli($servername, $username, $password, $database);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Check if 'id' parameter is present in URL
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    // Redirect back to complaints list or handle error
    header("Location: admincomplaint.php?error=missing_id");
    exit;
}

// Get 'id' parameter from URL and sanitize it
$complaint_id = intval($_GET['id']);
// Get 'sort' and 'order' parameters from URL and set defaults
$sort = isset($_GET['sort']) ? $_GET['sort'] : 'updated_at'; // Default sort by 'updated_at'
$order = isset($_GET['order']) && strtolower($_GET['order']) == 'asc' ? 'ASC' : 'DESC'; // Default 'DESC'

// Validate the 'sort' column to prevent SQL injection
$valid_columns = ['status', 'updated_at'];
if (!in_array($sort, $valid_columns)) {
    $sort = 'updated_at'; // Default to 'updated_at' if invalid column is given
}

// Prepare the SQL query to include sorting
$sql = "SELECT c.complaint_id, c.homeowner_id, c.subject, c.description, c.status, c.created_at, c.updated_at,
               h.name as homeowner_name
        FROM complaints c
        JOIN homeowners h ON c.homeowner_id = h.id
        WHERE c.complaint_id = ?
        ORDER BY $sort $order";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $complaint_id);
$stmt->execute();
$result = $stmt->get_result();

if (!$result) {
    // Query failed, show error message or redirect
    header("Location: admincomplaint.php?error=query_error");
    exit;
}

if ($result->num_rows == 1) {
    // Fetch complaint details
    $complaint = $result->fetch_assoc();
} else {
    // Complaint not found, redirect back to complaints list or handle error
    header("Location: admincomplaint.php?error=complaint_not_found");
    exit;
}

// Close database connection
$stmt->close();

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $complaint_id);
$stmt->execute();
$result = $stmt->get_result();

if (!$result) {
    // Query failed, show error message or redirect
    header("Location: admincomplaint.php?error=query_error");
    exit;
}

if ($result->num_rows == 1) {
    // Fetch complaint details
    $complaint = $result->fetch_assoc();
} else {
    // Complaint not found, redirect back to complaints list or handle error
    header("Location: admincomplaint.php?error=complaint_not_found");
    exit;
}


// Close database connection
$stmt->close();
$conn->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin - View Complaint</title>
    <link rel="stylesheet" href="dashbcss.css">
    <link rel="stylesheet" href="admin_view_complaints.css">
</head>
<body>
<?php include 'sidebar.php'; ?>
<div class="main-content">
    <div class="container">
        <h1>View Complaint</h1>
        <div class="complaint">
            <p><strong>ID:</strong> <?php echo htmlspecialchars($complaint['complaint_id']); ?></p>
            <p><strong>Homeowner ID:</strong> <?php echo htmlspecialchars($complaint['homeowner_id']); ?></p>
            <p><strong>Homeowner Name:</strong> <?php echo htmlspecialchars($complaint['homeowner_name']); ?></p>
            <p><strong>Subject:</strong> <?php echo htmlspecialchars($complaint['subject']); ?></p>
            <p><strong>Description:</strong> <?php echo htmlspecialchars($complaint['description']); ?></p>
            <p><strong>Status:</strong> <?php echo htmlspecialchars($complaint['status']); ?></p>
            <p><strong>Created At:</strong> <?php echo htmlspecialchars($complaint['created_at']); ?></p>
            <p><strong>Updated At:</strong> <?php echo htmlspecialchars($complaint['updated_at']); ?></p>

            <!-- Form to update complaint status -->
            <form action="update_complaint.php?id=<?php echo $complaint_id; ?>" method="post">
                <label for="status">Status:</label>
                <select id="status" name="status">
                    <option value="Pending" <?php if ($complaint['status'] == 'Pending') echo 'selected'; ?>>Pending</option>
                    <option value="In Progress" <?php if ($complaint['status'] == 'In Progress') echo 'selected'; ?>>In Progress</option>
                    <option value="Resolved" <?php if ($complaint['status'] == 'Resolved') echo 'selected'; ?>>Resolved</option>
                </select>
                <br>
                <button type="submit">Update</button>
            </form>
        </div>
    </div>
</div>
</body>
</html>
