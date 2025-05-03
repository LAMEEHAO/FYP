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

$result = $conn->query("SELECT id, subject_name as name FROM subjects");
$subjects = [];

if ($result->num_rows > 0) {
  while($row = $result->fetch_assoc()) {
    $subjects[] = $row;
  }
}

echo json_encode($subjects);
$conn->close();
?>