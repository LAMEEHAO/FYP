<?php
header('Content-Type: application/json');

$host = 'localhost';
$db = 'assign_task';
$user = 'root';
$pass = '';

$conn = new mysqli($host, $user, $pass, $db);
if ($conn->connect_error) {
  die(json_encode(['error' => "Connection failed: " . $conn->connect_error]));
}

$result = $conn->query("SELECT * FROM tasks ORDER BY created_at DESC");
$tasks = [];

while ($row = $result->fetch_assoc()) {
  $tasks[] = $row;
}

echo json_encode($tasks);
$conn->close();
?>