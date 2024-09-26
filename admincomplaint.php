<?php
session_name('admin_session'); // Set a unique session name for admins
session_start();

$servername = "localhost";
$username = "root";
$password = "";
$database = "homeowner";

$conn = new mysqli($servername, $username, $password, $database);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Handle search input for homeowner_id
$search_query = "";
if (isset($_GET['search'])) {
    $search_query = trim($_GET['search']);
}

// Handle delete request
if (isset($_POST['delete_id'])) {
    $delete_id = $_POST['delete_id'];

    // Prepare and execute the delete query
    $delete_query = "DELETE FROM complaints WHERE complaint_id = ?";
    $stmt = $conn->prepare($delete_query);
    $stmt->bind_param("i", $delete_id);

    if ($stmt->execute()) {
        echo "<script>alert('Complaint deleted successfully.'); window.location.href='admincomplaint.php';</script>";
    } else {
        echo "<script>alert('Failed to delete the complaint.');</script>";
    }

    $stmt->close();
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
    <h1>Admin Complaints</h1>
    <div class="container">

        <!-- Search Form -->
        <form method="GET" action="admincomplaint.php" class="search-form">
            <input type="number" name="search" value="<?= htmlspecialchars($search_query) ?>" placeholder="Search by Homeowner ID...">
            <button type="submit">Search</button>
        </form>

        <table class="table">
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
                // Pagination settings
                $results_per_page = 10; // Number of results per page

                // Adjust total count query to handle search
                $query_total = "SELECT COUNT(*) AS total FROM complaints WHERE homeowner_id LIKE '%$search_query%'";
                $result_total = mysqli_query($conn, $query_total);
                $row_total = mysqli_fetch_assoc($result_total);
                $total_results = $row_total['total'];
                $total_pages = ceil($total_results / $results_per_page);

                // Get current page from URL or default to 1
                $current_page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
                $current_page = max($current_page, 1); // Ensure current page is at least 1
                $current_page = min($current_page, $total_pages); // Ensure current page does not exceed total pages

                // Calculate the offset for the query
                $offset = ($current_page - 1) * $results_per_page;

                // Query to fetch complaints filtered by homeowner_id with pagination and sorting by status
                $query = "
                SELECT * 
                FROM complaints 
                WHERE homeowner_id LIKE '%$search_query%' 
                ORDER BY 
                    CASE 
                        WHEN status = 'Pending' THEN 1
                        WHEN status = 'In Progress' THEN 2
                        WHEN status = 'Resolved' THEN 3
                        ELSE 4
                    END,
                    updated_at DESC,
                    created_at DESC
                LIMIT ?, ?
                ";

                $stmt = $conn->prepare($query);
                $stmt->bind_param("ii", $offset, $results_per_page);
                $stmt->execute();
                $result = $stmt->get_result();
                
                if ($result && mysqli_num_rows($result) > 0) {
                    while ($row = mysqli_fetch_assoc($result)) {
                        echo "<tr>";
                        echo "<td>" . htmlspecialchars($row['complaint_id']) . "</td>";
                        echo "<td>" . htmlspecialchars($row['homeowner_id']) . "</td>";
                        echo "<td>" . htmlspecialchars($row['subject']) . "</td>";
                        echo "<td>" . htmlspecialchars($row['description']) . "</td>";

                        // Set status color
                        $status_color = "";
                        if ($row['status'] === 'In Progress') {
                            $status_color = "style='color: red;'";
                        } elseif ($row['status'] === 'Resolved') {
                            $status_color = "style='color: green;'";
                        } elseif ($row['status'] === 'Pending') {
                            $status_color = "style='color: orange;'";
                        }

                        echo "<td $status_color>" . htmlspecialchars($row['status']) . "</td>";
                        echo "<td>" . htmlspecialchars($row['created_at']) . "</td>";
                        echo "<td>" . htmlspecialchars($row['updated_at']) . "</td>";
                        echo "<td>";
                        echo "<a class='btn btn-edit' href='admin_view_complaints.php?id=" . htmlspecialchars($row['complaint_id']) . "'>View</a>";
                        echo "<form method='POST' action='admincomplaint.php' class='delete-form' style='display:inline; margin-left:10px;'>";
                        echo "<input type='hidden' name='delete_id' value='" . htmlspecialchars($row['complaint_id']) . "'>";
                        echo "<a href='#' onclick='confirmDelete(event, this)' class='btn'>Delete</a>";
                        echo "</form>";
                        echo "</td>";
                        echo "</tr>";
                    }
                } else {
                    echo "<tr><td colspan='8'>No complaints found for this Homeowner ID.</td></tr>";
                }
                ?>
            </tbody>
        </table>

        <!-- Pagination controls -->
        <div id="pagination">
            <?php if ($total_pages > 1): ?>
                <!-- Previous button -->
                <?php if ($current_page > 1): ?>
                    <form method="GET" action="admincomplaint.php" style="display: inline;">
                        <input type="hidden" name="page" value="<?= $current_page - 1 ?>">
                        <input type="hidden" name="search" value="<?= htmlspecialchars($search_query) ?>">
                        <button type="submit" class="btn">&lt;</button>
                    </form>
                <?php endif; ?>

                <form method="GET" action="admincomplaint.php" style="display: inline;">
                    <input type="number" name="page" value="<?= $current_page ?>" min="1" max="<?= $total_pages ?>" class="pagination-input">
                    <input type="hidden" name="search" value="<?= htmlspecialchars($search_query) ?>">
                </form>

                <span>of</span>
                <a href="admincomplaint.php?page=<?= $total_pages ?>&search=<?= htmlspecialchars($search_query) ?>" class="page-link <?= ($current_page == $total_pages) ? 'active' : '' ?>"><?= $total_pages ?></a>

                <?php if ($current_page < $total_pages): ?>
                    <form method="GET" action="admincomplaint.php" style="display: inline;">
                        <input type="hidden" name="page" value="<?= $current_page + 1 ?>">
                        <input type="hidden" name="search" value="<?= htmlspecialchars($search_query) ?>">
                        <button type="submit" class="btn">&gt;</button>
                    </form>
                <?php endif; ?>
            <?php endif; ?>
        </div>

    </div>
</div>

</body>
<script>
function confirmDelete(event, link) {
    event.preventDefault(); // Prevent the default link behavior

    var confirmation = confirm('Are you sure you want to delete this complaint?');
    if (confirmation) {
        var form = link.closest('form');
        form.submit(); // Submit the form if confirmed
    }
}
</script>

</html>
