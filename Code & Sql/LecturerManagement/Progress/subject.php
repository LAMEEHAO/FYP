<?php
header('Content-Type: application/json');
require_once '../db_connection.php';

$data = json_decode(file_get_contents('php://input'), true);
$subjectName = $data['subjectName'] ?? '';

try {
    // Check if subject exists
    $stmt = $pdo->prepare("SELECT id FROM subjects WHERE subject_name = ?");
    $stmt->execute([$subjectName]);
    $subject = $stmt->fetch();
    
    if ($subject) {
        echo json_encode(['success' => true, 'subjectId' => $subject['id']]);
    } else {
        // Create new subject
        $stmt = $pdo->prepare("INSERT INTO subjects (subject_name) VALUES (?)");
        $stmt->execute([$subjectName]);
        $subjectId = $pdo->lastInsertId();
        echo json_encode(['success' => true, 'subjectId' => $subjectId]);
    }
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'message' => 'Database error: ' . $e->getMessage()]);
}
?>