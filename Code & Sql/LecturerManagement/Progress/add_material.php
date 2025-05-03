<?php
header('Content-Type: application/json');

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "learning_materials";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    echo json_encode(["success" => false, "message" => "Connection failed: " . $conn->connect_error]);
    exit;
}

$input = json_decode(file_get_contents('php://input'), true);
$title = $conn->real_escape_string($input['title']);
$link = $conn->real_escape_string($input['link']);
$quiz_id = isset($input['quiz_id']) && $input['quiz_id'] ? (int)$input['quiz_id'] : null;

if (empty($title) || empty($link)) {
    echo json_encode(["success" => false, "message" => "Missing title or link."]);
    exit;
}

$sql = "INSERT INTO learning_materials (title, link, quiz_id) VALUES ('$title', '$link', " . ($quiz_id ? "'$quiz_id'" : "NULL") . ")";

if ($conn->query($sql) === TRUE) {
    echo json_encode(["success" => true, "message" => "Material added successfully."]);
} else {
    echo json_encode(["success" => false, "message" => "Error: " . $conn->error]);
}

$conn->close();
?>