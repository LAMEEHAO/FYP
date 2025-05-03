<?php
header('Content-Type: application/json');

// Get the raw POST data
$data = json_decode(file_get_contents('php://input'), true);

if (!$data) {
    echo json_encode(['success' => false, 'message' => 'Invalid data']);
    exit;
}

// Connect to database
$conn = new mysqli("localhost", "root", "", "quiz_creation");

// Check connection
if ($conn->connect_error) {
    echo json_encode(['success' => false, 'message' => 'Database connection failed']);
    exit;
}

// Prepare and bind
$stmt = $conn->prepare("UPDATE quiz_modes SET 
                        room_type = ?, 
                        player_mode = ?, 
                        difficulty_level = ?, 
                        quiz_description = ? 
                        WHERE id = ?");

if (!$stmt) {
    echo json_encode(['success' => false, 'message' => 'Prepare failed: ' . $conn->error]);
    $conn->close();
    exit;
}

$stmt->bind_param("ssssi", 
    $data['room_type'],
    $data['player_mode'],
    $data['difficulty_level'],
    $data['quiz_description'],
    $data['id']
);

// Update quiz in quiz_modes table
$success = $stmt->execute();

// Also update quiz title in quizzes table if it exists
if ($success) {
    $stmt2 = $conn->prepare("UPDATE quizzes SET quiz_title = ? WHERE id = (SELECT quiz_id FROM quiz_modes WHERE id = ?)");
    if ($stmt2) {
        $stmt2->bind_param("si", $data['quiz_title'], $data['id']);
        $stmt2->execute();
        $stmt2->close();
    }
}

$stmt->close();
$conn->close();

echo json_encode(['success' => $success, 'message' => $success ? 'Quiz updated successfully' : 'Failed to update quiz']);
?>