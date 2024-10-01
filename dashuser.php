<?php
session_name('user_session'); 
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
// Check if user is logged in
if (!isset($_SESSION['homeowner_id'])) {
    header("Location: login.php");
    exit;
}

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "homeowner";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$homeowner_id = $_SESSION['homeowner_id'];


$announcementsQuery = "SELECT * FROM announcements ORDER BY date DESC LIMIT 5";
$announcementsResult = $conn->query($announcementsQuery);

$sql_total_balance = "SELECT SUM(total_amount) as total_balance FROM billing WHERE homeowner_id = ?";
$stmt_total_balance = $conn->prepare($sql_total_balance);
$stmt_total_balance->bind_param("i", $homeowner_id);
$stmt_total_balance->execute();
$result_total_balance = $stmt_total_balance->get_result();
$row_total_balance = $result_total_balance->fetch_assoc();

// Get the total balance (if no records, set to 0)
$total_balance = isset($row_total_balance['total_balance']) ? $row_total_balance['total_balance'] : 0;

// Query to fetch accepted appointments for the homeowner
$sql_accepted_appointments = "SELECT date, amount, status FROM accepted_appointments WHERE homeowner_id = ?";
$stmt_accepted_appointments = $conn->prepare($sql_accepted_appointments);
$stmt_accepted_appointments->bind_param("i", $homeowner_id);
$stmt_accepted_appointments->execute();
$result_accepted_appointments = $stmt_accepted_appointments->get_result();

// Initialize total appointments amount
$total_appointments_amount = 0;

// Calculate total amount for accepted appointments
while ($row = $result_accepted_appointments->fetch_assoc()) {
    $total_appointments_amount += $row['amount'];
}

// Reset the pointer of the result set to use it again for displaying in the table
$result_accepted_appointments->data_seek(0); // Move the pointer back to the start

// Prepare billing query
$sql_billing = "SELECT billing_date, due_date, monthly_due, status, total_amount FROM billing WHERE homeowner_id = ?";
$stmt_billing = $conn->prepare($sql_billing);
$stmt_billing->bind_param("i", $homeowner_id);

// Check if the statement executes successfully
if ($stmt_billing->execute()) {
    $result_billing = $stmt_billing->get_result();
} else {
    die("Error fetching billing data: " . $stmt_billing->error);
}

// Ensure homeowner_id is set in the session
if (!isset($_SESSION['homeowner_id'])) {
    die("Homeowner ID not found in session.");
}


$homeowner_id = $_SESSION['homeowner_id'];

// Retrieve user name from session
$user_name = $_SESSION['homeowner_name'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Welcome, <?php echo htmlspecialchars($user_name); ?></title>
    <link rel="stylesheet" href="usersidebar.css">
    <link rel="stylesheet" href="dashusercss.css">
    <link rel="stylesheet" href="dashusercalendarcss.css">
    <link rel="stylesheet" href="dashuserannouncement.css">

</head>
<body>
    <?php include 'usersidebar.php'; ?>

    <div class="main-content">
        <div class="container">
            <h1>St. Monique User Dashboard</h1>
            <h2>Welcome, <?php echo htmlspecialchars($homeowner['name']); ?></h2>
            <div class="dashboard">
            <div class="announcement-container">
        <div class="announcement-main">
            <div class="carousel">
                <div class="carousel-inner">
                    <div class="carousel-item active">
                        <img src="undermaintenance.jpg" alt="Announcement Image 1">
                    </div>
                    <div class="carousel-item">
                        <img src="webinar.jpg" alt="Announcement Image 2">
                    </div>
                    <div class="carousel-item">
                        <img src="basketball-poster.jpg" alt="Announcement Image 3">
                    </div>
                </div>
                <a class="carousel-control-prev" role="button">
                    <span class="carousel-control-prev-icon" aria-hidden="false">&lt;</span>
                </a>
                <a class="carousel-control-next" role="button">
                    <span class="carousel-control-next-icon" aria-hidden="true">&gt;</span>
                </a>
            </div>
        </div>

<div class="announcement-news">
    <h3>Latest Announcements</h3>
    <ul>
        <?php if ($announcementsResult->num_rows > 0): ?>
            <?php while ($row = $announcementsResult->fetch_assoc()): ?>
                <li>
                    <strong><?php echo date('F d, Y', strtotime($row['date'])); ?></strong>: 
                    <?php echo htmlspecialchars($row['content']); ?>
                </li>
            <?php endwhile; ?>
        <?php else: ?>
            <li>No announcements at the moment.</li>
        <?php endif; ?>
    </ul>
        </div>
</div>

<section class="payment-history">
    <h2>Upcoming Payment</h2>
    <ul>
        <?php if ($result_billing->num_rows > 0) { ?>
            <?php while ($row = $result_billing->fetch_assoc()) { ?>
                <li>
                    <img src="dashbill.png" alt="User Image">
                    <div class="payment-info">
                        <p><strong><?php echo htmlspecialchars(date('F Y', strtotime($row['billing_date']))); ?></strong></p>
                        <p>Due Date: <?php echo htmlspecialchars(date('F d, Y', strtotime($row['due_date']))); ?></p>
                        <p>Monthly Due: ₱<?php echo number_format($row['monthly_due'], 2); ?></p>
                        <p>Total Amount: ₱<?php echo number_format($row['total_amount'], 2); ?></p>
                    </div>
                    <span class="status <?php echo strtolower($row['status']); ?>">
                        <?php echo ucfirst($row['status']); ?>
                    </span>
                </li>
            <?php } ?>
        <?php } else { ?>
            <li>No payment records found.</li>
        <?php } ?>
    </ul>
    <div class="payment-summary">
    <p>To be paid:</p>
    <h2>₱<?php echo number_format($total_balance + $total_appointments_amount, 2); ?></h2>
    <p>For the month of <?php echo date('F'); ?></p> <!-- Display current month -->
    <a href="payment.php">
        <button class="pay-now">Pay now</button>
    </a>
</div>
</section>
<div class="appointments">
    <a href="amenity_booking.php" class="appointment-link">
        <button class="appointment-button">
            <img src="appointment-calendar (1).png" alt="Appointment Image">
            <span class="button-title">Setup an appointment</span>
        </button>
    </a>
    <a href="serviceuser.php" class="service-link">
        <button class="service-button">
            <img src="appointment-calendar (2).png" alt="Service Image">
            <span class="button-title">Request a service</span>
        </button>
    </a>
    <a href="usercomplaint.php" class="service-link">
        <button class="complaint-button">
            <img src="complaintreq.png" alt="Service Image">
            <span class="button-title">Request a complaint</span>
        </button>
    </a>
</div>


           <div id="calendar-box">
                <div id="calendar-container">
                    <div id="calendar-nav">
                        <button id="prev-month" type="button">&lt;</button>
                        <span id="month-year"></span>
                        <button id="next-month" type="button">&gt;</button>
                    </div>
                    <div id="calendar">
                        <div class="calendar-header-cell">Sun</div>
                        <div class="calendar-header-cell">Mon</div>
                        <div class="calendar-header-cell">Tue</div>
                        <div class="calendar-header-cell">Wed</div>
                        <div class="calendar-header-cell">Thu</div>
                        <div class="calendar-header-cell">Fri</div>
                        <div class="calendar-header-cell">Sat</div>
                        <!-- Days will be generated by JavaScript -->
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
    <script>
        //NewsCarousel
document.addEventListener('DOMContentLoaded', function () {
    const carouselInner = document.querySelector('.carousel-inner');
    const items = document.querySelectorAll('.carousel-item');
    const prevButton = document.querySelector('.carousel-control-prev');
    const nextButton = document.querySelector('.carousel-control-next');
    let currentIndex = 0;
    const totalItems = items.length;

    function updateCarousel() {
        const offset = -currentIndex * 100;
        carouselInner.style.transform = `translateX(${offset}%)`;
    }

    function nextSlide() {
        currentIndex = (currentIndex + 1) % totalItems;
        updateCarousel();
    }

    function prevSlide() {
        currentIndex = (currentIndex - 1 + totalItems) % totalItems;
        updateCarousel();
    }

    nextButton.addEventListener('click', nextSlide);
    prevButton.addEventListener('click', prevSlide);

    // Auto slide every 5 seconds
    setInterval(nextSlide, 5000);
});
    </script>
 <script src="dashusercalendar.js"></script>

</body>
</html>
