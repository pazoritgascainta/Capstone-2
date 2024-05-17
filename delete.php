<?php
if (isset($_GET["id"])) {
    $id = $_GET["id"];

    $servername = "localhost";
    $username = "root";
    $password = "";
    $database = "homeowner";

    $conn = new mysqli($servername, $username, $password, $database);

    $sql = "DELETE FROM homeowners WHERE id = $id";
    $conn->query($sql);


}
header("location: homeowneradmin.php");
exit;
?>