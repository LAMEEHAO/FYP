<?php
header('Content-Type: application/json');

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "learning_materials";
$quiz_dbname = "quiz_creation";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    echo json_encode(["success" => false, "message" => "Connection failed: " . $conn->connect_error]);
    exit;
}

$sql = "SELECT lm.id, lm.title, lm.link, lm.quiz_id, q.quiz_title 
        FROM $dbname.learning_materials lm 
        LEFT JOIN $quiz_dbname.quizzes q ON lm.quiz_id = q.id";
$result = $conn->query($sql);

$materials = [];
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $materials[] = $row;
    }
}

echo json_encode(["success" => true, "materials" => $materials]);

$conn->close();
?>