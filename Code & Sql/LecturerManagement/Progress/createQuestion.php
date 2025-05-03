<?php
header('Content-Type: application/json');
require_once '../db_connection.php';

$data = json_decode(file_get_contents('php://input'), true);
$quizId = $data['quizId'];
$questionText = $data['questionText'];
$points = $data['points'];
$type = $data['type'];

$stmt = $pdo->prepare("INSERT INTO questions (quiz_id, question_text, points, type) VALUES (?, ?, ?, ?)");
$stmt->execute([$quizId, $questionText, $points, $type]);
$questionId = $pdo->lastInsertId();

echo json_encode(['id' => $questionId]);
?>