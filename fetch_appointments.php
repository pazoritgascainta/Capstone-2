<?php
session_name('admin_session');
session_start();

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "homeowner";

$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    error_log("Connection failed: " . $conn->connect_error);
    die("Internal server error.");
}

// Function to delete old records from appointment_counts
function deleteOldCounts($conn) {
    $sql_delete = "DELETE FROM appointment_counts WHERE DATE(date) < CURDATE() - INTERVAL 1 DAY";
    if (!$conn->query($sql_delete)) {
        error_log("Delete failed: " . $conn->error);
        die("Internal server error.");
    }
}

// Call the function to delete old appointment counts
deleteOldCounts($conn);

// Validate and sanitize inputs
$date = isset($_GET['date']) ? $_GET['date'] : '';
$page = isset($_GET['page']) ? intval($_GET['page']) : 1;
$limit = isset($_GET['limit']) ? intval($_GET['limit']) : 10;

if (empty($date) || !preg_match('/^\d{4}-\d{2}-\d{2}$/', $date)) {
    error_log("Invalid date format. Expected YYYY-MM-DD.");
    die("Invalid date format. Expected YYYY-MM-DD.");
}

// Calculate offset for pagination
$offset = ($page - 1) * $limit;

// Get appointment counts by amenity
$sql_counts = "
    SELECT
        COALESCE(SUM(CASE WHEN t.amenity_id = 1 THEN 1 ELSE 0 END), 0) AS Amenity_1,
        COALESCE(SUM(CASE WHEN t.amenity_id = 2 THEN 1 ELSE 0 END), 0) AS Amenity_2,
        COALESCE(SUM(CASE WHEN t.amenity_id = 3 THEN 1 ELSE 0 END), 0) AS Amenity_3,
        COALESCE(SUM(CASE WHEN t.amenity_id = 4 THEN 1 ELSE 0 END), 0) AS Amenity_4,
        COALESCE(SUM(CASE WHEN t.amenity_id = 5 THEN 1 ELSE 0 END), 0) AS Amenity_5,
        COALESCE(SUM(CASE WHEN t.amenity_id = 6 THEN 1 ELSE 0 END), 0) AS Amenity_6
    FROM appointments a
    JOIN timeslots t ON a.timeslot_id = t.id
    WHERE a.date = ? AND a.status = 'Pending'
";
$stmt_counts = $conn->prepare($sql_counts);

if ($stmt_counts === false) {
    error_log("Prepare failed: " . $conn->error);
    die("Internal server error.");
}

$stmt_counts->bind_param("s", $date);
$stmt_counts->execute();
$result_counts = $stmt_counts->get_result();
$counts = $result_counts->fetch_assoc();

// Check if all counts are zero
if (array_sum($counts) === 0) {
    header('Content-Type: application/json');
    echo json_encode([
        'appointments' => [],
        'total_count' => 0,
        'counts' => $counts
    ]);

    $stmt_counts->close();
    $conn->close();
    exit();
}

// Get total count for pagination
$sql_count = "SELECT COUNT(*) AS total_count FROM appointments WHERE date = ? AND status = 'Pending'";
$stmt_count = $conn->prepare($sql_count);

if ($stmt_count === false) {
    error_log("Prepare failed: " . $conn->error);
    die("Internal server error.");
}

$stmt_count->bind_param("s", $date);
$stmt_count->execute();
$result_count = $stmt_count->get_result();
$total_count = $result_count->fetch_assoc()['total_count'];

// Fetch appointments data
$sql = "
    SELECT
        a.id,
        a.date,
        am.name AS amenity,
        t.time_start,
        t.time_end,
        a.name,
        a.email,
        a.purpose,
        a.homeowner_id,
        a.status
    FROM appointments a
    JOIN timeslots t ON a.timeslot_id = t.id
    JOIN amenities am ON t.amenity_id = am.id
    WHERE a.date = ? AND a.status = 'Pending'
    GROUP BY a.id, a.date, am.name, t.time_start, t.time_end, a.name, a.email, a.purpose, a.homeowner_id, a.status
    LIMIT ? OFFSET ?
";
$stmt = $conn->prepare($sql);

if ($stmt === false) {
    error_log("Prepare failed: " . $conn->error);
    die("Internal server error.");
}

$stmt->bind_param("sii", $date, $limit, $offset);
$stmt->execute();
$result = $stmt->get_result();

$appointments = [];
while ($row = $result->fetch_assoc()) {
    $appointments[] = $row;
}

// Insert or Update counts in the appointment_counts table
$sql_upsert = "
    INSERT INTO appointment_counts (date, Amenity_1, Amenity_2, Amenity_3, Amenity_4, Amenity_5, Amenity_6)
    VALUES (?, ?, ?, ?, ?, ?, ?)
    ON DUPLICATE KEY UPDATE
        Amenity_1 = VALUES(Amenity_1),
        Amenity_2 = VALUES(Amenity_2),
        Amenity_3 = VALUES(Amenity_3),
        Amenity_4 = VALUES(Amenity_4),
        Amenity_5 = VALUES(Amenity_5),
        Amenity_6 = VALUES(Amenity_6)
";
$stmt_upsert = $conn->prepare($sql_upsert);

if ($stmt_upsert === false) {
    error_log("Prepare failed: " . $conn->error);
    die("Internal server error.");
}

$stmt_upsert->bind_param("siiiiii", $date, $counts['Amenity_1'], $counts['Amenity_2'], $counts['Amenity_3'], $counts['Amenity_4'], $counts['Amenity_5'], $counts['Amenity_6']);
if (!$stmt_upsert->execute()) {
    error_log("Execute failed: " . $stmt_upsert->error);
    die("Internal server error.");
}

header('Content-Type: application/json');
echo json_encode([
    'appointments' => $appointments,
    'total_count' => $total_count,
    'counts' => $counts
]);

// Close all statements and connection
$stmt->close();
$stmt_count->close();
$stmt_counts->close();
$stmt_upsert->close();
$conn->close();
?>
