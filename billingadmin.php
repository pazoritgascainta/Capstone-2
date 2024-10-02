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

$search_query = isset($_GET['search']) ? intval($_GET['search']) : '';

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
    // Get the current month's due amount and other details
    $sql_get_due = "SELECT homeowner_id, monthly_due, billing_date, due_date FROM billing WHERE billing_id = ?";
    if ($stmt_get_due = $conn->prepare($sql_get_due)) {
        $stmt_get_due->bind_param("i", $billing_id);
        $stmt_get_due->execute();
        $stmt_get_due->bind_result($homeowner_id, $monthly_due, $billing_date, $due_date);
        $stmt_get_due->fetch();
        $stmt_get_due->close();
    } else {
        throw new Exception("Prepare statement failed: " . $conn->error);
    }

    // Reset the total amount for the current record and record the paid date
    $sql_update_total = "UPDATE billing SET total_amount = ?, paid_date = ? WHERE billing_id = ?";
    if ($stmt_update_total = $conn->prepare($sql_update_total)) {
        $paid_date = date('Y-m-d');
        // Keep the monthly due for the current month in the total amount
        $stmt_update_total->bind_param("dsi", $monthly_due, $paid_date, $billing_id);
        if (!$stmt_update_total->execute()) {
            throw new Exception("Failed to reset total amount: " . $stmt_update_total->error);
        }
        $stmt_update_total->close();
    } else {
        throw new Exception("Prepare statement failed: " . $conn->error);
    }

    // Update billing date and due date for the next month
    $next_billing_date = new DateTime($paid_date);
    $next_billing_date->modify('first day of next month'); // First day of next month
    $next_due_date = clone $next_billing_date;
    $next_due_date->modify('+1 month'); // Next due date is one month after the next billing date

    // Update billing details with the new dates
    $sql_update_dates = "UPDATE billing SET billing_date = ?, due_date = ? WHERE billing_id = ?";
    if ($stmt_update_dates = $conn->prepare($sql_update_dates)) {
        $stmt_update_dates->bind_param("ssi", $next_billing_date->format('Y-m-d'), $next_due_date->format('Y-m-d'), $billing_id);
        if (!$stmt_update_dates->execute()) {
            throw new Exception("Failed to update billing dates: " . $stmt_update_dates->error);
        }
        $stmt_update_dates->close();
    } else {
        throw new Exception("Prepare statement failed: " . $conn->error);
    }

    // Optionally update the status of the billing record to 'Paid'
    $sql_update_status = "UPDATE billing SET status = 'Paid' WHERE billing_id = ?";
    if ($stmt_update_status = $conn->prepare($sql_update_status)) {
        $stmt_update_status->bind_param("i", $billing_id);
        if (!$stmt_update_status->execute()) {
            throw new Exception("Failed to update billing record status: " . $stmt_update_status->error);
        }
        $stmt_update_status->close();
    } else {
        throw new Exception("Prepare statement failed: " . $conn->error);
    }

    // Calculate the number of months from billing date to today
    $billing_date_obj = new DateTime($billing_date);
    $current_date = new DateTime();
    $interval = $billing_date_obj->diff($current_date);
    $months_passed = $interval->m + ($interval->y * 12); // Total months from billing date to today

    // Insert monthly records into billing_history
    for ($i = 0; $i <= $months_passed; $i++) {
        $month_record_date = clone $billing_date_obj; // Clone the billing date object
        $month_record_date->modify("+$i month"); // Move forward by $i months
        $formatted_date = $month_record_date->format('Y-m-d');

        // Insert the record into billing_history
        $sql_insert_history = "INSERT INTO billing_history (homeowner_id, monthly_due, billing_date, due_date, status, total_amount, paid_date) 
                               VALUES (?, ?, ?, ?, 'Paid', ?, ?)";
        if ($stmt_insert_history = $conn->prepare($sql_insert_history)) {
            $total_amount = $monthly_due; // Assuming total amount is the same as monthly due
            $stmt_insert_history->bind_param("idssds", $homeowner_id, $monthly_due, $formatted_date, $due_date, $total_amount, $paid_date);
            if (!$stmt_insert_history->execute()) {
                throw new Exception("Failed to insert billing record into history: " . $stmt_insert_history->error);
            }
            $stmt_insert_history->close();
        } else {
            throw new Exception("Prepare statement failed: " . $conn->error);
        }
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

function fetchBillingRecords($conn, $offset, $limit, $search_query = '') {
    $sql_billing_records = "
        SELECT b.billing_id, b.homeowner_id, h.name AS homeowner_name, h.address, b.total_amount, b.billing_date, b.due_date, b.status, b.monthly_due
        FROM billing b
        JOIN homeowners h ON b.homeowner_id = h.id
        WHERE (b.homeowner_id = ? OR ? = '')
        LIMIT ?, ?
    ";
    $stmt = $conn->prepare($sql_billing_records);
    $stmt->bind_param("iiii", $search_query, $search_query, $offset, $limit);
    $stmt->execute();
    return $stmt->get_result();
}

function getTotalPages($conn, $limit, $search_query = '') {
    $sql_count = "SELECT COUNT(*) AS total FROM billing WHERE (homeowner_id = ? OR ? = '')";
    $stmt_count = $conn->prepare($sql_count);
    $stmt_count->bind_param("ii", $search_query, $search_query);
    $stmt_count->execute();
    $result = $stmt_count->get_result();
    $row = $result->fetch_assoc();
    return ceil($row['total'] / $limit);
}

// Main execution
handlePostRequest($conn);

// Pagination logic
$current_page = isset($_GET['page']) ? intval($_GET['page']) : 1;
$limit = 10; // Number of records per page
$total_pages = getTotalPages($conn, $limit, $search_query);
$offset = ($current_page - 1) * $limit;

$result_billing = fetchBillingRecords($conn, $offset, $limit, $search_query);
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
        <form method="GET" action="billingadmin.php" class="search-form">
                <input type="number" name="search" value="<?= htmlspecialchars($search_query) ?>" placeholder="Search by Homeowner ID...">
                <button type="submit">Search</button>
            </form>
        <div class="container">
            <!-- Display message -->
            <div class="message">
                <?php if (isset($_SESSION['message'])): ?>
                    <p><?php echo $_SESSION['message']; unset($_SESSION['message']); ?></p>
                <?php else: ?>
                    <p>No current messages.</p>
                <?php endif; ?>
            </div>
            <fieldset> 
    <legend> Send SMS </legend>
    <h1>Contact Homeowner</h1>
    <form method="post" action="send.php"> 
        <div>
            <textarea class="phoneNumbers" name="phoneNumbers" required></textarea>
            <span>Phone Numbers</span>
        </div>
        <div>
            <textarea class="message" name="message" required></textarea>
            <span>Message</span>
        </div>
        <button type="submit">Send</button>
    </form>
</fieldset>




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
    <form method="GET" action="payment_history_admin.php" style="display:inline;">
        <input type="hidden" name="billing_id" value="<?php echo htmlspecialchars($row['billing_id']); ?>">
        <input type="hidden" name="homeowner_id" value="<?php echo htmlspecialchars($row['homeowner_id']); ?>"> <!-- Add this line -->
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
                
                <!-- Pagination -->
                <div id="pagination">
                    <?php
                    $total_pages = max($total_pages, 1); // Ensure there's at least 1 page
                    $input_page = $current_page; // Default to the current page for the input

                    // Previous button
                    if ($current_page > 1): ?>
                        <form method="GET" action="billingadmin.php" style="display: inline;">
                            <input type="hidden" name="search" value="<?= htmlspecialchars($search_query); ?>">
                            <input type="hidden" name="page" value="<?= $current_page - 1 ?>">
                            <button type="submit">&lt;</button>
                        </form>
                    <?php endif; ?>

                    <!-- Page input for user to change the page -->
                    <form method="GET" action="billingadmin.php" style="display: inline;">
                        <input type="hidden" name="search" value="<?= htmlspecialchars($search_query); ?>">
                        <input type="number" name="page" value="<?= $input_page ?>" min="1" max="<?= $total_pages ?>" style="width: 50px;">
                    </form>

                    <!-- "of" text and last page link -->
                    <?php if ($total_pages > 1): ?>
                        <span>of</span>
                        <a href="?search=<?= urlencode($search_query); ?>&page=<?= $total_pages ?>" class="<?= ($current_page == $total_pages) ? 'active' : '' ?>"><?= $total_pages ?></a>
                    <?php endif; ?>

                    <!-- Next button -->
                    <?php if ($current_page < $total_pages): ?>
                        <form method="GET" action="billingadmin.php" style="display: inline;">
                            <input type="hidden" name="search" value="<?= htmlspecialchars($search_query); ?>">
                            <input type="hidden" name="page" value="<?= $current_page + 1 ?>">
                            <button type="submit">></button>
                        </form>
                    <?php endif; ?>
                </div>
                <!-- End of Pagination -->
                
            </section>
        </div>
    </div>
</body>
</html>

<?php
$conn->close();
?>
