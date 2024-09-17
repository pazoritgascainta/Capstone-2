<?php
session_name('admin_session'); // Set a unique session name for admins
session_start();

// Database connection
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "homeowner";

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Enable error reporting
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

// Initialize status message
$status_message = "";

// Handle approval or rejection of appointments
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['appointment_id']) && isset($_POST['new_status'])) {
    $appointment_id = intval($_POST['appointment_id']);
    $new_status = $_POST['new_status'];

    // Validate new status
    if (!in_array($new_status, ['Accepted', 'Rejected'])) {
        $status_message = "Invalid status value.";
    } else {
        if ($new_status == 'Accepted') {
            // Move the appointment to the accepted_appointments table
            $sql_move = "INSERT INTO accepted_appointments (date, name, email, purpose, homeowner_id, timeslot_id, amenity_id)
                         SELECT a.date, a.name, a.email, a.purpose, a.homeowner_id, a.timeslot_id, a.amenity_id
                         FROM appointments a
                         WHERE a.id = ?";
            $stmt_move = $conn->prepare($sql_move);
            if (!$stmt_move) {
                $status_message = "Prepare statement failed: " . $conn->error;
            } else {
                $stmt_move->bind_param("i", $appointment_id);
                if ($stmt_move->execute()) {
                    // Delete the appointment from the appointments table
                    $sql_delete = "DELETE FROM appointments WHERE id = ?";
                    $stmt_delete = $conn->prepare($sql_delete);
                    if ($stmt_delete) {
                        $stmt_delete->bind_param("i", $appointment_id);
                        $stmt_delete->execute();
                        $status_message = "Appointment accepted and moved successfully!";
                    } else {
                        $status_message = "Error: " . $stmt_delete->error;
                    }
                } else {
                    $status_message = "Error: " . $stmt_move->error;
                }
            }
        } elseif ($new_status == 'Rejected') {
            // Move the appointment to the rejected_appointments table
            $sql_move = "INSERT INTO rejected_appointments (date, name, email, purpose, homeowner_id, timeslot_id, amenity_id)
                         SELECT a.date, a.name, a.email, a.purpose, a.homeowner_id, a.timeslot_id, a.amenity_id
                         FROM appointments a
                         WHERE a.id = ?";
            $stmt_move = $conn->prepare($sql_move);
            if (!$stmt_move) {
                $status_message = "Prepare statement failed: " . $conn->error;
            } else {
                $stmt_move->bind_param("i", $appointment_id);
                if ($stmt_move->execute()) {
                    // Delete the appointment from the appointments table
                    $sql_delete = "DELETE FROM appointments WHERE id = ?";
                    $stmt_delete = $conn->prepare($sql_delete);
                    if ($stmt_delete) {
                        $stmt_delete->bind_param("i", $appointment_id);
                        $stmt_delete->execute();
                        $status_message = "Appointment rejected and moved successfully!";
                    } else {
                        $status_message = "Error: " . $stmt_delete->error;
                    }
                } else {
                    $status_message = "Error: " . $stmt_move->error;
                }
            }
        }
    }

    // Redirect to the same page to avoid resubmission
    header("Location: passed_appointments.php");
    exit();
}

// Number of records to display per page
$records_per_page = 10;

// Get the current page number from the URL, default to page 1 if not set
$current_page = isset($_GET['page']) ? (int)$_GET['page'] : 1;

// Calculate the offset for the SQL LIMIT clause
$offset = ($current_page - 1) * $records_per_page;

// Fetch passed appointments with pagination
$sql_passed_appointments = "
    SELECT id, date, name, email, purpose, homeowner_id, amenity_id, timeslot_id
    FROM passed_appointments
    LIMIT $records_per_page OFFSET $offset
";

$result_passed_appointments = $conn->query($sql_passed_appointments);

// Check for SQL errors
if (!$result_passed_appointments) {
    die("Query failed: " . $conn->error);
}

// Fetch total number of passed appointments
$sql_total_passed_appointments = "
    SELECT COUNT(*) AS total 
    FROM passed_appointments
";

$result_total_passed_appointments = $conn->query($sql_total_passed_appointments);

// Check for SQL errors
if (!$result_total_passed_appointments) {
    die("Query failed: " . $conn->error);
}

$total_passed_appointments = $result_total_passed_appointments->fetch_assoc()['total'];

// Calculate the total number of pages for passed appointments
$total_pages_passed = ceil($total_passed_appointments / $records_per_page);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Missed Appointments</title>
    <link rel="stylesheet" href="admin_approval.css">
    <link rel="stylesheet" href="accepted_appointments.css">
</head>
<body>
<?php include 'sidebar.php'; ?>

<div class="main-content">
    <h1>Missed Appointments</h1>
    <div class="container">
        <div class="admin_approval">
            <a href="admin_approval.php" class="btn-admin-approval">Go Back to Admin Approval</a>
        </div>
        <?php if ($result_passed_appointments->num_rows > 0): ?>
            <table>
                <tr>
                    <th>ID</th>
                    <th>Date</th>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Purpose</th>
                    <th>Homeowner ID</th>
                    <th>Amenity ID</th>
                    <th>Time Slot ID</th>

                </tr>
                <?php while ($row = $result_passed_appointments->fetch_assoc()): ?>
                    <tr>
                        <td><?= htmlspecialchars($row['id']) ?></td>
                        <td><?= htmlspecialchars($row['date']) ?></td>
                        <td><?= htmlspecialchars($row['name']) ?></td>
                        <td><?= htmlspecialchars($row['email']) ?></td>
                        <td><?= htmlspecialchars($row['purpose']) ?></td>
                        <td><?= htmlspecialchars($row['homeowner_id']) ?></td>
                        <td><?= htmlspecialchars($row['amenity_id']) ?></td>
                        <td><?= htmlspecialchars($row['timeslot_id']) ?></td>
                    </tr>
                <?php endwhile; ?>
            </table>

            <div id="pagination">
                <?php
                $total_pages = max($total_pages_passed, 1);
                $input_page = $current_page;
                
                if ($current_page > 1): ?>
                    <form method="GET" action="passed_appointments.php" style="display: inline;">
                        <input type="hidden" name="page" value="<?= $current_page - 1 ?>">
                        <button type="submit">&lt;</button>
                    </form>
                <?php endif; ?>
                
                <form method="GET" action="passed_appointments.php" style="display: inline;">
                    <input type="number" name="page" value="<?= $input_page ?>" min="1" max="<?= $total_pages ?>" style="width: 50px;">
                </form>

                <?php if ($total_pages > 1): ?>
                    <span>of</span>
                    <a href="?page=<?= $total_pages ?>" class="<?= ($current_page == $total_pages) ? 'active' : '' ?>"><?= $total_pages ?></a>
                <?php endif; ?>
                
                <?php if ($current_page < $total_pages): ?>
                    <form method="GET" action="passed_appointments.php" style="display: inline;">
                        <input type="hidden" name="page" value="<?= $current_page + 1 ?>">
                        <button type="submit">&gt;</button>
                    </form>
                <?php endif; ?>
            </div>
        <?php else: ?>
            <p>No passed appointments.</p>
        <?php endif; ?>
    </div>
</div>
</body>
</html>

<?php
$conn->close();
?>
