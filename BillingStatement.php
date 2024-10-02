<?php
session_name('user_session');
session_start();

// Database connection
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "homeowner";

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Retrieve homeowner_id from session
if (!isset($_SESSION['homeowner_id'])) {
    die("Homeowner ID not found in the session.");
}

$homeowner_id = $_SESSION['homeowner_id'];

// Fetch billing and homeowner details
$sql_billing_records = "
    SELECT b.billing_id, b.homeowner_id, h.name AS homeowner_name, h.address, b.total_amount, b.billing_date, b.due_date, b.status, b.monthly_due
    FROM billing b
    JOIN homeowners h ON b.homeowner_id = h.id
    WHERE b.homeowner_id = $homeowner_id
";

$result = $conn->query($sql_billing_records);

if ($result->num_rows > 0) {
    $billing_data = $result->fetch_assoc();
} else {
    die("No billing records found.");
}

$conn->close();
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Billing Statement</title>
    <link rel="stylesheet" href="BillingStatement.css" type="text/css" media="all" />
</head>

<body>
    <div>
        <div class="py-4">
            <div class="px-14 py-6">
                <table class="w-full border-collapse border-spacing-0">
                    <tbody>
                        <tr>
                            <td class="w-full align-top">
                                <div>
                                    <img src="monique logo.jpg" width="248" height="360" class="h-12" />
                                </div>
                            </td>

                            <td class="align-top">
                                <div class="text-sm">
                                    <table class="border-collapse border-spacing-0">
                                        <tbody>
                                            <tr>
                                                <td class="border-r pr-4">
                                                    <div>
                                                        <p class="whitespace-nowrap text-slate-400 text-right">Date</p>
                                                        <p class="whitespace-nowrap font-bold text-main text-right" id="currentDate"></p>
                                                    </div>
                                                </td>
                                        
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <div class="bg-slate-100 px-14 py-6 text-sm">
                <table class="w-full border-collapse border-spacing-0">
                    <tbody>
                        <tr>
                            <td class="w-1/2 align-top">
                                <div class="text-sm text-neutral-600">
                                    <p class="font-bold"><?php echo htmlspecialchars($billing_data['address']); ?></p>
                                    <p>Binangonan, Rizal</p>
                                    <p>Philippines</p>
                                </div>
                            </td>
                            <td class="w-1/2 align-top text-right">
                                <div class="text-sm text-neutral-600">
                                    <p class="font-bold"><?php echo htmlspecialchars($billing_data['homeowner_name']); ?></p>                                  
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <div class="px-14 py-10 text-sm text-neutral-700">
                <table class="w-full border-collapse border-spacing-0">
                    <thead>
                    <thead>
    <tr>
        <td class="border-b-2 border-main pb-3 pl-3 font-bold text-main">Details</td>
        <td class="border-b-2 border-main pb-3 pl-2 text-right font-bold text-main">Billing Date</td>
        <td class="border-b-2 border-main pb-3 pl-2 text-right font-bold text-main">Due Date</td>
        <td class="border-b-2 border-main pb-3 pl-2 text-right font-bold text-main">Price</td>
        <td class="border-b-2 border-main pb-3 pl-2 text-center font-bold text-main"># of Months</td>
        <td class="border-b-2 border-main pb-3 pl-2 text-right font-bold text-main">Subtotal</td>
    </tr>
</thead>
<tbody>
    <tr>
        <td class="border-b py-3 pl-2">Monthly Dues</td>
        <td class="border-b py-3 pl-2 text-right"><?php echo htmlspecialchars($billing_data['billing_date']); ?></td>
        <td class="border-b py-3 pl-2 text-right"><?php echo htmlspecialchars($billing_data['due_date']); ?></td>
        <td class="border-b py-3 pl-2 text-right">₱ <?php echo number_format($billing_data['monthly_due'], 2); ?></td>
        <td class="border-b py-3 pl-2 text-center">1</td>
        <td class="border-b py-3 pl-2 text-right">₱ <?php echo number_format($billing_data['total_amount'], 2); ?></td>
    </tr>
</tbody>

                        <tr>
                            <td colspan="7">
                                <table class="w-full border-collapse border-spacing-0">
                                    <tbody>
                                        <tr>
                                            <td class="w-full"></td>
                                            <td>
                                                <table class="w-full border-collapse border-spacing-0">
                                                    <tbody>
                                                        <tr>
                                                            <td class="border-b p-3">
                                                                <div class="whitespace-nowrap text-slate-400">Net total:</div>
                                                            </td>
                                                            <td class="border-b p-3 text-right">
                                                                <div class="whitespace-nowrap font-bold text-main">₱ <?php echo number_format($billing_data['total_amount'], 2); ?></div>
                                                            </td>
                                                        </tr>
                                                        <tr>
                                                            <td class="bg-main p-3">
                                                                <div class="whitespace-nowrap font-bold text-white">Total:</div>
                                                            </td>
                                                            <td class="bg-main p-3 text-right">
                                                                <div class="whitespace-nowrap font-bold text-white">₱ <?php echo number_format($billing_data['total_amount'], 2); ?></div>
                                                            </td>
                                                        </tr>
                                                    </tbody>
                                                </table>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <div class="px-14 py-10 text-sm text-neutral-700">
                <p class="text-main font-bold">Notes</p>
                <p class="italic">DISREGARD THIS BILLING IF PAYMENT HAS BEEN MADE</p>
            </div>

            <footer class="fixed bottom-0 left-0 bg-slate-100 w-full text-neutral-600 text-center text-xs py-3">
                St Monique Valais Homeowners Association Inc
                <span class="text-slate-300 px-2">|</span>
                StMonique@gmail.com
                <span class="text-slate-300 px-2">|</span>
                Tel No 806-7587
            </footer>
        </div>
    </div>

    <!-- JavaScript for Real-time Date, Random Invoice Number, and Fetch API -->
    <script>
        // Set Current Date
        document.getElementById('currentDate').textContent = new Date().toLocaleDateString();

        // Generate Random Payment Reference Numbers
        function generateRandomNumber(prefix, length) {
            let randomNum = Math.floor(Math.random() * Math.pow(10, length));
            return prefix + randomNum.toString().padStart(length, '0');
        }

        document.getElementById('paymentReference').textContent = generateRandomNumber('GCH-', 5);
    </script>
</body>

</html>
