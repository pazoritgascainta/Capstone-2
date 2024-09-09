<?php
session_name('admin_session'); // Set a unique session name for admins
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Database configuration
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

// Fetch the admin's ID from the session
$admin_id = $_SESSION['admin_id'] ?? null;

if (!$admin_id) {
    // Redirect to admin login page
    header('Location: admin_login.php');
    exit();
}

// Fetch the admin's current information
$sql = "SELECT * FROM admin WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $admin_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $admin = $result->fetch_assoc();
} else {
    echo "Admin not found.";
    exit();
}

// Default profile image
$default_image = 'profile.png';
$profile_image_url = $admin['profile_image'] ? $admin['profile_image'] : 'profile.png';

$conn->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Profile</title>

    <link rel="stylesheet" href="editpro.css">
</head>
<body>

<?php include 'sidebar.php'; ?>
<div class="main-content">
    <h1>Edit Profile</h1>
    <div class="container">

        <form id="profileForm" action="update_admin_profile.php" method="post" enctype="multipart/form-data">
                <div>
            <label for="profile_image_input">Profile Image:</label>
            <img src="<?php echo htmlspecialchars($admin['profile_image'] ?? 'profile.png'); ?>" alt="Profile Image" id="profile_image_displays">
            <input type="file" id="profile_image_input" name="profile_image" class="editable" style="display:none;">
            <span id="profile_image_input_edit" class="edit-button" onclick="enableEdit('profile_image_input', 'profile_image_input_save')">edit</span>
            <span id="profile_image_input_save" class="save-button" onclick="saveChanges('profile_image_input')">save</span>
              </div><br>

            <div>
                <label for="id">ID:</label>
                <span id="id" class="editable"><?php echo htmlspecialchars($admin['id']); ?></span>
            </div><br>

            <div>
            <label for="username">Username:</label>
            <input type="text" id="username" name="username" value="<?php echo htmlspecialchars($admin['username']); ?>" class="editable">
            <span id="username_save" class="save-button" onclick="saveChanges('username')">save</span>
            </div><br>

            <input type="submit" value="Update Profile">
        </form>
    </div>
</div>

<script src="editpro.js"></script>
</body>
</html>
