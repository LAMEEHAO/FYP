<?php
include('db.php');

if (isset($_GET['subject_id'])) {
    $subject_id = $_GET['subject_id'];

    $stmt = $pdo->prepare('SELECT * FROM quizzes WHERE subject_id = ?');
    $stmt->execute([$subject_id]);
    $quizzes = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode($quizzes);
}
?>
