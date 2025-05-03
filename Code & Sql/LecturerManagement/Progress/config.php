<?php
// config.php
$conn = mysqli_connect("localhost", "root", "", "your_database_name");
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}
?>
