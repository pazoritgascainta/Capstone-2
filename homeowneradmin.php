<?php
session_start();

// Database connection
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "homeowner";

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Initialize status message
$status_message = "";

// Handle homeowner activation or deactivation
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['homeowner_id']) && isset($_POST['new_status'])) {
    $homeowner_id = intval($_POST['homeowner_id']);
    $new_status = $_POST['new_status'];

    // Update the homeowner status
    if (in_array($new_status, ['active', 'inactive'])) {
        $sql_update_status = "UPDATE homeowners SET status = ? WHERE id = ?";
        $stmt_update = $conn->prepare($sql_update_status);
        if ($stmt_update) {
            $stmt_update->bind_param("si", $new_status, $homeowner_id);
            if ($stmt_update->execute()) {
                $status_message = "Homeowner status updated successfully!";
            } else {
                $status_message = "Failed to update homeowner status: " . $stmt_update->error;
            }
            $stmt_update->close();
        } else {
            $status_message = "Prepare statement failed: " . $conn->error;
        }
    } else {
        $status_message = "Invalid status value.";
    }

    $_SESSION['message'] = ['status' => 'success', 'message' => $status_message];
    header('Location: homeowneradmin.php');
    exit();
}

// Pagination settings
$records_per_page = 10;
$current_page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($current_page - 1) * $records_per_page;

// Fetch active homeowners with pagination
$sql_homeowners = "
    SELECT id, name, email, phone_number, address, created_at, status 
    FROM homeowners 
    LIMIT $records_per_page OFFSET $offset
";
$result_homeowners = $conn->query($sql_homeowners);

// Get total homeowners count
$sql_total_homeowners = "SELECT COUNT(*) AS total FROM homeowners";
$result_total_homeowners = $conn->query($sql_total_homeowners);
$total_homeowners = $result_total_homeowners->fetch_assoc()['total'];

// Calculate total pages
$total_pages = ceil($total_homeowners / $records_per_page);
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

            <!-- Action Buttons -->
            <a class="btn btn-primary" href="create.php" role="button">New Homeowner</a>
            <a class="btn btn-primary" href="archive.php" role="button">Archived Homeowners</a>

            <br><br>

            <!-- Display message if no homeowners are found -->
            <?php if (!empty($_SESSION['message'])): ?>
                <p class="<?= $_SESSION['message']['status'] ?>">
                    <?= htmlspecialchars($_SESSION['message']['message']) ?>
                </p>
                <?php unset($_SESSION['message']); ?>
            <?php endif; ?>

            <?php if ($result_homeowners->num_rows > 0): ?>
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
                        <!-- Loop through homeowners and display their info -->
                        <?php while ($row = $result_homeowners->fetch_assoc()): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($row['id']); ?></td>
                                <td><?php echo htmlspecialchars($row['name']); ?></td>
                                <td><?php echo htmlspecialchars($row['email']); ?></td>
                                <td><?php echo htmlspecialchars($row['phone_number']); ?></td>
                                <td><?php echo htmlspecialchars($row['address']); ?></td>
                                <td><?php echo htmlspecialchars($row['created_at']); ?></td>
                                <td>
                                    <!-- Edit and Archive buttons -->
                                    <a class="btn btn-primary btn-sm" href="edit.php?id=<?php echo $row['id']; ?>">Edit</a>
                                    <span class="button-margin"></span>
                                    <a class="btn btn-primary btn-sm archive-btn" href="archive.php?id=<?php echo $row['id']; ?>" onclick="return confirm('Are you sure you want to archive this homeowner?');">Archive</a>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>

                <!-- Pagination controls -->
                <div id="pagination">
                    <?php
                    $total_pages = max($total_pages, 1); // Ensure there's at least 1 page
                    $input_page = $current_page; // Default to the current page for the input

                    // Previous button
                    if ($current_page > 1): ?>
                        <form method="GET" action="homeowneradmin.php" style="display: inline;">
                            <input type="hidden" name="page" value="<?= $current_page - 1 ?>">
                            <button type="submit">&lt;</button>
                        </form>
                    <?php endif; ?>

                    <!-- Page input for user to change the page -->
                    <form method="GET" action="homeowneradmin.php" style="display: inline;">
                        <input type="number" name="page" value="<?= $input_page ?>" min="1" max="<?= $total_pages ?>" style="width: 50px;">
                    </form>

                    <!-- "of" text and last page link -->
                    <?php if ($total_pages > 1): ?>
                        <span>of</span>
                        <a href="?page=<?= $total_pages ?>" class="<?= ($current_page == $total_pages) ? 'active' : '' ?>"><?= $total_pages ?></a>
                    <?php endif; ?>

                    <!-- Next button -->
                    <?php if ($current_page < $total_pages): ?>
                        <form method="GET" action="homeowneradmin.php" style="display: inline;">
                            <input type="hidden" name="page" value="<?= $current_page + 1 ?>">
                            <button type="submit"></button>
                        </form>
                    <?php endif; ?>
                </div>
            <?php else: ?>
                <p>No homeowners found.</p>
            <?php endif; ?>
        </div>
    </div>

</body>
</html>

<?php $conn->close(); ?>
