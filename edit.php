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

$errorMessage = "";
$successMessage = "";

// Check if ID is provided in the URL
if (isset($_GET['id'])) {
    $id = $_GET['id'];

    // Retrieve the homeowner's data from the database
    $sql = "SELECT * FROM homeowners WHERE id = '$id'";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $name = $row['name'];
        $email = $row['email'];
        $phone = $row['phone_number'];
        $address = $row['address'];
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

    try {
        if (empty($name) || empty($email) || empty($phone) || empty($address)) {
            $errorMessage = "All fields are required!";
            throw new Exception($errorMessage);
        }

        // Check if email already exists
        $check_sql = "SELECT * FROM homeowners WHERE email = '$email' AND id != '$id'";
        $check_result = $conn->query($check_sql);
        if ($check_result->num_rows > 0) {
            throw new Exception("Email is already taken");
        }

        $sql = "UPDATE homeowners SET name = '$name', email = '$email', phone_number = '$phone', address = '$address' WHERE id = '$id'";
        if ($conn->query($sql) === TRUE) {
            $successMessage = "Homeowner updated successfully!";
        } else {
            throw new Exception("Error: " . $sql . "<br>" . $conn->error);
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
    <title>Homeowners</title>
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
