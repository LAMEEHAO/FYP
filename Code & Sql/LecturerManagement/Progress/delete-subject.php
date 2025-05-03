<?php
header('Content-Type: application/json');
require_once 'db_connection.php';

$data = json_decode(file_get_contents('php://input'), true);
$subjectName = $data['subject_name'] ?? '';

if (empty($subjectName)) {
    echo json_encode(['success' => false, 'message' => 'Subject name is required']);
    exit;
}

try {
    // First get subject ID
    $stmt = $pdo->prepare("SELECT id FROM subjects WHERE subject_name = ?");
    $stmt->execute([$subjectName]);
    $subject = $stmt->fetch();
    
    if (!$subject) {
        echo json_encode(['success' => false, 'message' => 'Subject not found']);
        exit;
    }
    
    $subjectId = $subject['id'];
    
    // Begin transaction
    $pdo->beginTransaction();
    
    // Delete quizzes and related data
    $stmt = $pdo->prepare("DELETE quizzes, questions, options 
                          FROM quizzes 
                          LEFT JOIN questions ON quizzes.id = questions.quiz_id 
                          LEFT JOIN options ON questions.id = options.question_id 
                          WHERE quizzes.subject_id = ?");
    $stmt->execute([$subjectId]);
    
    // Delete subject
    $stmt = $pdo->prepare("DELETE FROM subjects WHERE id = ?");
    $stmt->execute([$subjectId]);
    
    $pdo->commit();
    echo json_encode(['success' => true]);
} catch (PDOException $e) {
    $pdo->rollBack();
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
?>