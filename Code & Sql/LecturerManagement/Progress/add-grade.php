<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Content-Type');

$host = "localhost";
$user = "root";
$password = "";
$dbname = "grade_results";

$conn = new mysqli($host, $user, $password, $dbname);

if ($conn->connect_error) {
  http_response_code(500);
  echo json_encode(["success" => false, "error" => "Database connection failed"]);
  exit;
}

$data = json_decode(file_get_contents("php://input"));

$name = $conn->real_escape_string($data->name);
$quiz_id = intval($data->quiz_id);
$grade = (float)$data->grade;

if (empty($name) || $quiz_id <= 0 || !is_numeric($grade)) {
  http_response_code(400);
  echo json_encode(["success" => false, "error" => "Invalid input"]);
  exit;
}

$sql = "INSERT INTO grade_results (student_name, quiz_id, grade) VALUES ('$name', $quiz_id, $grade)";

if ($conn->query($sql) === TRUE) {
  echo json_encode(["success" => true]);
} else {
  echo json_encode(["success" => false, "error" => $conn->error]);
}

$conn->close();
?>