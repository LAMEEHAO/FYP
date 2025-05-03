<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: DELETE');
header('Access-Control-Allow-Headers: Content-Type');

// Database configuration - UPDATE THESE WITH YOUR CREDENTIALS
$servername = "127.0.0.1";
$username = "root";
$password = "";
$dbname = "assign_task";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die(json_encode([
        'success' => false,
        'message' => 'Database connection failed: ' . $conn->connect_error
    ]));
}

$taskId = $_GET['id'] ?? null;

if (!$taskId || !is_numeric($taskId)) {
    die(json_encode([
        'success' => false,
        'message' => 'Invalid task ID'
    ]));
}

// Prepare statement
$stmt = $conn->prepare("DELETE FROM tasks WHERE id = ?");
$stmt->bind_param("i", $taskId);

if ($stmt->execute()) {
    echo json_encode([
        'success' => true,
        'message' => 'Task deleted successfully'
    ]);
} else {
    echo json_encode([
        'success' => false,
        'message' => 'Error deleting task: ' . $stmt->error
    ]);
}

$stmt->close();
$conn->close();
?>