<?php
header('Content-Type: application/json');
require_once '../db_connection.php';

$data = json_decode(file_get_contents('php://input'), true);
$quizTitle = $data['quizTitle'] ?? '';
$subjectId = $data['subjectId'] ?? 0;
$questions = $data['questions'] ?? [];

try {
    $pdo->beginTransaction();
    
    // Create the quiz
    $stmt = $pdo->prepare("INSERT INTO quizzes (quiz_title, subject_id) VALUES (?, ?)");
    $stmt->execute([$quizTitle, $subjectId]);
    $quizId = $pdo->lastInsertId();
    
    // Insert questions and options
    foreach ($questions as $q) {
        $stmt = $pdo->prepare("INSERT INTO questions (quiz_id, question_text, points, type, correct_answer) 
                              VALUES (?, ?, ?, 'objective', ?)");
        $correctAnswer = $q['options'][$q['correctIndex']];
        $stmt->execute([$quizId, $q['questionText'], $q['points'], $correctAnswer]);
        $questionId = $pdo->lastInsertId();
        
        // Insert options
        foreach ($q['options'] as $index => $optionText) {
            $isCorrect = ($index === $q['correctIndex']) ? 1 : 0;
            $stmt = $pdo->prepare("INSERT INTO options (question_id, option_text, is_correct) 
                                  VALUES (?, ?, ?)");
            $stmt->execute([$questionId, $optionText, $isCorrect]);
        }
    }
    
    $pdo->commit();
    echo json_encode(['success' => true, 'quizId' => $quizId]);
} catch (PDOException $e) {
    $pdo->rollBack();
    echo json_encode(['success' => false, 'message' => 'Database error: ' . $e->getMessage()]);
}
?>