<?php
session_name('admin_session');
session_start();

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "homeowner";

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Get the homeowner_id from the URL
$homeowner_id = isset($_GET['homeowner_id']) ? intval($_GET['homeowner_id']) : 0;

// Search by date if provided
$search_date = isset($_GET['search']) ? $_GET['search'] : '';

// Pagination variables
$results_per_page = 10; // Adjust the number of results per page as needed
$current_page = isset($_GET['page']) ? intval($_GET['page']) : 1;
$start_from = ($current_page - 1) * $results_per_page;

// Fetch total records for pagination
$sql_total = "SELECT COUNT(*) AS total FROM billing_history WHERE homeowner_id = ?";

// If search by billing date is provided, append search condition
if (!empty($search_date)) {
    $sql_total .= " AND billing_date = ?";
}

$stmt_total = $conn->prepare($sql_total);
if (!empty($search_date)) {
    $stmt_total->bind_param("is", $homeowner_id, $search_date);
} else {
    $stmt_total->bind_param("i", $homeowner_id);
}
$stmt_total->execute();
$result_total = $stmt_total->get_result();
$total_records = $result_total->fetch_assoc()['total'];
$total_pages = ceil($total_records / $results_per_page);

// Fetch the payment history with pagination and search filter
$sql_history = "SELECT history_id, monthly_due, billing_date, due_date, total_amount, paid_date
                FROM billing_history
                WHERE homeowner_id = ?";

// If search by billing date is provided, append search condition
if (!empty($search_date)) {
    $sql_history .= " AND billing_date = ?";
}

// Order by billing_date in descending order to show the latest dates first
$sql_history .= " ORDER BY billing_date DESC LIMIT ?, ?";

$stmt_history = $conn->prepare($sql_history);
if (!empty($search_date)) {
    $stmt_history->bind_param("isii", $homeowner_id, $search_date, $start_from, $results_per_page);
} else {
    $stmt_history->bind_param("iii", $homeowner_id, $start_from, $results_per_page);
}
$stmt_history->execute();
$result_history = $stmt_history->get_result();

// Fetch distinct billing dates for suggestions
$sql_dates = "SELECT DISTINCT billing_date FROM billing_history WHERE homeowner_id = ?";
$stmt_dates = $conn->prepare($sql_dates);
$stmt_dates->bind_param("i", $homeowner_id);
$stmt_dates->execute();
$result_dates = $stmt_dates->get_result();

$billing_dates = [];
while ($row = $result_dates->fetch_assoc()) {
    $billing_dates[] = $row['billing_date'];
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payment History</title>
    <link rel="stylesheet" href="dashbcss.css">
    <link rel="stylesheet" href="recordingadmin.css">
</head>
<body>
<?php include 'sidebar.php'; ?>
<div class="main-content">
    <div class="container">
        <section>
            <h2>Payment History for Homeowner ID: <?php echo htmlspecialchars($homeowner_id); ?></h2>

            <!-- Search Form -->
            <form method="GET" action="input_billing.php" class="search-form">
                <input type="hidden" name="homeowner_id" value="<?= htmlspecialchars($homeowner_id); ?>">
                
                <!-- Search input with suggestions for billing dates -->
                <input type="text" name="search" placeholder="Search by billing date (YYYY-MM-DD)" list="billing-dates" value="<?= htmlspecialchars($search_date); ?>">
                <datalist id="billing-dates">
                    <?php foreach ($billing_dates as $date): ?>
                        <option value="<?= htmlspecialchars($date); ?>"></option>
                    <?php endforeach; ?>
                </datalist>

                <button type="submit">Search</button>
            </form>
            <a href="recordingadmin.php">Back</a>
            <a href="payment_history_admin.php">Add Previous Records</a>

            <!-- Payment History Table -->
            <table class="table">
                <thead>
                    <tr>
                        <th>History ID</th>
                        <th>Monthly Due</th>
                        <th>Billing Date</th>
                        <th>Due Date</th>
                        <th>Total Amount</th>
                        <th>Paid Date</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($result_history->num_rows > 0): ?>
                        <?php while ($row = $result_history->fetch_assoc()): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($row['history_id']); ?></td>
                                <td><?php echo number_format($row['monthly_due'], 2); ?></td>
                                <td><?php echo htmlspecialchars($row['billing_date']); ?></td>
                                <td><?php echo htmlspecialchars($row['due_date']); ?></td>
                                <td><?php echo number_format($row['total_amount'], 2); ?></td>
                                <td><?php echo htmlspecialchars($row['paid_date']); ?></td>
                            </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr><td colspan="6">No payment history found for this homeowner.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>

            <div id="pagination">
                <?php if ($total_pages > 1): ?>
                    <!-- Previous Button -->
                    <?php if ($current_page > 1): ?>
                        <form method="GET" action="input_billing.php" style="display: inline;">
                            <input type="hidden" name="homeowner_id" value="<?= htmlspecialchars($homeowner_id); ?>">
                            <input type="hidden" name="search" value="<?= htmlspecialchars($search_date); ?>">
                            <input type="hidden" name="page" value="<?= $current_page - 1 ?>">
                            <button type="submit">&lt;</button>
                        </form>
                    <?php endif; ?>

                    <!-- Current Page Info -->

                    <!-- Page Input Field -->
                    <form method="GET" action="input_billing.php" style="display: inline;">
                        <input type="hidden" name="homeowner_id" value="<?= htmlspecialchars($homeowner_id); ?>">
                        <input type="hidden" name="search" value="<?= htmlspecialchars($search_date); ?>">
                        <input type="number" name="page" value="<?= $current_page ?>" min="1" max="<?= $total_pages ?>" style="width: 50px;">
                    </form>

                    <!-- Next Button -->
                    <?php if ($current_page < $total_pages): ?>
                        <form method="GET" action="input_billing.php" style="display: inline;">
                            <input type="hidden" name="homeowner_id" value="<?= htmlspecialchars($homeowner_id); ?>">
                            <input type="hidden" name="search" value="<?= htmlspecialchars($search_date); ?>">
                            <input type="hidden" name="page" value="<?= $current_page + 1 ?>">
                            <button type="submit">&gt;</button>
                        </form>
                    <?php endif; ?>
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
