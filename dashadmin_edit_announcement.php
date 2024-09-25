<?php
// Database connection
$servername = "localhost";
$username = "root";
$password = "";
$database = "homeowner";

$conn = new mysqli($servername, $username, $password, $database);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$id = $_GET['id'];

// Fetch the announcement details
$query = "SELECT content FROM announcements WHERE id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $id);
$stmt->execute();
$stmt->store_result();
$stmt->bind_result($content);
$stmt->fetch();

// Handle form submission for updating announcement
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['update_announcement'])) {
    $updatedContent = $_POST['content'];
    $updateStmt = $conn->prepare("UPDATE announcements SET content = ? WHERE id = ?");
    $updateStmt->bind_param("si", $updatedContent, $id);
    $updateStmt->execute();
    header("Location: dashadmin.php");
}

$stmt->close();
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="dasheditannouncement.css">

    <title>Edit Announcement</title>

</head>
<body>

<h2>Edit Announcement</h2>

<form method="POST" action="">
    <label for="content">Announcement Content:</label><br>
    <textarea id="content" name="content" rows="4" cols="50" required><?php echo htmlspecialchars($content); ?></textarea><br><br>
    <button type="submit" name="update_announcement">Update Announcement</button>
</form>

</body>
</html>
