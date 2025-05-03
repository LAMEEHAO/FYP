<?php
header('Content-Type: application/json');
require_once '../db_connection.php';

$data = json_decode(file_get_contents('php://input'), true);
$questionId = $data['questionId'];
$optionText = $data['optionText'];
$isCorrect = $data['isCorrect'];

$stmt = $pdo->prepare("INSERT INTO options (question_id, option_text, is_correct) VALUES (?, ?, ?)");
$stmt->execute([$questionId, $optionText, $isCorrect]);

echo json_encode(['success' => true]);
?>