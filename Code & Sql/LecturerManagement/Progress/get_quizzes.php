<?php
header('Content-Type: application/json');

$host = "localhost";
$user = "root";
$password = "";
$dbname = "quiz_creation";

$conn = new mysqli($host, $user, $password, $dbname);

if ($conn->connect_error) {
  die(json_encode(["error" => "Connection failed: " . $conn->connect_error]));
}

$subjectId = (int)$_GET['subjectId'];
$quizzes = [];

// Get quizzes for this subject
$quizQuery = $conn->query("SELECT * FROM quizzes WHERE subject_id = $subjectId");
while ($quiz = $quizQuery->fetch_assoc()) {
  $quiz['questions'] = [];
  
  // Get questions for this quiz
  $questionQuery = $conn->query("SELECT * FROM questions WHERE quiz_id = {$quiz['id']}");
  while ($question = $questionQuery->fetch_assoc()) {
    $question['options'] = [];
    
    // Get options for this question
    $optionQuery = $conn->query("SELECT * FROM options WHERE question_id = {$question['id']}");
    while ($option = $optionQuery->fetch_assoc()) {
      $question['options'][] = $option;
    }
    
    $quiz['questions'][] = $question;
  }
  
  $quizzes[] = $quiz;
}

echo json_encode($quizzes);
$conn->close();
?>