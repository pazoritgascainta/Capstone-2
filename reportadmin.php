<?php
session_name('admin_session');
session_start();

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

// Get the selected time period from the form or default to 'this_month'
$timePeriod = isset($_POST['timePeriod']) ? $_POST['timePeriod'] : 'this_month';

// Homeowner Count
$homeownerCountQuery = "SELECT COUNT(*) AS homeowner_count FROM homeowners";
$homeownerCountResult = mysqli_query($conn, $homeownerCountQuery);
$homeownerCount = mysqli_fetch_assoc($homeownerCountResult)['homeowner_count'] ?? 0;

// Prepare SQL queries based on the selected time period
switch ($timePeriod) {
    case 'today':
        $dateCondition = "DATE(paid_date) = CURDATE()";
        break;

    case 'last_week':
        $dateCondition = "paid_date >= CURDATE() - INTERVAL WEEKDAY(CURDATE()) + 7 DAY 
                          AND paid_date < CURDATE() - INTERVAL WEEKDAY(CURDATE()) DAY";
        break;

    case 'this_week':
        $dateCondition = "paid_date >= CURDATE() - INTERVAL WEEKDAY(CURDATE()) DAY 
                          AND paid_date < CURDATE() + INTERVAL (7 - WEEKDAY(CURDATE())) DAY";
        break;

    case 'this_month':
        $dateCondition = "MONTH(paid_date) = MONTH(CURDATE()) AND YEAR(paid_date) = YEAR(CURDATE())";
        break;

    case 'last_month':
        $dateCondition = "MONTH(paid_date) = MONTH(CURDATE() - INTERVAL 1 MONTH) 
                          AND YEAR(paid_date) = YEAR(CURDATE())";
        break;

    case 'this_year':
        $dateCondition = "YEAR(paid_date) = YEAR(CURDATE())";
        break;

    case 'last_year':
        $dateCondition = "YEAR(paid_date) = YEAR(CURDATE()) - 1";
        break;

    case 'all_time':
    default:
        $dateCondition = "1"; // No filtering for all time
        break;
}

// Retrieve total earnings based on the selected time period
$totalEarningsQuery = "
    SELECT SUM(total_amount) AS total_earnings 
    FROM billing 
    WHERE $dateCondition AND status NOT IN ('Overdue', 'Pending')"; // Exclude overdue and pending payments

// Retrieve paid monthly dues from the billing_history table
$paidMonthlyDuesQuery = "
    SELECT 
        SUM(monthly_due) AS total_paid_monthly_dues 
    FROM 
        billing_history 
    WHERE 
        paid_date IS NOT NULL AND $dateCondition
";

$paidMonthlyDuesResult = mysqli_query($conn, $paidMonthlyDuesQuery);
$paidMonthlyDuesData = mysqli_fetch_assoc($paidMonthlyDuesResult);

$totalPaidMonthlyDues = $paidMonthlyDuesData['total_paid_monthly_dues'] ?? 0.00;

// Run the query for total earnings
$totalEarningsResult = mysqli_query($conn, $totalEarningsQuery);
$totalEarnings = mysqli_fetch_assoc($totalEarningsResult)['total_earnings'] ?? 0.00;

// Add the total paid monthly dues to the total earnings
$totalEarnings += $totalPaidMonthlyDues;

// Get pending payments count
$pendingPaymentsQuery = "SELECT COUNT(*) AS pending_payments_count FROM billing 
                         WHERE status = 'Pending' 
                         AND ($dateCondition)";
$pendingPaymentsResult = mysqli_query($conn, $pendingPaymentsQuery);
$pendingPaymentsCount = mysqli_fetch_assoc($pendingPaymentsResult)['pending_payments_count'] ?? 0;

// Amount Pending
$pendingPaymentsQuery = "SELECT SUM(total_amount) AS pending_payments_amount FROM billing 
                         WHERE status = 'Pending' 
                         AND ($dateCondition)";
$pendingPaymentsResult = mysqli_query($conn, $pendingPaymentsQuery);
$pendingPaymentsData = mysqli_fetch_assoc($pendingPaymentsResult);
$pendingPaymentsAmount = $pendingPaymentsData['pending_payments_amount'] ?? 0.00; // Default to 0.00 if null

// Get overdue payments count and total
$overduePaymentsQuery = "SELECT COUNT(*) AS overdue_count, SUM(total_amount) AS overdue_total FROM billing 
                         WHERE status = 'Overdue' 
                         AND ($dateCondition)";
$overduePaymentsResult = mysqli_query($conn, $overduePaymentsQuery);
$overdueData = mysqli_fetch_assoc($overduePaymentsResult);
$overdueCount = $overdueData['overdue_count'] ?? 0;
$overdueTotal = $overdueData['overdue_total'] ?? 0.00;

// Accepted Appointments Earnings and Total Count
$appointmentsEarningsQuery = "SELECT COUNT(*) AS total_appointments, SUM(amount) AS accepted_appointments_earnings 
                               FROM accepted_appointments WHERE status = 'paid'";
$appointmentsEarningsResult = mysqli_query($conn, $appointmentsEarningsQuery);
$appointmentsData = mysqli_fetch_assoc($appointmentsEarningsResult);
$totalAppointments = $appointmentsData['total_appointments'] ?? 0;
$acceptedAppointmentsEarnings = $appointmentsData['accepted_appointments_earnings'] ?? 0.00;

// Pending Appointments Count
$pendingAppointmentsQuery = "SELECT COUNT(*) AS pending_appointments_count FROM appointments 
                             WHERE status = 'Pending'";
$pendingAppointmentsResult = mysqli_query($conn, $pendingAppointmentsQuery);
$pendingAppointmentsCount = mysqli_fetch_assoc($pendingAppointmentsResult)['pending_appointments_count'] ?? 0;

// Passed Appointments Count
$passedAppointmentsQuery = "SELECT COUNT(*) AS passed_appointments_count FROM passed_appointments";
$passedAppointmentsResult = mysqli_query($conn, $passedAppointmentsQuery);
$passedAppointmentsCount = mysqli_fetch_assoc($passedAppointmentsResult)['passed_appointments_count'] ?? 0;

// Rejected Appointments Count
$rejectedAppointmentsQuery = "SELECT COUNT(*) AS rejected_appointments_count FROM rejected_appointments";
$rejectedAppointmentsResult = mysqli_query($conn, $rejectedAppointmentsQuery);
$rejectedAppointmentsCount = mysqli_fetch_assoc($rejectedAppointmentsResult)['rejected_appointments_count'] ?? 0;

// Accepted Appointments Count
$acceptedAppointmentsQuery = "SELECT COUNT(*) AS accepted_appointments_count FROM accepted_appointments";
$acceptedAppointmentsResult = mysqli_query($conn, $acceptedAppointmentsQuery);
$acceptedAppointmentsCount = mysqli_fetch_assoc($acceptedAppointmentsResult)['accepted_appointments_count'] ?? 0;

// Total Pending Service Requests
$pendingQuery = "SELECT COUNT(*) AS total_pending FROM serreq WHERE status = 'Pending'";
$pendingResult = mysqli_query($conn, $pendingQuery);
$totalPending = mysqli_fetch_assoc($pendingResult)['total_pending'] ?? 0;

// Total In Progress Service Requests
$inProgressQuery = "SELECT COUNT(*) AS total_in_progress FROM serreq WHERE status = 'In Progress'";
$inProgressResult = mysqli_query($conn, $inProgressQuery);
$totalInProgress = mysqli_fetch_assoc($inProgressResult)['total_in_progress'] ?? 0;

// Total Resolved Service Requests
$resolvedQuery = "SELECT COUNT(*) AS total_resolved FROM serreq WHERE status = 'Resolved'";
$resolvedResult = mysqli_query($conn, $resolvedQuery);
$totalResolved = mysqli_fetch_assoc($resolvedResult)['total_resolved'] ?? 0;

// Fetch counts for each status of complaints
$totalPendingComplaintsQuery = "SELECT COUNT(*) AS total_pending FROM complaints WHERE status = 'Pending'";
$totalInProgressComplaintsQuery = "SELECT COUNT(*) AS total_in_progress FROM complaints WHERE status = 'In Progress'";
$totalResolvedComplaintsQuery = "SELECT COUNT(*) AS total_resolved FROM complaints WHERE status = 'Resolved'";

$totalPendingComplaintsResult = mysqli_query($conn, $totalPendingComplaintsQuery);
$totalInProgressComplaintsResult = mysqli_query($conn, $totalInProgressComplaintsQuery);
$totalResolvedComplaintsResult = mysqli_query($conn, $totalResolvedComplaintsQuery);

$totalPendingComplaints = mysqli_fetch_assoc($totalPendingComplaintsResult)['total_pending'] ?? 0;
$totalInProgressComplaints = mysqli_fetch_assoc($totalInProgressComplaintsResult)['total_in_progress'] ?? 0;
$totalResolvedComplaints = mysqli_fetch_assoc($totalResolvedComplaintsResult)['total_resolved'] ?? 0;


// Calculate total combined earnings
$totalCombinedEarnings = $totalEarnings + $acceptedAppointmentsEarnings; // Combined earnings from dues and appointments

?>



<!DOCTYPE html>
<html lang="en">
<head>
    <title>St. Monique Report</title>
    <link rel="stylesheet" href="reportadmin.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body>
<?php include 'sidebar.php'; ?>
<div class="main-content">
    <h1>St. Monique Reports</h1>
    <div class="report-wrapper"> <!-- Added wrapper for flex layout -->
    <!-- Move side-container to be on top -->
    <div class="side-container">
        <div class="widget">
            <h2>Total Homeowners</h2>
            <p><?php echo $homeownerCount; ?></p>
            <canvas id="homeownerChart"></canvas>
        </div>

       
<div class="widget">
    <h2>Service Requests Summary</h2>
<br>
    <canvas id="serviceRequestsChart"></canvas>
</div>

<div class="widget">
    <h2>Appointments Summary</h2>
<br>
    <canvas id="appointmentsComparisonChart"></canvas>`
</div>
<div class="widget">
    <h2> Complaints Summary</h2>
<br>
    <canvas id="complaintsChart"></canvas>
</div>

    </div></div>

    <div class="reports">
    <div class="separator">
<div class="report-filter-container">

<div class="filter-section">
        <label for="timePeriod">Select Time Period:</label>
        <form method="POST" action="">
            <select id="timePeriod" name="timePeriod" onchange="this.form.submit()">
                <option value="today" <?php echo ($timePeriod == 'today') ? 'selected' : ''; ?>>Today</option>
                <option value="last_week" <?php echo ($timePeriod == 'last_week') ? 'selected' : ''; ?>>Last Week</option>
                <option value="this_week" <?php echo ($timePeriod == 'this_week') ? 'selected' : ''; ?>>This Week</option>
                <option value="this_month" <?php echo ($timePeriod == 'this_month') ? 'selected' : ''; ?>>This Month</option>
                <option value="last_month" <?php echo ($timePeriod == 'last_month') ? 'selected' : ''; ?>>Last Month</option>
                <option value="this_year" <?php echo ($timePeriod == 'this_year') ? 'selected' : ''; ?>>This Year</option>
                <option value="last_year" <?php echo ($timePeriod == 'last_year') ? 'selected' : ''; ?>>Last Year</option>
                <option value="all_time" <?php echo ($timePeriod == 'all_time') ? 'selected' : ''; ?>>All Time</option>
            </select>
        </form>
    </div>
    <div class="report-selector">
        <label for="reportSelect">Select Report:</label>
        <select id="reportSelect" onchange="updateReport()">
            <option value="combined">Total Earnings</option>
            <option value="monthly">Monthly Dues</option>
            <option value="appointments">Appointments Earnings</option>
            <option value="pending">Pending Payments</option>
            <option value="overdue">Overdue Payments</option>
        </select>
    </div>

</div>
<br>
<div class="report-section" id="combinedEarningsSection">
    <h2>Total Earnings</h2>
    <p>₱<?php echo number_format($totalEarnings + $acceptedAppointmentsEarnings, 2); ?></p>
    <canvas id="combinedEarningsChart" class="chart"></canvas>
</div>


        <div class="report-section" id="monthlyDuesSection" style="display: none;">
    <h2>Monthly Dues</h2>
    <p>₱<?php echo number_format($totalPaidMonthlyDues, 2); ?></p>
    <canvas id="earningsChart" class="chart"></canvas>
</div>

        

        <div class="report-section" id="appointmentsEarningsSection" style="display: none;">
            <h2>Appointments Earnings</h2>
            <p>₱<?php echo number_format($acceptedAppointmentsEarnings, 2); ?></p>
            <canvas id="appointmentsEarningsChart" class="chart"></canvas>
        </div>

        <div class="report-section" id="pendingPaymentsSection" style="display: none;">
    <h2>Pending Payments</h2>
    <p>Count: <?php echo $pendingPaymentsCount; ?></p>
    <p>Total Amount: ₱<?php echo number_format($pendingPaymentsAmount, 2); ?></p> <!-- Add this line -->
    <canvas id="pendingPaymentsChart" class="chart"></canvas>
</div>


        <div class="report-section" id="overduePaymentsSection" style="display: none;">
            <h2>Overdue Payments</h2>
            <p>Count: <?php echo $overdueCount; ?></p>
            <p>Total: ₱<?php echo number_format($overdueTotal, 2); ?></p>
            <canvas id="overduePaymentsChart" class="chart"></canvas>
        </div>
    </div>
</div>

</div>

<script>
window.onload = function() {
    // Chart contexts
    const ctxHomeowner = document.getElementById('homeownerChart').getContext('2d');
    const ctxEarnings = document.getElementById('earningsChart').getContext('2d');
    const ctxPendingPayments = document.getElementById('pendingPaymentsChart').getContext('2d');
    const ctxOverduePayments = document.getElementById('overduePaymentsChart').getContext('2d');
    const ctxAppointmentsEarnings = document.getElementById('appointmentsEarningsChart').getContext('2d');
    const ctxServiceRequests = document.getElementById('serviceRequestsChart').getContext('2d'); // Updated
    const ctxCombinedEarnings = document.getElementById('combinedEarningsChart').getContext('2d');
    const ctxComplaints = document.getElementById('complaintsChart').getContext('2d'); // Added context for complaints

    // Homeowner Count Chart
    drawBarChart(ctxHomeowner, <?php echo $homeownerCount; ?>, 'Total Homeowners', '#4caf50');

    // Total Earnings Chart
    drawBarChart(ctxEarnings, <?php echo $totalEarnings; ?>, 'Total Earnings', '#2196F3');

    // Pending Payments Chart
    drawBarChart(ctxPendingPayments, <?php echo $pendingPaymentsCount; ?>, 'Pending Payments', '#FFC107');

    // Overdue Payments Chart
    drawBarChart(ctxOverduePayments, <?php echo $overdueCount; ?>, 'Overdue Payments', '#F44336');

    // Accepted Appointments Earnings Chart
    drawBarChart(ctxAppointmentsEarnings, <?php echo $acceptedAppointmentsEarnings; ?>, 'Accepted Appointments Earnings', '#673AB7');

    // Service Requests Summary Chart
    drawServiceRequestsChart(ctxServiceRequests, [
        <?php echo $totalPending; ?>,
        <?php echo $totalInProgress; ?>,
        <?php echo $totalResolved; ?>
    ]);

    // Complaints Summary Chart
    drawComplaintsChart(ctxComplaints, [
        <?php echo $totalPendingComplaints; ?>,
        <?php echo $totalInProgressComplaints; ?>,
        <?php echo $totalResolvedComplaints; ?>
    ]);

    // Total Combined Earnings Chart
    drawBarChart(ctxCombinedEarnings, [<?php echo $totalEarnings; ?>, <?php echo $acceptedAppointmentsEarnings; ?>], 'Total Earnings', '#3F51B5');

    // Appointments Comparison Chart
    const ctxAppointmentsComparison = document.getElementById('appointmentsComparisonChart').getContext('2d');
    drawAppointmentsComparisonChart(ctxAppointmentsComparison, [
        <?php echo $pendingAppointmentsCount; ?>,
        <?php echo $passedAppointmentsCount; ?>,
        <?php echo $rejectedAppointmentsCount; ?>,
        <?php echo $acceptedAppointmentsCount; ?>
    ]);

    // Call the updateReport function to show the default report
    updateReport();
};

// Function to draw a bar chart
function drawBarChart(ctx, data, label, color) {
    // Check if data is an array; if not, convert to an array for single values
    if (!Array.isArray(data)) {
        data = [data];
    }

    // Create the chart
    new Chart(ctx, {
        type: 'bar',
        data: {
            labels: data.length > 1 ? ['Monthly Dues', 'Accepted Appointments'] : [label],
            datasets: [{
                label: label,
                data: data,
                backgroundColor: color,
            }]
        },
        options: {
            scales: {
                y: {
                    beginAtZero: true
                }
            }
        }
    });
}

function drawServiceRequestsChart(ctx, data) {
    new Chart(ctx, {
        type: 'bar',
        data: {
            labels: ['Pending', 'In Progress', 'Resolved'],
            datasets: [{
                label: 'Service Requests Count',
                data: data,
                backgroundColor: [
                    '#FF9800', // Color for Pending
                    '#2196F3', // Color for In Progress
                    '#4CAF50'  // Color for Resolved
                ],
            }]
        },
        options: {
            scales: {
                y: {
                    beginAtZero: true,
                    title: {
                        display: true,
                        text: 'Count'
                    }
                },
                x: {
                    title: {
                        display: true,
                        text: 'Service Request Status'
                    }
                }
            }
        }
    });
}

// New function to draw the complaints chart
function drawComplaintsChart(ctx, data) {
    new Chart(ctx, {
        type: 'bar',
        data: {
            labels: ['Pending', 'In Progress', 'Resolved'],
            datasets: [{
                label: 'Complaints Count',
                data: data,
                backgroundColor: [
                    '#FF5722', // Color for Pending Complaints
                    '#9C27B0', // Color for In Progress Complaints
                    '#4CAF50'  // Color for Resolved Complaints
                ],
            }]
        },
        options: {
            scales: {
                y: {
                    beginAtZero: true,
                    title: {
                        display: true,
                        text: 'Count'
                    }
                },
                x: {
                    title: {
                        display: true,
                        text: 'Complaint Status'
                    }
                }
            }
        }
    });
}

function drawAppointmentsComparisonChart(ctx, data) {
    new Chart(ctx, {
        type: 'bar',
        data: {
            labels: ['Pending', 'Passed', 'Rejected', 'Accepted'],
            datasets: [{
                label: 'Appointments Count',
                data: data,
                backgroundColor: [
                    '#FF9800', // Color for Pending Appointments
                    '#FF5722', // Color for Passed Appointments
                    '#9C27B0', // Color for Rejected Appointments
                    '#795548'  // Color for Accepted Appointments
                ],
            }]
        },
        options: {
            scales: {
                y: {
                    beginAtZero: true,
                    title: {
                        display: true,
                        text: 'Count'
                    }
                },
                x: {
                    title: {
                        display: true,
                        text: 'Appointment Types'
                    }
                }
            }
        }
    });
}

function updateReport() {
    const selectedValue = document.getElementById('reportSelect').value;

    // Hide all report sections
    document.querySelectorAll('.report-section').forEach(section => {
        section.style.display = 'none';
    });

    // Show the selected report section
    const reportSections = {
        combined: 'combinedEarningsSection',
        monthly: 'monthlyDuesSection',
        appointments: 'appointmentsEarningsSection',
        pending: 'pendingPaymentsSection',
        overdue: 'overduePaymentsSection',
        serviceRequests: 'serviceRequestsSection',
        complaints: 'complaintsSection'
    };

    if (reportSections[selectedValue]) {
        document.getElementById(reportSections[selectedValue]).style.display = 'block';
    }
}
</script>





</body>
</html>
