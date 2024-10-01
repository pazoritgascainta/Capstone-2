<?php
session_name('user_session');
session_start();

// Check if homeowner is logged in
if (!isset($_SESSION['homeowner_id'])) {
    header("Location: login.php");
    exit;
}

// Database connection
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "homeowner";

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Initialize homeowner ID from session
$homeowner_id = intval($_SESSION['homeowner_id']);

// Fetch images and their dates from the payments table
$sql_images = "SELECT date, file_path FROM payments WHERE homeowner_id = ?";
$stmt_images = $conn->prepare($sql_images);
$stmt_images->bind_param("i", $homeowner_id);
$stmt_images->execute();
$result_images = $stmt_images->get_result();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Uploaded Images</title>
    <link rel="stylesheet" href="uploaded_payment.css">

</head>
<body>
    <?php include 'usersidebar.php'; ?>

    <div class="main-content">
        <h1>Your Uploaded Images</h1>

        <div class="recent-payments">
            <?php if ($result_images->num_rows > 0): ?>
                <?php while ($row = $result_images->fetch_assoc()): ?>
                    <div class="payment">
                        <div class="payment-details">
                            <span>Date: <?php echo htmlspecialchars($row['date']); ?></span>
                        </div>
                        <div class="payment-image">
                            <?php if (!empty($row['file_path'])): ?>
                                <img src="<?php echo htmlspecialchars($row['file_path']); ?>" alt="Image" class="zoomable">
                            <?php else: ?>
                                <p>No image available</p>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endwhile; ?>
            <?php else: ?>
                <p>No images found.</p>
            <?php endif; ?>
        </div>
    </div>

    <div id="myModal" class="modal">
        <span class="close">&times;</span>
        <img class="modal-content" id="img01">
        <div id="caption"></div>
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
 
</body>
</html>
