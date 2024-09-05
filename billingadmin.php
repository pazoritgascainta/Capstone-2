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
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Billing Management</title>
    <link rel="stylesheet" href="billcss.css">
</head>
<body>
    <!-- Sidebar -->
    <?php include 'sidebar.php'; ?>

    <div class="main-content">
        <div class="container">
            
            <h1>Billing Management</h1>

            <!-- Display Success/Error Message (for static purposes) -->
            <div class="message">
                <!-- Replace with static message or leave empty if you prefer -->
                <p>Message goes here.</p>
            </div>

            <!-- Create Billing Button -->
            <h2>Create New Billing Record</h2>
            <br>
            <a href="create_billing.php" class="btn-action">Create Billing Record</a>
            <br></br>

            <!-- Display Billing Records (Static Example) -->
            <h2>Existing Billing Records</h2>
            <table>
                <thead>
                    <tr>
                        <th>Billing ID</th>
                        <th>Homeowner ID</th>
                        <th>Homeowner Name</th>
                        <th>Total Amount</th>
                        <th>Billing Date</th>
                        <th>Due Date</th>
                        <th>Statuses</th>
                        <th>Types</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>1</td>
                        <td>101</td>
                        <td>John Doe</td>
                        <td>150.00</td>
                        <td>2024-08-15</td>
                        <td>2024-09-15</td>
                        <td>Paid</td>
                        <td>Electric, Water</td>
                        <td>
                            <!-- View Billing Button -->
                            <form method="GET" action="view_billing.php" style="display:inline;">
                                <input type="hidden" name="billing_id" value="1">
                                <input type="submit" value="View" class="btn-action">
                            </form>
                        </td>
                    </tr>
                    <!-- Add more rows as needed -->
                </tbody>
            </table>
        </div>
    </div>
</body>
</html>
