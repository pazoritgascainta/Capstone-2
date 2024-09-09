<?php
session_name('user_session'); 
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
    echo json_encode(['success' => false, 'message' => 'Database connection failed']);
    exit;
}

// Fetch the homeowner's ID from the session
$homeowner_id = $_SESSION['homeowner_id'] ?? null;

if (!$homeowner_id) {
    // Redirect to login.php if no homeowner ID is found in the session
    header("Location: login.php");
    exit;
}

// Fetch the homeowner's current information
$sql = "SELECT * FROM homeowners WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $homeowner_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $homeowner = $result->fetch_assoc();
} else {
    echo json_encode(['success' => false, 'message' => 'Homeowner not found.']);
    exit;
}

// Default profile image
$default_image = 'profile.png';
$profile_image = $homeowner['profile_image'] ? $homeowner['profile_image'] : $default_image;

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Sanitize and validate input
    $name = filter_var($_POST['name'], FILTER_SANITIZE_STRING);
    $email = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);
    $phone_number = filter_var($_POST['phone_number'], FILTER_SANITIZE_STRING);
    $address = filter_var($_POST['address'], FILTER_SANITIZE_STRING);

    // Validate email
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        echo json_encode(['success' => false, 'message' => 'Invalid email format.']);
        exit;
    }

    // Handle file upload
    if (isset($_FILES['profile_image']) && $_FILES['profile_image']['error'] == UPLOAD_ERR_OK) {
        $target_dir = "uploads/";
        $target_file = $target_dir . basename($_FILES["profile_image"]["name"]);
        $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

        $check = getimagesize($_FILES["profile_image"]["tmp_name"]);
        if ($check !== false && $_FILES["profile_image"]["size"] <= 500000 && in_array($imageFileType, ['jpg', 'png', 'jpeg', 'gif'])) {
            if (move_uploaded_file($_FILES["profile_image"]["tmp_name"], $target_file)) {
                $profile_image = $target_file;
            } else {
                echo json_encode(['success' => false, 'message' => 'Error uploading file.']);
                exit;
            }
        } else {
            echo json_encode(['success' => false, 'message' => 'Invalid file type or size.']);
            exit;
        }
    }

    // Update data in the database using prepared statements
    $sql = "UPDATE homeowners SET name=?, email=?, phone_number=?, address=?, profile_image=? WHERE id=?";
    $stmt = $conn->prepare($sql);

    if (!$stmt) {
        echo json_encode(['success' => false, 'message' => 'Prepare failed: ' . $conn->error]);
        exit;
    }

    // Bind parameters
    $stmt->bind_param("sssssi", $name, $email, $phone_number, $address, $profile_image, $homeowner_id);

    // Execute statement
    if ($stmt->execute()) {
        echo json_encode(['success' => true, 'profile_image_url' => $profile_image]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Error updating profile: ' . $stmt->error]);
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Profile</title>
    <link rel="stylesheet" href="usersidebar.css">
    <link rel="stylesheet" href="usereditcss.css">
</head>
<body>

<?php include 'usersidebar.php'; ?>
<div class="main-content">
    <h1>Edit Profile</h1>
    <div class="container">
        <form id="profileForm" action="update_profile.php" method="post" enctype="multipart/form-data">
        <div>
                <label>Profile Image:</label>   <img src="<?php echo htmlspecialchars($profile_image); ?>" alt="Profile Image" id="profile_image_display"> 
                <input type="file" id="profile_image" name="profile_image" class="editable" style="display:none;">
                <span id="profile_image_edit" class="edit-button" onclick="enableEdit('profile_image', 'profile_image_save')">edit</span>
                <span id="profile_image_save" class="save-button" onclick="disableEdit('profile_image', 'profile_image_save')">save</span>
            </div><br>

            <div>
                <label for="name">Name:</label>              
                <input type="text" id="name" name="name" class="editable" value="<?php echo htmlspecialchars($homeowner['name']); ?>" style="display:flex;">              
                <span id="name_save" class="save-button" onclick="saveChanges('name')">save</span>
            </div><br>

            <div>
                <label for="email">Email:</label>
                <input type="email" id="email" name="email" class="editable" value="<?php echo htmlspecialchars($homeowner['email']); ?>" style="display:flex;">
                <span id="email_save" class="save-button" onclick="saveChanges('email')">save</span>
            </div><br>

            <div>
                <label for="phone_number">Phone Number:</label>
                <input type="text" id="phone_number" name="phone_number" class="editable" value="<?php echo htmlspecialchars($homeowner['phone_number']); ?>" style="display:flex;">
                <span id="phone_number_save" class="save-button" onclick="saveChanges('phone_number')">save</span>
            </div><br>

            <div>
                <label for="address">Address:</label>
                <input type="text" id="address" name="address" class="editable" value="<?php echo htmlspecialchars($homeowner['address']); ?>" style="display:flex;">
                <span id="address_save" class="save-button" onclick="saveChanges('address')">save</span>
            </div><br>

            <input type="submit" value="Update Profile">
        </form>
    </div>
</div>
</div>

<script src="useredit.js"></script>
</body>
</html>

