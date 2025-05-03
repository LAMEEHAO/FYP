<?php
header('Content-Type: application/json');

// Get the raw POST data
$data = json_decode(file_get_contents('php://input'), true);

if (!$data || !isset($data['id'])) {
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

// First get the quiz_id to delete related questions and options
$quiz_id = null;
$stmt = $conn->prepare("SELECT quiz_id FROM quiz_modes WHERE id = ?");
$stmt->bind_param("i", $data['id']);
$stmt->execute();
$stmt->bind_result($quiz_id);
$stmt->fetch();
$stmt->close();

// Delete from quiz_modes
$stmt = $conn->prepare("DELETE FROM quiz_modes WHERE id = ?");
$stmt->bind_param("i", $data['id']);
$success = $stmt->execute();
$stmt->close();

// If we have a quiz_id, delete related records
if ($quiz_id) {
    // Delete options first (foreign key constraint)
    $conn->query("DELETE o FROM options o JOIN questions q ON o.question_id = q.id WHERE q.quiz_id = $quiz_id");
    
    // Then delete questions
    $conn->query("DELETE FROM questions WHERE quiz_id = $quiz_id");
    
    // Finally delete the quiz
    $conn->query("DELETE FROM quizzes WHERE id = $quiz_id");
}

$conn->close();

echo json_encode(['success' => $success, 'message' => $success ? 'Quiz deleted successfully' : 'Failed to delete quiz']);
?>