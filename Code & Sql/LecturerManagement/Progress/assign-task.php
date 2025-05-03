<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

$host = 'localhost';
$db = 'assign_task';
$user = 'your_db_user';
$pass = 'your_db_password';

header('Content-Type: application/json');

$conn = new mysqli($host, $user, $pass, $db);

if ($conn->connect_error) {
    echo json_encode(['success' => false, 'message' => 'Connection failed: ' . $conn->connect_error]);
    exit;
}

$input = file_get_contents('php://input');
$data = json_decode($input, true);

if (!$data || !isset($data['name']) || !isset($data['description']) || !isset($data['type'])) {
    echo json_encode(['success' => false, 'message' => 'Invalid input: ' . $input]);
    exit;
}

$name = $conn->real_escape_string($data['name']);
$description = $conn->real_escape_string($data['description']);
$type = $conn->real_escape_string($data['type']);

$sql = "INSERT INTO tasks (name, description, type) VALUES ('$name', '$description', '$type')";

if ($conn->query($sql) === TRUE) {
    echo json_encode(['success' => true, 'id' => $conn->insert_id]);
} else {
    echo json_encode(['success' => false, 'message' => 'SQL error: ' . $conn->error]);
}

$conn->close();
