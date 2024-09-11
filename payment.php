<?php
session_name('user_session'); 
session_start();


if (!isset($_SESSION['homeowner_id'])) {
    header("Location: login.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <title>Dashboard</title>
    <link rel="stylesheet" href="">
    <style>

    </style>
</head>
<body>
    <?php include 'usersidebar.php'; ?>

    <div class="main-content">
        <div class="container">
            <h1>St. Monique User Payment</h1>

            <h1>Monthly Dues Payment statement will be shown here</h1>

            <div id="gcash-payment" class="card">
                <div class="card-header">Website Gcash Payment</div>
                <div class="card-body">
                    <p>Please follow the instructions below to make a payment using Gcash on the Website</p>
                    <ol>
                        <li>Click the Link down below that will redirect you to Gcash Gateway</li>
                        <a href="BillingStatement.php">CLICK HERE</a>
                        <li>Enter Your Number.</li>
                        <li>Enter Pin Code </li>
                        <li>Enter the amount to pay.</li>
                        <li>Confirm the payment.</li>
                        <li>Save a Screenshot or Download Reciept Copy of your Payment</li>
                        <li>Upload your Reciept Down Below on the Dropbox</li>
                        <li>Wait for the Admin to Confirm Your Payment</li>
                    </ol>
                    <p>Notes</p>
                    <p>If you encounter any issues during the payment due to connectivity issues or device compatibility (e.g., lags, slow connection, hangups, non-responsive page), please contact the St. Monique admin immediately to resolve your issues.</p>
                </div>
            </div>

            <div id="mobile-gcash-payment" class="card">
                <div class="card-header">Mobile Gcash Payment</div>
                <div class="card-body">
                    <p>Please follow the instructions below to make a payment using Gcash on Mobile</p>
                    <ol>
                        <li>Open your Gcash app.</li>
                        <li>Select 'Express Send'.</li>
                        <li>Enter #09123456789 </li>
                        <li>Enter the amount to pay.</li>
                        <li>Confirm the payment.</li>
                        <li>Save a Screenshot or Download Reciept Copy of your Payment</li>
                        <li>Upload your Reciept Down Below on the Dropbox</li>
                        <li>Wait for the Admin to Confirm Your Payment</li>
                    </ol>
                </div>
            </div>

            <!-- Upload Proof of Payment Section -->
            <div id="upload-proof" class="card">
                <div class="card-header">Upload Proof of Payment</div>
                <div class="card-body">
                    <form action="/upload-proof" method="post" enctype="multipart/form-data">
                        <div class="form-group">
                            <label for="proof">Upload your proof of payment:</label>
                            <input type="file" class="form-control-file" id="proof" name="proof">
                        </div>
                        <button type="submit" class="btn btn-primary">Submit</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.3.1.slim.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js"></script>
 
</body>
</html>
