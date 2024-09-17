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

</head>
<body>
    <?php include 'usersidebar.php'; ?>

    <div class="main-content">
        <div class="container">
            <h1>St. Monique User Dashboard</h1>
            <h2>Welcome, <?php echo htmlspecialchars($homeowner['name']); ?></h2>
            <div class="dashboard">
        <div class="payment-history">
            <h2>Payment History</h2>
            <div class="view-toggle">
                <button>today</button>
                <button>weekly</button>
                <button>monthly</button>
            </div>
            <ul>
                <li>
                    <img src="user.png" alt="User Image">
                    <div class="payment-info">
                        <p><strong>Temitope</strong> Online shop</p>
                        <p>August 4, 2022, 7:30AM</p>
                        <p>+6,000.32 Mastercard 404</p>
                    </div>
                    <span class="status pending">Pending</span>
                </li>
                <li>
                    <img src="user.png" alt="User Image">
                    <div class="payment-info">
                        <p><strong>Temitope</strong> Online shop</p>
                        <p>August 4, 2022, 7:30AM</p>
                        <p>+6,000.32 Mastercard 404</p>
                    </div>
                    <span class="status completed">Completed</span>
                </li>
                <li>
                    <img src="user.png" alt="User Image">
                    <div class="payment-info">
                        <p><strong>Temitope</strong> Online shop</p>
                        <p>August 4, 2022, 7:30AM</p>
                        <p>+6,000.32 Mastercard 404</p>
                    </div>
                    <span class="status failed">Failed</span>
                </li>
                <li>
                    <img src="user.png" alt="User Image">
                    <div class="payment-info">
                        <p><strong>Temitope</strong> Online shop</p>
                        <p>August 4, 2022, 7:30AM</p>
                        <p>+6,000.32 Mastercard 404</p>
                    </div>
                    <span class="status pending">Pending</span>
                </li>
            </ul>
        </div>

        <div class="payment-summary">
            <p>To be paid:</p>
            <h2>₱356.00</h2>
            <p>For the month of may</p>
            <button class="pay-now">Pay now</button>
            <button class="transfer">Transfer</button>
        </div>

        <div class="appointments">
        <a href="amenity_booking.php">
            <button>Setup an appointment</button>
            </a>
        </div>

        <div class="services">
        <a href="serviceuser.php">
            <button>Request for a service</button>
            </a>
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
        
    </script>
<div id="calendar-box">
                <div id="calendar-container">
                    <div id="calendar-nav">
                        <button id="prev-month" type="button">&lt;</button>
                        <span id="month-year">2024 September</span>
                        <button id="next-month" type="button">&gt;</button>
                    </div>
                    <div id="calendar"><div class="calendar-cell past-day">1</div><div class="calendar-cell past-day">2</div><div class="calendar-cell past-day">3</div><div class="calendar-cell past-day">4</div><div class="calendar-cell past-day">5</div><div class="calendar-cell past-day">6</div><div class="calendar-cell past-day">7</div><div class="calendar-cell past-day">8</div><div class="calendar-cell past-day">9</div><div class="calendar-cell past-day">10</div><div class="calendar-cell past-day">11</div><div class="calendar-cell past-day">12</div><div class="calendar-cell past-day">13</div><div class="calendar-cell past-day">14</div><div class="calendar-cell past-day">15</div><div class="calendar-cell past-day">16</div><div class="calendar-cell today">17</div><div class="calendar-cell">18</div><div class="calendar-cell">19</div><div class="calendar-cell">20</div><div class="calendar-cell">21</div><div class="calendar-cell">22</div><div class="calendar-cell">23</div><div class="calendar-cell">24</div><div class="calendar-cell">25</div><div class="calendar-cell">26</div><div class="calendar-cell">27</div><div class="calendar-cell">28</div><div class="calendar-cell">29</div><div class="calendar-cell">30</div><div class="calendar-cell empty">1</div><div class="calendar-cell empty">2</div><div class="calendar-cell empty">3</div><div class="calendar-cell empty">4</div><div class="calendar-cell empty">5</div></div>
                </div>
            </div>
</body>
</html>
