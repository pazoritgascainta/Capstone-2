<?php
session_name('admin_session');
session_start();

if (!isset($_SESSION['admin_id'])) {
    header("Location: admin_login.php");
    exit;
}

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "homeowner";

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id'])) {
    $paymentId = intval($_POST['id']);
    // Update the payment status in the database
    $stmt = $conn->prepare("UPDATE payments SET viewed = 1 WHERE id = ?");
    $stmt->bind_param("i", $paymentId);
    $stmt->execute();
    $stmt->close();
    
    echo json_encode(['success' => true]); // Return a success message
}

$conn->close();
?>
