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

// Function to insert new billing record when a payment is made
function recordPayment($conn, $homeowner_id, $monthly_due, $billing_date, $due_date) {
    $sql_insert = "INSERT INTO billing (homeowner_id, monthly_due, billing_date, due_date, status, total_amount, paid_date) 
                   VALUES (?, ?, ?, ?, 'Paid', ?, ?)";
    $stmt_insert = $conn->prepare($sql_insert);
    
    if ($stmt_insert) {
        $paid_date = date('Y-m-d'); // Set current date as paid date
        // Insert the new record
        $stmt_insert->bind_param("idssds", $homeowner_id, $monthly_due, $billing_date, $due_date, $monthly_due, $paid_date);
        if (!$stmt_insert->execute()) {
            throw new Exception("Failed to insert billing record: " . $stmt_insert->error);
        }
        $stmt_insert->close();
        
        // Move the overdue bill to the history table
        moveOverdueToHistory($conn, $homeowner_id, $monthly_due, $billing_date, $due_date, $paid_date);
    } else {
        throw new Exception("Prepare statement failed: " . $conn->error);
    }
}

// Function to move overdue records to a history table
function moveOverdueToHistory($conn, $homeowner_id, $monthly_due, $billing_date, $due_date, $paid_date) {
    $sql_move = "INSERT INTO billing_history (homeowner_id, monthly_due, billing_date, due_date, total_amount, paid_date) 
                 VALUES (?, ?, ?, ?, ?, ?)";
    $stmt_move = $conn->prepare($sql_move);
    
    if ($stmt_move) {
        $total_amount = $monthly_due; // Assuming total amount is the same as monthly due
        $stmt_move->bind_param("idssds", $homeowner_id, $monthly_due, $billing_date, $due_date, $total_amount, $paid_date);
        if (!$stmt_move->execute()) {
            throw new Exception("Failed to move billing record to history: " . $stmt_move->error);
        }
        $stmt_move->close();
    } else {
        throw new Exception("Prepare statement for moving record failed: " . $conn->error);
    }
}

// Fetching paid records
function fetchPaidRecords($conn) {
    $sql = "SELECT b.billing_id, b.homeowner_id, h.name AS homeowner_name, h.address, 
            b.monthly_due, b.billing_date, b.due_date, b.total_amount, b.paid_date, 
            MONTH(b.paid_date) AS payment_month, YEAR(b.paid_date) AS payment_year
            FROM billing b 
            JOIN homeowners h ON b.homeowner_id = h.id 
            WHERE b.status = 'Paid' 
            ORDER BY b.paid_date DESC"; // Order by paid date
    $stmt = $conn->prepare($sql);
    $stmt->execute();
    return $stmt->get_result();
}

// Handle payment form submission
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['payment_submit'])) {
    // Extract values from the form
    $homeowner_id = intval($_POST['homeowner_id']); // Assuming you have this in the form
    $monthly_due = floatval($_POST['monthly_due']); // Monthly due amount
    $billing_date = $_POST['billing_date']; // Billing date
    $due_date = $_POST['due_date']; // Due date

    // Record the payment
    try {
        recordPayment($conn, $homeowner_id, $monthly_due, $billing_date, $due_date);
        $_SESSION['message'] = "Payment recorded successfully!";
    } catch (Exception $e) {
        $_SESSION['message'] = $e->getMessage();
    }

    header("Location: billingadmin.php"); // Redirect after recording payment
    exit();
}

// Fetching paid records
$result_paid = fetchPaidRecords($conn);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Billing Records</title>
    <link rel="stylesheet" href="dashbcss.css">
    <link rel="stylesheet" href="recordingadmin.css">
</head>
<body>
    <?php include 'sidebar.php'; ?>

    <div class="main-content">
        <div class="container">
            <section>
                <h2>Recently Paid</h2>
                <?php if (isset($_SESSION['message'])): ?>
                    <div class="alert">
                        <?php echo htmlspecialchars($_SESSION['message']); ?>
                        <?php unset($_SESSION['message']); // Clear message after displaying ?>
                    </div>
                <?php endif; ?>
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
                            <th>Payment Month</th>
                            <th>Payment Year</th>
                            <th>Actions</th>
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
                                <td><?php echo htmlspecialchars($row['payment_month']); ?></td>
                                <td><?php echo htmlspecialchars($row['payment_year']); ?></td>
                                <td>
                                    <a href="input_billing.php?homeowner_id=<?php echo urlencode($row['homeowner_id']); ?>" class="btn">View</a>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr><td colspan="12">No paid records found.</td></tr>
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
