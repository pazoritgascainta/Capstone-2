<?php
session_name('user_session'); 
session_start();

if (!isset($_SESSION['homeowner_id'])) {
    header("Location: login.php");
    exit;
}

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "homeowner";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$homeowner_id = $_SESSION['homeowner_id'];

// Query to fetch the sum of total_amount for the homeowner
$sql_total_balance = "SELECT SUM(total_amount) as total_balance FROM billing WHERE homeowner_id = ?";
$stmt_total_balance = $conn->prepare($sql_total_balance);
$stmt_total_balance->bind_param("i", $homeowner_id);
$stmt_total_balance->execute();
$result_total_balance = $stmt_total_balance->get_result();
$row_total_balance = $result_total_balance->fetch_assoc();

// Get the total balance (if no records, set to 0)
$total_balance = isset($row_total_balance['total_balance']) ? $row_total_balance['total_balance'] : 0;

// Now, query to fetch all the billing data for the homeowner
$sql_billing = "SELECT billing_date, due_date, monthly_due, status, total_amount FROM billing WHERE homeowner_id = ?";
$stmt_billing = $conn->prepare($sql_billing);
$stmt_billing->bind_param("i", $homeowner_id);
$stmt_billing->execute();
$result_billing = $stmt_billing->get_result();

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>Dashboard</title>
    <link rel="stylesheet" href="payment.css">
</head>
<body>
    <?php include 'usersidebar.php'; ?>

    <div class="main-content">
        <div class="container">
            <!-- Header section with date and balance information -->
            <header>
                <div class="date-section">
                    <h1>Current Date: <span id="current-date"></span></h1>
                </div>
                <div class="balance-section">
                    <div class="balance">
                        <span>Total Balance</span>
                        <h2>₱<?php echo number_format($total_balance, 2); ?></h2> <!-- Dynamic total balance from PHP -->
                    </div>
                    <div class="modues">
                        <span>Example</span>
                        <h2>₱00.00</h2>
                    </div>
                    <div class="appointments">
                        <span>Example</span>
                        <h2>₱00.00</h2>
                    </div>
                </div>
            </header>

            <!-- Payment Schedule section with a table displaying the billing details -->
            <section class="payment-schedule">
                <h2>Payment Schedule</h2>
                <table>
                    <thead>
                        <tr>
                            <th>Billing Date</th>
                            <th>Due Date</th>
                            <th>Monthly Due</th>
                            <th>Status</th>
                            <th>Total Amount</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php 
                        // Loop through each record and display it in the table
                        while ($row = $result_billing->fetch_assoc()) {
                            echo "<tr>";
                            echo "<td>" . $row['billing_date'] . "</td>";
                            echo "<td>" . $row['due_date'] . "</td>";
                            echo "<td>₱" . number_format($row['monthly_due'], 2) . "</td>";
                            echo "<td>" . ucfirst($row['status']) . "</td>"; // Capitalize the first letter of status
                            echo "<td>₱" . number_format($row['total_amount'], 2) . "</td>";
                            echo "</tr>";
                        }
                        ?>
                    </tbody>
                </table>
            </section>

            <!-- Section for viewing the billing statement -->
            <section class="proof-of-payment">
                <h3>View Billing Statement</h3>
                <a href="BillingStatement.php" id="billing-link">Billing Statement for the Month of: </a>
            </section>

            <!-- Section for uploading proof of payment -->
            <section class="proof-of-payment">
                <h3>Proof of Payment</h3>
                <input type="file" id="upload-file">
                <button id="upload-button">Upload</button>
            </section>
        </div>
    </div>

    <!-- Include the JavaScript for dynamic functionalities -->

    <script src="payment.js"></script>
</body>
</html>
