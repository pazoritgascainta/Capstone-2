<?php
// Example data retrieval logic
$booked_dates = array('2024-08-01', '2024-08-05', '2024-08-10'); // Replace with your actual data retrieval logic

// Output JSON
header('Content-Type: application/json');
echo json_encode($booked_dates);
?>
