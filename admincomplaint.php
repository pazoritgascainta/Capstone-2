<?php
session_start();

$servername = "localhost";
$username = "root";
$password = "";
$database = "homeowner";

$conn = new mysqli($servername, $username, $password, $database);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin - Complaints</title>
    <link rel="stylesheet" href="dashbcss.css">
    <link rel="stylesheet" href="admincomplaint.css">
</head>
<body>

<?php include 'sidebar.php'; ?>
<div class="main-content">
    <div class="container">
        
<div>
<h1>Admin Complaints</h1>
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Homeowner ID</th>
                    <th>Subject</th>
                    <th>Description</th>
                    <th>Status</th>
                    <th>Created At</th>
                    <th>Updated At</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php
                // Include database connection
                include 'config.php';

                // Query to fetch complaints
                $query = "SELECT * FROM complaints";
                $result = mysqli_query($conn, $query);

                if ($result && mysqli_num_rows($result) > 0) {
                    while ($row = mysqli_fetch_assoc($result)) {
                        echo "<tr>";
                        echo "<td>" . $row['complaint_id'] . "</td>";
                        echo "<td>" . $row['homeowner_id'] . "</td>";
                        echo "<td>" . $row['subject'] . "</td>";
                        echo "<td>" . $row['description'] . "</td>";
                        echo "<td>" . $row['status'] . "</td>";
                        echo "<td>" . $row['created_at'] . "</td>";
                        echo "<td>" . $row['updated_at'] . "</td>";
                        echo "<td>";
                        echo "<a href='admin_view_complaints.php?id=" . $row['complaint_id'] . "'>View</a>";
                        echo "</td>";
                        echo "</tr>";
                        echo "<tr><td colspan='8'><hr></td></tr>"; // Adds a horizontal rule after each row
                    }
                } else {
                    echo "<tr><td colspan='8'>No complaints found.</td></tr>";
                }

                // Close database connection
                mysqli_close($conn);
                ?>
            </tbody>
        </table>
    </div>
            </div></div>
        
</body>
</html>
