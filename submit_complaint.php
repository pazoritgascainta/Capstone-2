<?php
session_name('user_session');
session_start();

if (!isset($_SESSION['homeowner_id'])) {
    header("Location: usercomplaint.php");
    exit;
}

$homeowner_id = $_SESSION['homeowner_id'];
$user_name = $_SESSION['homeowner_name']; // Get homeowner's name from session

$subject = $_POST['subject']; 
$description = $_POST['description'];

$servername = "localhost";
$username = "root";
$password = "";
$database = "homeowner";

$conn = new mysqli($servername, $username, $password, $database);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Insert complaint into database
$sql = "INSERT INTO complaints (homeowner_id, subject, description, status, created_at) VALUES (?, ?, ?, 'Pending', NOW())";
$stmt = $conn->prepare($sql);
$stmt->bind_param("iss", $homeowner_id, $subject, $description);

if ($stmt->execute()) {
    // Notify admin about the new complaint with the homeowner's name
    $admins_result = $conn->query("SELECT id FROM admin");
    while ($admin_row = $admins_result->fetch_assoc()) {
        $admin_id = $admin_row['id'];
        $conn->query("INSERT INTO admin_inbox (admin_id, message, date) VALUES ('$admin_id', 'New complaint submitted by $user_name: $subject', NOW())");
    }

    header("Location: view_complaints.php");
    exit;
} else {
    echo "Error: " . $stmt->error;
}

$stmt->close();
$conn->close();

?>
