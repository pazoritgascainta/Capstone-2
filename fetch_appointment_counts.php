<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
header('Content-Type: application/json');

session_name('admin_session');
session_start();

// Start timing
$startTime = microtime(true);

// Database connection
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "homeowner";

$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    echo json_encode(['error' => 'Database connection failed: ' . $conn->connect_error]);
    exit();
}

// Get year and month from query parameters, default to current year and month
$year = isset($_GET['year']) ? intval($_GET['year']) : date('Y');
$month = isset($_GET['month']) ? intval($_GET['month']) : date('m');

// Validate year and month values
if ($year < 1900 || $year > 2100 || $month < 1 || $month > 12) {
    echo json_encode(['error' => 'Invalid year or month']);
    exit();
}

// Prepare SQL query to get appointment counts for the specified year and month
$sql = "
    SELECT date, Amenity_1, Amenity_2, Amenity_3, Amenity_4, Amenity_5, Amenity_6
    FROM appointment_counts
    WHERE YEAR(date) = ? AND MONTH(date) = ?
";
$stmt = $conn->prepare($sql);

// Check if prepare was successful
if (!$stmt) {
    echo json_encode(['error' => 'SQL prepare failed: ' . $conn->error]);
    exit();
}

// Bind parameters
$stmt->bind_param('ii', $year, $month);

// Execute statement
if (!$stmt->execute()) {
    echo json_encode(['error' => 'SQL execute failed: ' . $stmt->error]);
    exit();
}

$result = $stmt->get_result();

// Check if query execution was successful
if (!$result) {
    echo json_encode(['error' => 'SQL query failed: ' . $conn->error]);
    exit();
}

// Fetch and structure results by date
$counts = [];
while ($row = $result->fetch_assoc()) {
    $date = $row['date'];
    $counts[$date] = [
        'Amenity_1' => intval($row['Amenity_1']),
        'Amenity_2' => intval($row['Amenity_2']),
        'Amenity_3' => intval($row['Amenity_3']),
        'Amenity_4' => intval($row['Amenity_4']),
        'Amenity_5' => intval($row['Amenity_5']),
        'Amenity_6' => intval($row['Amenity_6'])
    ];
}

// If no data found, return an empty object
if (empty($counts)) {
    echo json_encode([]);
    exit();
}

echo json_encode($counts);

// End timing and log execution time
$endTime = microtime(true);
$executionTime = $endTime - $startTime;
error_log('Script execution time: ' . $executionTime . ' seconds');

// Close statement and connection
$stmt->close();
$conn->close();
?>
