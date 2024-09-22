<?php
session_name('admin_session');
session_start();

// Check if admin is logged in
if (!isset($_SESSION['admin_id'])) {
    header("Location: admin_login.php");
    exit;
}

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "homeowner";

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
// Function to check if a billing record exists for the homeowner_id
function recordExists($conn, $homeowner_id) {
    $sql_check = "SELECT COUNT(*) FROM billing WHERE homeowner_id = ?";
    $stmt_check = $conn->prepare($sql_check);
    if ($stmt_check) {
        $stmt_check->bind_param("i", $homeowner_id);
        $stmt_check->execute();
        $stmt_check->bind_result($count);
        $stmt_check->fetch();
        $stmt_check->close();
        return $count > 0; // Returns true if record exists
    } else {
        $_SESSION['message'] = "Prepare statement failed: " . $conn->error;
        return false;
    }
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['homeowner_id'])) {
    $homeowner_id = intval($_POST['homeowner_id']);
    $billing_date = $_POST['billing_date'] . '-01'; // Set to the first of the selected month
    $status = $_POST['status'];

    // Check if billing record already exists for the homeowner_id
    if (recordExists($conn, $homeowner_id)) {
        $_SESSION['message'] = "This homeowner's billing record already exists.";
    } else {
        // Fetch sqm value based on homeowner_id
        $sql = "SELECT sqm FROM homeowners WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $homeowner_id);
        $stmt->execute();
        $stmt->bind_result($sqm);
        $stmt->fetch();
        $stmt->close();

        // Calculate monthly due and other fields...
        $monthly_due = $sqm * 5;
        $due_date = (new DateTime($billing_date))->modify('+1 month')->format('Y-m-d');
        $total_amount = ($status === 'Pending') ? $monthly_due : calculateTotalAmount($conn, $homeowner_id, $billing_date, $monthly_due);

        // Insert new billing record
        $sql_insert = "INSERT INTO billing (homeowner_id, billing_date, due_date, status, monthly_due, total_amount) 
                       VALUES (?, ?, ?, ?, ?, ?)";
        $stmt_insert = $conn->prepare($sql_insert);
        if ($stmt_insert) {
            $stmt_insert->bind_param("issssd", $homeowner_id, $billing_date, $due_date, $status, $monthly_due, $total_amount);
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

    header("Location: billingadmin.php");
    exit();
}


// Function to calculate total amount for overdue status
function calculateTotalAmount($conn, $homeowner_id, $billing_date, $monthly_due) {
    $current_date = new DateTime();
    $billing_date_obj = new DateTime($billing_date);

    if ($current_date <= $billing_date_obj) {
        return 0.00; // Not overdue
    }

    $interval = $billing_date_obj->diff($current_date);
    $months_overdue = $interval->y * 12 + $interval->m + 1;

    return $monthly_due * $months_overdue;
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
                    <input type="number" id="homeowner_id" name="homeowner_id" oninput="fetchHomeownerData()" required>
                </div>

                <div class="form-group">
                    <label for="homeowner_name">Homeowner Name:</label>
                    <input type="text" id="homeowner_name" name="homeowner_name" readonly>
                </div>

                <div class="form-group">
                    <label for="sqm">Square Meters:</label>
                    <input type="text" id="sqm" name="sqm" readonly>
                </div>

                <div class="form-group">
                    <label for="billing_date">Billing Month:</label>
                    <input type="month" id="billing_date" name="billing_date" required>
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
                    <input type="number" step="0.01" id="monthly_due" name="monthly_due" value="" readonly>
                </div>

                <button type="submit" class="submit-btn" name="create_billing">Create Billing Record</button>
            </form>
        </div>
    </div>

    <script>
        let fetchedNames = {};

        function fetchHomeownerData() {
            const homeownerId = document.getElementById('homeowner_id').value;
            if (homeownerId) {
                if (!fetchedNames[homeownerId]) {
                    fetch(`get_homeowner.php?id=${homeownerId}`)
                        .then(response => response.json())
                        .then(data => {
                            if (data.name) {
                                document.getElementById('homeowner_name').value = data.name;
                                document.getElementById('sqm').value = data.sqm; // Set sqm value
                                document.getElementById('monthly_due').value = (data.sqm * 5).toFixed(2); // Calculate monthly due
                                fetchedNames[homeownerId] = { name: data.name, sqm: data.sqm };
                            } else {
                                document.getElementById('homeowner_name').value = '';
                                document.getElementById('sqm').value = ''; // Clear sqm if no name found
                                document.getElementById('monthly_due').value = ''; // Clear monthly due if no homeowner found
                            }
                        });
                } else {
                    document.getElementById('homeowner_name').value = fetchedNames[homeownerId].name;
                    document.getElementById('sqm').value = fetchedNames[homeownerId].sqm; // Set sqm value
                    document.getElementById('monthly_due').value = (fetchedNames[homeownerId].sqm * 5).toFixed(2); // Calculate monthly due
                }
            } else {
                document.getElementById('homeowner_name').value = '';
                document.getElementById('sqm').value = ''; // Clear sqm if no ID
                document.getElementById('monthly_due').value = ''; // Clear monthly due if no ID
            }
        }

        // Automatically set the due_date when billing_date changes
        document.getElementById('billing_date').addEventListener('change', function() {
            const selectedMonth = this.value; // Get the selected month (YYYY-MM)
            const billingDate = new Date(selectedMonth + '-01'); // Always set to the first day of the month
            billingDate.setMonth(billingDate.getMonth() + 1); // Set the due date to the first of the next month
            const dueDate = billingDate.toISOString().slice(0, 10); // Format as YYYY-MM-DD
            document.getElementById('due_date').value = dueDate;
        });
    </script>
</body>
</html>
