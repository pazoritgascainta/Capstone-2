<?php
session_name('user_session'); 
session_start();

// Redirect to login if homeowner is not logged in
if (!isset($_SESSION['homeowner_id'])) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Database connection
    $servername = "localhost";
    $username = "root";
    $password = "";
    $dbname = "homeowner";

    $conn = new mysqli($servername, $username, $password, $dbname);
    if ($conn->connect_error) {
        echo json_encode(['success' => false, 'message' => 'Database connection failed']);
        exit();
    }

    // Sanitize and validate input
    $appointment_id = intval($_POST['appointment_id']);
    $homeowner_id = $_SESSION['homeowner_id'];

    // Check if the appointment belongs to the logged-in homeowner
    $sql_check = "SELECT id FROM appointments WHERE id = ? AND homeowner_id = ?";
    $stmt_check = $conn->prepare($sql_check);
    $stmt_check->bind_param("ii", $appointment_id, $homeowner_id);
    $stmt_check->execute();
    $result_check = $stmt_check->get_result();

    if ($result_check->num_rows > 0) {
        // Cancel the appointment by setting its status to 'Cancelled'
        $sql_cancel = "UPDATE appointments SET status = 'Cancelled' WHERE id = ?";
        $stmt_cancel = $conn->prepare($sql_cancel);
        $stmt_cancel->bind_param("i", $appointment_id);
        if ($stmt_cancel->execute()) {
            echo json_encode(['success' => true]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Failed to cancel the appointment']);
        }
    } else {
        echo json_encode(['success' => false, 'message' => 'Appointment not found']);
    }

    $conn->close();
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request']);
}
?>
