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

// Function to sanitize input data
function sanitize_input($data) {
    global $conn;
    return $conn->real_escape_string(trim(htmlspecialchars($data)));
}

// Archive old timeslots
$today = date('Y-m-d');
$sql = "INSERT INTO archived_timeslots (date, time_start, time_end, amenity_id, is_available)
        SELECT date, time_start, time_end, amenity_id, is_available
        FROM timeslots
        WHERE date < ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $today);
$stmt->execute();

// Delete old timeslots from the main table
$sql = "DELETE FROM timeslots WHERE date < ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $today);
$stmt->execute();

// Handle form submissions for generating timeslots
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['action']) && $_POST['action'] == 'generate_timeslots') {
        $date = sanitize_input($_POST['date']);
        $amenity_id = sanitize_input($_POST['amenity_id']);
        
        $start_time = strtotime("09:00");
        $end_time = strtotime("22:00");
        $interval = 60; // 1 hour interval
        
        $timeslot_count = 0; // Counter to track number of timeslots created
        
        while ($start_time < $end_time) {
            $next_time = strtotime("+{$interval} minutes", $start_time);

            // Prepare and execute the SQL statement
            $sql = "INSERT INTO timeslots (date, time_start, time_end, amenity_id, is_available) VALUES (?, ?, ?, ?, TRUE)";
            $stmt = $conn->prepare($sql);

            // Bind parameters
            $start_time_formatted = date("H:i", $start_time);
            $next_time_formatted = date("H:i", $next_time);
            $stmt->bind_param("ssss", $date, $start_time_formatted, $next_time_formatted, $amenity_id);

            // Execute and check for success
            if ($stmt->execute()) {
                $timeslot_count++;
            } else {
                echo "<p>Error: " . $stmt->error . "</p>";
            }

            $start_time = $next_time;
        }

        // Set the success message if timeslots were created
        if ($timeslot_count > 0) {
            $_SESSION['success_message'] = "{$timeslot_count} timeslot(s) created.";
        }

        // Close statement
        $stmt->close();

        // Redirect to the same page to clear POST data
        header("Location: " . $_SERVER['PHP_SELF']);
        exit();
    } elseif (isset($_POST['action']) && $_POST['action'] == 'manual_add_timeslots') {
        $date = sanitize_input($_POST['manual_date']);
        $amenity_id = sanitize_input($_POST['manual_amenity_id']);
        $timeslots = $_POST['timeslots']; // Array of timeslots

        $timeslot_count = 0;

        foreach ($timeslots as $timeslot) {
            list($start_time, $end_time) = explode('-', $timeslot);
            $start_time = trim($start_time);
            $end_time = trim($end_time);

            // Prepare and execute the SQL statement
            $sql = "INSERT INTO timeslots (date, time_start, time_end, amenity_id, is_available) VALUES (?, ?, ?, ?, TRUE)";
            $stmt = $conn->prepare($sql);

            // Bind parameters
            $stmt->bind_param("ssss", $date, $start_time, $end_time, $amenity_id);

            // Execute and check for success
            if ($stmt->execute()) {
                $timeslot_count++;
            } else {
                echo "<p>Error: " . $stmt->error . "</p>";
            }

            // Close statement
            $stmt->close();
        }

        // Set the success message if timeslots were created
        if ($timeslot_count > 0) {
            $_SESSION['success_message'] = "{$timeslot_count} manual timeslot(s) created.";
        }

        // Redirect to the same page to clear POST data
        header("Location: " . $_SERVER['PHP_SELF']);
        exit();
    }
}

// Fetch all timeslots for display
$sql = "SELECT timeslots.*, amenities.name AS amenity_name
        FROM timeslots
        JOIN amenities ON timeslots.amenity_id = amenities.id";
$result = $conn->query($sql);
$timeslots = $result->fetch_all(MYSQLI_ASSOC);

// Group timeslots by amenity_id
$grouped_timeslots = [];
foreach ($timeslots as $timeslot) {
    $grouped_timeslots[$timeslot['amenity_id']][] = $timeslot;
}

// Fetch amenities for the form
$sql = "SELECT * FROM amenities";
$amenities_result = $conn->query($sql);
$amenities = $amenities_result->fetch_all(MYSQLI_ASSOC);

// Create an associative array for amenities
$amenity_options = [];
foreach ($amenities as $amenity) {
    $amenity_options[$amenity['id']] = $amenity['name'];
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Manage Timeslots</title>
    <link rel="stylesheet" href="styles.css">
    <script>
        // JavaScript to display success message if set
        document.addEventListener('DOMContentLoaded', function() {
            <?php if (isset($_SESSION['success_message'])): ?>
                alert("<?php echo addslashes($_SESSION['success_message']); ?>");
                <?php unset($_SESSION['success_message']); // Clear the message ?>
            <?php endif; ?>
        });
    </script>
</head>
<body>
    <h1>Manage Timeslots</h1>

    <!-- Form for generating automatic timeslots -->
    <form method="POST">
        <h2>Generate Timeslots Automatically</h2>
        <div>
            <label for="date">Date:</label>
            <input type="date" id="date" name="date" required>
        </div>
        <div>
            <label for="amenity_id">Amenity ID:</label>
            <input type="number" id="amenity_id" name="amenity_id" min="1" max="6" required>
            <datalist id="amenity_list">
                <?php foreach ($amenity_options as $id => $name): ?>
                    <option value="<?php echo htmlspecialchars($id); ?>"><?php echo htmlspecialchars($name); ?></option>
                <?php endforeach; ?>
            </datalist>
        </div>
        <div>
            <button type="submit" name="action" value="generate_timeslots">Generate Timeslots</button>
        </div>
    </form>

    <!-- Form for manually adding timeslots -->
    <form method="POST">
        <h2>Manually Add Timeslots</h2>
        <div>
            <label for="manual_date">Date:</label>
            <input type="date" id="manual_date" name="manual_date" required>
        </div>
        <div>
            <label for="manual_amenity_id">Amenity ID:</label>
            <input type="number" id="manual_amenity_id" name="manual_amenity_id" min="1" max="6" required>
            <datalist id="manual_amenity_list">
                <?php foreach ($amenity_options as $id => $name): ?>
                    <option value="<?php echo htmlspecialchars($id); ?>"><?php echo htmlspecialchars($name); ?></option>
                <?php endforeach; ?>
            </datalist>
        </div>
        <div id="manual_timeslots">
            <label for="timeslots">Timeslots (format: HH:MM - HH:MM):</label>
            <textarea id="timeslots" name="timeslots[]" rows="5" placeholder="Enter each timeslot on a new line" required></textarea>
        </div>
        <div>
            <button type="submit" name="action" value="manual_add_timeslots">Add Timeslots</button>
        </div>
    </form>

    <h2>Available Timeslots</h2>
    <?php foreach ($grouped_timeslots as $amenity_id => $timeslots): ?>
        <h3><?php echo htmlspecialchars($amenity_options[$amenity_id]); ?></h3>
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Date</th>
                    <th>Start Time</th>
                    <th>End Time</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($timeslots as $timeslot): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($timeslot['id']); ?></td>
                        <td><?php echo htmlspecialchars($timeslot['date']); ?></td>
                        <td><?php echo htmlspecialchars($timeslot['time_start']); ?></td>
                        <td><?php echo htmlspecialchars($timeslot['time_end']); ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endforeach; ?>

    <?php
    // Close the database connection
    $conn->close();
    ?>
</body>
</html>
