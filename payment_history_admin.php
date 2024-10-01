<?php
session_name('admin_session');
session_start();

// Check if admin is logged in
if (!isset($_SESSION['admin_id'])) {
    header("Location: admin_login.php");
    exit;
}

// Database connection
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "homeowner";

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Function to check if a history record exists for the homeowner_id and billing_date
function historyRecordExists($conn, $homeowner_id, $billing_date) {
    $sql_check = "SELECT COUNT(*) FROM billing_history WHERE homeowner_id = ? AND billing_date = ?";
    $stmt_check = $conn->prepare($sql_check);
    if ($stmt_check) {
        $stmt_check->bind_param("is", $homeowner_id, $billing_date);
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

// Initialize homeowner ID from GET request or default to 0
$homeowner_id = isset($_GET['homeowner_id']) ? intval($_GET['homeowner_id']) : 0; 

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['homeowner_id'])) {
    $homeowner_id = intval($_POST['homeowner_id']); // Get homeowner ID from POST
    $billing_date = $_POST['billing_date'] . '-01'; // Set to the first of the selected month
    $paid_date = $_POST['paid_date'] . '-01';
    $status = 'Paid'; // Only 'Paid' status for this table

    // Convert dates for processing
    $billingDate = new DateTime($billing_date);
    $paidDate = new DateTime($paid_date);

    // Check if history records exist and insert records for each month
    while ($billingDate <= $paidDate) {
        $currentBillingDate = $billingDate->format('Y-m-d');

        if (historyRecordExists($conn, $homeowner_id, $currentBillingDate)) {
            $_SESSION['message'] = "Billing record for $currentBillingDate already exists for this homeowner.";
        } else {
            // Fetch sqm value based on homeowner_id
            $sql = "SELECT sqm FROM homeowners WHERE id = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("i", $homeowner_id);
            $stmt->execute();
            $stmt->bind_result($sqm);
            $stmt->fetch();
            $stmt->close();

            // Calculate monthly due
            $monthly_due = $sqm * 5;
            $due_date = (clone $billingDate)->modify('first day of next month')->format('Y-m-d');
            $total_amount = $monthly_due;

            // Insert new billing history record
            $sql_insert = "INSERT INTO billing_history (homeowner_id, monthly_due, billing_date, due_date, status, total_amount, paid_date) 
                           VALUES (?, ?, ?, ?, ?, ?, ?)";
            $stmt_insert = $conn->prepare($sql_insert);
            if ($stmt_insert) {
                $stmt_insert->bind_param("issssds", $homeowner_id, $monthly_due, $currentBillingDate, $due_date, $status, $total_amount, $paid_date);
                if (!$stmt_insert->execute()) {
                    $_SESSION['message'] = "Failed to create billing history record for $currentBillingDate: " . $stmt_insert->error; // Improved error reporting
                    error_log("Insert error: " . $stmt_insert->error); // Log the error for further analysis
                }
                $stmt_insert->close();
            } else {
                $_SESSION['message'] = "Prepare statement failed: " . $conn->error;
                error_log("Prepare error: " . $conn->error); // Log the error
            }
        }

        // Move to the next month
        $billingDate->modify('+1 month');
    }

    // Always redirect to the input_billing.php page with the homeowner_id
    header("Location: input_billing.php?homeowner_id=" . $homeowner_id);
    exit(); // Ensure exit after header to stop script execution
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>Create Billing History Record</title>
    <link rel="stylesheet" href="create_billing.css">
</head>
<body>
    <?php include 'sidebar.php'; ?>

    <div class="main-content">
        <div class="container">
            <h1>Create Billing History Record</h1>
            <form method="POST" action="">
                <div class="form-group">
                    <label for="homeowner_id">Homeowner ID:</label>
                    <input type="number" id="homeowner_id" name="homeowner_id" value="<?php echo htmlspecialchars($homeowner_id); ?>" oninput="fetchHomeownerData()" required>
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
                    <label for="paid_date">Paid Month:</label>
                    <input type="month" id="paid_date" name="paid_date" required>
                </div>

                <div class="form-group">
                    <label for="status">Status:</label>
                    <input type="text" id="status" name="status" value="Paid" readonly>
                </div>

                <div class="form-group">
                    <label for="monthly_due">Monthly Due:</label>
                    <input type="number" step="0.01" id="monthly_due" name="monthly_due" value="" readonly>
                </div>

                <button type="submit" class="submit-btn" name="create_billing">Create Billing History Record</button>
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
