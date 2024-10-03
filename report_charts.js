
window.onload = function() {
    // Chart contexts
    const ctxHomeowner = document.getElementById('homeownerChart').getContext('2d');
    const ctxEarnings = document.getElementById('earningsChart').getContext('2d');
    const ctxPendingPayments = document.getElementById('pendingPaymentsChart').getContext('2d');
    const ctxOverduePayments = document.getElementById('overduePaymentsChart').getContext('2d');
    const ctxAppointmentsEarnings = document.getElementById('appointmentsEarningsChart').getContext('2d');
    const ctxComplaints = document.getElementById('complaintsChart').getContext('2d');
    const ctxCombinedEarnings = document.getElementById('combinedEarningsChart').getContext('2d');

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
    const ctxServiceRequests = document.getElementById('serviceRequestsChart').getContext('2d');
    drawServiceRequestsChart(ctxServiceRequests, [
        <?php echo $totalPending; ?>,
        <?php echo $totalInProgress; ?>,
        <?php echo $totalResolved; ?>
    ]);

    // Total Complaints Chart
    drawBarChart(ctxComplaints, <?php echo $totalComplaints; ?>, 'Total Complaints', '#795548');

    // Total Combined Earnings Chart
    drawBarChart(ctxCombinedEarnings, <?php echo $totalCombinedEarnings; ?>, 'Total Combined Earnings', '#3F51B5');

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

function drawBarChart(ctx, data, label, color) {
    new Chart(ctx, {
        type: 'bar',
        data: {
            labels: [label],
            datasets: [{
                label: label,
                data: [data],
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
    if (selectedValue === 'combined') {
        document.getElementById('combinedEarningsSection').style.display = 'block';
    } else if (selectedValue === 'monthly') {
        document.getElementById('monthlyDuesSection').style.display = 'block';
    } else if (selectedValue === 'appointments') {
        document.getElementById('appointmentsEarningsSection').style.display = 'block';
    } else if (selectedValue === 'pending') {
        document.getElementById('pendingPaymentsSection').style.display = 'block';
    } else if (selectedValue === 'overdue') {
        document.getElementById('overduePaymentsSection').style.display = 'block';
    }
}
