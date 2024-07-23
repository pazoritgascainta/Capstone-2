<?php
// Database connection
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "homeowner";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Function to insert time slots for a specific date range
function insertTimeSlots($start_date, $end_date) {
    global $conn;

    $current_date = new DateTime($start_date);
    $end_date = new DateTime($end_date);

    // Loop through each date in the range
    while ($current_date <= $end_date) {
        $date = $current_date->format('Y-m-d');
        
        // Generate time slots from 9am to 7pm
        $start_time = new DateTime($date . ' 09:00:00');
        $end_time = new DateTime($date . ' 19:00:00');

        $interval = new DateInterval('PT1H'); // 1 hour interval
        $timeslots = new DatePeriod($start_time, $interval, $end_time);

        // Insert each time slot into the database
        foreach ($timeslots as $slot) {
            $start = $slot->format('H:i:s');
            $end = (clone $slot)->add($interval)->format('H:i:s');

            $sql = "INSERT INTO timeslots (date, start_time, end_time, available) VALUES (?, ?, ?, 1)";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("sss", $date, $start, $end);

            if (!$stmt->execute()) {
                echo "Error inserting timeslot: " . $stmt->error;
            }
        }

        $current_date->modify('+1 day'); // Move to the next day
    }

    return true;
}

// Example usage: Insert time slots for a range of dates
insertTimeSlots('2024-07-20', '2024-08-20');

$conn->close();
?>
