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
            <button>Setup an appointment</button>
        </div>

        <div class="services">
            <button>Request for a service</button>
        </div>

        <div class="calendar">
            <h2>May 2023</h2>
            <table>
                <tr>
                    <td>1</td><td>2</td><td>3</td><td>4</td><td>5</td><td>6</td><td>7</td>
                </tr>
                <tr>
                    <td>8</td><td>9</td><td>10</td><td>11</td><td>12</td><td>13</td><td>14</td>
                </tr>
                <!-- Continue for the rest of the days -->
            </table>
        </div>
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
