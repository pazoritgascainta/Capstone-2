<?php
// Set a unique session name for users
session_name('user_session'); 
session_start();

// Check if a specific session variable exists to ensure the session is active
if (isset($_SESSION['homeowner_id'])) {
    // Clear all session variables
    session_unset();

    // Destroy the session
    session_destroy();

    // Remove the session cookie by setting its expiration time in the past
    if (ini_get("session.use_cookies")) {
        $params = session_get_cookie_params();
        setcookie(session_name(), '', time() - 42000,
            $params["path"], $params["domain"],
            $params["secure"], $params["httponly"]
        );
    }
}

// Add headers to prevent caching
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");

// Redirect to the login page with a logout message
header("Location: homepage.php?message=loggedout");
exit;
