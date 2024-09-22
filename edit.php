<?php
$servername = "localhost";
$username = "root";
$password = "";
$database = "homeowner";

$conn = new mysqli($servername, $username, $password, $database);

$id = "";
$name = "";
$email = "";
$phone = "";
$address = "";
$sqm = ""; // Add squaremeters variable
$password = ""; // Keep track of password field

$errorMessage = "";
$successMessage = "";

// Check if ID is provided in the URL
if (isset($_GET['id'])) {
    $id = $_GET['id'];

    // Retrieve the homeowner's data from the database, including squaremeters
    $sql = "SELECT * FROM homeowners WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $name = $row['name'];
        $email = $row['email'];
        $phone = $row['phone_number'];
        $address = $row['address'];
        $sqm = $row['sqm']; // Fetch squaremeters
    } else {
        $errorMessage = "Homeowner not found.";
    }
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $id = $_POST["id"];
    $name = $_POST["name"];
    $email = $_POST["email"];
    $phone = $_POST["phone"];
    $address = $_POST["address"];
    $sqm = $_POST["sqm"]; // Fetch squaremeters from form
    $password = $_POST["password"];

    try {
        if (empty($name) || empty($email) || empty($phone) || empty($address) || empty($sqm)) {
            throw new Exception("All fields are required!");
        }

        // Check if email already exists
        $sql = "SELECT * FROM homeowners WHERE email = ? AND id != ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("si", $email, $id);
        $stmt->execute();
        $check_result = $stmt->get_result();
        if ($check_result->num_rows > 0) {
            throw new Exception("Email is already taken.");
        }

        // Check if password was provided
        if (!empty($password)) {
            // Hash the new password
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
            
            // Update with new password
            $sql = "UPDATE homeowners SET name = ?, email = ?, phone_number = ?, address = ?, sqm = ?, password = ? WHERE id = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("ssssssi", $name, $email, $phone, $address, $sqm, $hashed_password, $id);
        } else {
            // Update without changing the password
            $sql = "UPDATE homeowners SET name = ?, email = ?, phone_number = ?, address = ?, sqm = ? WHERE id = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("sssssi", $name, $email, $phone, $address, $sqm, $id);
        }

        if ($stmt->execute()) {
            $successMessage = "Homeowner updated successfully!";
        } else {
            throw new Exception("Error updating record: " . $stmt->error);
        }
    } catch (Exception $e) {
        $errorMessage = "Error: " . $e->getMessage();
    }
}

$conn->close();
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Homeowner</title>
    <link rel="stylesheet" href="createcss.css">
</head>
<body>
    <div class="container my-5">
        <h2>Edit Homeowner</h2>
        <?php
        if (!empty($errorMessage)) {
            echo "<div style='color: red;'>$errorMessage</div>";
        }

        if (!empty($successMessage)) {
            header("Location: homeowneradmin.php");
            exit;
        }
        ?>

        <form method="post">
            <input type="hidden" name="id" value="<?php echo $id; ?>">
            <div class="row">
                <label class="col-form-label">Name</label>
                <div class="col">
                    <input type="text" class="form-control" name="name" value="<?php echo $name; ?>">
                </div>
            </div>
            <div class="row">
                <label class="col-form-label">Email</label>
                <div class="col">
                    <input type="text" class="form-control" name="email" value="<?php echo $email; ?>">
                </div>
            </div>
            <div class="row">
                <label class="col-form-label">Phone</label>
                <div class="col">
                    <input type="text" class="form-control" name="phone" value="<?php echo $phone; ?>">
                </div>
            </div>
            <div class="row">
                <label class="col-form-label">Address</label>
                <div class="col">
                    <input type="text" class="form-control" name="address" value="<?php echo $address; ?>">
                </div>
            </div>
            <div class="row">
                <label class="col-form-label">Square Meters</label>
                <div class="col">
                    <input type="number" class="form-control" name="sqm" value="<?php echo $sqm; ?>">
                </div>
            </div>
            <div class="row">
                <label class="col-form-label">Password</label>
                <div class="col">
                    <input type="password" class="form-control" name="password" value="">
                </div>
            </div>
            <div class="row mt-3">
                <div class="col">
                    <button type="submit" class="btn btn-primary">Submit</button>
                    <a class="btn btn-outline-primary" href="homeowneradmin.php" role="button">Cancel</a>
                </div>
            </div>
        </form>
    </div>
</body>
</html>
