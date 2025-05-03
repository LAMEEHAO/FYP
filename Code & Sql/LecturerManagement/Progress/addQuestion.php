<?php
include('db.php');

if (isset($_POST['subject_id'], $_POST['question'], $_POST['points'], $_POST['type'])) {
    $subject_id = $_POST['subject_id'];
    $question = $_POST['question'];
    $points = $_POST['points'];
    $type = $_POST['type'];
    $answers = isset($_POST['answers']) ? json_encode($_POST['answers']) : null;
    $correct_answer_index = isset($_POST['correct_answer_index']) ? $_POST['correct_answer_index'] : null;
    $correct_answer = isset($_POST['correct_answer']) ? $_POST['correct_answer'] : null;

    $stmt = $pdo->prepare('INSERT INTO quizzes (subject_id, question, points, type, answers, correct_answer_index, correct_answer) VALUES (?, ?, ?, ?, ?, ?, ?)');
    $stmt->execute([$subject_id, $question, $points, $type, $answers, $correct_answer_index, $correct_answer]);

    echo json_encode(['status' => 'success', 'message' => 'Question added successfully!']);
}
?>
