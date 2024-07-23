<?php
session_start();

// Redirect to login if homeowner is not logged in
if (!isset($_SESSION['homeowner_id'])) {
    header('Location: login.php');
    exit();
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

// Function to sanitize input data
function sanitize_input($data) {
    global $conn;
    return $conn->real_escape_string(trim(htmlspecialchars($data)));
}

// Handle form submission to book an appointment
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['book_appointment'])) {
    $date = sanitize_input($_POST['date']);
    $name = sanitize_input($_POST['name']);
    $email = sanitize_input($_POST['email']);
    $purpose = sanitize_input($_POST['purpose']);
    $amenity_id = sanitize_input($_POST['amenity_id']);
    $timeslot_ids = isset($_POST['timeslot_ids']) ? $_POST['timeslot_ids'] : []; // Handle array of timeslot IDs

    $homeowner_id = $_SESSION['homeowner_id'];
    $status = 'Pending';

    // Insert appointments for each selected timeslot
    foreach ($timeslot_ids as $timeslot_id) {
        // Ensure timeslot_id is an integer
        $timeslot_id = intval($timeslot_id);

        // Insert each appointment with the selected timeslot
        $sql_insert = "INSERT INTO appointments (homeowner_id, date, name, email, purpose, status, timeslot_id, amenity_id) 
                       VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
        $stmt_insert = $conn->prepare($sql_insert);
        $stmt_insert->bind_param("isssssii", $homeowner_id, $date, $name, $email, $purpose, $status, $timeslot_id, $amenity_id);

        if (!$stmt_insert->execute()) {
            echo "<p>Error: " . $stmt_insert->error . "</p>";
        }
    }

    // Redirect to avoid form resubmission
    header('Location: ' . $_SERVER['PHP_SELF']);
    exit();
}

// Fetch amenities for the dropdown
$sql_amenities = "SELECT * FROM amenities";
$result_amenities = $conn->query($sql_amenities);
$amenities = $result_amenities->fetch_all(MYSQLI_ASSOC);

// Fetch available timeslots for the selected date and amenity
$timeslots = [];
$selected_date = "";
if (isset($_GET['date']) && isset($_GET['amenity_id'])) {
    $selected_date = sanitize_input($_GET['date']);
    $amenity_id = sanitize_input($_GET['amenity_id']);
    $sql_timeslots = "SELECT * FROM timeslots WHERE amenity_id = ? AND date = ?";
    $stmt_timeslots = $conn->prepare($sql_timeslots);
    $stmt_timeslots->bind_param("is", $amenity_id, $selected_date);
    $stmt_timeslots->execute();
    $result_timeslots = $stmt_timeslots->get_result();
    $timeslots = $result_timeslots->fetch_all(MYSQLI_ASSOC);
}

// Remove past and rejected appointments
$sql_remove_past_rejected = "DELETE FROM appointments WHERE date < CURDATE() OR status = 'Rejected'";
$conn->query($sql_remove_past_rejected);

// Fetch booked appointments
$sql_booked_appointments = "SELECT a.id, a.date, a.name, a.email, a.purpose, a.status, t.time_start, t.time_end, am.name AS amenity_name
                            FROM appointments a
                            JOIN timeslots t ON a.timeslot_id = t.id
                            JOIN amenities am ON a.amenity_id = am.id
                            WHERE a.homeowner_id = ? AND a.status = 'Pending' AND a.date >= CURDATE()";
$stmt_booked_appointments = $conn->prepare($sql_booked_appointments);
$stmt_booked_appointments->bind_param("i", $_SESSION['homeowner_id']);
$stmt_booked_appointments->execute();
$result_booked_appointments = $stmt_booked_appointments->get_result();
$booked_appointments = $result_booked_appointments->fetch_all(MYSQLI_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Appointment</title>
    <link rel="stylesheet" href="custom_calendar.css">
    <link rel="stylesheet" href="modal.css">
    <link rel="stylesheet" href="amenity_booking.css">
</head>
<body>
<?php include 'usersidebar.php'; ?>

   <!-- TIMESLOTS MODAL -->
   <div id="timeslot-modal" class="modal">
        <div class="modal-backdrop"></div>
            <div class="modal-content">
                <span class="close">&times;</span>
                <h2>Select Time Slots</h2>
                <form id="timeslot-form" method="POST" action="book_appointment.php" onsubmit="return validateTimeslotSelection()">
                    <div class="form-section">
                        <button type="button" class="collapsible">Available Time Slots</button>
                        <div class="collapsible-content" id="timeslot-container">
                            <!-- Checkboxes will be populated here -->
                        </div>
                    </div>
                    <p id="no-timeslots" style="display: none;">No timeslots available.</p>

                    <!-- Error message for timeslot selection -->
                    <p id="timeslot-error-message" style="color: red; display: none;">Please select at least one timeslot.</p>

                    <!-- Form fields -->
                    <div class="form-field">
                        <label for="name">Name:</label>
                        <input type="text" id="name" name="name" required>
                    </div>

                    <div class="form-field">
                        <label for="email">Email:</label>
                        <input type="email" id="email" name="email" required>
                    </div>

                    <div class="form-field">
                        <label for="purpose">Purpose:</label>
                        <input type="text" id="purpose" name="purpose" required>
                    </div>

                    <input type="hidden" id="selected-date" name="date">
                    <input type="hidden" id="amenity-id" name="amenity_id">

                    <div class="form-field">
                        <button type="submit" name="book_appointment">Book Appointment</button>
                    </div>
                </form>
            </div>
        </div>

<div class="main-content">
    <div class="container">
        <h1>Appointment</h1>
        <!-- Booking Form -->
        <form method="GET" action="">
            <div>
                <label for="amenity">Select Amenity:</label>
                <select id="amenity" name="amenity_id" required>
                    <option value="">-- Select Amenity --</option>
                    <?php foreach ($amenities as $amenity): ?>
                        <option value="<?php echo htmlspecialchars($amenity['id']); ?>" <?php echo (isset($_GET['amenity_id']) && $_GET['amenity_id'] == $amenity['id']) ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($amenity['name']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <!-- CALENDAR -->
            <div id="calendar-container">
                <div id="calendar-nav">
                    <button id="prev-month" type="button">&lt;</button>
                    <span id="month-year"></span>
                    <button id="next-month" type="button">&gt;</button>
                </div>
                <div id="calendar">
                    <!-- Header cells for days of the week -->
                    <div class="calendar-header-cell">Sun</div>
                    <div class="calendar-header-cell">Mon</div>
                    <div class="calendar-header-cell">Tue</div>
                    <div class="calendar-header-cell">Wed</div>
                    <div class="calendar-header-cell">Thu</div>
                    <div class="calendar-header-cell">Fri</div>
                    <div class="calendar-header-cell">Sat</div>
                    <!-- Calendar cells for days will be inserted here -->
                </div>
            </div>

            <input type="hidden" id="selected-date" name="date">
  
        </form>

     

        <!-- Display Booked Appointments -->
        <h2>My Booked Appointments</h2>
        <?php if (!empty($booked_appointments)): ?>
            <table border="1">
                <tr>
                    <th>Seq. ID</th>
                    <th>Date</th>
                    <th>Amenity</th>
                    <th>Time Slot</th>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Purpose</th>
                    <th>Status</th>
                </tr>
                <?php
                $seq_id = 1; // Initialize sequential ID counter
                foreach ($booked_appointments as $appointment): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($seq_id++); // Display sequential ID ?></td>
                        <td><?php echo htmlspecialchars($appointment['date']); ?></td>
                        <td><?php echo htmlspecialchars($appointment['amenity_name']); ?></td>
                        <td><?php echo htmlspecialchars($appointment['time_start'] . ' - ' . $appointment['time_end']); ?></td>
                        <td><?php echo htmlspecialchars($appointment['name']); ?></td>
                        <td><?php echo htmlspecialchars($appointment['email']); ?></td>
                        <td><?php echo htmlspecialchars($appointment['purpose']); ?></td>
                        <td><?php echo htmlspecialchars($appointment['status']); ?></td>
                    </tr>
                <?php endforeach; ?>
            </table>
        <?php else: ?>
            <p>You have no booked appointments.</p>
        <?php endif; ?>
    </div>
</div>

<script src="custom_calendar.js" defer></script>
<script>
    function validateTimeslotSelection() {
        var checkboxes = document.querySelectorAll('#timeslot-container input[type="checkbox"]');
        var errorMessage = document.getElementById('timeslot-error-message');
        var atLeastOneChecked = Array.from(checkboxes).some(checkbox => checkbox.checked);

        if (!atLeastOneChecked) {
            errorMessage.style.display = 'block';
            return false; // Prevent form submission
        } else {
            errorMessage.style.display = 'none'; // Hide error message if valid
        }
        return true; // Allow form submission
    }

  

    document.querySelector('.modal .close').addEventListener('click', function() {
        document.getElementById('timeslot-modal').style.display = 'none';
    });
</script>
</body>
</html>

<?php
// Close the database connection
$conn->close();
?>
