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

// Example initialization (adjust according to your actual logic)

// Get the current page from the URL, default to 1 if not set
$current_page = isset($_GET['page']) ? (int)$_GET['page'] : 1;

// Define the number of items per page
$items_per_page = 10;

// Get the total number of items from your data source (e.g., database)
$total_items = 1000; // Example value, replace with actual query to count items

// Calculate the total number of pages
$total_pages = ceil($total_items / $items_per_page);

// Ensure the current page is within valid range
$current_page = max(1, min($current_page, $total_pages));

// Fetch the items for the current page (adjust query as needed)
$offset = ($current_page - 1) * $items_per_page;
$timeslots = []; // Replace with actual data fetching logic


// Function to sanitize input data
function sanitize_input($data) {
    global $conn;
    return $conn->real_escape_string(trim(htmlspecialchars($data)));
}

// Archive timeslots
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

// Fetch amenities for the form
$sql = "SELECT * FROM amenities";
$amenities_result = $conn->query($sql);
$amenities = $amenities_result->fetch_all(MYSQLI_ASSOC);

// Create an associative array for amenities
$amenity_options = [];
foreach ($amenities as $amenity) {
    $amenity_options[$amenity['id']] = $amenity['name'];
}

// Pagination and filtering
$amenity_filter = isset($_GET['amenity_id']) ? (int)$_GET['amenity_id'] : null;
$date_filter = isset($_GET['date']) ? sanitize_input($_GET['date']) : null;
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$limit = 10; // Number of timeslots per page
$offset = ($page - 1) * $limit;

// Fetch timeslots with pagination and filtering
$sql = "SELECT timeslots.*, amenities.name AS amenity_name
        FROM timeslots
        JOIN amenities ON timeslots.amenity_id = amenities.id";
$params = [];
$types = '';
if ($amenity_filter) {
    $sql .= " WHERE timeslots.amenity_id = ?";
    $params[] = $amenity_filter;
    $types .= 'i';
}
if ($date_filter) {
    $sql .= $amenity_filter ? " AND" : " WHERE";
    $sql .= " timeslots.date = ?";
    $params[] = $date_filter;
    $types .= 's';
}
$sql .= " LIMIT ? OFFSET ?";
$params[] = $limit;
$params[] = $offset;
$types .= 'ii';
$stmt = $conn->prepare($sql);
$stmt->bind_param($types, ...$params);
$stmt->execute();
$result = $stmt->get_result();
$timeslots = $result->fetch_all(MYSQLI_ASSOC);

// Fetch total count of timeslots for pagination
$sql = "SELECT COUNT(*) AS total FROM timeslots";
$params = [];
$types = '';
if ($amenity_filter) {
    $sql .= " WHERE amenity_id = ?";
    $params[] = $amenity_filter;
    $types .= 'i';
}
if ($date_filter) {
    $sql .= $amenity_filter ? " AND" : " WHERE";
    $sql .= " date = ?";
    $params[] = $date_filter;
    $types .= 's';
}
$stmt = $conn->prepare($sql);
if ($params) {
    $stmt->bind_param($types, ...$params);
}
$stmt->execute();
$result = $stmt->get_result();
$total_timeslots = $result->fetch_assoc()['total'];
$total_pages = ceil($total_timeslots / $limit);

// Close the database connection
$conn->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Manage Timeslots</title>
    <link rel="stylesheet" href="manage_timeslots.css">
</head>
<body>
<?php include 'sidebar.php'; ?>
    <div class="main-content">
    <h1>Manage Timeslots</h1>
        <div class="container">
     
            <a href="admin_approval.php" class="btn-admin_approval">Back to Admin Approval</a>

            <!-- Buttons for choosing form type -->
            <div>
                <button id="auto_btn">Generate Timeslots Automatically</button>
                <button id="manual_btn">Manually Add Timeslots</button>
            </div>

            <!-- Form for generating automatic timeslots -->
            <form id="auto_form" method="POST" style="display: none;">
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
            <form id="manual_form" method="POST" style="display: none;">
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

            <!-- Date Filter Form -->
            <!-- Your date filter form here -->

            <!-- Buttons for filtering by amenities -->
            <h2>Available Timeslots</h2>
            <div>
                <form method="GET" action="">
                    <div>
                        <label for="filter_date">Select Date:</label>
                        <input type="date" id="filter_date" name="date" value="<?php echo htmlspecialchars($date_filter); ?>">
                        <br></br>
                        <button type="submit">Filter</button>
                    </div>
                </form>
                <select onchange="window.location.href=this.value;">
                    <option value="?date=<?php echo urlencode($date_filter); ?>">See All</option>
                    <?php foreach ($amenity_options as $id => $name): ?>
                        <option value="?amenity_id=<?php echo $id; ?>&date=<?php echo urlencode($date_filter); ?>">
                            <?php echo htmlspecialchars($name); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <?php if (!empty($timeslots)): ?>
                <table>
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Date</th>
                            <th>Start Time</th>
                            <th>End Time</th>
                            <th>Amenity</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($timeslots as $timeslot): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($timeslot['id']); ?></td>
                                <td><?php echo htmlspecialchars($timeslot['date']); ?></td>
                                <td><?php echo htmlspecialchars($timeslot['time_start']); ?></td>
                                <td><?php echo htmlspecialchars($timeslot['time_end']); ?></td>
                                <td><?php echo htmlspecialchars($timeslot['amenity_name']); ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <p>No timeslots available.</p>
            <?php endif; ?>

            <!-- Pagination -->
            <div id="pagination">
                <?php
                // Ensure the total number of pages is at least 1
                $total_pages = max($total_pages, 1);
                $input_page = $current_page; // Default to the current page for the input

                // Previous button
                if ($current_page > 1): ?>
                    <form method="GET" action="manage_timeslots.php" style="display: inline;">
                        <input type="hidden" name="page" value="<?= $current_page - 1 ?>">
                        <button type="submit"><</button>
                    </form>
                <?php endif; ?>

                <!-- Page input for user to change the page -->
                <form method="GET" action="manage_timeslots.php" style="display: inline;">
                    <input type="number" name="page" value="<?= $input_page ?>" min="1" max="<?= $total_pages ?>" style="width: 50px;">
                </form>

                <!-- "of" text and last page link -->
                <?php if ($total_pages > 1): ?>
                    <span>of</span>
                    <a href="?page=<?= $total_pages ?>" class="<?= ($current_page == $total_pages) ? 'active' : '' ?>"><?= $total_pages ?></a>
                <?php endif; ?>

                <!-- Next button -->
                <?php if ($current_page < $total_pages): ?>
                    <form method="GET" action="manage_timeslots.php" style="display: inline;">
                        <input type="hidden" name="page" value="<?= $current_page + 1 ?>">
                        <button type="submit">></button>
                    </form>
                <?php endif; ?>
            </div>
        </div>
    </div>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Function to show a specific form
            function showForm(formId) {
                document.getElementById('auto_form').style.display = formId === 'auto' ? 'block' : 'none';
                document.getElementById('manual_form').style.display = formId === 'manual' ? 'block' : 'none';
            }

            // Initialize by showing the first form
            showForm('auto'); // or 'manual' based on your preference

            // Add event listeners to buttons
            document.getElementById('auto_btn').addEventListener('click', function() {
                showForm('auto');
            });

            document.getElementById('manual_btn').addEventListener('click', function() {
                showForm('manual');
            });

            // Display success message if set
            <?php if (isset($_SESSION['success_message'])): ?>
                alert("<?php echo addslashes($_SESSION['success_message']); ?>");
                <?php unset($_SESSION['success_message']); // Clear the message ?>
            <?php endif; ?>
        });
    </script>
</body>
</html>