<?php
session_name('user_session');
session_start();

// Check if homeowner is logged in
if (!isset($_SESSION['homeowner_id'])) {
    header("Location: login.php");
    exit;
}

// Database connection
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "homeowner";

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Initialize homeowner ID from session
$homeowner_id = intval($_SESSION['homeowner_id']);

// Search functionality
$search_date = isset($_GET['search']) ? $_GET['search'] : '';

// Fetch distinct billing dates for the datalist
$sql_dates = "SELECT DISTINCT billing_date FROM billing_history WHERE homeowner_id = ? ORDER BY billing_date DESC";
$stmt_dates = $conn->prepare($sql_dates);
$stmt_dates->bind_param("i", $homeowner_id);
$stmt_dates->execute();
$result_dates = $stmt_dates->get_result();
$billing_dates = [];
while ($row_dates = $result_dates->fetch_assoc()) {
    $billing_dates[] = $row_dates['billing_date'];
}

// Pagination setup
$results_per_page = 10; // Adjust results per page as needed
$current_page = isset($_GET['page']) ? intval($_GET['page']) : 1;
$start_from = ($current_page - 1) * $results_per_page;

// Fetch total records for pagination with or without search
if ($search_date) {
    $sql_total_payments = "SELECT COUNT(*) AS total FROM billing_history WHERE homeowner_id = ? AND billing_date = ?";
    $stmt_total_payments = $conn->prepare($sql_total_payments);
    $stmt_total_payments->bind_param("is", $homeowner_id, $search_date);
} else {
    $sql_total_payments = "SELECT COUNT(*) AS total FROM billing_history WHERE homeowner_id = ?";
    $stmt_total_payments = $conn->prepare($sql_total_payments);
    $stmt_total_payments->bind_param("i", $homeowner_id);
}
$stmt_total_payments->execute();
$result_total_payments = $stmt_total_payments->get_result();
$total_records_payments = $result_total_payments->fetch_assoc()['total'];
$total_pages_payments = ceil($total_records_payments / $results_per_page);

// Fetch payment history with or without search
if ($search_date) {
    $sql_payments = "SELECT billing_date, monthly_due, total_amount, status, paid_date 
                     FROM billing_history 
                     WHERE homeowner_id = ? AND billing_date = ?
                     ORDER BY billing_date DESC 
                     LIMIT ?, ?";
    $stmt_payments = $conn->prepare($sql_payments);
    $stmt_payments->bind_param("isii", $homeowner_id, $search_date, $start_from, $results_per_page);
} else {
    $sql_payments = "SELECT billing_date, monthly_due, total_amount, status, paid_date 
                     FROM billing_history 
                     WHERE homeowner_id = ? 
                     ORDER BY billing_date DESC 
                     LIMIT ?, ?";
    $stmt_payments = $conn->prepare($sql_payments);
    $stmt_payments->bind_param("iii", $homeowner_id, $start_from, $results_per_page);
}
$stmt_payments->execute();
$result_payments = $stmt_payments->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payment History</title>
    <link rel="stylesheet" href="payment.css">
</head>
<body>
    <?php include 'usersidebar.php'; ?>

    <div class="main-content">
        <h1>Your Payment History</h1>

        <!-- Search bar -->
        <form method="GET" action="payment_history_user.php" class="search-form">
    <input type="text" name="search" placeholder="Search by billing date (YYYY-MM-DD)" list="billing-dates" value="<?= htmlspecialchars($search_date); ?>">
    <datalist id="billing-dates">
        <?php foreach ($billing_dates as $date): ?>
            <option value="<?= htmlspecialchars($date); ?>"></option>
        <?php endforeach; ?>
    </datalist>
    <button type="submit">Search</button>
</form>

<a href="uploaded_payment.php">View Uploaded Payments</a>

        <table class="table">
            <thead>
                <tr>
                    <th>Billing Date</th>
                    <th>Monthly Due</th>
                    <th>Total Amount</th>
                    <th>Status</th>
                    <th>Paid Date</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($result_payments->num_rows > 0): ?>
                    <?php while ($row = $result_payments->fetch_assoc()): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($row['billing_date']); ?></td>
                            <td><?php echo number_format($row['monthly_due'], 2); ?></td>
                            <td><?php echo number_format($row['total_amount'], 2); ?></td>
                            <td><?php echo htmlspecialchars($row['status']); ?></td>
                            <td><?php echo htmlspecialchars($row['paid_date']); ?></td>
                        </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr><td colspan="5">No payment records found.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>

        <div id="pagination">
            <?php if ($total_pages_payments > 1): ?>
                <!-- Previous Button -->
                <?php if ($current_page > 1): ?>
                    <form method="GET" action="payment_history_user.php" style="display: inline;">
                        <input type="hidden" name="search" value="<?= htmlspecialchars($search_date); ?>">
                        <input type="hidden" name="page" value="<?php echo $current_page - 1; ?>">
                        <button type="submit">&lt;</button>
                    </form>
                <?php endif; ?>

                <!-- Page Input Field -->
                <form method="GET" action="payment_history_user.php" style="display: inline;">
                    <input type="hidden" name="search" value="<?= htmlspecialchars($search_date); ?>">
                    <input type="number" name="page" value="<?php echo $current_page; ?>" min="1" max="<?php echo $total_pages_payments; ?>" style="width: 50px;">
                </form>

                <!-- Next Button -->
                <?php if ($current_page < $total_pages_payments): ?>
                    <form method="GET" action="payment_history_user.php" style="display: inline;">
                        <input type="hidden" name="search" value="<?= htmlspecialchars($search_date); ?>">
                        <input type="hidden" name="page" value="<?php echo $current_page + 1; ?>">
                        <button type="submit">&gt;</button>
                    </form>
                <?php endif; ?>
            <?php endif; ?>
        </div>
    </div>

    <?php
    // Close the connection
    $conn->close();
    ?>
</body>
</html>
