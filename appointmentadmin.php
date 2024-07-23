<?php
session_start();

// Check if admin is logged in
if (!isset($_SESSION['admin_logged_in'])) {
    header("Location: admin_login.php?error=not_logged_in");
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin - Appointments</title>
  
    <link rel="stylesheet" href="datecs.css">
    <link rel="stylesheet" href="appointmentadmin.css">
</head>
<body>
<?php include 'sidebar.php'; ?>

<div class="main-content">
    <div class="container">
        <h1>Manage Appointments</h1>
        
        <!-- Calendar component, if required -->
        <div class="calendar body">
            <div class="month"></div>
            <div class="days">
                <div class="day">Sunday</div>
                <div class="day">Monday</div>
                <div class="day">Tuesday</div>
                <div class="day">Wednesday</div>
                <div class="day">Thursday</div>
                <div class="day">Friday</div>
                <div class="day">Saturday</div>
            </div>
            <div class="dates"></div>
        </div>
        
        <!-- Appointments table -->
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Date</th>
                    <th>Time</th>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Purpose</th>
                    <th>Status</th> <!-- Changed column header to Status -->
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php
                // Establish database connection
                $conn = new mysqli('localhost', 'root', '', 'homeowner');

                // Check connection
                if ($conn->connect_error) {
                    die("Connection failed: " . $conn->connect_error);
                }

                // Query to fetch appointments
                $sql = "SELECT * FROM appointments";
                $result = $conn->query($sql);

                // Display appointments if found
                if ($result->num_rows > 0) {
                    while($row = $result->fetch_assoc()) {
                        // Determine status text based on 'status' field
                        $status = ($row['status'] == 'Approved') ? 'Approved' : 'Pending';
                
                        echo "<tr>
                                <td>{$row['id']}</td>
                                <td>{$row['date']}</td>
                                <td>{$row['time']}</td>
                                <td>{$row['name']}</td>
                                <td>{$row['email']}</td>
                                <td>{$row['purpose']}</td>
                                <td>{$status}</td> <!-- Display 'Approved' or 'Pending' -->
                                <td>
                                    <form method='POST' action='approve_appointment.php'>
                                        <input type='hidden' name='id' value='{$row['id']}'>
                                        <button type='submit'>Approve</button>
                                    </form>
                                </td>
                              </tr>";
                    }
                } else {
                    echo "<tr><td colspan='8'>No appointments found</td></tr>";
                }

                // Close database connection
                $conn->close();
                ?>
            </tbody>
        </table>
    </div>
</div>

<script src="date.js"></script> <!-- Include JavaScript if needed -->
</body>
</html>
