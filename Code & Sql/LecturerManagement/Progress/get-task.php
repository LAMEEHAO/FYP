<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');

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

$sql = "SELECT id, name, description, type, created_at FROM tasks ORDER BY created_at DESC";
$result = $conn->query($sql);

$tasks = [];
if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        $tasks[] = $row;
    }
}

echo json_encode([
    'success' => true,
    'tasks' => $tasks
]);

$conn->close();
?>