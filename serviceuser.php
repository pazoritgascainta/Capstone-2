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

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $homeowner_id = $_SESSION['homeowner_id'];
    $details = $conn->real_escape_string($_POST['message']);
    $urgency = $conn->real_escape_string($_POST['urgency']);
    $type = $conn->real_escape_string($_POST['type']); // Get service type from hidden input

    // Check if service_type is set
    if (!isset($_POST['type'])) {
        echo "Service type is not set.";
    } else {
        $sql = "INSERT INTO serreq (homeowner_id, details, urgency, type) VALUES ('$homeowner_id', '$details', '$urgency', '$type')";

        if ($conn->query($sql) === TRUE) {
            echo "Service request submitted successfully!";
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
</head>
<body>
    <?php include 'usersidebar.php'; ?>

    <div class="main-content">
        <div class="Container">
            <h1>St. Monique User Service Requests</h1>
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
                    <label for="message">Service Request Details:</label>
                    <textarea id="message" name="message" rows="4" required></textarea>

                    <label for="urgency">Level of Urgency:</label>
                    <fieldset>
                        <div class="radio-group">
                            <label>
                                <input type="radio" name="urgency" value="High" required> High
                            </label>
                            <label>
                                <input type="radio" name="urgency" value="Medium"> Medium
                            </label>
                            <label>
                                <input type="radio" name="urgency" value="Low"> Low
                            </label>
                        </div>
                    </fieldset>

                    <button type="submit">Submit Form</button>
                </form>
            </div>
        </div>
    </div>

    <script >
        document.addEventListener("DOMContentLoaded", function() {
    const buttons = document.querySelectorAll(".button-group .btn");
    const serviceTypeInput = document.getElementById('type');

    buttons.forEach(button => {
        button.addEventListener("click", function() {
            // Set the hidden input value to the clicked button's value
            serviceTypeInput.value = this.value;
            // Remove active class from other buttons
            buttons.forEach(btn => btn.classList.remove("active"));
            // Add active class to the clicked button
            this.classList.add("active");
        });
    });

    // Optionally set the default value for service type on page load
    serviceTypeInput.value = buttons[0].value; // Set to the first button's value (Maintenance)
});

    </script>
</body>
</html>
