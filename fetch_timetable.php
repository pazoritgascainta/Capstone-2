<?php
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

// Function to sanitize input data
function sanitize_input($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $data;
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['date'])) {
    $date = sanitize_input($_POST['date']);
    $homeowner_id = isset($_SESSION['homeowner_id']) ? (int)$_SESSION['homeowner_id'] : 0;

    // Check if the homeowner ID is valid
    if ($homeowner_id > 0) {
        $sql_appointments = "SELECT * FROM appointments WHERE homeowner_id = ? AND date = ?";
        $stmt = $conn->prepare($sql_appointments);
        if ($stmt) {
            $stmt->bind_param("is", $homeowner_id, $date);
            $stmt->execute();
            $result_appointments = $stmt->get_result();

            if ($result_appointments->num_rows > 0) {
                echo "<h2>Appointments for $date:</h2>";
                echo "<table>";
                echo "<tr><th>Time</th><th>Purpose</th><th>Status</th></tr>";
                while ($row = $result_appointments->fetch_assoc()) {
                    echo "<tr>";
                    echo "<td>" . htmlspecialchars($row["time"]) . "</td>";
                    echo "<td>" . htmlspecialchars($row["purpose"]) . "</td>";
                    echo "<td>" . htmlspecialchars($row["status"]) . "</td>";
                    echo "</tr>";
                }
                echo "</table>";
            } else {
                echo "<p>No appointments booked for $date.</p>";
            }

            $stmt->close();
        } else {
            echo "<p>Error preparing the SQL statement.</p>";
        }
    } else {
        echo "<p>Invalid homeowner ID.</p>";
    }
}

$conn->close();
?>
