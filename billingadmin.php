<?php
session_start(); // Start the session

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

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $homeowner_id = $_POST['homeowner_id'];
    $total_amount = $_POST['total_amount'];
    $billing_date = $_POST['billing_date'];
    $due_date = $_POST['due_date'];
    $status = $_POST['status'];
    $type = $_POST['type'];

    // Check if any record already exists for this homeowner
    $sql = "SELECT billing_id FROM billing WHERE homeowner_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $homeowner_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        // Record exists
        $_SESSION['message'] = "A billing record for this homeowner already exists. New records cannot be created.";
    } else {
        // No existing record, insert a new one
        $sql = "INSERT INTO billing (homeowner_id, total_amount, billing_date, due_date, status, type)
                VALUES (?, ?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("idssss", $homeowner_id, $total_amount, $billing_date, $due_date, $status, $type);

        if ($stmt->execute()) {
            $_SESSION['message'] = "Billing record created successfully";
        } else {
            $_SESSION['message'] = "Error: " . $stmt->error;
        }
    }

    $stmt->close();
    header("Location: billingadmin.php"); // Redirect to the same page
    exit();
}

// Fetch all billing records, grouping by homeowner and concatenating types
$sql = "SELECT 
            b.billing_id,
            b.homeowner_id, 
            h.name as homeowner_name, 
            SUM(b.total_amount) as total_amount, 
            MAX(b.billing_date) as billing_date, 
            MAX(b.due_date) as due_date, 
            GROUP_CONCAT(b.type SEPARATOR ', ') as types, 
            GROUP_CONCAT(b.status SEPARATOR ', ') as statuses
        FROM billing b
        JOIN homeowners h ON b.homeowner_id = h.id
        GROUP BY b.homeowner_id, b.billing_id";
$result = $conn->query($sql);
$billing_records = [];
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $billing_records[] = $row;
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>Billing Management</title>
    <link rel="stylesheet" href="dashbcss.css">
    <link rel="stylesheet" href="billcss.css">
</head>
<body>
    <?php include 'sidebar.php'; ?>

    <div class="main-content">
        <div class="container">
            <h1>Billing Management</h1>

            <!-- Display Success/Error Message -->
            <?php if (isset($_SESSION['message'])): ?>
                <div class="message">
                    <?= htmlspecialchars($_SESSION['message']) ?>
                    <?php unset($_SESSION['message']); // Clear the message ?>
                </div>
            <?php endif; ?>

            <!-- Create Billing Button -->
            <h2>Create New Billing Record</h2>
            <br>
            <a href="create_billing.php" class="btn-action">Create Billing Record</a>
            <br></br>

            <!-- Display Billing Records -->
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
                    <?php foreach ($billing_records as $record): ?>
                        <tr>
                            <td><?= htmlspecialchars($record['billing_id']) ?></td>
                            <td><?= htmlspecialchars($record['homeowner_id']) ?></td>
                            <td><?= htmlspecialchars($record['homeowner_name']) ?></td>
                            <td><?= htmlspecialchars(number_format($record['total_amount'], 2)) ?></td>
                            <td><?= htmlspecialchars($record['billing_date']) ?></td>
                            <td><?= htmlspecialchars($record['due_date']) ?></td>
                            <td><?= htmlspecialchars($record['statuses']) ?></td>
                            <td><?= htmlspecialchars($record['types']) ?></td>
                            <td>
                                <!-- View Billing Button -->
                              <!-- View Billing Button -->
<form method="GET" action="view_billing.php" style="display:inline;">
    <input type="hidden" name="billing_id" value="<?= htmlspecialchars($record['billing_id']) ?>">
    <input type="submit" value="View" class="btn-action">
</form>

                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</body>
</html>
