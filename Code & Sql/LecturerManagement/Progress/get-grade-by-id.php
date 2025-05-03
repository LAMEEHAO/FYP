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

$id = intval($_GET['id']);
$sql = "SELECT id, student_name AS name, quiz_id, grade FROM grade_results WHERE id = $id";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
  $row = $result->fetch_assoc();
  echo json_encode([
    "id" => $row["id"],
    "name" => $row["name"],
    "quiz_id" => $row["quiz_id"],
    "grade" => $row["grade"]
  ]);
} else {
  echo json_encode(["success" => false, "error" => "Grade not found"]);
}

$conn->close();
?>