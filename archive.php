<?php
$servername = "localhost";
$username = "root";
$password = "";
$database = "homeowner";

$conn = new mysqli($servername, $username, $password, $database);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if (isset($_GET['id'])) {
    $id = $_GET['id'];
    $sql = "UPDATE homeowners SET status='archived' WHERE id=$id";

    if ($conn->query($sql) === TRUE) {
        // Redirect back to homeowneradmin.php after archiving
        header("Location: homeowneradmin.php");
        exit();
    } else {
        echo "Error archiving record: " . $conn->error;
    }
}

$sql = "SELECT id, name, email, phone_number, address, created_at FROM homeowners WHERE status='archived'";
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
    <title>Archived Homeowners</title>
    <link rel="stylesheet" href="dashbcss.css">
    <link rel="stylesheet" href="homeownercss.css">
</head>
<body>
    <?php include 'sidebar.php'; ?>
    <div class="main-content">
        <div class="Container">
            <div class="container">
                <h2>Archived Homeowners</h2>
                <a class="btn btn-primary" href="homeowneradmin.php" role="button">Back to Homeowners</a>

                <br>
                <table class="table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Phone</th>
                            <th>Address</th>
                            <th>Created At</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($row = $result->fetch_assoc()): ?>
                            <tr>
                                <td><?php echo $row['id']; ?></td>
                                <td><?php echo $row['name']; ?></td>
                                <td><?php echo $row['email']; ?></td>
                                <td><?php echo $row['phone_number']; ?></td>
                                <td><?php echo $row['address']; ?></td>
                                <td><?php echo $row['created_at']; ?></td>
                                <td>
                                    <a class="btn btn-success" href="restore.php?id=<?php echo $row['id']; ?>" role="button">Restore</a>
                                    <a class="btn btn-danger" href="delete.php?id=<?php echo $row['id']; ?>" role="button">Delete</a>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
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
