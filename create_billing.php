<?php
session_name('admin_session');
session_start();

// Check if admin is logged in
if (!isset($_SESSION['admin_id'])) {
    header("Location: admin_login.php");
    exit;
}

// Include database connection
require 'db_connection.php'; // Ensure you have this file to connect to the database

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['homeowner_id'])) {
    $homeowner_id = intval($_POST['homeowner_id']);
    $billing_date = $_POST['billing_date'];
    $status = $_POST['status'];
    $monthly_due = floatval($_POST['monthly_due']);
    
    // Calculate due_date as one month from billing_date
    $billing_date_obj = new DateTime($billing_date);
    $billing_date_obj->modify('+1 month');
    $due_date = $billing_date_obj->format('Y-m-d H:i:s');

    // Check if billing record already exists
    $sql_check = "SELECT COUNT(*) FROM billing WHERE homeowner_id = ? AND billing_date = ?";
    $stmt_check = $conn->prepare($sql_check);
    if ($stmt_check) {
        $stmt_check->bind_param("ss", $homeowner_id, $billing_date);
        $stmt_check->execute();
        $stmt_check->bind_result($count);
        $stmt_check->fetch();
        $stmt_check->close();

        if ($count > 0) {
            $_SESSION['message'] = "Billing record already exists for this homeowner and billing date.";
        } else {
            // Insert new billing record
            $sql_insert = "INSERT INTO billing (homeowner_id, billing_date, due_date, status, monthly_due) 
                           VALUES (?, ?, ?, ?, ?)";
            $stmt_insert = $conn->prepare($sql_insert);
            if ($stmt_insert) {
                $stmt_insert->bind_param("ssssd", $homeowner_id, $billing_date, $due_date, $status, $monthly_due);
                if ($stmt_insert->execute()) {
                    $_SESSION['message'] = "Billing record created successfully!";
                } else {
                    $_SESSION['message'] = "Failed to create billing record: " . $stmt_insert->error;
                }
                $stmt_insert->close();
            } else {
                $_SESSION['message'] = "Prepare statement failed: " . $conn->error;
            }
        }
    } else {
        $_SESSION['message'] = "Prepare statement failed: " . $conn->error;
    }
    header("Location: billingadmin.php");
    exit();
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <title>Create Billing Record</title>
    <link rel="stylesheet" href="create_billing.css">
</head>
<body>
    <?php include 'sidebar.php'; ?>

    <div class="main-content">
        <div class="container">
            <h1>Create Billing Record</h1>
            <form method="POST" action="create_billing.php">
                <div class="form-group">
                    <label for="homeowner_id">Homeowner ID:</label>
                    <input type="number" id="homeowner_id" name="homeowner_id" oninput="fetchHomeownerName()" required>
                </div>

                <div class="form-group">
                    <label for="homeowner_name">Homeowner Name:</label>
                    <input type="text" id="homeowner_name" name="homeowner_name" readonly>
                </div>

                <div class="form-group">
                    <label for="billing_date">Billing Date:</label>
                    <input type="datetime-local" id="billing_date" name="billing_date" required>
                </div>

                <div class="form-group">
                    <label for="due_date">Due Date:</label>
                    <input type="text" id="due_date" name="due_date" readonly>
                </div>

                <div class="form-group">
                    <label for="status">Status:</label>
                    <select id="status" name="status" required>
                        <option value="Pending">Pending</option>
                        <option value="Paid">Paid</option>
                        <option value="Overdue">Overdue</option>
                    </select>
                </div>

                <div class="form-group">
                    <label for="monthly_due">Monthly Due:</label>
                    <input type="number" step="0.01" id="monthly_due" name="monthly_due" required>
                </div>

                <button type="submit" class="submit-btn" name="create_billing">Create Billing Record</button>
            </form>
        </div>
    </div>

    <script>
        let fetchedNames = {};

        function fetchHomeownerName() {
            const homeownerId = document.getElementById('homeowner_id').value;
            if (homeownerId) {
                if (!fetchedNames[homeownerId]) {
                    fetch(`get_homeowner.php?id=${homeownerId}`)
                        .then(response => response.json())
                        .then(data => {
                            if (data.name) {
                                document.getElementById('homeowner_name').value = data.name;
                                fetchedNames[homeownerId] = data.name;
                            } else {
                                document.getElementById('homeowner_name').value = '';
                            }
                        });
                } else {
                    document.getElementById('homeowner_name').value = fetchedNames[homeownerId];
                }
            } else {
                document.getElementById('homeowner_name').value = '';
            }
        }

        // Automatically set the due_date when billing_date changes
        document.getElementById('billing_date').addEventListener('change', function() {
            const billingDate = new Date(this.value);
            billingDate.setMonth(billingDate.getMonth() + 1);
            const dueDate = billingDate.toISOString().slice(0, 19).replace('T', ' ');
            document.getElementById('due_date').value = dueDate;
        });
    </script>
</body>
</html>
