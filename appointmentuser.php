<?php
session_start();

// Redirect to login if homeowner is not logged in
if (!isset($_SESSION['homeowner_id'])) {
    header('Location: login.php');
    exit();
}

// Database connection (adjust with your credentials)
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

// Function to sanitize input data
function sanitize_input($data) {
    global $conn;
    return $conn->real_escape_string(trim(htmlspecialchars($data)));
}

// Function to format date for JavaScript
function format_date_for_js($date) {
    return date("Y-m-d", strtotime($date));
}

// Handling form submission to book appointment
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $date = sanitize_input($_POST['date']);
    $time = sanitize_input($_POST['time']);
    $name = sanitize_input($_POST['name']);
    $email = sanitize_input($_POST['email']);
    $purpose = sanitize_input($_POST['purpose']);

    // Check if the appointment slot is available
    $sql_check = "SELECT COUNT(*) AS count FROM appointments WHERE date = ? AND time = ?";
    $stmt_check = $conn->prepare($sql_check);
    $stmt_check->bind_param("ss", $date, $time);
    $stmt_check->execute();
    $result_check = $stmt_check->get_result();
    $row_check = $result_check->fetch_assoc();

    if ($row_check['count'] > 0) {
        echo "Error: Appointment slot already booked.";
        exit();
    }

    // Insert appointment into database with pending status
    $homeowner_id = $_SESSION['homeowner_id'];
    $status = 'Pending';

    $sql_insert = "INSERT INTO appointments (homeowner_id, date, time, name, email, purpose, status) 
                   VALUES (?, ?, ?, ?, ?, ?, ?)";
    $stmt_insert = $conn->prepare($sql_insert);
    $stmt_insert->bind_param("issssss", $homeowner_id, $date, $time, $name, $email, $purpose, $status);

    if ($stmt_insert->execute()) {
        echo "Appointment booked successfully!";
    } else {
        echo "Error: " . $stmt_insert->error;
    }
    exit();
}

// SQL to get appointments for the logged-in homeowner
$homeowner_id = $_SESSION['homeowner_id'];
$sql_appointments = "SELECT * FROM appointments WHERE homeowner_id = ?";
$stmt_appointments = $conn->prepare($sql_appointments);
$stmt_appointments->bind_param("i", $homeowner_id);
$stmt_appointments->execute();
$result_appointments = $stmt_appointments->get_result();

// Fetch all booked appointments into an array
$booked_appointments = [];
if ($result_appointments->num_rows > 0) {
    while ($row = $result_appointments->fetch_assoc()) {
        $booked_appointments[] = $row;
    }
}

// Prepare booked dates for JavaScript
$booked_dates = array_map('format_date_for_js', array_column($booked_appointments, 'date'));

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Appointment</title>
    <link rel="stylesheet" href="usersidebar.css">
    <link rel="stylesheet" href="datecs.css">
    <link rel="stylesheet" href="appointmentuser.css">
</head>
<body>
    <?php include 'usersidebar.php'; ?>

    <div class="main-content">
        <div class="container">
            <h1>St. Monique Appointment Schedule</h1>
            <div class="calendar body">
                <div class="month-navigation">
                    <button id="prevMonth">&lt;</button>
                    <div class="month"></div>
                    <button id="nextMonth">&gt;</button>
                </div>
                <div class="days">
                    <div class="day">Sunday</div>
                    <div class="day">Monday</div>
                    <div class="day">Tuesday</div>
                    <div class="day">Wednesday</div>
                    <div class="day">Thursday</div>
                    <div class="day">Friday</div>
                    <div class="day">Saturday</div>
                </div>
                <div class="dates">
                    <?php
                    $currentDate = new DateTime();
                    $daysInMonth = intval($currentDate->format('t'));
                    $firstDay = new DateTime('first day of this month');
                    $firstDayIndex = intval($firstDay->format('w'));

                    for ($i = 1; $i <= $firstDayIndex + $daysInMonth; $i++) {
                        $day = $i - $firstDayIndex;

                        if ($i > $firstDayIndex && $day <= $daysInMonth) {
                            $dateObj = new DateTime($currentDate->format('Y-m') . "-$day");
                            $date = $dateObj->format('Y-m-d');
                            $formatted_date = $dateObj->format('Y-m-d');
                            $is_booked = in_array($formatted_date, $booked_dates);
                            $class_name = $is_booked ? 'booked-date' : '';

                            echo "<div class='date $class_name' onclick='showTimetable(\"$date\")'>$day</div>";
                        } else {
                            echo "<div class='date empty'></div>";
                        }
                    }
                    ?>
                </div>
            </div>
            <div id="selectedDate"></div>
            <div id="timetable-container">
                <!-- Timetable for the selected day will be displayed here -->
            </div>
            <div class="booked-appointments">
                <?php
                if (!empty($booked_appointments)) {
                    echo "<h2>Your Booked Appointments:</h2>";
                    echo "<table>";
                    echo "<tr><th>Date</th><th>Time</th><th>Purpose</th><th>ID</th><th>Status</th></tr>";
                    foreach ($booked_appointments as $appointment) {
                        echo "<tr>";
                        echo "<td>" . htmlspecialchars($appointment["date"]) . "</td>";
                        echo "<td>" . htmlspecialchars($appointment["time"]) . "</td>";
                        echo "<td>" . htmlspecialchars($appointment["purpose"]) . "</td>";
                        echo "<td>" . htmlspecialchars($appointment["id"]) . "</td>";
                        echo "<td>" . htmlspecialchars($appointment["status"]) . "</td>";
                        echo "</tr>";
                    }
                    echo "</table>";
                } else {
                    echo "<p>No appointments booked.</p>";
                }
                ?>
            </div>
        </div>
    </div>

   
    <script src="date.js"></script>
    <script src="popup.js"></script>
  
</body>
</html>

<?php
$conn->close();
?>
