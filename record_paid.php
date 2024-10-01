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

function fetchPaidRecords($conn) {
    $sql = "SELECT b.billing_id, b.homeowner_id, h.name AS homeowner_name, h.address, 
            b.monthly_due, b.billing_date, b.due_date, b.total_amount, b.paid_date 
            FROM billing b 
            JOIN homeowners h ON b.homeowner_id = h.id 
            WHERE b.status = 'Paid'";

    $stmt = $conn->prepare($sql);
    $stmt->execute();
    return $stmt->get_result();
}

// Fetching paid records
$result_paid = fetchPaidRecords($conn);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <title>Billing Records</title>
    <link rel="stylesheet" href="dashbcss.css">
    <link rel="stylesheet" href="recordingadmin.css">
</head>
<body>
<?php include 'sidebar.php'; ?>
<div class="main-content">

                <a href="billingadmin.php" class="btn-action">Back to Billing Management</a>
            </section>
        </header>

        <div class="container">
            <section>
                <h2>Records of Paid Billings</h2>
                <table class="table">
                    <thead>
                        <tr>
                            <th>Billing ID</th>
                            <th>Homeowner ID</th>
                            <th>Homeowner Name</th>
                            <th>Address</th>
                            <th>Monthly Due</th>
                            <th>Billing Date</th>
                            <th>Due Date</th>
                            <th>Total Amount</th>
                            <th>Paid Date</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if ($result_paid->num_rows > 0): ?>
                            <?php while ($row = $result_paid->fetch_assoc()): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($row['billing_id']); ?></td>
                                    <td><?php echo htmlspecialchars($row['homeowner_id']); ?></td>
                                    <td><?php echo htmlspecialchars($row['homeowner_name']); ?></td>
                                    <td><?php echo htmlspecialchars($row['address']); ?></td>
                                    <td><?php echo number_format($row['monthly_due'], 2); ?></td>
                                    <td><?php echo htmlspecialchars($row['billing_date']); ?></td>
                                    <td><?php echo htmlspecialchars($row['due_date']); ?></td>
                                    <td><?php echo number_format($row['total_amount'], 2); ?></td>
                                    <td><?php echo htmlspecialchars($row['paid_date']); ?></td>
                                </tr>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <tr><td colspan="9">No paid records found.</td></tr>
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
