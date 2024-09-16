<?php
session_name('admin_session'); // Set a unique session name for admins
session_start();

// Check if admin is logged in
if (!isset($_SESSION['admin_id'])) {
    header("Location: admin_login.php");
    exit;
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dash Admin</title>

    <link rel="stylesheet" href="dashadmincss.css">
    <link rel="stylesheet" href="dashboardadmincss.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

</head>
<body>
    <?php include 'sidebar.php'; ?>

    <div class="main-content">
        <div class="container">
            <h1>St. Monique Admin Dashboard</h1>
            <h2>Welcome,  <?php echo htmlspecialchars($admin['username'] ?? 'Admin Name'); ?></h2>
            <div class="dashboard">
        <div class="header">
            <h1>Today's Sales</h1>
            <button class="export-btn">Export</button>
        </div>

        <div class="summary-cards">
            <div class="card">
                <h3>$1k</h3>
                <p>Total Sales</p>
                <span>+8% from yesterday</span>
            </div>
            <div class="card">
                <h3>300</h3>
                <p>Total Orders</p>
                <span>+5% from yesterday</span>
            </div>
            <div class="card">
                <h3>5</h3>
                <p>Product Sold</p>
                <span>+1.2% from yesterday</span>
            </div>
            <div class="card">
                <h3>8</h3>
                <p>New Customers</p>
                <span>0.5% from yesterday</span>
            </div>
        </div>

        <div class="charts">
            <div class="chart" id="visitor-insights">
                <h3>Visitor Insights</h3>
                <canvas id="visitorChart"></canvas>
            </div>

            <div class="chart" id="total-revenue">
                <h3>Total Revenue</h3>
                <canvas id="revenueChart"></canvas>
            </div>

            <div class="chart" id="customer-satisfaction">
                <h3>Customer Satisfaction</h3>
                <canvas id="satisfactionChart"></canvas>
            </div>

            <div class="chart" id="target-vs-reality">
                <h3>Target vs Reality</h3>
                <canvas id="targetChart"></canvas>
            </div>

            <div class="chart" id="top-products">
                <h3>Top Products</h3>
                <ul>
                    <li>Home Decor Range - 45%</li>
                    <li>Disney Princess Pink Bag - 29%</li>
                    <li>Bathroom Essentials - 18%</li>
                    <li>Apple Smartwatches - 25%</li>
                </ul>
            </div>

            <div class="chart" id="sales-mapping">
                <h3>Sales Mapping by Country</h3>
                <canvas id="mapChart"></canvas>
            </div>

            <div class="chart" id="volume-vs-service">
                <h3>Volume vs Service Level</h3>
                <canvas id="volumeServiceChart"></canvas>
            </div>
        </div>
    </div>
    <script src="script.js"></script>
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
