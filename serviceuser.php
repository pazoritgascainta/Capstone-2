<?php
session_name('user_session'); 
session_start();

if (!isset($_SESSION['homeowner_id'])) {
    header("Location: login.php");
    exit;
}

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

// Initialize a variable for the success message
$success_message = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $homeowner_id = $_SESSION['homeowner_id'];
    $details = $conn->real_escape_string($_POST['message']);
    $urgency = $conn->real_escape_string($_POST['urgency']);
    $type = $conn->real_escape_string($_POST['type']); // Get service type from hidden input

    // Check if service_type is set
    if (!isset($_POST['type']) || empty($_POST['type'])) {
        echo "Service type is not set.";
    } else {
        $sql = "INSERT INTO serreq (homeowner_id, details, urgency, type) VALUES ('$homeowner_id', '$details', '$urgency', '$type')";

        if ($conn->query($sql) === TRUE) {
            $success_message = "Service request submitted successfully!"; // Set success message
        } else {
            echo "Error: " . $sql . "<br>" . $conn->error;
        }
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>Dashboard</title>
    <link rel="stylesheet" href="usersidebar.css">
    <link rel="stylesheet" href="userService.css">
    <script>
        // Function to show the alert if the PHP variable is set
        function showAlert(message) {
            if (message) {
                alert(message);
            }
        }
    </script>
</head>
<body>
    <?php include 'usersidebar.php'; ?>

    <div class="main-content">
        <div class="Container">
            <h1>St. Monique User Service Requests</h1>

            <script>
                // Call the alert function with the success message
                showAlert(<?php echo json_encode($success_message); ?>);
            </script>
        </div>
        <div class="service-request-form">
            <div class="form-container">
                <div class="button-group">
                    <button class="btn active" id="maintenance" value="Maintenance">Maintenance</button>
                    <button class="btn" id="installation" value="Installation">Installation</button>
                    <button class="btn" id="road-repair" value="Road Repair">Road Repair</button>
                    <button class="btn" id="lawn-cleanup" value="Lawn Cleanup">Lawn Cleanup</button>
                    <button class="btn" id="general-cleaning" value="General Cleaning">General Cleaning</button>
                    <button class="btn" id="other" value="Other">Other</button>
                </div>
    
                <form id="serviceForm" method="POST" action="">
                    <input type="hidden" id="type" name="type" value="Maintenance"> <!-- Hidden input for service type -->
                    <label for="message">Service Request Details:</label>
                    <textarea id="message" name="message" rows="4" required></textarea>

                    <label for="urgency">Level of Urgency:</label>
                    <fieldset>
                   <div class="radio-group">
        <label class="radio-label">
            <input type="radio" name="urgency" value="High" required> 
            <span>High</span>
        </label>
        <label class="radio-label">
            <input type="radio" name="urgency" value="Medium"> 
            <span>Medium</span>
        </label>
        <label class="radio-label">
            <input type="radio" name="urgency" value="Low"> 
            <span>Low</span>
        </label>
    </div>
                   </fieldset>

                    <button type="submit">Submit Form</button>
                    <a href="view_service_requests.php" class="service-link">View Your Service Requests</a>

                </form>

            </div>
        </div>
    </div>

    <!-- Include the JavaScript file at the end of the body -->
    <script src="userservice.js"></script>
</body>
</html>
