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

// Query to get the total number of homeowners
$sql = "SELECT COUNT(id) AS total_homeowners FROM homeowners";
$result = $conn->query($sql);
$total_homeowners = 0;

if ($result->num_rows > 0) {
    // Fetch the result
    $row = $result->fetch_assoc();
    $total_homeowners = $row['total_homeowners'];
}
$sql = "SELECT COUNT(*) AS total_complaints FROM complaints";
$result = $conn->query($sql);

$total_complaints = 0;
if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    $total_complaints = $row['total_complaints'];
}

$sql = "SELECT COUNT(*) AS total_billing FROM billing";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    // Fetch the row
    $row = $result->fetch_assoc();
    $total_billing = $row['total_billing'];
} else {
    $total_billing = 0;
}

$sql = "SELECT COUNT(*) AS total FROM accepted_appointments";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    $totalAppointments = $row['total'];
} else {
    $totalAppointments = 0;
}

$sql = "SELECT COUNT(*) AS total_servicereq FROM serreq";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    $total_service_requests = $row['total_servicereq'];
} else {
    $total_service_requests = 0;
}
// Handle form submission for adding new announcements
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['add_announcement'])) {
    $content = $_POST['content'];
    // Limit to max 5 announcements
    $countQuery = "SELECT COUNT(*) AS total FROM announcements";
    $countResult = $conn->query($countQuery);
    $count = $countResult->fetch_assoc()['total'];
    
    if ($count < 5) {
        $stmt = $conn->prepare("INSERT INTO announcements (content) VALUES (?)");
        $stmt->bind_param("s", $content);
        $stmt->execute();
        $stmt->close();
    } else {
        $error = "You can only have a maximum of 5 announcements.";
    }
}

// Handle deletion of announcements
if (isset($_GET['delete_id'])) {
    $id = $_GET['delete_id'];
    $deleteStmt = $conn->prepare("DELETE FROM announcements WHERE id = ?");
    $deleteStmt->bind_param("i", $id);
    $deleteStmt->execute();
    $deleteStmt->close();
}

// Fetch all announcements
$announcementsQuery = "SELECT * FROM announcements ORDER BY date DESC";
$announcementsResult = $conn->query($announcementsQuery);

$conn->close();
?>



<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dash Admin</title>
    <link rel="stylesheet" href="dashadmincss.css">
    <link rel="stylesheet" href="dashboardadmincss.css">


</head>
<body>
    <?php include 'sidebar.php'; ?>

    <div class="main-content">
        <div class="container">
            <h1>St. Monique Admin Dashboard</h1>
            <h2>Welcome,  <?php echo htmlspecialchars($admin['username'] ?? 'Admin Name'); ?></h2>

            <div class="dashboard">
        <h2>Analytics</h2>
        <div class="tiles">
            <article class="tile">
                <div class="tile-header">
                    <i class="ph-lightning-light"></i>
                    <h3>
                        <span>Homeowners</span>
                        <span>Total Homeowners Account created</span>
                    </h3>
                </div>
                <div class="tile-content">
                    <span><?php echo $total_homeowners; ?></span>
                </div>
                <a href="homeowneradmin.php">
                    <span>Go to Homeowners</span>
                    <span class="icon-button">
                        <i class="ph-caret-right-bold"></i>
                    </span>
                </a>
            </article>

            <article class="tile">
                <div class="tile-header">
                    <i class="ph-fire-simple-light"></i>
                    <h3>
                        <span>Complaints</span>
                        <span>total Complaints Recieved</span>
                    </h3>
                </div>
                <div class="tile-content">
                    <span><?php echo $total_complaints; ?></span>
                </div>
                <a href="admincomplaint.php">
                    <span>Go to Complaints</span>
                    <span class="icon-button">
                        <i class="ph-caret-right-bold"></i>
                    </span>
                </a>
            </article>

            <article class="tile">
                <div class="tile-header">
                    <i class="ph-file-light"></i>
                    <h3>
                        <span>Billing</span>
                        <span>Total Billing created</span>
                    </h3>
                </div>
                <div class="tile-content">
                    <span><?php echo $total_billing; ?></span>
                </div>
                <a href="billingadmin.php">
                    <span>Go to Billing</span>
                    <span class="icon-button">
                        <i class="ph-caret-right-bold"></i>
                    </span>
                </a>
            </article>

            <article class="tile">
                <div class="tile-header">
                    <i class="ph-fire-simple-light"></i>
                    <h3>
                        <span>Appointment</span>
                        <span>total Appointments Recieved</span>
                    </h3>
                </div>
                <div class="tile-content">
                    <span><?php echo $totalAppointments; ?></span>
                </div>
                <a href="admin_approval.php">
                    <span>Go to Appointment</span>
                    <span class="icon-button">
                        <i class="ph-caret-right-bold"></i>
                    </span>
                </a>
            </article>

            <article class="tile">
                <div class="tile-header">
                    <i class="ph-file-light"></i>
                    <h3>
                        <span>Service Requests</span>
                        <span>total Service Requests Recieved</span>
                    </h3>
                </div>
                <div class="tile-content">
                    <span><?php echo $total_service_requests; ?></span>
                </div>
                <a href="serviceadmin.php">
                    <span>Go to Service Requests</span>
                    <span class="icon-button">
                        <i class="ph-caret-right-bold"></i>
                    </span>
                </a>
            </article>
        </div>
        <div class="flex-container">
        <div class="announcement-widget">
    <h2>Manage Announcements</h2>
    <!-- Form for adding a new announcement -->
    <form method="POST" action="dashadmin_add_announcement.php">
        <label for="content">New Announcement:</label><br>
        <textarea id="content" name="content" rows="4" cols="50" required></textarea><br><br>
        <button type="submit" name="add_announcement">Add Announcement</button>
    </form>

    <?php if (isset($error)): ?>
        <p class="error"><?php echo $error; ?></p>
    <?php endif; ?>

    <h3>Current Announcements</h3>
    <table id="announcementTable">
        <thead>
            <tr>
                <th>ID</th>
                <th>Content</th>
                <th>Date</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php if ($announcementsResult->num_rows > 0): ?>
                <?php 
                $count = 0; 
                while ($row = $announcementsResult->fetch_assoc()): 
                    $count++; 
                ?>
                    <tr class="announcement-row <?php echo $count > 5 ? 'hidden-row' : ''; ?>">
                        <td><?php echo $row['id']; ?></td>
                        <td><?php echo htmlspecialchars($row['content']); ?></td>
                        <td><?php echo date('F d, Y', strtotime($row['date'])); ?></td>
                        <td>
                            <a href="dashadmin_edit_announcement.php?id=<?php echo $row['id']; ?>">Edit</a>
                            <a href="?delete_id=<?php echo $row['id']; ?>" onclick="return confirm('Are you sure you want to delete this announcement?');">Delete</a>
                        </td>
                    </tr>
                <?php endwhile; ?>
            <?php else: ?>
                <tr>
                    <td colspan="4">No announcements found.</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>

    <?php if ($announcementsResult->num_rows > 5): ?>
    <button id="seeAllBtn" onclick="toggleAnnouncements()">See All</button>
<?php endif; ?>

</div>



    <div class="recent-payments">
        <h2>Recent Payments</h2>
        <?php
        // Assuming you have a valid database connection in $conn
        $result = mysqli_query($conn, "SELECT p.file_path, p.date, p.billing_reference, h.name AS homeowner_name 
                                        FROM payments p 
                                        JOIN homeowners h ON p.homeowner_id = h.id 
                                        ORDER BY p.date DESC"); // Fetch recent payments

        if ($result && mysqli_num_rows($result) > 0) {
            while ($row = mysqli_fetch_assoc($result)) {
                echo '<div class="payment">';
                echo '<img src="' . htmlspecialchars($row['file_path']) . '" alt="Proof of Payment" class="zoomable">';
                echo '<div class="payment-details">';
                echo '<p>Homeowner: <span>' . htmlspecialchars($row['homeowner_name']) . '</span></p>';
                echo '<p>Date: <span>' . htmlspecialchars($row['date']) . '</span></p>';
                echo '<p>Billing Reference: <span>' . htmlspecialchars($row['billing_reference']) . '</span></p>';
                echo '</div>';
                echo '</div>';
            }
        } else {
            echo '<p>No recent payments found.</p>';
        }
        ?>

<div id="myModal" class="modal">
        <span class="close">&times;</span>
        <img class="modal-content" id="img01">
        <div id="caption"></div>
    </div>
</div>

<script>
        // JavaScript for Modal Image Zoom
        const modal = document.getElementById('myModal');
        const modalImg = document.getElementById('img01');
        const captionText = document.getElementById('caption');
        const zoomableImages = document.querySelectorAll('.zoomable');

        zoomableImages.forEach(img => {
            img.onclick = function () {
                modal.style.display = 'block';
                modalImg.src = this.src;
                captionText.innerHTML = this.alt;
            }
        });

        // Close the modal when the user clicks on <span> (x)
        const span = document.getElementsByClassName('close')[0];
        span.onclick = function () {
            modal.style.display = 'none';
        }

        // Also close the modal when the user clicks anywhere outside of the image
        modal.onclick = function (event) {
            if (event.target == modal) {
                modal.style.display = 'none';
            }
        }
        
    </script>

<script>
function toggleAnnouncements() {
    var hiddenRows = document.querySelectorAll('.hidden-row');
    var seeAllBtn = document.getElementById('seeAllBtn');

    // Check the current state (whether hidden or shown)
    if (hiddenRows[0].style.display === 'none' || hiddenRows[0].style.display === '') {
        // If hidden, show the rows
        hiddenRows.forEach(function(row) {
            row.style.display = 'table-row';
        });
        // Update button text to "Show Less"
        seeAllBtn.textContent = 'Show Less';
    } else {
        // If shown, hide the rows again
        hiddenRows.forEach(function(row) {
            row.style.display = 'none';
        });
        // Update button text to "See All"
        seeAllBtn.textContent = 'See All';
    }
}
</script>


<script src="dashadmin.js"></script>

    

</body>
</html>
