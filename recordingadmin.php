<?php
session_name('admin_session'); // Set a unique session name for admins
session_start();

// Check if admin is logged in
if (!isset($_SESSION['admin_id'])) {
    header("Location: admin_login.php");
    exit;
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <title>Recording</title>
    <link rel="stylesheet" href="dashbcss.css">
    <link rel="stylesheet" href="recordingadmin.css">
</head>
<body>
<?php include 'sidebar.php'; ?>
<div class="main-content">
    <div class="container">
    <h1>St. Monique Recording</h1>
        <div class="Recordingsidebar">
            <div class="menu-item" onclick="showContent('billing')">Billing Records</div>
            <div class="menu-item" onclick="showContent('payment')">Payment Records</div>
            <div class="menu-item" onclick="showContent('purchase')">Purchase Records</div>
            <div class="menu-item" onclick="showContent('paid')">Paid Homeowners</div>
            <div class="menu-item" onclick="showContent('unpaid')">Unpaid Homeowners</div>
        </div>
        <div class="content">
            <div id="billing" class="content-section">Billing
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
            </table>
            </div>
            <div id="payment" class="content-section">Payment
            <table class="table">
                    <thead>
                        <tr>
                            <th>Payment ID</th>
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
            </table>
            </div>
            <div id="purchase" class="content-section">Purchase
            <table class="table">
                    <thead>
                        <tr>
                            <th>Purchase ID</th>
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
            </table>
            </div>
            <div id="paid" class="content-section">Paid
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
                        </tr>
                    </thead>
            </table>
            </div>
            <div id="unpaid" class="content-section">Unpaid
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
            </table>
            </div>
        </div>
    </div>
    <script src="Recording.js"></script>
</body>
</html>