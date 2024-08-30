<?php
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

// Fetch homeowner names
$sql = "SELECT id, name FROM homeowners";
$result = $conn->query($sql);
$homeowners = [];
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $homeowners[] = $row;
    }
}
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>Create Billing Record</title>
    <link rel="stylesheet" href="dashbcss.css">
    <link rel="stylesheet" href="billcss.css">

</head>
<body>
    <?php include 'sidebar.php'; ?>

    <div class="main-content">
        <div class="Container">
            <h1>Create Billing Record</h1>
            <form method="POST" action="billingadmin.php">
                <table>
                    <tr>
                        <td><label for="homeowner_id">Homeowner ID:</label></td>
                        <td><input type="number" id="homeowner_id" name="homeowner_id" oninput="fetchHomeownerName()" required></td>
                    </tr>
                    <tr>
                        <td><label for="homeowner_name">Homeowner Name:</label></td>
                        <td><input type="text" id="homeowner_name" name="homeowner_name" readonly></td>
                    </tr>
                    <tr>
                        <td><label for="total_amount">Total Amount:</label></td>
                        <td><input type="number" step="0.01" id="total_amount" name="total_amount" required></td>
                    </tr>
                    <tr>
                        <td><label for="billing_date">Billing Date:</label></td>
                        <td><input type="datetime-local" id="billing_date" name="billing_date" required></td>
                    </tr>
                    <tr>
                        <td><label for="due_date">Due Date:</label></td>
                        <td><input type="datetime-local" id="due_date" name="due_date" required></td>
                    </tr>
                    <tr>
                        <td><label for="status">Status:</label></td>
                        <td>
                            <select id="status" name="status" required>
                                <option value="Pending">Pending</option>
                                <option value="Paid">Paid</option>
                                <option value="Overdue">Overdue</option>
                            </select>
                        </td>
                    </tr>
                    <tr>
                        <td><label for="type">Type:</label></td>
                        <td>
                            <select id="type" name="type" required>
                                <option value="Monthly Dues">Monthly Dues</option>
                                <option value="Appointment">Appointment</option>
                            </select>
                        </td>
                    </tr>
                    <tr>
                        <td colspan="2">
                            <input type="submit" name="create_billing" value="Create Billing Record">
                        </td>
                    </tr>
                </table>
            </form>
        </div>
    </div>
</body>
<script>
        function fetchHomeownerName() {
            const homeownerId = document.getElementById('homeowner_id').value;
            if (homeownerId) {
                fetch(`get_homeowner.php?id=${homeownerId}`)
                    .then(response => response.json())
                    .then(data => {
                        if (data.name) {
                            document.getElementById('homeowner_name').value = data.name;
                        } else {
                            document.getElementById('homeowner_name').value = '';
                        }
                    });
            } else {
                document.getElementById('homeowner_name').value = '';
            }
        }
    </script>
</html>

