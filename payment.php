<?php
session_name('user_session'); 
session_start();


if (!isset($_SESSION['homeowner_id'])) {
    header("Location: login.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <title>Dashboard</title>
    <link rel="stylesheet" href="payment.css">
    <style>

    </style>
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
                    <h2>₱500.00</h2>
                </div>
                <div class="example">
                    <span>Example</span>
                    <h2>₱00.00</h2>
                </div>
                <div class="example">
                    <span>Example</span>
                    <h2>₱00.00</h2>
                </div>
            </div>
        </header>

        <section class="payment-schedule">
            <h2>Payment Schedule</h2>
            <input type="text" placeholder="Type a name to search" id="search-bar">
            <button id="refresh-button">Refresh Rates</button>
            <table>
                <thead>
                    <tr>
                        <th>Month</th>
                        <th>Due</th>
                        <th>Payment</th>
                        <th>type</th>
                        <th>Payment Status</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>January</td>
                        <td>20,000,000</td>
                        <td>10,000,000</td>
                        <td>Internship</td>
                        <td><span class="paid">Paid</span></td>
                    </tr>
                    <tr>
                        <td>February</td>
                        <td>40,000,000</td>
                        <td>30,000,000</td>
                        <td>Contractor</td>
                        <td><span class="paid">Paid</span></td>
                    </tr>
                    <tr>
                        <td>March</td>
                        <td>20,000,000</td>
                        <td>10,000,000</td>
                        <td>Internship</td>
                        <td><span class="paid">Paid</span></td>
                    </tr>
                    <tr>
                        <td>April</td>
                        <td>40,000,000</td>
                        <td>30,000,000</td>
                        <td>Contractor</td>
                        <td><span class="unpaid">Unpaid</span></td>
                    </tr>
                    <tr>
                        <td>May</td>
                        <td>20,000,000</td>
                        <td>10,000,000</td>
                        <td>Internship</td>
                        <td><span class="unpaid">Unpaid</span></td>
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
            <input type="file" id="upload-file">
            <button id="upload-button">Upload</button>
        </section>
    </div> </div>
    <script src="payment.js"></script>
 
</body>
</html>
