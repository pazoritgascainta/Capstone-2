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

// Fetching overdue records
function fetchOverdueRecords($conn) {
    $sql = "SELECT billing_id, homeowner_id, total_amount, billing_date, due_date, status, monthly_due, paid_date
            FROM billing 
            WHERE status = 'Overdue'
            ORDER BY due_date DESC"; // Order by due date
    $stmt = $conn->prepare($sql);
    $stmt->execute();
    return $stmt->get_result();
}

// Fetching overdue records
$result_overdue = fetchOverdueRecords($conn);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Overdue Billing Records</title>
    <link rel="stylesheet" href="dashbcss.css">
    <link rel="stylesheet" href="recordingadmin.css">
</head>
<body>
    <?php include 'sidebar.php'; ?>

    <div class="main-content">
        <div class="container">
            <section>
                <h2>Overdue Billing Records</h2>
                <table class="table">
                    <thead>
                        <tr>
                            <th>Billing ID</th>
                            <th>Homeowner ID</th>
                            <th>Total Amount</th>
                            <th>Billing Date</th>
                            <th>Due Date</th>
                            <th>Status</th> <!-- Status Column -->
                            <th>Monthly Due</th>
                            <th>Paid Date</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php if ($result_overdue->num_rows > 0): ?>
                        <?php while ($row = $result_overdue->fetch_assoc()): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($row['billing_id']); ?></td>
                                <td><?php echo htmlspecialchars($row['homeowner_id']); ?></td>
                                <td><?php echo number_format($row['total_amount'], 2); ?></td>
                                <td><?php echo htmlspecialchars($row['billing_date']); ?></td>
                                <td><?php echo htmlspecialchars($row['due_date']); ?></td>
                                <td><?php echo htmlspecialchars($row['status']); ?></td> <!-- Displaying Status -->
                                <td><?php echo number_format($row['monthly_due'], 2); ?></td>
                                <td><?php echo htmlspecialchars($row['paid_date']); ?></td>
                                <td>
                                    <a href="input_billing.php?homeowner_id=<?php echo urlencode($row['homeowner_id']); ?>" class="btn">View</a>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr><td colspan="9">No overdue records found.</td></tr>
                    <?php endif; ?>
                    </tbody>
                </table>
            </section>
        </div>
    </div>
</body>
</html>

<?php
$conn->close();
?>
