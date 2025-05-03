<?php
$host = 'localhost';
$db = 'assign_task';
$user = 'root';
$pass = ''; // your MySQL password

$conn = new mysqli($host, $user, $pass, $db);

// Debug connection error
if ($conn->connect_error) {
  die(json_encode(['status' => 'error', 'message' => 'Connection failed: ' . $conn->connect_error]));
}

$name = $_POST['name'] ?? '';
$description = $_POST['description'] ?? '';
$type = $_POST['type'] ?? '';

// Debug input data
if (!$name || !$description || !$type) {
  die(json_encode(['status' => 'error', 'message' => 'Missing input']));
}

$stmt = $conn->prepare("INSERT INTO tasks (name, description, type) VALUES (?, ?, ?)");
if (!$stmt) {
  die(json_encode(['status' => 'error', 'message' => 'Prepare failed: ' . $conn->error]));
}

$stmt->bind_param("sss", $name, $description, $type);
if (!$stmt->execute()) {
  die(json_encode(['status' => 'error', 'message' => 'Execute failed: ' . $stmt->error]));
}

echo json_encode(['status' => 'success']);
$stmt->close();
$conn->close();
?>
