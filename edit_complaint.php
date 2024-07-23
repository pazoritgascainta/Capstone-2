<?php
session_start();

if (!isset($_SESSION['homeowner_id'])) {
    header("Location: usercomplaint.php");
    exit;
}


$servername = "localhost";
$username = "root";
$password = "";
$database = "homeowner";

$conn = new mysqli($servername, $username, $password, $database);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if (!isset($_GET['id'])) {
    header("Location: view_complaints.php");
    exit;
}

$complaint_id = $_GET['id'];


$sql = "SELECT * FROM complaints WHERE complaint_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $complaint_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows == 1) {
    $complaint = $result->fetch_assoc();
} else {

    header("Location: view_complaints.php");
    exit;
}


if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $subject = $_POST['subject'];
    $description = $_POST['description'];

    $update_sql = "UPDATE complaints SET subject = ?, description = ? WHERE complaint_id = ?";
    $update_stmt = $conn->prepare($update_sql);
    $update_stmt->bind_param("ssi", $subject, $description, $complaint_id);

    if ($update_stmt->execute()) {
        header("Location: view_complaints.php");
        exit;
    } else {
        echo "Error updating complaint: " . $update_stmt->error;
    }
}

$stmt->close();
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Edit Complaint</title>
    <link rel="stylesheet" href="usersidebar.css">
    <link rel="stylesheet" href="usercomplaint.css">
</head>
<body>
<?php include 'usersidebar.php'; ?>
<div class="main-content">
<div>

            <div class="container">
    <h2>Edit Complaint</h2>
    <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]) . '?id=' . $complaint_id; ?>" method="post">
        <label for="subject">Subject:</label><br>
        <input type="text" id="subject" name="subject" value="<?php echo htmlspecialchars($complaint['subject']); ?>" required><br><br>
        
        <label for="description">Description:</label><br>
        <textarea id="description" name="description" required><?php echo htmlspecialchars($complaint['description']); ?></textarea><br><br>
        
        <button type="submit">Update</button>
    </form>


    <a href="view_complaints.php">Back to Complaints</a>
</div></div></div>
</body>
</html>
