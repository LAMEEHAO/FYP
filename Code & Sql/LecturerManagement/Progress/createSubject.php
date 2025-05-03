<?php
header('Content-Type: application/json');
require_once '../db_connection.php';

$data = json_decode(file_get_contents('php://input'), true);
$subjectName = $data['subjectName'];

$stmt = $pdo->prepare("INSERT INTO subjects (subject_name) VALUES (?)");
$stmt->execute([$subjectName]);
$subjectId = $pdo->lastInsertId();

echo json_encode(['id' => $subjectId]);
?>