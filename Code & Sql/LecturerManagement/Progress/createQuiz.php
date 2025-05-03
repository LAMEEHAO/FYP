<?php
header('Content-Type: application/json');
require_once '../db_connection.php';

$data = json_decode(file_get_contents('php://input'), true);
$quizTitle = $data['quizTitle'];
$subjectId = $data['subjectId'];

$stmt = $pdo->prepare("INSERT INTO quizzes (quiz_title, subject_id) VALUES (?, ?)");
$stmt->execute([$quizTitle, $subjectId]);
$quizId = $pdo->lastInsertId();

echo json_encode(['id' => $quizId]);
?>