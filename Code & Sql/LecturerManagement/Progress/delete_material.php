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
$titles = $input['titles'];

if (empty($titles) || !is_array($titles)) {
    echo json_encode(["success" => false, "message" => "Invalid input."]);
    exit;
}

$placeholders = implode(',', array_fill(0, count($titles), '?'));
$types = str_repeat('s', count($titles));
$stmt = $conn->prepare("DELETE FROM learning_materials WHERE title IN ($placeholders)");
$stmt->bind_param($types, ...$titles);

if ($stmt->execute()) {
    echo json_encode(["success" => true, "message" => "Materials deleted successfully."]);
} else {
    echo json_encode(["success" => false, "message" => "Error deleting materials: " . $stmt->error]);
}

$stmt->close();
$conn->close();
?>