<?php
session_name('admin_session');
session_start();

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "homeowner";

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Get the homeowner_id from the URL
$homeowner_id = isset($_GET['homeowner_id']) ? intval($_GET['homeowner_id']) : 0;

// Pagination variables for accepted appointments
$results_per_page = 10; // Adjust the number of results per page as needed
$current_page = isset($_GET['page']) ? intval($_GET['page']) : 1;
$start_from = ($current_page - 1) * $results_per_page;

// Fetch total records for pagination from accepted appointments
$sql_total_appointments = "SELECT COUNT(*) AS total FROM accepted_appointments WHERE homeowner_id = ?";
$stmt_total_appointments = $conn->prepare($sql_total_appointments);
$stmt_total_appointments->bind_param("i", $homeowner_id);
$stmt_total_appointments->execute();
$result_total_appointments = $stmt_total_appointments->get_result();
$total_records_appointments = $result_total_appointments->fetch_assoc()['total'];
$total_pages_appointments = ceil($total_records_appointments / $results_per_page);

// Fetch accepted appointments including status
$sql_appointments = "SELECT date AS billing_date, amount AS total_amount, status FROM accepted_appointments WHERE homeowner_id = ? ORDER BY date DESC LIMIT ?, ?";
$stmt_appointments = $conn->prepare($sql_appointments);
$stmt_appointments->bind_param("iii", $homeowner_id, $start_from, $results_per_page);
$stmt_appointments->execute();
$result_appointments = $stmt_appointments->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Accepted Appointments</title>
    <link rel="stylesheet" href="dashbcss.css">
    <link rel="stylesheet" href="recordingadmin.css">
    <style>
        .appointment-row {
            background-color: #f9f9f9; /* Light gray for appointments */
        }
    </style>
</head>
<body>
<?php include 'sidebar.php'; ?>
<div class="main-content">
    <div class="container">
        <section>
            <h2>Accepted Appointments for Homeowner ID: <?php echo htmlspecialchars($homeowner_id); ?></h2>
            <a href="input_billing.php?homeowner_id=<?= htmlspecialchars($homeowner_id); ?>">Back</a>
            <!-- Accepted Appointments Table -->
            <h3>Accepted Appointments</h3>
            <table class="table">
                <thead>
                    <tr>
                        <th>Billing Date</th>
                        <th>Total Amount</th>
                        <th>Status</th> <!-- New column for status -->
                    </tr>
                </thead>
                <tbody>
                    <?php if ($result_appointments->num_rows > 0): ?>
                        <?php while ($row = $result_appointments->fetch_assoc()): ?>
                            <tr class="appointment-row">
                                <td><?php echo htmlspecialchars($row['billing_date']); ?></td>
                                <td><?php echo number_format($row['total_amount'], 2); ?></td>
                                <td><?php echo htmlspecialchars($row['status']); ?></td> <!-- Display status -->
                            </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr><td colspan="3">No accepted appointments found for this homeowner.</td></tr> <!-- Adjust colspan -->
                    <?php endif; ?>
                </tbody>
            </table>

            <div id="pagination">
                <?php if ($total_pages_appointments > 1): ?>
                    <!-- Previous Button for Appointments -->
                    <?php if ($current_page > 1): ?>
                        <form method="GET" action="accepted_appointments_history.php" style="display: inline;">
                            <input type="hidden" name="homeowner_id" value="<?= htmlspecialchars($homeowner_id); ?>">
                            <input type="hidden" name="page" value="<?= $current_page - 1 ?>">
                            <button type="submit">&lt;</button>
                        </form>
                    <?php endif; ?>

                    <!-- Page Input Field for Appointments -->
                    <form method="GET" action="accepted_appointments_history.php" style="display: inline;">
                        <input type="hidden" name="homeowner_id" value="<?= htmlspecialchars($homeowner_id); ?>">
                        <input type="number" name="page" value="<?= $current_page ?>" min="1" max="<?= $total_pages_appointments ?>" style="width: 50px;">
                    </form>

                    <!-- Next Button for Appointments -->
                    <?php if ($current_page < $total_pages_appointments): ?>
                        <form method="GET" action="accepted_appointments_history.php" style="display: inline;">
                            <input type="hidden" name="homeowner_id" value="<?= htmlspecialchars($homeowner_id); ?>">
                            <input type="hidden" name="page" value="<?= $current_page + 1 ?>">
                            <button type="submit">&gt;</button>
                        </form>
                    <?php endif; ?>
                <?php endif; ?>
            </div>
        </section>
    </div>
</div>
</body>
</html>

<?php
$conn->close();
?>
