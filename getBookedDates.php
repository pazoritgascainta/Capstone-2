<?php
// Check if the date parameter is set
if (isset($_GET['date'])) {
    // Validate and sanitize the date input (optional)
    $date = htmlspecialchars($_GET['date']); // Sanitize if needed
    // Example: Validate date format (YYYY-MM-DD)
    if (preg_match("/^\d{4}-\d{2}-\d{2}$/", $date)) {
        // Database connection parameters
        $servername = "localhost";
        $username = "root";
        $password = "";
        $database = "homeowner";

        // Create connection
        $conn = new mysqli($servername, $username, $password, $database);

        // Check connection
        if ($conn->connect_error) {
            http_response_code(500); // Internal Server Error
            echo json_encode(['error' => 'Database connection failed: ' . $conn->connect_error]);
            exit;
        }

        try {
            // Prepare SQL query to fetch appointments for the given date
            $stmt = $conn->prepare("SELECT * FROM appointments WHERE DATE(appointment_date) = ?");
            $stmt->bind_param('s', $date);
            $stmt->execute();
            $result = $stmt->get_result();

            // Fetch all appointments as associative array
            $appointments = [];
            while ($row = $result->fetch_assoc()) {
                $appointments[] = $row;
            }

            // Close statement and connection
            $stmt->close();
            $conn->close();

            // Return JSON response
            header('Content-Type: application/json');
            echo json_encode(['appointments' => $appointments]);

        } catch (Exception $e) {
            // Handle database query errors
            http_response_code(500); // Internal Server Error
            echo json_encode(['error' => 'Database error: ' . $e->getMessage()]);
        }
    } else {
        // Invalid date format
        http_response_code(400); // Bad Request
        echo json_encode(['error' => 'Invalid date format. Please use YYYY-MM-DD.']);
    }
} else {
    // Date parameter not provided
    http_response_code(400); // Bad Request
    echo json_encode(['error' => 'Date parameter is missing.']);
}
?>
