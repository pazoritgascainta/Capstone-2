<?php
session_name('admin_session');
session_start();

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "homeowner";
$records_per_page = 10; // You can adjust the number of records per page
$current_page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($current_page - 1) * $records_per_page;

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Fetch search query if provided
$search_query = isset($_GET['search']) ? trim($_GET['search']) : '';

// Fetching the most recent paid record for each homeowner with search functionality
function fetchMostRecentPaidRecords($conn, $search_query, $offset, $records_per_page) {
    $sql = "
        SELECT bh.homeowner_id, h.name AS homeowner_name, h.address, 
               bh.monthly_due, bh.billing_date, bh.due_date, bh.total_amount, bh.paid_date,
               MONTH(bh.paid_date) AS payment_month, YEAR(bh.paid_date) AS payment_year
        FROM billing_history bh
        JOIN homeowners h ON bh.homeowner_id = h.id
        WHERE bh.history_id = (
            SELECT MAX(history_id) 
            FROM billing_history 
            WHERE homeowner_id = bh.homeowner_id
        )";
    
    if (!empty($search_query)) {
        $sql .= " AND (h.name LIKE ? OR h.email LIKE ?)";
    }
    
    $sql .= " ORDER BY bh.homeowner_id LIMIT ?, ?";
    $stmt = $conn->prepare($sql);
    
    if (!empty($search_query)) {
        $like_query = "%$search_query%";
        $stmt->bind_param("ssii", $like_query, $like_query, $offset, $records_per_page);
    } else {
        $stmt->bind_param("ii", $offset, $records_per_page);
    }
    
    $stmt->execute();
    return $stmt->get_result();
}

// Get total number of homeowners for pagination
function getTotalHomeowners($conn, $search_query) {
    $sql = "SELECT COUNT(DISTINCT h.id) AS total_homeowners FROM homeowners h";
    
    if (!empty($search_query)) {
        $sql .= " WHERE h.name LIKE ? OR h.email LIKE ?";
    }

    $stmt = $conn->prepare($sql);
    
    if (!empty($search_query)) {
        $like_query = "%$search_query%";
        $stmt->bind_param("ss", $like_query, $like_query);
    }
    
    $stmt->execute();
    $result = $stmt->get_result()->fetch_assoc();
    return $result['total_homeowners'];
}

// Fetch data for the current page
$result_recent_paid = fetchMostRecentPaidRecords($conn, $search_query, $offset, $records_per_page);
$total_homeowners = getTotalHomeowners($conn, $search_query);
$total_pages = ceil($total_homeowners / $records_per_page);

// Grouping records by homeowner
$grouped_recent_paid = [];
while ($row = $result_recent_paid->fetch_assoc()) {
    $homeowner_id = $row['homeowner_id'];
    if (!isset($grouped_recent_paid[$homeowner_id])) {
        $grouped_recent_paid[$homeowner_id] = [
            'homeowner_name' => $row['homeowner_name'],
            'address' => $row['address'],
            'record' => $row
        ];
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Most Recent Paid Billing Records</title>
    <link rel="stylesheet" href="dashbcss.css">
    <link rel="stylesheet" href="recordingadmin.css">
</head>
<body>
    <?php include 'sidebar.php'; ?>

    <div class="main-content">
        <div class="container">
            <section>
                <h2>Billing Records</h2>
                <a href="recent_paid.php" class="btn">View Recently Paid Records</a>
                <a href="deliquents.php" class="btn">View Deliquents</a>
                <a href="pending.php" class="btn">View Pending Payments</a>


                <!-- Search form -->
                <form method="GET" action="recordingadmin.php" class="search-form">
                    <input type="text" name="search" placeholder="Search by name or email" value="<?= htmlspecialchars($search_query); ?>">
                    <button type="submit">Search</button>
                </form>

                <!-- Display records if any are found -->
                <?php if ($grouped_recent_paid): ?>
                    <?php foreach ($grouped_recent_paid as $homeowner_id => $data): ?>
                        <h3><?php echo htmlspecialchars($data['homeowner_name']); ?> (ID: <?php echo htmlspecialchars($homeowner_id); ?>)</h3>
                        <p>Address: <?php echo htmlspecialchars($data['address']); ?></p>
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>Monthly Due</th>
                                    <th>Billing Date</th>
                                    <th>Due Date</th>
                                    <th>Total Amount</th>
                                    <th>Paid Date</th>
                                    <th>Payment Month</th>
                                    <th>Payment Year</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td><?php echo number_format($data['record']['monthly_due'], 2); ?></td>
                                    <td><?php echo htmlspecialchars($data['record']['billing_date']); ?></td>
                                    <td><?php echo htmlspecialchars($data['record']['due_date']); ?></td>
                                    <td><?php echo number_format($data['record']['total_amount'], 2); ?></td>
                                    <td><?php echo htmlspecialchars($data['record']['paid_date']); ?></td>
                                    <td><?php echo htmlspecialchars($data['record']['payment_month']); ?></td>
                                    <td><?php echo htmlspecialchars($data['record']['payment_year']); ?></td>
                                    <td>
                                        <a href="input_billing.php?homeowner_id=<?php echo urlencode($homeowner_id); ?>" class="btn">View</a>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                        <hr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <p>No Records found.</p>
                <?php endif; ?>

                <!-- Pagination controls -->
                <div id="pagination">
                    <?php
                    // Previous button
                    if ($current_page > 1): ?>
                        <form method="GET" action="recordingadmin.php" style="display: inline;">
                            <input type="hidden" name="search" value="<?= htmlspecialchars($search_query); ?>">
                            <input type="hidden" name="page" value="<?= $current_page - 1 ?>">
                            <button type="submit">&lt;</button>
                        </form>
                    <?php endif; ?>

                    <!-- Page input for user to change the page -->
                    <form method="GET" action="recordingadmin.php" style="display: inline;">
                        <input type="hidden" name="search" value="<?= htmlspecialchars($search_query); ?>">
                        <input type="number" name="page" value="<?= $current_page ?>" min="1" max="<?= $total_pages ?>" style="width: 50px;">
                    </form>

                    <!-- "of" text and last page link -->
                    <?php if ($total_pages > 1): ?>
                        <span>of</span>
                        <a href="?search=<?= urlencode($search_query); ?>&page=<?= $total_pages ?>" class="<?= ($current_page == $total_pages) ? 'active' : '' ?>"><?= $total_pages ?></a>
                    <?php endif; ?>

                    <!-- Next button -->
                    <?php if ($current_page < $total_pages): ?>
                        <form method="GET" action="recordingadmin.php" style="display: inline;">
                            <input type="hidden" name="search" value="<?= htmlspecialchars($search_query); ?>">
                            <input type="hidden" name="page" value="<?= $current_page + 1 ?>">
                            <button type="submit">&gt;</button>
                        </form>
                    <?php endif; ?>
                </div>
            </section>
        </div>
    </div>
</body>
</html>

<?php
$conn->close();
?>
