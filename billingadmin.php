<?php
session_name('admin_session');
session_start();

// Database connection
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "homeowner";

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Initialize message
$status_message = "";

// Handle update of monthly due and billing date
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['billing_id'])) {
    $billing_id = intval($_POST['billing_id']);
    $monthly_due = floatval($_POST['monthly_due']);
    $status = $_POST['status'];
    $billing_date = $_POST['billing_date'];

    // Update the billing record
    $sql_update = "UPDATE billing SET monthly_due = ?, billing_date = ?, status = ? WHERE billing_id = ?";
    $stmt_update = $conn->prepare($sql_update);
    if ($stmt_update) {
        $stmt_update->bind_param("dssi", $monthly_due, $billing_date, $status, $billing_id);
        if ($stmt_update->execute()) {
            $status_message = "Billing record updated successfully!";
        } else {
            $status_message = "Failed to update billing record: " . $stmt_update->error;
        }
        $stmt_update->close();
    } else {
        $status_message = "Prepare statement failed: " . $conn->error;
    }

    // Accumulate total amount based on monthly dues
    if ($status !== 'Paid') {
        $sql_get_current_due = "SELECT SUM(monthly_due) AS total_due FROM billing WHERE homeowner_id = (SELECT homeowner_id FROM billing WHERE billing_id = ?)";
        $stmt_get_current_due = $conn->prepare($sql_get_current_due);
        if ($stmt_get_current_due) {
            $stmt_get_current_due->bind_param("i", $billing_id);
            $stmt_get_current_due->execute();
            $stmt_get_current_due->bind_result($total_due);
            $stmt_get_current_due->fetch();
            $stmt_get_current_due->close();

            // Update total amount
            $sql_update_total = "UPDATE billing SET total_amount = ? WHERE billing_id = ?";
            $stmt_update_total = $conn->prepare($sql_update_total);
            if ($stmt_update_total) {
                $stmt_update_total->bind_param("di", $total_due, $billing_id);
                if ($stmt_update_total->execute()) {
                    // Total amount updated
                } else {
                    $status_message = "Failed to update total amount: " . $stmt_update_total->error;
                }
                $stmt_update_total->close();
            }
        }
    } else {
        // Reset the total amount if status is paid
        $sql_reset_amount = "UPDATE billing SET total_amount = 0.00 WHERE billing_id = ?";
        $stmt_reset_amount = $conn->prepare($sql_reset_amount);
        if ($stmt_reset_amount) {
            $stmt_reset_amount->bind_param("i", $billing_id);
            if ($stmt_reset_amount->execute()) {
                // Total amount reset
            }
            $stmt_reset_amount->close();
        }
    }

    $_SESSION['message'] = $status_message;
    header("Location: billingadmin.php");
    exit();
}

// Fetch billing records
$sql_billing_records = "
    SELECT b.billing_id, b.homeowner_id, h.name AS homeowner_name, h.address, b.total_amount, b.billing_date, b.due_date, b.status, b.monthly_due
    FROM billing b
    JOIN homeowners h ON b.homeowner_id = h.id
";

$result_billing = $conn->query($sql_billing_records);

// Check if query was successful
if (!$result_billing) {
    die("Query failed: " . $conn->error);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Billing Management</title>
    <link rel="stylesheet" href="billcss.css">
</head>
<body>
    <!-- Sidebar -->
    <?php include 'sidebar.php'; ?>

    <div class="main-content">
        <header>
            <h1>Billing Management</h1>
            <section>
                <a href="create_billing.php" class="btn-action">Create Billing Record</a>
            </section>
        </header>

        <div class="container">
            <!-- Display message -->
            <div class="message">
                <?php if (isset($_SESSION['message'])): ?>
                    <p><?php echo $_SESSION['message']; unset($_SESSION['message']); ?></p>
                <?php else: ?>
                    <p>No current messages.</p>
                <?php endif; ?>
            </div>

            <!-- Display Billing Records -->
            <section>
                <h2>Existing Billing Records</h2>
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
                            <th>Status</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if ($result_billing->num_rows > 0): ?>
                            <?php while ($row = $result_billing->fetch_assoc()): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($row['billing_id']); ?></td>
                                    <td><?php echo htmlspecialchars($row['homeowner_id']); ?></td>
                                    <td><?php echo htmlspecialchars($row['homeowner_name']); ?></td>
                                    <td><?php echo htmlspecialchars($row['address']); ?></td>
                                    <td>
                                        <form method="POST" action="billingadmin.php" style="display:inline;">
                                            <input type="hidden" name="billing_id" value="<?php echo htmlspecialchars($row['billing_id']); ?>">
                                            <input type="number" step="0.01" name="monthly_due" value="<?php echo number_format($row['monthly_due'], 2); ?>" required>
                                    </td>
                                    <td>
                                            <input type="date" name="billing_date" value="<?php echo htmlspecialchars($row['billing_date']); ?>" required>
                                    </td>
                                    <td><?php echo htmlspecialchars($row['due_date']); ?></td>
                                    <td><?php echo number_format($row['total_amount'], 2); ?></td>
                                    <td>
                                        <form method="POST" action="billingadmin.php" style="display:inline;">
                                            <select name="status" required>
                                                <option value="Pending" <?php if ($row['status'] == 'Pending') echo 'selected'; ?>>Pending</option>
                                                <option value="Paid" <?php if ($row['status'] == 'Paid') echo 'selected'; ?>>Paid</option>
                                                <option value="Overdue" <?php if ($row['status'] == 'Overdue') echo 'selected'; ?>>Overdue</option>
                                            </select>
                                            <input type="hidden" name="billing_id" value="<?php echo htmlspecialchars($row['billing_id']); ?>">
                                    </td>
                                    <td>
                                        <input type="submit" value="Update" class="btn-action" aria-label="Update Billing Record">
                                        </form>
                                        <form method="GET" action="BillingStatement.php" style="display:inline;">
                                            <input type="hidden" name="billing_id" value="<?php echo htmlspecialchars($row['billing_id']); ?>">
                                            <input type="submit" value="View" class="btn-action" aria-label="View Billing for <?php echo htmlspecialchars($row['homeowner_name']); ?>">
                                        </form>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <tr><td colspan="10">No records found.</td></tr>
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
