<?php
$servername = "localhost"; // Use 'localhost' or your server address
$username = "root"; // Your MySQL username
$password = ""; // Your MySQL password
$dbname = "Quiz_Creation"; // The database you created

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>
