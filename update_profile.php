<?php
session_name('user_session'); 
header('Content-Type: application/json');

// Start the session if not already started
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
    echo json_encode(['success' => false, 'message' => 'Connection failed: ' . $conn->connect_error]);
    exit();
}

// Fetch the homeowner's ID from the session
$homeowner_id = $_SESSION['homeowner_id'] ?? null;

if (!$homeowner_id) {
    echo json_encode(['success' => false, 'message' => 'No homeowner ID found in session.']);
    exit();
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
    exit();
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
        exit();
    }

    // Handle file upload
    if (isset($_FILES['profile_image']) && $_FILES['profile_image']['error'] == UPLOAD_ERR_OK) {
        $target_dir = "uploads/";
        $file_name = basename($_FILES["profile_image"]["name"]);
        $target_file = $target_dir . preg_replace('/[^a-zA-Z0-9_\-\.]/', '', $file_name);
        $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

        // Validate file type and size
        $check = getimagesize($_FILES["profile_image"]["tmp_name"]);
        if ($check !== false && $_FILES["profile_image"]["size"] <= 500000 && in_array($imageFileType, ['jpg', 'png', 'jpeg', 'gif'])) {
            if (move_uploaded_file($_FILES["profile_image"]["tmp_name"], $target_file)) {
                $profile_image = $target_file;
            } else {
                echo json_encode(['success' => false, 'message' => 'Error uploading the file.']);
                exit();
            }
        } else {
            echo json_encode(['success' => false, 'message' => 'Invalid file type or size.']);
            exit();
        }
    }

    // Update data in the database using prepared statements
    $sql = "UPDATE homeowners SET name=?, email=?, phone_number=?, address=?, profile_image=? WHERE id=?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sssssi", $name, $email, $phone_number, $address, $profile_image, $homeowner_id);

    if ($stmt->execute()) {
        echo json_encode([
            'success' => true,
            'profile_image_url' => $profile_image,
            'name' => $name
        ]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Error updating profile: ' . $conn->error]);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request method.']);
}

$conn->close();
?>
