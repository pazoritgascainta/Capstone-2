<?php
session_start();

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "homeowner";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $billing_id = $_POST['billing_id'];
    $homeowner_id = $_POST['homeowner_id'];
    $new_amount = $_POST['new_amount'];
    $new_type = $_POST['new_type'];

    // Validate input
    if (filter_var($billing_id, FILTER_VALIDATE_INT) && filter_var($new_amount, FILTER_VALIDATE_FLOAT) && !empty($new_type)) {
        // Fetch existing record
        $sql = "SELECT total_amount, type FROM billing WHERE billing_id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $billing_id);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($result->num_rows > 0) {
            $record = $result->fetch_assoc();
            $existing_amount = $record['total_amount'];
            $existing_types = $record['type'];

            // Debugging: Check existing types
            error_log("Existing types: " . $existing_types);

            // Update total amount
            $updated_amount = $existing_amount + $new_amount;
            
            // Process existing types
            $types_array = array_map('trim', explode(',', $existing_types));
            if (!in_array($new_type, $types_array)) {
                $types_array[] = $new_type;
            }
            $updated_types = implode(', ', $types_array);

            // Debugging: Check updated types
            error_log("Updated types: " . $updated_types);

            // Update the billing record
            $sql = "UPDATE billing SET total_amount = ?, type = ? WHERE billing_id = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("dsi", $updated_amount, $updated_types, $billing_id);

            if ($stmt->execute()) {
                $_SESSION['message'] = "Billing record updated successfully";
            } else {
                $_SESSION['message'] = "Error: " . $stmt->error;
            }
        } else {
            $_SESSION['message'] = "No record found for billing ID: " . htmlspecialchars($billing_id);
        }

        $stmt->close();
    } else {
        $_SESSION['message'] = "Invalid input.";
    }

    $conn->close();
    header("Location: view_billing.php?billing_id=" . htmlspecialchars($billing_id));
    exit();
}
?>
