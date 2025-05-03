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

// Get the posted data
$title = $_POST['title'];
$link = $_POST['link'];

// Prepare the SQL statement
$stmt = $conn->prepare("INSERT INTO learning_materials (title, link) VALUES (?, ?)");
$stmt->bind_param("ss", $title, $link);

// Execute the statement
if ($stmt->execute()) {
    echo json_encode(["success" => true, "message" => "Material added successfully."]);
} else {
    echo json_encode(["success" => false, "message" => "Error: " . $stmt->error]);
}

// Close the connection
$stmt->close();
$conn->close();
?>
