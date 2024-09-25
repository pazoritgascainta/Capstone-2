<?php
$servername = "localhost";
$username = "root";
$password = "";
$database = "homeowner";

$conn = new mysqli($servername, $username, $password, $database);

$name = "";
$email = "";
$phone = "";
$address = "";
$sqm = ""; // New field for Square Meters
$password = "";

$errorMessage = "";
$successMessage = "";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = $_POST["name"];
    $email = $_POST["email"];
    $phone = $_POST["phone"];
    $address = $_POST["address"];
    $sqm = $_POST["sqm"]; // Retrieve sqm value
    $password = $_POST["password"];
    $password_hash = password_hash($password, PASSWORD_DEFAULT);

    do {
        if (empty($name) || empty($email) || empty($phone) || empty($address) || empty($sqm) || empty($password)) {
            $errorMessage = "All fields are required!";
            break;
        }

        // Check if email already exists
        $check_sql = "SELECT * FROM homeowners WHERE email = '$email'";
        $check_result = $conn->query($check_sql);
        if ($check_result->num_rows > 0) {
            $errorMessage = "Email is already taken";
            break;
        }

        // Insert homeowner data including sqm
        $sql = "INSERT INTO homeowners (name, email, phone_number, address, sqm, password, status) VALUES (?, ?, ?, ?, ?, ?, 'active')";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ssssss", $name, $email, $phone, $address, $sqm, $password_hash);

        if ($stmt->execute()) {
            $successMessage = "Homeowner added successfully!";
            
            $name = "";
            $email = "";
            $phone = "";
            $address = "";
            $sqm = ""; // Reset the sqm field
            $password = "";

            header("location: homeowneradmin.php");
            exit;
        } else {
            $errorMessage = "Error: " . $stmt->error;
        }

        $stmt->close();

    } while (false);
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create Homeowner</title>
    <link rel="stylesheet" href="createcss.css">
    <script>
    function checkEmail() {
        var email = document.getElementById("email").value;
        var emailError = document.getElementById("email-error");
        emailError.innerHTML = "";
        try {
            var xhttp = new XMLHttpRequest();
            xhttp.onreadystatechange = function() {
                if (this.readyState == 4 && this.status == 200) {
                    if (this.responseText == "taken") {
                        emailError.innerHTML = "Email is already taken";
                    } else {
                        emailError.innerHTML = "";
                    }
                }
            };
            xhttp.open("GET", "?action=check_email&email=" + email, true);
            xhttp.send();
        } catch (error) {
            emailError.innerHTML = "An error occurred while checking email availability";
        }
    }
    </script>
</head>
<body>
    <div class="container my-5">
        <h2>Create Homeowner</h2>
        <?php
        if (!empty($errorMessage)) {
            echo "<div style='color: red;'>$errorMessage</div>";
        }
        ?>
       <form method="post" onsubmit="return validateForm()">
    <div class="row">
        <label class="col-form-label">Name</label>
        <div class="col">
            <input type="text" class="form-control" name="name" value="<?php echo $name; ?>">
        </div>
    </div>
    <div class="row">
        <label class="col-form-label">Email</label>
        <div class="col">
            <input type="text" class="form-control" id="email" name="email" value="<?php echo $email; ?>" onblur="checkEmail()">
            <span id="email-error" style="color: red;"></span>
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
        <label class="col-form-label">Square Meters</label> <!-- New Square Meters field -->
        <div class="col">
            <input type="number" class="form-control" name="sqm" value="<?php echo $sqm; ?>"> <!-- Input for sqm -->
        </div>
    </div>
    <div class="row">
        <label class="col-form-label">Password</label>
        <div class="col">
            <input type="password" class="form-control" name="password" value="<?php echo $password; ?>">
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
