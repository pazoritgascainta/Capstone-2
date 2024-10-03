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

// Function to update appointments to 'Seen' if the date is one day past and status is still 'Pending'
function updateSeenAppointments($conn) {
    // Move pending appointments older than 1 day to passed_appointments
    $sql_update_seen = "
        INSERT INTO passed_appointments (date, name, email, purpose, homeowner_id, amenity_id, timeslot_id)
        SELECT a.date, a.name, a.email, a.purpose, a.homeowner_id, t.amenity_id, t.id
        FROM appointments a
        JOIN timeslots t ON a.timeslot_id = t.id
        WHERE a.status = 'Pending' AND DATE(a.date) < CURDATE() - INTERVAL 1 DAY
    ";

    // Update the status of these appointments in the original table to 'Seen'
    $sql_update_status = "
        UPDATE appointments 
        SET status = 'Seen' 
        WHERE status = 'Pending' AND DATE(date) < CURDATE() - INTERVAL 1 DAY
    ";

    // Execute the insert query to move appointments to passed_appointments
    if ($conn->query($sql_update_seen) === TRUE) {
        // Execute the update query to mark these appointments as 'Seen'
        if ($conn->query($sql_update_status) === TRUE) {
            return "Appointments updated to 'Seen' successfully.";
        } else {
            return "Error updating status to 'Seen': " . $conn->error;
        }
    } else {
        return "Error moving appointments to passed_appointments: " . $conn->error;
    }
}

// Call the function to update 'Seen' appointments automatically
echo updateSeenAppointments($conn);

// Handle appointment status update manually (e.g., via POST request)
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['appointment_id']) && isset($_POST['new_status'])) {
    $appointment_id = intval($_POST['appointment_id']);
    $new_status = $_POST['new_status'];

    // Validate new status
    if (!in_array($new_status, ['Accepted', 'Rejected', 'Seen'])) {
        $_SESSION['message'] = ['status' => 'error', 'message' => 'Invalid status value.'];
        header('Location: admin_approval.php');
        exit();
    }

    // Handle status change based on $new_status value
    if ($new_status == 'Accepted') {
        // Move to accepted_appointments table
        $sql_move = "INSERT INTO accepted_appointments (date, name, email, purpose, homeowner_id, amenity_id, timeslot_id)
                     SELECT a.date, a.name, a.email, a.purpose, a.homeowner_id, t.amenity_id, t.id
                     FROM appointments a
                     JOIN timeslots t ON a.timeslot_id = t.id
                     WHERE a.id = ?";
    } elseif ($new_status == 'Rejected') {
        // Move to rejected_appointments table
        $sql_move = "INSERT INTO rejected_appointments (date, name, email, purpose, homeowner_id, amenity_id, timeslot_id)
                     SELECT a.date, a.name, a.email, a.purpose, a.homeowner_id, t.amenity_id, t.id
                     FROM appointments a
                     JOIN timeslots t ON a.timeslot_id = t.id
                     WHERE a.id = ?";
    } elseif ($new_status == 'Seen') {
        // Move to passed_appointments table
        $sql_move = "INSERT INTO passed_appointments (date, name, email, purpose, homeowner_id, amenity_id, timeslot_id)
                     SELECT a.date, a.name, a.email, a.purpose, a.homeowner_id, t.amenity_id, t.id
                     FROM appointments a
                     JOIN timeslots t ON a.timeslot_id = t.id
                     WHERE a.id = ?";
    }

    // Execute the status change and move the appointment to the respective table
    $stmt_move = $conn->prepare($sql_move);
    if ($stmt_move) {
        $stmt_move->bind_param("i", $appointment_id);
        if ($stmt_move->execute()) {
            // Update the appointment status in the original table
            $sql_update = "UPDATE appointments SET status = ? WHERE id = ?";
            $stmt_update = $conn->prepare($sql_update);
            if ($stmt_update) {
                $stmt_update->bind_param("si", $new_status, $appointment_id);
                if ($stmt_update->execute()) {
                    $_SESSION['message'] = ['status' => 'success', 'message' => "Appointment $new_status successfully!"];
                    header('Location: admin_approval.php');
                    exit();
                } else {
                    $_SESSION['message'] = ['status' => 'error', 'message' => 'Error updating appointment: ' . $stmt_update->error];
                }
            }
        } else {
            $_SESSION['message'] = ['status' => 'error', 'message' => 'Error moving appointment: ' . $stmt_move->error];
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

<!-- HTML and pagination code goes here -->


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin Approval</title>
    <link rel="stylesheet" href="admin_approval.css">
    <link rel="stylesheet" href="calendar.css">
</head>
<body>
<?php include 'sidebar.php'; ?>
    <div class="main-content">
    <h1>Pending Appointments</h1>
        <div class="container">
      
            <div class="button-container">
    <div class="manage-timeslots">
        <a href="manage_timeslots.php" class="btn-manage-timeslots">Manage Timeslots</a>
    </div>
    <div class="view-accepted-appointments">
        <a href="accepted_appointments.php" class="btn-view-accepted">View Accepted Appointments</a>
    </div>

<div class="rejected-appointments">
        <a href="rejected_appointments.php" class="btn-manage-timeslots">Rejected Appointments</a>
    </div>
    <div class="passed-appointments">
        <a href="passed_appointments.php" class="btn-view-accepted">Passed Appointments</a>
    </div>
</div>

<div class="legend-container">
    <h4>Amenity Appointments Legend</h4>
    <ul>
        <li><span class="legend-color clubhousecourt"></span> Clubhouse Court </li>
        <li><span class="legend-color townhousecourt"></span> Townhouse Court </li>
        <li><span class="legend-color clubhouseswimmingpool"></span> Clubhouse Swimming Pool </li>
        <li><span class="legend-color townhouseswimmingpool"></span> Townhouse Swimming Pool </li>
        <li><span class="legend-color consultation"></span>  Consultation </li>
        <li><span class="legend-color bluehousecourt"></span> Bluehouse Court </li>
    </ul>
</div>

           <!-- Calendar section -->
<div id="calendar-box">
    
    <div id="calendar-nav">
        
        <button id="prev-month">&lt;</button>
        <span id="month-year"></span>
        <button id="next-month">&gt;</button>
    </div>
    <div id="calendar"></div>
</div>

<div id="appointments-table-container" style="display: none;">
        <h3>Appointments for <span id="selected-date"></span></h3>
        <table id="appointments-table">
            
            <!-- Table content will be populated by JavaScript -->
        </table>
    </div>
    <div id="status-message-section">
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
                            <td class="centered-actions">
                                <form method="POST" action="admin_approval.php" style="display: inline;">
                                    <input type="hidden" name="appointment_id" value="<?= htmlspecialchars($row['id']) ?>">
                                    <input type="hidden" name="new_status" value="Accepted">
                                    <button type="submit">Accept</button>
                                </form>
                                <form method="POST" action="admin_approval.php" style="display: inline;">
                                    <input type="hidden" name="appointment_id" value="<?= htmlspecialchars($row['id']) ?>">
                                    <input type="hidden" name="new_status" value="Rejected">
                                    <button type="submit">Reject</button>
                                </form>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                </table>
                    </div>
    <div id="pagination">
    <?php
    $total_pages = max($total_pages, 1); // Ensure there's at least 1 page
    $input_page = $current_page; // Default to the current page for the input

    // Previous button
    if ($current_page > 1): ?>
        <form method="GET" action="admin_approval.php" style="display: inline;">
            <input type="hidden" name="page" value="<?= $current_page - 1 ?>">
            <button type="submit"><</button>
        </form>
    <?php else: ?>
    
    <?php endif; ?>

    <!-- Page input for user to change the page -->
    <form method="GET" action="admin_approval.php" style="display: inline;">
        <input type="number" name="page" value="<?= $input_page ?>" min="1" max="<?= $total_pages ?>" style="width: 50px;">
    </form>

    <!-- "of" text and last page link -->
    <?php if ($total_pages > 1): ?>
        <span>of</span>
        <a href="?page=<?= $total_pages ?>" class="<?= ($current_page == $total_pages) ? 'active' : '' ?>"><?= $total_pages ?></a>
    <?php endif; ?>

    <!-- Next button -->
    <?php if ($current_page < $total_pages): ?>
        <form method="GET" action="admin_approval.php" style="display: inline;">
            <input type="hidden" name="page" value="<?= $current_page + 1 ?>">
            <button type="submit">></button>
        </form>
    <?php else: ?>

    <?php endif; ?>
</div>
            <?php else: ?>
                <p>No pending appointments.</p>
            <?php endif; ?>
        </div>
        <!-- Add this button in your HTML where you want it to appear -->

    </div>
    <script src="calendar.js"></script>


</body>
</html>


<?php
$conn->close();
?>
