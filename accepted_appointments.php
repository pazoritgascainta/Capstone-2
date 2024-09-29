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

// Initialize status message
$status_message = "";

// Initialize search query
$search_query = isset($_GET['search']) ? $_GET['search'] : '';

// Handle approval, rejection, or status update of appointments
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['appointment_id'])) {
    $appointment_id = intval($_POST['appointment_id']);

    if (isset($_POST['new_status'])) {
        $new_status = $_POST['new_status'];

        // Validate new status
        if (!in_array($new_status, ['paid', 'unpaid'])) {
            $status_message = "Invalid status value.";
        } else {
            // Update the appointment's status in the database
            $sql_update_status = "UPDATE accepted_appointments SET status = ? WHERE id = ?";
            $stmt_update_status = $conn->prepare($sql_update_status);
            if ($stmt_update_status) {
                $stmt_update_status->bind_param("si", $new_status, $appointment_id);
                if ($stmt_update_status->execute()) {
                    $status_message = "Status updated successfully!";
                } else {
                    $status_message = "Error updating status: " . $stmt_update_status->error;
                }
            } else {
                $status_message = "Prepare statement failed: " . $conn->error;
            }
        }
    } elseif (isset($_POST['new_amount'])) {
        $new_amount = floatval($_POST['new_amount']);

        // Update the appointment's amount in the database
        $sql_update_amount = "UPDATE accepted_appointments SET amount = ? WHERE id = ?";
        $stmt_update_amount = $conn->prepare($sql_update_amount);
        if ($stmt_update_amount) {
            $stmt_update_amount->bind_param("di", $new_amount, $appointment_id);
            if ($stmt_update_amount->execute()) {
                $status_message = "Amount updated successfully!";
            } else {
                $status_message = "Error updating amount: " . $stmt_update_amount->error;
            }
        } else {
            $status_message = "Prepare statement failed: " . $conn->error;
        }
    } elseif (isset($_POST['new_appointment_status'])) {
        $new_appointment_status = $_POST['new_appointment_status'];

        // Validate new appointment status
        if (!in_array($new_appointment_status, ['Accepted', 'Rejected'])) {
            $status_message = "Invalid status value.";
        } else {
            if ($new_appointment_status == 'Accepted') {
                // Move the appointment to the accepted_appointments table
                $sql_move = "INSERT INTO accepted_appointments (date, name, email, purpose, homeowner_id, timeslot_id, amenity_id, amount, status)
                             SELECT a.date, a.name, a.email, a.purpose, a.homeowner_id, a.timeslot_id, a.amenity_id, 0, 'unpaid'
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
                        }
                        $status_message = "Appointment accepted and moved successfully!";
                    } else {
                        $status_message = "Error: " . $stmt_move->error;
                    }
                }
            } elseif ($new_appointment_status == 'Rejected') {
                // Delete the appointment from the appointments table
                $sql_delete = "DELETE FROM appointments WHERE id = ?";
                $stmt_delete = $conn->prepare($sql_delete);
                if ($stmt_delete) {
                    $stmt_delete->bind_param("i", $appointment_id);
                    if ($stmt_delete->execute()) {
                        $status_message = "Appointment rejected and removed successfully!";
                    } else {
                        $status_message = "Error: " . $stmt_delete->error;
                    }
                } else {
                    $status_message = "Prepare statement failed: " . $conn->error;
                }
            }
        }
    }

    // Redirect to avoid form resubmission
    header("Location: accepted_appointments.php");
    exit();
}

// Number of records to display per page
$records_per_page = 10;

// Get the current page number from the URL, default to page 1 if not set
$current_page = isset($_GET['page']) ? (int)$_GET['page'] : 1;

// Calculate the offset for the SQL LIMIT clause
$offset = ($current_page - 1) * $records_per_page;

// Fetch accepted appointments with pagination, including amount and status
$sql_accepted_appointments = "
    SELECT a.id, a.date, a.name, a.email, a.purpose, 
           am.name AS amenity, t.time_start, t.time_end, 
           a.amount, a.status
    FROM accepted_appointments a
    JOIN timeslots t ON a.timeslot_id = t.id
    JOIN amenities am ON t.amenity_id = am.id
    WHERE a.name LIKE ? OR a.email LIKE ?
    LIMIT $records_per_page OFFSET $offset
";
$stmt_accepted_appointments = $conn->prepare($sql_accepted_appointments);
$search_term = '%' . $search_query . '%';
$stmt_accepted_appointments->bind_param('ss', $search_term, $search_term);
$stmt_accepted_appointments->execute();
$result_accepted_appointments = $stmt_accepted_appointments->get_result();

// Fetch total number of accepted appointments
$sql_total_accepted_appointments = "
    SELECT COUNT(*) AS total 
    FROM accepted_appointments
    WHERE name LIKE ? OR email LIKE ?
";
$stmt_total_accepted_appointments = $conn->prepare($sql_total_accepted_appointments);
$stmt_total_accepted_appointments->bind_param('ss', $search_term, $search_term);
$stmt_total_accepted_appointments->execute();
$result_total_accepted_appointments = $stmt_total_accepted_appointments->get_result();
$total_accepted_appointments = $result_total_accepted_appointments->fetch_assoc()['total'];

// Calculate the total number of pages for accepted appointments
$total_pages_accepted = ceil($total_accepted_appointments / $records_per_page);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Appointments Management</title>
    <link rel="stylesheet" href="admin_approval.css">
    <link rel="stylesheet" href="accepted_appointments.css">
</head>
<body>
<?php include 'sidebar.php'; ?>

<div class="main-content">
    <h1>Accepted Appointments</h1>
    <div class="container">

    
        <div class="admin_approval">
            <a href="admin_approval.php" class="btn-admin-approval">Go Back to Admin Approval</a>
        </div> <br>
        <form method="GET" action="accepted_appointments.php" class="search-form"> 
            <input type="text" name="search" placeholder="Search by name or email" value="<?= htmlspecialchars($search_query); ?>">
            <button type="submit">Search</button>
        </form>
        <?php if ($result_accepted_appointments->num_rows > 0): ?>
            <table>
                <tr>
                    <th>ID</th>
                    <th>Date</th>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Purpose</th>
                    <th>Amenity</th>
                    <th>Time Slot</th>
                    <th>Amount</th> <!-- New Column -->
                    <th>Status</th> <!-- New Column -->
                    <th>Action</th> <!-- New Column for status update -->
                </tr>
                <?php while ($row = $result_accepted_appointments->fetch_assoc()): ?>
                    <tr>
                        <td><?= htmlspecialchars($row['id']) ?></td>
                        <td><?= htmlspecialchars($row['date']) ?></td>
                        <td><?= htmlspecialchars($row['name']) ?></td>
                        <td><?= htmlspecialchars($row['email']) ?></td>
                        <td><?= htmlspecialchars($row['purpose']) ?></td>
                        <td><?= htmlspecialchars($row['amenity']) ?></td>
                        <td><?= htmlspecialchars($row['time_start'] . ' - ' . $row['time_end']) ?></td>
                        <td>
                            <form method="POST" action="accepted_appointments.php" style="display:inline;">
                                <input type="number" name="new_amount" value="<?= htmlspecialchars($row['amount']) ?>" step="0.01" required>
                                <input type="hidden" name="appointment_id" value="<?= htmlspecialchars($row['id']) ?>">
                                <button type="submit">Update Amount</button>
                            </form>
                        </td>
                        <td><?= htmlspecialchars($row['status']) ?></td>
                        <td>
                            <form method="POST" action="accepted_appointments.php" style="display:inline;">
                                <input type="hidden" name="appointment_id" value="<?= htmlspecialchars($row['id']) ?>">
                                <select name="new_status">
                                    <option value="paid" <?= $row['status'] == 'paid' ? 'selected' : '' ?>>Paid</option>
                                    <option value="unpaid" <?= $row['status'] == 'unpaid' ? 'selected' : '' ?>>Unpaid</option>
                                </select>
                                <button type="submit">Update Status</button>
                            </form>
                        </td>
                    </tr>
                <?php endwhile; ?>

            </table>

            <div id="pagination">
                <?php
                $total_pages = max($total_pages_accepted, 1); // Ensure there's at least 1 page
                $input_page = $current_page; // Default to the current page for the input

                // Previous button
                if ($current_page > 1): ?>
                    <form method="GET" action="accepted_appointments.php" style="display: inline;">
                        <input type="hidden" name="page" value="<?= $current_page - 1 ?>">
                        <button type="submit"><</button>
                    </form>
                <?php endif; ?>
                
                <!-- Page input for user to change the page -->
                <form method="GET" action="accepted_appointments.php" style="display: inline;">
                    <input type="number" name="page" value="<?= $input_page ?>" min="1" max="<?= $total_pages ?>" style="width: 50px;">
                </form>
                
                <!-- "of" text and last page link -->
                <?php if ($total_pages > 1): ?>
                    <span>of</span>
                    <a href="accepted_appointments.php?page=<?= $total_pages ?>"><?= $total_pages ?></a>
                <?php endif; ?>

                <!-- Next button -->
                <?php if ($current_page < $total_pages): ?>
                    <form method="GET" action="accepted_appointments.php" style="display: inline;">
                        <input type="hidden" name="page" value="<?= $current_page + 1 ?>">
                        <button type="submit">></button>
                    </form>
                <?php endif; ?>
            </div>
        <?php else: ?>
            <p>No accepted appointments found.</p>
        <?php endif; ?>
        
        <?php if ($status_message): ?>
            <p><?= htmlspecialchars($status_message) ?></p>
        <?php endif; ?>

    </div>
</div>

</body>
</html>
