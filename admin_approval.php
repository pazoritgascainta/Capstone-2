<?php
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
            $sql_move = "INSERT INTO accepted_appointments (date, name, email, purpose, homeowner_id, amenity_id, timeslot_id)
                         SELECT a.date, a.name, a.email, a.purpose, a.homeowner_id, t.amenity_id, t.id
                         FROM appointments a
                         JOIN timeslots t ON a.timeslot_id = t.id
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
                    }
                    $status_message = "Appointment accepted and moved successfully!";
                } else {
                    $status_message = "Error: " . $stmt_move->error;
                }
            }
        } elseif ($new_status == 'Rejected') {
            // Delete the appointment from the appointments table
            $sql_delete = "DELETE FROM appointments WHERE id = ?";
            $stmt_delete = $conn->prepare($sql_delete);
            if (!$stmt_delete) {
                $status_message = "Prepare statement failed: " . $conn->error;
            } else {
                $stmt_delete->bind_param("i", $appointment_id);
                if ($stmt_delete->execute()) {
                    $status_message = "Appointment rejected and removed successfully!";
                } else {
                    $status_message = "Error: " . $stmt_delete->error;
                }
            }
        }
    }
}

// Number of records to display per page
$records_per_page = 10;

// Get the current page number from the URL, default to page 1 if not set
$current_page = isset($_GET['page']) ? (int)$_GET['page'] : 1;

// Calculate the offset for the SQL LIMIT clause
$offset = ($current_page - 1) * $records_per_page;

// Fetch all pending appointments with pagination, excluding past dates
$sql_pending_appointments = "
    SELECT a.id, a.date, a.name, a.email, a.purpose, a.status, a.homeowner_id, 
           am.name AS amenity, t.time_start, t.time_end
    FROM appointments a
    JOIN timeslots t ON a.timeslot_id = t.id
    JOIN amenities am ON t.amenity_id = am.id
    WHERE a.status = 'Pending' AND a.date >= CURDATE()
    LIMIT $records_per_page OFFSET $offset
";
$result_pending_appointments = $conn->query($sql_pending_appointments);

// Check for SQL errors
if (!$result_pending_appointments) {
    die("Query failed: " . $conn->error);
}

// Get the total number of pending appointments excluding past dates
$sql_total_appointments = "
    SELECT COUNT(*) AS total 
    FROM appointments 
    WHERE status = 'Pending' AND date >= CURDATE()
";
$result_total_appointments = $conn->query($sql_total_appointments);

// Check for SQL errors
if (!$result_total_appointments) {
    die("Query failed: " . $conn->error);
}

$total_appointments = $result_total_appointments->fetch_assoc()['total'];

// Calculate the total number of pages
$total_pages = ceil($total_appointments / $records_per_page);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin Approval</title>
    <link rel="stylesheet" href="admin_approval.css">
</head>
<body>
    <h1>Pending Appointments</h1>

    <!-- Display any status messages -->
    <?php if (!empty($status_message)): ?>
        <p><?= htmlspecialchars($status_message) ?></p>
    <?php endif; ?>

    <?php if ($result_pending_appointments->num_rows > 0): ?>
        <table>
            <tr>
                <th>ID</th>
                <th>Amenity</th>
                <th>Date</th>
                <th>Time Slot</th>
                <th>Name</th>
                <th>Email</th>
                <th>Purpose</th>
                <th>Homeowner ID</th>
                <th>Status</th>
                <th>Action</th>
            </tr>
            <?php while ($row = $result_pending_appointments->fetch_assoc()): ?>
                <tr>
                    <td><?= htmlspecialchars($row['id']) ?></td>
                    <td><?= htmlspecialchars($row['amenity']) ?></td>
                    <td><?= htmlspecialchars($row['date']) ?></td>
                    <td><?= htmlspecialchars($row['time_start'] . ' - ' . $row['time_end']) ?></td>
                    <td><?= htmlspecialchars($row['name']) ?></td>
                    <td><?= htmlspecialchars($row['email']) ?></td>
                    <td><?= htmlspecialchars($row['purpose']) ?></td>
                    <td><?= htmlspecialchars($row['homeowner_id']) ?></td>
                    <td><?= htmlspecialchars($row['status']) ?></td>
                    <td>
                        <form method="POST" style="display: inline;">
                            <input type="hidden" name="appointment_id" value="<?= htmlspecialchars($row['id']) ?>">
                            <input type="hidden" name="new_status" value="Accepted">
                            <button type="submit">Accept</button>
                        </form>
                        <form method="POST" style="display: inline;">
                            <input type="hidden" name="appointment_id" value="<?= htmlspecialchars($row['id']) ?>">
                            <input type="hidden" name="new_status" value="Rejected">
                            <button type="submit">Reject</button>
                        </form>
                    </td>
                </tr>
            <?php endwhile; ?>
        </table>

        <div class="pagination">
            <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                <a href="?page=<?= $i ?>" class="<?= ($i == $current_page) ? 'active' : '' ?>"><?= $i ?></a>
            <?php endfor; ?>
        </div>
    <?php else: ?>
        <p>No pending appointments.</p>
    <?php endif; ?>

</body>
</html>

<?php
$conn->close();
?>
