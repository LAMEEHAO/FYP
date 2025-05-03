<?php
header('Content-Type: application/json');
require_once '../db_connection.php';

$data = json_decode(file_get_contents('php://input'), true);
$subjectName = $data['subjectName'];

$stmt = $pdo->prepare("SELECT id FROM subjects WHERE subject_name = ?");
$stmt->execute([$subjectName]);
$subject = $stmt->fetch();

echo json_encode(['id' => $subject ? $subject['id'] : null]);
?>