<?php
session_start();

// Check if admin is logged in
if (!isset($_SESSION['admin_logged_in'])) {
    header("Location: admin_login.php?error=not_logged_in");
    exit;
}

// Include database connection
include 'config.php';

// Check if 'id' parameter is present in URL
if (!isset($_GET['id'])) {
    // Redirect back to complaints list or handle error
    header("Location: admincomplaint.php?error=missing_id");
    exit;
}

// Get 'id' parameter from URL
$complaint_id = $_GET['id'];

// Query to fetch complaint details with homeowner information
$sql = "SELECT c.complaint_id, c.homeowner_id, c.subject, c.description, c.status, c.created_at, c.updated_at,
               h.name as homeowner_name
        FROM complaints c
        JOIN homeowners h ON c.homeowner_id = h.id
        WHERE c.complaint_id = $complaint_id";
$result = $conn->query($sql);

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
