<?php
header('Content-Type: application/json');

// Your database connection and query logic here
// Example:
$year = $_GET['year'];
$month = $_GET['month'];
$query = "SELECT date, Amenity_1, Amenity_2, Amenity_3, Amenity_4, Amenity_5, Amenity_6 FROM appointments WHERE YEAR(date) = ? AND MONTH(date) = ?";

$stmt = $pdo->prepare($query);
$stmt->execute([$year, $month]);
$results = $stmt->fetchAll(PDO::FETCH_ASSOC);

echo json_encode($results);
?>
