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

function handlePostRequest($conn) {
    if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['billing_id'])) {
        $billing_id = intval($_POST['billing_id']);
        $monthly_due = floatval($_POST['monthly_due']);
        $status = $_POST['status'];
        $billing_date = $_POST['billing_date'];

        // Calculate the new due date (one month from the billing date)
        $billing_date_obj = new DateTime($billing_date);
        $billing_date_obj->modify('+1 month');
        $due_date = $billing_date_obj->format('Y-m-d');

        $conn->begin_transaction();
        try {
            // Update the billing record
            $sql_update = "UPDATE billing SET monthly_due = ?, billing_date = ?, due_date = ?, status = ? WHERE billing_id = ?";
            if ($stmt_update = $conn->prepare($sql_update)) {
                $stmt_update->bind_param("dsssi", $monthly_due, $billing_date, $due_date, $status, $billing_id);
                if (!$stmt_update->execute()) {
                    throw new Exception("Failed to update billing record: " . $stmt_update->error);
                }
                $stmt_update->close();
            } else {
                throw new Exception("Prepare statement failed: " . $conn->error);
            }

            // Handle status-specific updates
            if ($status === 'Paid') {
                handlePaidStatus($conn, $billing_id);
            } else if ($status === 'Pending') {
                handlePendingStatus($conn, $billing_id, $monthly_due);
            }

            $conn->commit();
            $_SESSION['message'] = "Billing record updated successfully!";
        } catch (Exception $e) {
            $conn->rollback();
            $_SESSION['message'] = $e->getMessage();
        }

        header("Location: billingadmin.php");
        exit();
    }
}

function handlePaidStatus($conn, $billing_id) {
    // Reset the total amount for the current record and record the paid date
    $sql_update_total = "UPDATE billing SET total_amount = 0.00, paid_date = ? WHERE billing_id = ?";
    if ($stmt_update_total = $conn->prepare($sql_update_total)) {
        $paid_date = date('Y-m-d');
        $stmt_update_total->bind_param("si", $paid_date, $billing_id);
        if (!$stmt_update_total->execute()) {
            throw new Exception("Failed to reset total amount: " . $stmt_update_total->error);
        }
        $stmt_update_total->close();
    } else {
        throw new Exception("Prepare statement failed: " . $conn->error);
    }
}

function handlePendingStatus($conn, $billing_id, $monthly_due) {
    // Check if the record is overdue
    $sql_check_overdue = "SELECT due_date FROM billing WHERE billing_id = ?";
    if ($stmt_check_overdue = $conn->prepare($sql_check_overdue)) {
        $stmt_check_overdue->bind_param("i", $billing_id);
        $stmt_check_overdue->execute();
        $result_check_overdue = $stmt_check_overdue->get_result();
        $row = $result_check_overdue->fetch_assoc();
        $due_date = $row['due_date'];
        
        $today = date('Y-m-d');
        if ($due_date < $today) {
            // Update total amount for overdue records
            updateOverdueAmounts($conn);
        }
        $stmt_check_overdue->close();
    } else {
        throw new Exception("Prepare statement failed: " . $conn->error);
    }
}

function updateOverdueAmounts($conn) {
    $today = date('Y-m-d');

    // Query to select overdue records
    $sql_overdue = "
        SELECT billing_id, due_date, monthly_due, total_amount
        FROM billing
        WHERE due_date < ? AND status <> 'Paid'
    ";

    $stmt_overdue = $conn->prepare($sql_overdue);
    if ($stmt_overdue) {
        $stmt_overdue->bind_param("s", $today);
        $stmt_overdue->execute();
        $result_overdue = $stmt_overdue->get_result();

        // Process overdue records
        while ($row = $result_overdue->fetch_assoc()) {
            $billing_id = $row['billing_id'];
            $due_date = $row['due_date'];
            $monthly_due = $row['monthly_due'];
            $total_amount = $row['total_amount'];

            // Calculate the number of overdue months
            $due_date_obj = new DateTime($due_date);
            $today_obj = new DateTime($today);
            $interval = $due_date_obj->diff($today_obj);
            $overdue_months = $interval->m + ($interval->y * 12) + 1; // Adjust to account for the current month

            // Calculate the new total amount considering overdue months
            $new_total_amount = $monthly_due * $overdue_months;

            // Update the billing record with the new total amount
            $sql_update_total = "UPDATE billing SET total_amount = ? WHERE billing_id = ?";
            $stmt_update_total = $conn->prepare($sql_update_total);
            if ($stmt_update_total) {
                $stmt_update_total->bind_param("di", $new_total_amount, $billing_id);
                $stmt_update_total->execute();
                $stmt_update_total->close();
            } else {
                throw new Exception("Prepare statement failed: " . $conn->error);
            }
        }
        $stmt_overdue->close();
    } else {
        throw new Exception("Prepare statement failed: " . $conn->error);
    }
}

function fetchBillingRecords($conn) {
    $sql_billing_records = "
        SELECT b.billing_id, b.homeowner_id, h.name AS homeowner_name, h.address, b.total_amount, b.billing_date, b.due_date, b.status, b.monthly_due
        FROM billing b
        JOIN homeowners h ON b.homeowner_id = h.id
    ";
    $result_billing = $conn->query($sql_billing_records);
    if (!$result_billing) {
        die("Query failed: " . $conn->error);
    }
    return $result_billing;
}

// Main execution
handlePostRequest($conn);
updateOverdueAmounts($conn);
$result_billing = fetchBillingRecords($conn);
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
                            <th>Total Balance</th>
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
