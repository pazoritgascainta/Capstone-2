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

$sql = "SELECT id, name, email, phone_number, address, created_at, password FROM homeowners WHERE status='active'";
$result = $conn->query($sql);

if (!$result) {
    die("Invalid query: " . $conn->error);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Homeowners</title>
    <link rel="stylesheet" href="dashbcss.css">
    <link rel="stylesheet" href="homeownercss.css">
</head>
<body>
    <?php include 'sidebar.php'; ?>
    <div class="main-content">
        <div class="container">
            <h2>List of Homeowners</h2>
            <a class="btn btn-primary" href="create.php" role="button">New Homeowner</a>
            <a class="btn btn-primary" href="archive.php" role="button">Archived Homeowners</a>
            
            <br>
            <table class="table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Phone</th>
                        <th>Address</th>
                        <th>Password</th>
                        <th>Created At</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    while ($row = $result->fetch_assoc()) {
                        echo "<tr>";
                        echo "<td>{$row['id']}</td>";
                        echo "<td>{$row['name']}</td>";
                        echo "<td>{$row['email']}</td>";
                        echo "<td>{$row['phone_number']}</td>";
                        echo "<td>{$row['address']}</td>";

                        // Display placeholder for password (you might want to adjust this)
                        echo "<td>Password Not Displayed</td>";

                        echo "<td>{$row['created_at']}</td>";
                        echo "<td>";
                        echo "<a class='btn btn-primary btn-sm' href='edit.php?id={$row['id']}'>Edit</a>";
                        echo "<span class='button-margin'></span>"; // Add a span with a class for margin
                        echo "<a class='btn btn-primary btn-sm archive-btn' href='archive.php?id={$row['id']}' onclick='return confirm(\"Are you sure you want to archive this homeowner?\");'>Archive</a>";
                        echo "</td>";
                        echo "</tr>";
                    }
                    ?>
                </tbody>
            </table>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        let btn = document.querySelector('#btn');
        let sidebar = document.querySelector('.sidebar');

        btn.onclick = function () {
            sidebar.classList.toggle('active');
        };
    </script>

</body>
</html>

<?php
$conn->close();
?>
