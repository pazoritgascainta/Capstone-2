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
<meta name="viewport" content="width=device-width, initial-scale=1.0">

    <meta charset="UTF-8">
    <title>User - Submit Complaint</title>
    <link rel="stylesheet" href="usersidebar.css">
    <link rel="stylesheet" href="usercomplaint.css">
</head>
<body>
    <?php include 'usersidebar.php'; ?>
<div class="main-content">
<div>

            <div class="container">
        <h1>Submit a Complaint</h1>
        <form action="submit_complaint.php" method="POST">
            <label for="subject">Subject:</label><br>
            <input type="text" id="subject" name="subject" required><br><br>
            
            <label for="description">Description:</label><br>
            <textarea id="description" name="description" required></textarea><br><br>

            <input type="hidden" name="homeowner_id" value="<?php echo $_SESSION['homeowner_id']; ?>"> 
            
            <button type="submit">Submit</button>
        </form>
        
        <?php
        if (isset($_GET['success']) && $_GET['success'] == 'true') {
            echo "<p>Complaint submitted successfully!</p>";
        }
        ?>
        
        <a href="view_complaints.php">View Your Complaints</a>
    </div>   </div>   </div>
</body>
</html>
