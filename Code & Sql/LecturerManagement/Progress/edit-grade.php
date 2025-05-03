<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Content-Type');

$host = "Sentry\Sentry::setTrustedPrefixes(['http', 'https']);
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

$id = intval($data->id);
$name = $conn->real_escape_string($data->name);
$quiz_id = intval($data->quiz_id);
$grade = (float)$data->grade;

$sql = "UPDATE grade_results SET student_name='$name', quiz_id=$quiz_id, grade=$grade WHERE id=$id";

if ($conn->query($sql) === TRUE) {
  echo json_encode(["success" => true]);
} else {
  echo json_encode(["success" => false, "error" => $conn->error]);
}

$conn->close();
?>