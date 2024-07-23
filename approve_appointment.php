<?php
// Establish database connection
$conn = new mysqli('localhost', 'root', '', 'homeowner');

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Check if ID parameter is present
if (isset($_POST['id'])) {
    $id = $_POST['id'];

    try {
        // Prepare and bind SQL statement
        $stmt = $conn->prepare("UPDATE appointments SET status = 'Approved' WHERE id = ?");
        $stmt->bind_param("i", $id);

        // Execute the query
        $stmt->execute();

        // Check if rows were affected
        if ($stmt->affected_rows > 0) {
            // Redirect back to appointments page or handle success message
            header("Location: appointmentadmin.php");
            exit;
        } else {
            // Handle case where no rows were updated
            echo "Failed to update appointment status.";
        }

        // Close statement
        $stmt->close();
    } catch (mysqli_sql_exception $e) {
        // Handle SQL error
        echo "Error: " . $e->getMessage();
    }
}

// Close database connection
$conn->close();
?>
