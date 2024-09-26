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

// Pagination variables
$limit = 10; // Set the number of records per page
$current_page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($current_page - 1) * $limit;

// Search query
$search_query = isset($_GET['search']) ? $_GET['search'] : '';

// Base SQL query
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
        homeowners h ON sr.homeowner_id = h.id
";

// Add search condition if a search query is present
if (!empty($search_query)) {
    $sql .= " WHERE sr.homeowner_id = " . intval($search_query); // Use prepared statements for production
}

// Get the total number of records
$total_result = $conn->query($sql);
$total_rows = $total_result->num_rows;
$total_pages = ceil($total_rows / $limit);

// Add pagination to the query
$sql .= " LIMIT $limit OFFSET $offset";
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
        <div class="container">
            <h1>St. Monique Service Requests</h1>
            
            <!-- Search Form -->
            <form method="GET" action="serviceadmin.php" class="search-form">
                <input type="number" name="search" value="<?= htmlspecialchars($search_query) ?>" placeholder="Search by Homeowner ID...">
                <button type="submit">Search</button>
            </form>
        </div>

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
                                    <a href="view_admin_service.php?id=<?php echo $request['service_req_id']; ?>" class="view-btn">View</a>
                                    <form method="POST" action="delete_service.php" style="display:inline;">
                                        <input type="hidden" name="service_req_id" value="<?php echo $request['service_req_id']; ?>">
                                        <button type="submit" class="delete-btn" onclick="return confirm('Are you sure you want to delete this request?');">Delete</button>
                                    </form>
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

        <!-- Pagination -->
        <div id="pagination">
            <?php if ($total_pages > 1): ?>
                <!-- Previous button -->
                <?php if ($current_page > 1): ?>
                    <form method="GET" action="serviceadmin.php" style="display: inline;">
                        <input type="hidden" name="page" value="<?= $current_page - 1 ?>">
                        <input type="hidden" name="search" value="<?= htmlspecialchars($search_query) ?>">
                        <button type="submit" class="btn">&lt;</button>
                    </form>
                <?php endif; ?>

                <!-- Page input for user to change the page -->
                <form method="GET" action="serviceadmin.php" style="display: inline;">
                    <input type="number" name="page" value="<?= $current_page ?>" min="1" max="<?= $total_pages ?>" class="pagination-input">
                    <input type="hidden" name="search" value="<?= htmlspecialchars($search_query) ?>">
                </form>

                <!-- "of" text and last page link -->
                <?php if ($total_pages > 1): ?>
                    <span>of</span>
                    <a href="serviceadmin.php?page=<?= $total_pages ?>&search=<?= htmlspecialchars($search_query) ?>" class="page-link <?= ($current_page == $total_pages) ? 'active' : '' ?>"><?= $total_pages ?></a>
                <?php endif; ?>

                <!-- Next button -->
                <?php if ($current_page < $total_pages): ?>
                    <form method="GET" action="serviceadmin.php" style="display: inline;">
                        <input type="hidden" name="page" value="<?= $current_page + 1 ?>">
                        <input type="hidden" name="search" value="<?= htmlspecialchars($search_query) ?>">
                        <button type="submit" class="btn">&gt;</button>
                    </form>
                <?php endif; ?>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>
