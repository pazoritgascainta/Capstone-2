<?php
session_name('admin_session');
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
    // Redirect back to service requests list or handle error
    header("Location: adminserreq.php?error=missing_id");
    exit;
}

// Get 'id' parameter from URL and sanitize it
$service_req_id = intval($_GET['id']);

// Prepare the SQL query to fetch service request details
$sql = "SELECT s.service_req_id, s.homeowner_id, s.details, s.urgency, s.type, s.status, s.created_at, s.updated_at,
               h.name as homeowner_name
        FROM serreq s
        JOIN homeowners h ON s.homeowner_id = h.id
        WHERE s.service_req_id = ?";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $service_req_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows == 1) {
    // Fetch service request details
    $service_req = $result->fetch_assoc();
} else {
    // Service request not found, redirect or handle error
    header("Location: adminserreq.php?error=req_not_found");
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
    <title>Admin - View Service Request</title>
    <link rel="stylesheet" href="dashbcss.css">
    <link rel="stylesheet" href="view_admin_service.css">
</head>
<body>
<?php include 'sidebar.php'; ?>
<div class="main-content">
    <div class="container">
        <h1>View Service Request</h1>
        <div class="service-request">
            <p><strong>ID:</strong> <?php echo htmlspecialchars($service_req['service_req_id']); ?></p>
            <p><strong>Homeowner ID:</strong> <?php echo htmlspecialchars($service_req['homeowner_id']); ?></p>
            <p><strong>Homeowner Name:</strong> <?php echo htmlspecialchars($service_req['homeowner_name']); ?></p>
            <p><strong>Details:</strong> <?php echo htmlspecialchars($service_req['details']); ?></p>
            <p><strong>Urgency:</strong> <?php echo htmlspecialchars($service_req['urgency']); ?></p>
            <p><strong>Type:</strong> <?php echo htmlspecialchars($service_req['type']); ?></p>
            <p><strong>Status:</strong> <?php echo htmlspecialchars($service_req['status']); ?></p>
            <p><strong>Created At:</strong> <?php echo htmlspecialchars($service_req['created_at']); ?></p>
            <p><strong>Updated At:</strong> <?php echo htmlspecialchars($service_req['updated_at']); ?></p>

            <!-- Form to update service request status -->
            <form action="update_serreq.php?id=<?php echo $service_req_id; ?>" method="post">
                <label for="status">Status:</label>
                <select id="status" name="status">
                    <option value="Pending" <?php if ($service_req['status'] == 'Pending') echo 'selected'; ?>>Pending</option>
                    <option value="In Progress" <?php if ($service_req['status'] == 'In Progress') echo 'selected'; ?>>In Progress</option>
                    <option value="Resolved" <?php if ($service_req['status'] == 'Resolved') echo 'selected'; ?>>Resolved</option>
                </select>
                <br>
                <button type="submit">Update</button>
            </form>
        </div>
    </div>
</div>
</body>
</html>
