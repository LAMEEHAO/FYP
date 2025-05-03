<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

$host = "localhost";
$user = "root"; // your database username
$password = ""; // your database password
$dbname = "quiz_creation";

// Create connection
$conn = new mysqli($host, $user, $password, $dbname);

// Check connection
if ($conn->connect_error) {
  die(json_encode(["status" => "error", "message" => "Connection failed: " . $conn->connect_error]));
}

// Get the JSON data sent from JS
$data = json_decode(file_get_contents("php://input"), true);

if (!$data) {
  die(json_encode(["status" => "error", "message" => "No data received"]));
}

// Save the subject if it's new
$subjectName = $conn->real_escape_string($data['subject']);
$subjectCheck = $conn->query("SELECT id FROM subjects WHERE subject_name = '$subjectName'");
if (!$subjectCheck) {
    die(json_encode(["status" => "error", "message" => "Subject check failed: " . $conn->error]));
}

if ($subjectCheck->num_rows > 0) {
    $subjectRow = $subjectCheck->fetch_assoc();
    $subjectId = $subjectRow['id'];
} else {
    $insertSubject = $conn->query("INSERT INTO subjects (subject_name) VALUES ('$subjectName')");
    if (!$insertSubject) {
        die(json_encode(["status" => "error", "message" => "Insert subject failed: " . $conn->error]));
    }
    $subjectId = $conn->insert_id;
}

// Save each question
foreach ($data['questions'] as $question) {
    $questionText = $conn->real_escape_string($question['question']);
    $points = (int)$question['points'];
    $type = $conn->real_escape_string($question['type']);
    $correctAnswer = $conn->real_escape_string($question['correctAnswer']);

    $insertQuestion = $conn->query("INSERT INTO questions (subject_id, question_text, points, type, correct_answer)
                  VALUES ($subjectId, '$questionText', $points, '$type', '$correctAnswer')");

    if (!$insertQuestion) {
        die(json_encode(["status" => "error", "message" => "Insert question failed: " . $conn->error]));
    }

    $questionId = $conn->insert_id;

    if ($type === 'objective') {
        foreach ($question['answers'] as $index => $optionText) {
            $optionText = $conn->real_escape_string($optionText);
            $isCorrect = ($index == $question['correctAnswerIndex']) ? 1 : 0;

            $insertOption = $conn->query("INSERT INTO options (question_id, option_text, is_correct)
                          VALUES ($questionId, '$optionText', $isCorrect)");
            if (!$insertOption) {
                die(json_encode(["status" => "error", "message" => "Insert option failed: " . $conn->error]));
            }
        }
    }
}

echo json_encode(["status" => "success"]);
$conn->close();
?>
