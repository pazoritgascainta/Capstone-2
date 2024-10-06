<?php
session_name('user_session'); 
session_start();

// Redirect to login if homeowner is not logged in
if (!isset($_SESSION['homeowner_id'])) {
    header('Location: login.php');
    exit();
}

// Retrieve the homeowner ID from the session
$homeowner_id = $_SESSION['homeowner_id'];

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

// Function to check if a timeslot is already booked by the same homeowner
function is_timeslot_booked($conn, $homeowner_id, $date, $timeslot_id, $amenity_id) {
    $sql_check = "SELECT COUNT(*) FROM appointments WHERE homeowner_id = ? AND date = ? AND timeslot_id = ? AND amenity_id = ?";
    $stmt_check = $conn->prepare($sql_check);
    $stmt_check->bind_param("issi", $homeowner_id, $date, $timeslot_id, $amenity_id);
    $stmt_check->execute();
    $stmt_check->bind_result($count);
    $stmt_check->fetch();
    $stmt_check->close();
    return $count > 0;
}

// Handle form submission to book an appointment
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['book_appointment'])) {
    $date = sanitize_input($_POST['date']);
    $name = sanitize_input($_POST['name']);
    $email = sanitize_input($_POST['email']);
    $purpose = sanitize_input($_POST['purpose']);
    $amenity_id = sanitize_input($_POST['amenity_id']);
    $timeslot_ids = isset($_POST['timeslot_ids']) ? $_POST['timeslot_ids'] : [];

    $homeowner_id = $_SESSION['homeowner_id'];
    $status = 'Pending';

    $errors = [];
    foreach ($timeslot_ids as $timeslot_id) {
        $timeslot_id = intval($timeslot_id);

        // Check if the timeslot is already booked
        if (is_timeslot_booked($conn, $homeowner_id, $date, $timeslot_id, $amenity_id)) {
            $errors[] = "You already booked timeslot ID $timeslot_id.";
            continue;
        }

        // Insert the new appointment
        $sql_insert = "INSERT INTO appointments (homeowner_id, date, name, email, purpose, status, timeslot_id, amenity_id) 
                       VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
        $stmt_insert = $conn->prepare($sql_insert);
        if (!$stmt_insert) {
            $errors[] = "Failed to prepare SQL statement: " . $conn->error;
            continue;
        }
        $stmt_insert->bind_param("isssssii", $homeowner_id, $date, $name, $email, $purpose, $status, $timeslot_id, $amenity_id);

        if (!$stmt_insert->execute()) {
            $errors[] = "Failed to execute SQL statement: " . $stmt_insert->error;
        }
    }

    // Prepare response and redirect
    if (empty($errors)) {
        $_SESSION['message'] = ['status' => 'success', 'message' => 'Appointment booked successfully.'];
    } else {
        $_SESSION['message'] = ['status' => 'error', 'message' => implode(', ', $errors)];
    }

    // Redirect to the booking page
    header('Location: amenity_booking.php');
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

// Pagination settings
$limit = 10; // Number of appointments per page
$current_page = isset($_GET['page']) ? intval($_GET['page']) : 1;
$offset = ($current_page - 1) * $limit;

// Fetch total number of appointments
$sql_count = "SELECT COUNT(*) as total FROM appointments WHERE homeowner_id = ? AND status = 'Pending' AND date >= CURDATE()";
$stmt_count = $conn->prepare($sql_count);
$stmt_count->bind_param("i", $homeowner_id);
$stmt_count->execute();
$result_count = $stmt_count->get_result();
$row_count = $result_count->fetch_assoc();
$total_appointments = $row_count['total'];
$total_pages = max(ceil($total_appointments / $limit), 1);


// Fetch accepted appointments
$sql_accepted_appointments = "SELECT a.id, a.date, a.name, a.email, a.purpose, a.status, t.time_start, t.time_end, am.name AS amenity_name, a.amount
                              FROM accepted_appointments a
                              JOIN timeslots t ON a.timeslot_id = t.id
                              JOIN amenities am ON a.amenity_id = am.id
                              WHERE a.homeowner_id = ? AND a.date >= CURDATE()";
$stmt_accepted_appointments = $conn->prepare($sql_accepted_appointments);
$stmt_accepted_appointments->bind_param("i", $_SESSION['homeowner_id']);
$stmt_accepted_appointments->execute();
$result_accepted_appointments = $stmt_accepted_appointments->get_result();
$accepted_appointments = $result_accepted_appointments->fetch_all(MYSQLI_ASSOC);



// Fetch booked appointments with pagination
$sql_booked_appointments = "SELECT a.id, a.date, a.name, a.email, a.purpose, a.status, t.time_start, t.time_end, am.name AS amenity_name
                            FROM appointments a
                            JOIN timeslots t ON a.timeslot_id = t.id
                            JOIN amenities am ON a.amenity_id = am.id
                            WHERE a.homeowner_id = ? AND a.status = 'Pending' AND a.date >= CURDATE()
                            LIMIT ?, ?";
$stmt_booked_appointments = $conn->prepare($sql_booked_appointments);
$stmt_booked_appointments->bind_param("iii", $_SESSION['homeowner_id'], $offset, $limit);
$stmt_booked_appointments->execute();
$result_booked_appointments = $stmt_booked_appointments->get_result();
$booked_appointments = $result_booked_appointments->fetch_all(MYSQLI_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta charset="UTF-8">
    <title>Appointment</title>
    <link rel="stylesheet" href="custom_calendar.css">
    <link rel="stylesheet" href="modal.css">
    <link rel="stylesheet" href="amenity_booking.css">
    <link rel="stylesheet" href="amenity.css">
</head>

<body>
<?php include 'usersidebar.php'; ?>



<!-- TIMESLOTS MODAL -->
<div id="timeslot-modal" class="modal">
    <div class="modal-backdrop"></div>
    <div class="modal-content">
        <span class="close">&times;</span>
        <h2>Select Time Slots</h2>
        <form id="timeslot-form" method="POST" onsubmit="handleFormSubmit(event)">
            <div class="form-section">
                <button type="button" class="collapsible">Available Time Slots</button>
                <div class="collapsible-content" id="timeslot-container">
                    <!-- Checkboxes will be populated here -->
                </div>
            </div>
            <p id="no-timeslots" style="display: none;">No timeslots available.</p>
            <p id="timeslot-error-message" style="color: red; display: none;">Please select at least one timeslot.</p>

            <!-- Hidden fields -->
            <input type="hidden" id="hidden-amenity-id" name="amenity_id">
            <input type="hidden" id="selected-date" name="date">
            <input type="hidden" id="homeowner-id" name="homeowner_id" value="<?php echo htmlspecialchars($homeowner_id, ENT_QUOTES, 'UTF-8'); ?>">

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

            <button type="submit" name="book_appointment">Book Appointment</button>
        </form>
    </div>
</div>

<div class="main-content">
<h1>Appointment</h1>
    <div class="container">
       
    
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
            <input type="hidden" id="hidden-amenity-id" name="amenity_id">
            <div id="message-container">
                <?php
// Display session message if it exists
if (isset($_SESSION['message'])) {
    $message = $_SESSION['message'];
    echo '<div class="alert alert-' . ($message['status'] == 'success' ? 'success' : 'danger') . '">';
    echo htmlspecialchars($message['message']);
    echo '</div>';
    unset($_SESSION['message']);
}
?>
<div id="message-container">
    <?php
    // Check for status and message parameters in the URL
    $status = isset($_GET['status']) ? $_GET['status'] : '';
    $message = isset($_GET['message']) ? htmlspecialchars($_GET['message'], ENT_QUOTES, 'UTF-8') : '';

    if ($status === 'success'): ?>
        <div id="message" class="message-success">
            Appointment booked successfully.
        </div>
    <?php elseif ($status === 'error'): ?>
        <div id="message" class="message-error">
            <?php echo $message; ?>
        </div>
    <?php endif; ?>
</div>
                <!-- Messages will be displayed here -->
            </div>
            <!-- CALENDAR -->
            <div id="calendar-box">
                <div id="calendar-container">
                    <div id="calendar-nav">
                        <button id="prev-month" type="button">&lt;</button>
                        <span id="month-year"></span>
                        <button id="next-month" type="button">&gt;</button>
                    </div>
                    <div id="calendar">
                        <div class="calendar-header-cell">Sun</div>
                        <div class="calendar-header-cell">Mon</div>
                        <div class="calendar-header-cell">Tue</div>
                        <div class="calendar-header-cell">Wed</div>
                        <div class="calendar-header-cell">Thu</div>
                        <div class="calendar-header-cell">Fri</div>
                        <div class="calendar-header-cell">Sat</div>
                        <!-- Days will be generated by JavaScript -->
                    </div>
                </div>
            </div>
    
        </form>
        <br>
        <div class="table-toggle">
        <button id="show-pending" onclick="showTable('pending')"> Pending Appointments</button>
    <button id="show-accepted" onclick="showTable('accepted')"> Accepted Appointments</button>

</div>
        <!-- Appointments Table -->
        <h2 id="pending-title">Pending Appointments</h2>
<table id="pending-appointments-table">
    <thead>
        <tr>
            <th>Date</th>
            <th>Time</th>
            <th>Name</th>
            <th>Email</th>
            <th>Purpose</th>
            <th>Status</th>
            <th>Amenity</th>
            <th>Action</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($booked_appointments as $appointment): ?>
            <tr id="appointment-<?php echo $appointment['id']; ?>">
                <td><?php echo htmlspecialchars($appointment['date']); ?></td>
                <td><?php echo htmlspecialchars($appointment['time_start']) . ' - ' . htmlspecialchars($appointment['time_end']); ?></td>
                <td><?php echo htmlspecialchars($appointment['name']); ?></td>
                <td><?php echo htmlspecialchars($appointment['email']); ?></td>
                <td><?php echo htmlspecialchars($appointment['purpose']); ?></td>
                <td><?php echo htmlspecialchars($appointment['status']); ?></td>
                <td><?php echo htmlspecialchars($appointment['amenity_name']); ?></td>
                <td>
                    <button onclick="cancelAppointment(<?php echo $appointment['id']; ?>)">Cancel</button>
                </td>
            </tr>
        <?php endforeach; ?>
    </tbody>
</table>

<!-- Accepted Appointments Table -->
<h2 style="display:none;" id="accepted-title">Accepted Appointments</h2>
<table id="accepted-appointments-table" style="display:none;">
    <thead>
        <tr>
            <th>Date</th>
            <th>Time</th>
            <th>Name</th>
            <th>Email</th>
            <th>Purpose</th>
            <th>Status</th>
            <th>Amenity</th>
            <th>Amount</th> <!-- New column for Amount -->
            <th>Action</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($accepted_appointments as $appointment): ?>
            <tr id="appointment-<?php echo $appointment['id']; ?>">
                <td><?php echo htmlspecialchars($appointment['date']); ?></td>
                <td><?php echo htmlspecialchars($appointment['time_start']) . ' - ' . htmlspecialchars($appointment['time_end']); ?></td>
                <td><?php echo htmlspecialchars($appointment['name']); ?></td>
                <td><?php echo htmlspecialchars($appointment['email']); ?></td>
                <td><?php echo htmlspecialchars($appointment['purpose']); ?></td>
                <td><?php echo htmlspecialchars($appointment['status']); ?></td>
                <td><?php echo htmlspecialchars($appointment['amenity_name']); ?></td>
                <td><?php echo htmlspecialchars($appointment['amount']); ?></td> <!-- Amount value -->
                <td>
                    <button onclick="cancelAppointment(<?php echo $appointment['id']; ?>)">Cancel</button>
                </td>
            </tr>
        <?php endforeach; ?>
    </tbody>
</table>



        <!-- Pagination Controls -->
<div id="pagination">
    <?php if ($current_page > 1): ?>
        <button onclick="window.location.href='?page=<?php echo $current_page - 1; ?>'"><</button>
    <?php else: ?>
      
    <?php endif; ?>

    <!-- Page input for user to change the page -->
    <form method="GET" action="" style="display: inline;">
        <input type="number" name="page" value="<?php echo $current_page; ?>" min="1" max="<?php echo $total_pages; ?>" style="width: 50px;">
    </form>

    <!-- "of" text and last page link -->
    <?php if ($total_pages > 1): ?>
        <span>of</span>
        <a href="?page=<?php echo $total_pages; ?>" class="<?php echo ($current_page == $total_pages) ? 'active' : ''; ?>"><?php echo $total_pages; ?></a>
    <?php endif; ?>

    <!-- Next button -->
    <?php if ($current_page < $total_pages): ?>
        <button onclick="window.location.href='?page=<?php echo $current_page + 1; ?>'">></button>
    <?php else: ?>
        
    <?php endif; ?>
</div>
    </div>
</div>

<script src="custom_calendar.js"></script>
<script src="modal.js"></script>
<script>
function handleFormSubmit(event) {
    event.preventDefault(); // Prevent default form submission

    const form = event.target;
    const formData = new FormData(form);

    fetch('book_appointment.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.text()) // Get response as text
    .then(responseText => {
        console.log('Raw Response:', responseText); // Log the raw response for debugging

        // Split the response into status and message
        const [status, ...messageParts] = responseText.split(':');
        const message = messageParts.join(':'); // Join remaining parts as message

        const messageElement = document.getElementById('message');
        messageElement.textContent = message; // Set the message text

        // Clear any previous classes
        messageElement.className = '';

        // Apply the appropriate class based on the status
        if (status === 'success') {
            messageElement.classList.add('message-success');
            
            // Clear the form fields
            form.reset();
            document.getElementById('timeslot-container').innerHTML = ''; // Clear time slots
            document.getElementById('hidden-amenity-id').value = ''; // Clear hidden fields
            document.getElementById('selected-date').value = ''; // Clear hidden fields
        } else if (status === 'error') {
            messageElement.classList.add('message-error');
        } else {
            messageElement.classList.add('message-unexpected');
        }
    })
    .catch(error => {
        const messageElement = document.getElementById('message');
        messageElement.textContent = 'An error occurred: ' + error.message; // Display fetch error
        messageElement.className = 'message-error'; // Apply error class
    });
}


</script>
<script>
function showTable(type) {
    const pendingTitle = document.getElementById('pending-title');
    const pendingTable = document.getElementById('pending-appointments-table');
    const acceptedTable = document.getElementById('accepted-appointments-table');
    const acceptedTitle = document.getElementById('accepted-title');

    if (type === 'accepted') {
        pendingTitle.style.display = 'none'; // Hide pending title
        pendingTable.style.display = 'none';  // Hide pending table
        acceptedTable.style.display = 'table'; // Show accepted table
        acceptedTitle.style.display = 'block';  // Show accepted title
    } else {
        pendingTitle.style.display = 'block';   // Show pending title
        pendingTable.style.display = 'table';    // Show pending table
        acceptedTable.style.display = 'none';     // Hide accepted table
        acceptedTitle.style.display = 'none';      // Hide accepted title
    }
}

// Set the initial table to show
document.addEventListener("DOMContentLoaded", function() {
    showTable('pending'); // Show pending by default
});
</script>


<script>
document.addEventListener("DOMContentLoaded", function() {
    // Function to hide the message after a delay
    function hideMessage() {
        var messageElement = document.getElementById('message-container');
        if (messageElement) {
            setTimeout(function() {
                messageElement.style.display = 'none';
            }, 5000); // Hide after 5 seconds
        }
    }

    // Call hideMessage if there's a success or error message
    hideMessage();
});
</script>
<script>
function cancelAppointment(appointmentId) {
    if (confirm('Are you sure you want to cancel this appointment?')) {
        const xhr = new XMLHttpRequest();
        xhr.open('POST', 'cancel_appointment.php', true);
        xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
        xhr.onreadystatechange = function () {
            if (xhr.readyState === 4 && xhr.status === 200) {
                const response = JSON.parse(xhr.responseText);
                if (response.success) {
                    document.getElementById('appointment-' + appointmentId).remove();
                    alert('Appointment canceled successfully.');
                } else {
                    alert('Failed to cancel the appointment: ' + response.message);
                }
            }
        };
        xhr.send('appointment_id=' + appointmentId);
    }
}
</script>

</body>
</html>

