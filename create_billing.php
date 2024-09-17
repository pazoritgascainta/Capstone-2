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

// Function to check if a billing record exists
function recordExists($conn, $homeowner_id, $billing_date) {
    $sql_check = "SELECT COUNT(*) FROM billing WHERE homeowner_id = ? AND billing_date = ?";
    $stmt_check = $conn->prepare($sql_check);
    if ($stmt_check) {
        $stmt_check->bind_param("is", $homeowner_id, $billing_date); // Ensure correct format
        $stmt_check->execute();
        $stmt_check->bind_result($count);
        $stmt_check->fetch();
        $stmt_check->close();
        return $count > 0;
    } else {
        $_SESSION['message'] = "Prepare statement failed: " . $conn->error;
        return false;
    }
}

// Function to calculate total amount for overdue status
function calculateTotalAmount($conn, $homeowner_id, $billing_date, $monthly_due) {
    $current_date = new DateTime();
    $billing_date_obj = new DateTime($billing_date);

    // Calculate the number of months overdue including the current month
    if ($current_date <= $billing_date_obj) {
        return 0.00; // Not overdue
    }

    $interval = $billing_date_obj->diff($current_date);
    $months_overdue = $interval->y * 12 + $interval->m;

    // Add one more month for the current month
    $months_overdue += 1;

    // Accumulate total amount
    return $monthly_due * $months_overdue;
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['homeowner_id'])) {
    $homeowner_id = intval($_POST['homeowner_id']);
    $billing_date = $_POST['billing_date'];
    $status = $_POST['status'];
    $monthly_due = floatval($_POST['monthly_due']);
    
    // Calculate due_date as one month from billing_date
    $billing_date_obj = new DateTime($billing_date);
    $billing_date_obj->modify('+1 month');
    $due_date = $billing_date_obj->format('Y-m-d');

    // Default total_amount
    $total_amount = 0.00;

    if ($status === 'Pending') {
        $total_amount = $monthly_due;
    } elseif ($status === 'Overdue') {
        $total_amount = calculateTotalAmount($conn, $homeowner_id, $billing_date, $monthly_due);
    }

    // Check if billing record already exists
    if (recordExists($conn, $homeowner_id, $billing_date)) {
        $_SESSION['message'] = "Billing record already exists for this homeowner and billing date.";
    } else {
        // Insert new billing record
        $sql_insert = "INSERT INTO billing (homeowner_id, billing_date, due_date, status, monthly_due, total_amount) 
                       VALUES (?, ?, ?, ?, ?, ?)";
        $stmt_insert = $conn->prepare($sql_insert);
        if ($stmt_insert) {
            $stmt_insert->bind_param("issssd", $homeowner_id, $billing_date, $due_date, $status, $monthly_due, $total_amount);
            try {
                if ($stmt_insert->execute()) {
                    $_SESSION['message'] = "Billing record created successfully!";
                }
            } catch (mysqli_sql_exception $e) {
                if ($e->getCode() === '23000') { // SQLSTATE[23000] is the error code for integrity constraint violations
                    $_SESSION['message'] = "Billing record already exists for this homeowner and billing date.";
                } else {
                    $_SESSION['message'] = "Failed to create billing record: " . $e->getMessage();
                }
            }
            $stmt_insert->close();
        } else {
            $_SESSION['message'] = "Prepare statement failed: " . $conn->error;
        }
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

