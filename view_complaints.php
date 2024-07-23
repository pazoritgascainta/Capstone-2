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

$homeowner_id = $_SESSION['homeowner_id'];
$sql = "SELECT * FROM complaints WHERE homeowner_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $homeowner_id);
$stmt->execute();
$result = $stmt->get_result();

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>View Complaints</title>
    <link rel="stylesheet" href="usersidebar.css">
    <link rel="stylesheet" href="view_complaints.css">
   
</head>
<body>
<?php include 'usersidebar.php'; ?>
<div class="main-content">
<div>

            <div class="container">
    <h2>View Your Complaints</h2>
    <?php if ($result->num_rows > 0) : ?>
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Subject</th>
                    <th>Description</th>
                    <th>Status</th>
                    <th>Created At</th>
                    <th>Updated At</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = $result->fetch_assoc()) : ?>
                    <tr>
                        <td><?php echo $row['complaint_id']; ?></td>
                        <td><?php echo $row['subject']; ?></td>
                        <td><?php echo $row['description']; ?></td>
                        <td><?php echo $row['status']; ?></td>
                        <td><?php echo $row['created_at']; ?></td>
                        <td><?php echo $row['updated_at']; ?></td>
                        <td>
                            <a href="edit_complaint.php?id=<?php echo $row['complaint_id']; ?>" class="action-buttons">Edit</a>
                            <a href="cancel_complaint.php?id=<?php echo $row['complaint_id']; ?>" class="action-buttons">Cancel</a>
                        </td>
                    </tr>
                    <tr>
                        <td colspan="7"><hr></td> <!-- HR after each complaint -->
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    <?php else : ?>
        <p>No complaints found.</p>
    <?php endif; ?>

    <a href="usercomplaint.php">Submit a Complaint</a>
    </div> 
</div> 
</div>
</body>
</html>

<?php
// Close statement and connection
$stmt->close();
$conn->close();
?>
