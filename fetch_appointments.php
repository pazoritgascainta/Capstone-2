<?php
header('Content-Type: application/json');

// Database connection
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "homeowner";

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Get the date, page, and limit from query parameters
$date = $_GET['date'] ?? '';
$page = isset($_GET['page']) ? intval($_GET['page']) : 1;
$limit = isset($_GET['limit']) ? intval($_GET['limit']) : 10;

// Validate the date format (optional, but recommended)
if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $date)) {
    echo json_encode(['error' => 'Invalid date format']);
    exit;
}

$offset = ($page - 1) * $limit;

// Fetch total count of appointments for the selected date
$sql_count = "
    SELECT COUNT(*) as total_count
    FROM appointments a
    JOIN timeslots t ON a.timeslot_id = t.id
    JOIN amenities am ON t.amenity_id = am.id
    WHERE a.date = ?
";
$stmt_count = $conn->prepare($sql_count);
$stmt_count->bind_param('s', $date);
$stmt_count->execute();
$result_count = $stmt_count->get_result();
$total_count = $result_count->fetch_assoc()['total_count'];
$stmt_count->close();

// Fetch appointments for the selected date with pagination
$sql = "
    SELECT a.id, am.name AS amenity, a.date, t.time_start, t.time_end, a.name, a.email, a.purpose, a.homeowner_id, a.status
    FROM appointments a
    JOIN timeslots t ON a.timeslot_id = t.id
    JOIN amenities am ON t.amenity_id = am.id
    WHERE a.date = ?
    LIMIT ? OFFSET ?
";
$stmt = $conn->prepare($sql);
$stmt->bind_param('sii', $date, $limit, $offset);
$stmt->execute();
$result = $stmt->get_result();

$appointments = [];
while ($row = $result->fetch_assoc()) {
    $appointments[] = $row;
}

$response = [
    'appointments' => $appointments,
    'total_count' => $total_count
];

echo json_encode($response);

$stmt->close();
$conn->close();
?>
