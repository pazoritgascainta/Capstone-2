<?php
session_name('admin_session'); // Set a unique session name for admins
session_start();

// Check if admin is logged in
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

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['add_announcement'])) {
    $content = $conn->real_escape_string($_POST['content']);
    $admin_id = $_SESSION['admin_id']; // Assuming you have stored admin_id in session

    // Fetch admin username
    $admin_name_result = $conn->query("SELECT username FROM admin WHERE id = '$admin_id'");
    $admin_name_row = $admin_name_result->fetch_assoc();
    $admin_name = $admin_name_row['username'];

    $sql = "INSERT INTO announcements (content, admin_name, date) VALUES ('$content', '$admin_name', NOW())";

    if ($conn->query($sql) === TRUE) {
        // Insert into homeowner inbox
        $announcement_id = $conn->insert_id;
        $homeowners_result = $conn->query("SELECT id FROM homeowners");

        while ($homeowner_row = $homeowners_result->fetch_assoc()) {
            $homeowner_id = $homeowner_row['id'];
            $conn->query("INSERT INTO inbox (homeowner_id, message, date) VALUES ('$homeowner_id', 'New announcement added by $admin_name: $content', NOW())");
        }

        header("Location: dashadmin.php");
    } else {
        $error = "Error: " . $sql . "<br>" . $conn->error;
    }
}

$conn->close();
?>
