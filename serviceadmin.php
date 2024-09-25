<?php
session_name('admin_session'); // Set a unique session name for admins
session_start();

// Check if admin is logged in
if (!isset($_SESSION['admin_id'])) {
    header("Location: admin_login.php");
    exit;
}

// Database connection
$servername = "localhost"; // Your server name
$username = "root"; // Your database username
$password = ""; // Your database password
$dbname = "homeowner"; // Your database name

$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Fetch service requests from the serreq table and join with homeowners table
$sql = "
    SELECT 
        sr.service_req_id, 
        sr.homeowner_id, 
        sr.details, 
        sr.urgency, 
        sr.type, 
        sr.status,
        h.address,
        h.phone_number,
        h.name,
        h.email
    FROM 
        serreq sr
    JOIN 
        homeowners h ON sr.homeowner_id = h.id  -- Update this line if the column is named differently
";

$result = $conn->query($sql);

// Store fetched data
$requests = [];
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $requests[] = $row; // Append each row to requests array
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>Service Requests</title>
    <link rel="stylesheet" href="dashbcss.css">
    <link rel="stylesheet" href="Serviceadmin.css">
</head>
<body>
    <?php include 'sidebar.php'; ?>
    <div class="main-content">
        <div class="Container">
            <h1>St. Monique Service Requests</h1>
        </div>
        
        <!-- Sidebar for categories -->


        <!-- Content Area for displaying requests -->
        <div class="content-area">
            <table id="requestsTable" border="1" style="width:100%; border-collapse: collapse;">
                <thead>
                    <tr>
                        <th>Request ID</th>
                        <th>Homeowner ID</th>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Phone Number</th>
                        <th>Address</th>
                        <th>Details</th>
                        <th>Urgency</th>
                        <th>Type</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (count($requests) > 0): ?>
                        <?php foreach ($requests as $request): ?>
                            <tr>
                                <td><?php echo $request['service_req_id']; ?></td>
                                <td><?php echo $request['homeowner_id']; ?></td>
                                <td><?php echo $request['name']; ?></td>
                                <td><?php echo $request['email']; ?></td>
                                <td><?php echo $request['phone_number']; ?></td>
                                <td><?php echo $request['address']; ?></td>
                                <td><?php echo $request['details']; ?></td>
                                <td><?php echo $request['urgency']; ?></td>
                                <td><?php echo $request['type']; ?></td>
                                <td><?php echo $request['status']; ?></td>
                                <td>
                                    <button class="view-btn">View</button>
                                    <button class="delete-btn">Delete</button>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="11">No service requests found.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</body>
</html>
