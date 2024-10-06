<?php
session_name('user_session'); 
session_start();

// Check if homeowner is logged in
if (!isset($_SESSION['homeowner_id'])) {
    header("Location: login.php");
    exit;
}

// Database connection details
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

// Query to fetch all the billing data for the homeowner
$sql_billing = "SELECT billing_date, due_date, monthly_due, status, total_amount FROM billing WHERE homeowner_id = ?";
$stmt_billing = $conn->prepare($sql_billing);
$stmt_billing->bind_param("i", $homeowner_id);
$stmt_billing->execute();
$result_billing = $stmt_billing->get_result();

// Query to fetch accepted appointments for the homeowner
$sql_accepted_appointments = "SELECT date, amount, status, purpose, amenity_id FROM accepted_appointments WHERE homeowner_id = ?";

$stmt_accepted_appointments = $conn->prepare($sql_accepted_appointments);
$stmt_accepted_appointments->bind_param("i", $homeowner_id);
$stmt_accepted_appointments->execute();
$result_accepted_appointments = $stmt_accepted_appointments->get_result();

// Initialize total appointments amount
$total_appointments_amount = 0;

// Calculate total amount for accepted appointments
while ($row = $result_accepted_appointments->fetch_assoc()) {
    $total_appointments_amount += $row['amount'];
}


// Reset the pointer of the result set to use it again for displaying in the table
$result_accepted_appointments->data_seek(0); // Move the pointer back to the start

// Define an associative array to map amenity IDs to names
$amenity_names = [
    1 => 'Clubhouse Court',
    2 => 'Townhouse Court',
    3 => 'Clubhouse Swimming Pool',
    4 => 'Townhouse Swimming Pool',
    5 => 'Consultation',
    6 => 'Bluehouse Court'
];
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Dashboard</title>
    <link rel="stylesheet" href="payment.css">
</head>
<body>
    <?php include 'usersidebar.php'; ?>

    <div class="main-content">
        <div class="container">
            <header>
                <div class="date-section">
                    <h1>Current Date: <span id="current-date"></span></h1>
                </div>
                <div class="balance-section">
                    <div class="balance">
                        <span>Total Balance</span>
                        <h2>₱<?php echo number_format($total_balance + $total_appointments_amount, 2); ?></h2>
                    </div>
                    <div class="modues">
                        <span>Monthly Dues</span>
                        <h2>₱<?php echo number_format($total_balance, 2); ?></h2>
                    </div>
                    <div class="appointments">
                        <span>Other Fees</span>
                        <h2>₱<?php echo number_format($total_appointments_amount, 2); ?></h2>
                    </div>
                </div>
            </header>

            <section class="combined-schedule">
                <h2>Payments</h2>
                <a href="payment_history_user.php">View Payment History</a>
                <table>
                <thead>
                        <tr>
                            <th colspan="5">Payment Schedule</th>
                        </tr>
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
                        // Loop through each billing record and display it in the table
                        while ($row = $result_billing->fetch_assoc()) {
                            echo "<tr>";
                            echo "<td>" . htmlspecialchars($row['billing_date']) . "</td>";
                            echo "<td>" . htmlspecialchars($row['due_date']) . "</td>";
                            echo "<td>₱" . number_format($row['monthly_due'], 2) . "</td>";
                            echo "<td>" . ucfirst($row['status']) . "</td>";
                            echo "<td>₱" . number_format($row['total_amount'], 2) . "</td>";
                            echo "</tr>";
                        }
                        ?>    
                    </tbody>
                    <thead>
                        <tr>
                            <th colspan="3">Other Fees (Appointments)</th>
                            <th colspan="2"></th>
                        </tr>
                        <tr>
                            <th>Date</th>
                            <th>Amenity</th>
                            <th>Purpose</th>
                            <th>Amount</th>
                            <th>Status</th>


                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        // Initialize an array to store the grouped appointments by date
                        $appointments_by_date = [];
                        $grand_total_amount = 0; // Variable to hold the grand total amount

                        // Loop through each accepted appointment and group by date
                        while ($row = $result_accepted_appointments->fetch_assoc()) {
                            $date = $row['date'];

                            // Initialize an array for this date if not already set
                            if (!isset($appointments_by_date[$date])) {
                                $appointments_by_date[$date] = [
                                    'amount' => 0,
                                    'status' => ucfirst($row['status']), // Capture the status
                                    'purpose' => $row['purpose'], // Capture the purpose
                                    'amenity_id' => $row['amenity_id'] // Capture the amenity_id
                                ];
                            }

                            // Add the amount to the total for this date
                            $appointments_by_date[$date]['amount'] += $row['amount'];
                        }

                        // Now loop through the grouped data and display it
                        foreach ($appointments_by_date as $date => $data) {
                            echo "<tr>";
                            echo "<td>" . htmlspecialchars($date) . "</td>"; // Display the grouped date
                            $amenity_id = $data['amenity_id'];
                            $amenity_name = isset($amenity_names[$amenity_id]) ? $amenity_names[$amenity_id] : 'Unknown Amenity';
                            
                            echo "<td>" . htmlspecialchars($amenity_name) . "</td>"; // Display the amenity name
                            echo "<td>" . htmlspecialchars($data['purpose']) . "</td>"; // Display the purpose
                            echo "<td>₱" . number_format($data['amount'], 2) . "</td>"; // Display the total amount for this date
                            echo "<td>" . $data['status'] . "</td>"; // Display the status (same for all records of the same date)
                         
                            echo "</tr>";

                            // Add to the grand total amount
                            $grand_total_amount += $data['amount'];
                        }
                        ?>
                        <!-- Total Row -->
                        <tr>
                            <td><strong>Total</strong></td>
                            <td><strong>₱<?php echo number_format($grand_total_amount, 2); ?></strong></td>
                            <td></td>
                            <td></td>
                            <td></td>
                        </tr>
                    </tbody>
                </table>
            </section>

            <section class="proof-of-payment">
                <h3>View Billing Statement</h3>
                <a href="BillingStatement.php" id="billing-link">Billing Statement for the Month of: </a>
            </section>

            <section class="proof-of-payment">
                <h3>Proof of Payment</h3>
                <form method="POST" enctype="multipart/form-data" action="upload.php">
                    <input type="file" id="upload-file" name="upload-file" required>
                    <input type="hidden" name="homeowner_id" value="<?php echo htmlspecialchars($homeowner_id); ?>">
                    <input type="hidden" name="billing_reference" id="billingReference">
                    <button type="submit" id="upload-button">Upload</button>
                </form>
            </section>
        </div>
    </div>

    <script src="payment.js"></script>
</body>
</html>
