<?php
session_start();

// Simulate a successful login
$dummy_homeowner_id = 1; // Replace with any dummy ID for testing

$_SESSION['homeowner_id'] = $dummy_homeowner_id;

header("Location: usercomplaint.php"); // Redirect to the user complaint page
exit;
?>
