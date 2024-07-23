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

// Handle form submissions for creating, updating, or deleting timeslots
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['action'])) {
        $id = isset($_POST['id']) ? sanitize_input($_POST['id']) : null;
        $date = isset($_POST['date']) ? sanitize_input($_POST['date']) : null;
        $time_start = isset($_POST['time_start']) ? sanitize_input($_POST['time_start']) : null;
        $time_end = isset($_POST['time_end']) ? sanitize_input($_POST['time_end']) : null;
        $amenity_id = isset($_POST['amenity_id']) ? sanitize_input($_POST['amenity_id']) : null;

        switch ($_POST['action']) {
            case 'create':
                $sql = "INSERT INTO timeslots (date, time_start, time_end, amenity_id, is_available) VALUES (?, ?, ?, ?, TRUE)";
                $stmt = $conn->prepare($sql);
                $stmt->bind_param("ssss", $date, $time_start, $time_end, $amenity_id);
                if ($stmt->execute()) {
                    echo "<p>Timeslot created successfully!</p>";
                } else {
                    echo "<p>Error: " . $stmt->error . "</p>";
                }
                break;

            case 'update':
                $sql = "UPDATE timeslots SET date = ?, time_start = ?, time_end = ?, amenity_id = ? WHERE id = ?";
                $stmt = $conn->prepare($sql);
                $stmt->bind_param("ssssi", $date, $time_start, $time_end, $amenity_id, $id);
                if ($stmt->execute()) {
                    echo "<p>Timeslot updated successfully!</p>";
                } else {
                    echo "<p>Error: " . $stmt->error . "</p>";
                }
                break;

            case 'delete':
                $sql = "DELETE FROM timeslots WHERE id = ?";
                $stmt = $conn->prepare($sql);
                $stmt->bind_param("i", $id);
                if ($stmt->execute()) {
                    echo "<p>Timeslot deleted successfully!</p>";
                } else {
                    echo "<p>Error: " . $stmt->error . "</p>";
                }
                break;
                
            case 'generate_timeslots':
                $start_time = sanitize_input($_POST['start_time']);
                $end_time = sanitize_input($_POST['end_time']);
                $interval = sanitize_input($_POST['interval']);
                $date = sanitize_input($_POST['date']);
                $amenity_id = sanitize_input($_POST['amenity_id']);
                
                $current_time = strtotime($start_time);
                $end_time = strtotime($end_time);
                
                while ($current_time < $end_time) {
                    $next_time = strtotime("+$interval minutes", $current_time);
                    if ($next_time > $end_time) break;
                    
                    $sql = "INSERT INTO timeslots (date, time_start, time_end, amenity_id, is_available) VALUES (?, ?, ?, ?, TRUE)";
                    $stmt = $conn->prepare($sql);
                    $stmt->bind_param("ssss", $date, date("H:i", $current_time), date("H:i", $next_time), $amenity_id);
                    if ($stmt->execute()) {
                        echo "<p>Timeslot created: " . date("H:i", $current_time) . " - " . date("H:i", $next_time) . "</p>";
                    } else {
                        echo "<p>Error: " . $stmt->error . "</p>";
                    }
                    $current_time = $next_time;
                }
                break;
        }
    }
}

// Fetch all timeslots for display
$sql = "SELECT * FROM timeslots";
$result = $conn->query($sql);
$timeslots = $result->fetch_all(MYSQLI_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Manage Timeslots</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <h1>Manage Timeslots</h1>

    <form method="POST">
        <h2>Generate Timeslots</h2>
        <div>
            <label for="date">Date:</label>
            <input type="date" id="date" name="date" required>
        </div>
        <div>
            <label for="start_time">Start Time:</label>
            <input type="time" id="start_time" name="start_time" required>
        </div>
        <div>
            <label for="end_time">End Time:</label>
            <input type="time" id="end_time" name="end_time" required>
        </div>
        <div>
            <label for="interval">Interval (minutes):</label>
            <input type="number" id="interval" name="interval" value="60" required>
        </div>
        <div>
            <label for="amenity_id">Amenity ID:</label>
            <input type="number" id="amenity_id" name="amenity_id" required>
        </div>
        <div>
            <button type="submit" name="action" value="generate_timeslots">Generate Timeslots</button>
        </div>
    </form>

    <h2>Available Timeslots</h2>
    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Date</th>
                <th>Start Time</th>
                <th>End Time</th>
                <th>Amenity ID</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($timeslots as $timeslot): ?>
                <tr>
                    <td><?php echo htmlspecialchars($timeslot['id']); ?></td>
                    <td><?php echo htmlspecialchars($timeslot['date']); ?></td>
                    <td><?php echo htmlspecialchars($timeslot['time_start']); ?></td>
                    <td><?php echo htmlspecialchars($timeslot['time_end']); ?></td>
                    <td><?php echo htmlspecialchars($timeslot['amenity_id']); ?></td>
                    <td>
                        <form method="POST" style="display:inline;">
                            <input type="hidden" name="id" value="<?php echo htmlspecialchars($timeslot['id']); ?>">
                            <input type="hidden" name="date" value="<?php echo htmlspecialchars($timeslot['date']); ?>">
                            <input type="hidden" name="time_start" value="<?php echo htmlspecialchars($timeslot['time_start']); ?>">
                            <input type="hidden" name="time_end" value="<?php echo htmlspecialchars($timeslot['time_end']); ?>">
                            <input type="hidden" name="amenity_id" value="<?php echo htmlspecialchars($timeslot['amenity_id']); ?>">
                            <button type="submit" name="action" value="update">Edit</button>
                        </form>
                        <form method="POST" style="display:inline;">
                            <input type="hidden" name="id" value="<?php echo htmlspecialchars($timeslot['id']); ?>">
                            <button type="submit" name="action" value="delete">Delete</button>
                        </form>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

    <?php
    // Close the database connection
    $conn->close();
    ?>
</body>
</html>
