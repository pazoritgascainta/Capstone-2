<?php
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

// Fetch billing details
$billing_record = null;
if (isset($_GET['billing_id'])) {
    $billing_id = $_GET['billing_id'];

    // Validate billing_id to ensure it's an integer
    if (filter_var($billing_id, FILTER_VALIDATE_INT)) {
        $sql = "SELECT b.billing_id, b.homeowner_id, h.name AS homeowner_name, b.total_amount, b.billing_date, b.due_date, b.status, b.type
                FROM billing b
                JOIN homeowners h ON b.homeowner_id = h.id
                WHERE b.billing_id = ?";
        $stmt = $conn->prepare($sql);

        if ($stmt === false) {
            die("Error preparing the statement: " . $conn->error);
        }

        $stmt->bind_param("i", $billing_id);
        if ($stmt->execute()) {
            $result = $stmt->get_result();
            if ($result->num_rows > 0) {
                $billing_record = $result->fetch_assoc();
            } else {
                echo "No record found for billing ID: " . htmlspecialchars($billing_id) . "<br>";
            }
        } else {
            echo "Error executing the query: " . $stmt->error . "<br>";
        }

        $stmt->close();
    } else {
        echo "Invalid billing ID provided.<br>";
    }
} else {
    echo "No billing ID provided.<br>";
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>View Billing Record</title>
    <link rel="stylesheet" href="dashbcss.css">
    <link rel="stylesheet" href="billcss.css">
</head>
<body>
    <?php include 'sidebar.php'; ?>

    <div class="main-content">
        <div class="Container">
            <h1>View Billing Record</h1>
            <?php if ($billing_record): ?>
                <table>
                    <tr>
                        <td><strong>Billing ID:</strong></td>
                        <td><?= htmlspecialchars($billing_record['billing_id']) ?></td>
                    </tr>
                    <tr>
                        <td><strong>Homeowner ID:</strong></td>
                        <td><?= htmlspecialchars($billing_record['homeowner_id']) ?></td>
                    </tr>
                    <tr>
                        <td><strong>Homeowner Name:</strong></td>
                        <td><?= htmlspecialchars($billing_record['homeowner_name']) ?></td>
                    </tr>
                    <tr>
                        <td><strong>Total Amount:</strong></td>
                        <td><?= htmlspecialchars(number_format($billing_record['total_amount'], 2)) ?></td>
                    </tr>
                    <tr>
                        <td><strong>Billing Date:</strong></td>
                        <td><?= htmlspecialchars($billing_record['billing_date']) ?></td>
                    </tr>
                    <tr>
                        <td><strong>Due Date:</strong></td>
                        <td><?= htmlspecialchars($billing_record['due_date']) ?></td>
                    </tr>
                    <tr>
                        <td><strong>Status:</strong></td>
                        <td><?= htmlspecialchars($billing_record['status']) ?></td>
                    </tr>
                    <tr>
                        <td><strong>Type:</strong></td>
                        <td><?= htmlspecialchars($billing_record['type']) ?></td>
                    </tr>
                </table>
            <?php else: ?>
                <p>No record found.</p>
            <?php endif; ?>
            <br>
            <a href="billingadmin.php" class="btn-action">Back to Billing Records</a>
        </div>
    </div>
</body>
</html>
