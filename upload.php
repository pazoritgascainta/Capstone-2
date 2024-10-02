<?php
session_start(); // Start the session

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "homeowner";

$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['upload-file'])) {
    // Retrieve homeowner ID from the form
    $homeowner_id = $_POST['homeowner_id'];

    $targetDir = "uploads/"; // Make sure this directory exists
    $targetFile = $targetDir . basename($_FILES['upload-file']['name']);
    $uploadOk = 1;

    // Check if file is an image
    $check = getimagesize($_FILES['upload-file']['tmp_name']);
    if ($check === false) {
        echo "File is not an image.";
        $uploadOk = 0;
    }

    // Removed the file exists check
    // Attempt to upload the file
    if ($uploadOk === 1) {
        if (move_uploaded_file($_FILES['upload-file']['tmp_name'], $targetFile)) {
            // Get billing reference
            $billing_reference = $_POST['billing_reference'];

            // Insert payment details into the database
            $date = date('Y-m-d'); // Current date
            $sql = "INSERT INTO payments (homeowner_id, date, billing_reference, file_path) 
                    VALUES ('$homeowner_id', '$date', '$billing_reference', '$targetFile')";
            if (mysqli_query($conn, $sql)) {
                // After successful insert, redirect back with a success message
                header("Location: payment.php?success=1"); // Change 'previous_page.php' to your actual page
                exit();
            } else {
                echo "Error: " . mysqli_error($conn);
            }
        } else {
            echo "Sorry, there was an error uploading your file.";
        }
    }
}
?>
