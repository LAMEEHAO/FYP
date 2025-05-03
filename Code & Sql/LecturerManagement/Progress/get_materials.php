<?php
// Database connection settings
$host = "localhost"; // or your host
$username = "your_db_username";
$password = "your_db_password";
$dbname = "learning_materials";

// Create connection
$conn = new mysqli($host, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Query to fetch materials
$result = $conn->query("SELECT * FROM learning_materials");

// Prepare the result as an associative array
$materials = [];
while ($row = $result->fetch_assoc()) {
    $materials[] = $row;
}

// Return as JSON
echo json_encode($materials);

// Close connection
$conn->close();
?>
