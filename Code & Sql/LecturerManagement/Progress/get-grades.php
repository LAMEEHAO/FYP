<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET');
header('Access-Control-Allow-Headers: Content-Type');

$host = "localhost";
$user = "root";
$password = "";
$dbname = "grade_results";

$conn = new mysqli($host, $user, $password, $dbname);

if ($conn->connect_error) {
  http_response_code(500);
  echo json_encode(["success" => false, "error" => "Connection failed"]);
  exit;
}

if (isset($_GET['action']) && $_GET['action'] === 'quizzes') {
  $quiz_conn = new mysqli($host, $user, $password, "quiz_creation");
  if ($quiz_conn->connect_error) {
    http_response_code(500);
    echo json_encode(["success" => false, "error" => "Quiz database connection failed"]);
    exit;
  }
  $sql = "SELECT id, quiz_title FROM quizzes";
  $result = $quiz_conn->query($sql);
  $quizzes = [];
  while ($row = $result->fetch_assoc()) {
    $quizzes[] = ["id" => $row["id"], "quiz_title" => $row["quiz_title"]];
  }
  echo json_encode($quizzes);
  $quiz_conn->close();
} else {
  $sql = "SELECT gr.id, gr.student_name AS name, gr.grade, q.quiz_title
          FROM grade_results gr
          LEFT JOIN quiz_creation.quizzes q ON gr.quiz_id = q.id";
  $result = $conn->query($sql);
  $grades = [];
  while ($row = $result->fetch_assoc()) {
    $grades[] = [
      "id" => $row["id"],
      "name" => $row["name"],
      "quiz_title" => $row["quiz_title"],
      "grade" => $row["grade"]
    ];
  }
  echo json_encode($grades);
}

$conn->close();
?>