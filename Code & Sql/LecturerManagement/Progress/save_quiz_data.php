<?php
// Database connection
$servername = "localhost";
$username = "root"; // Your MySQL username
$password = ""; // Your MySQL password
$dbname = "quiz_creation"; // Your MySQL database name

$conn = new mysqli($servername, $username, $password, $dbname);

// Check if connection was successful
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Get JSON data from the POST request
$inputData = json_decode(file_get_contents('php://input'), true);

// Extract quiz data from the input
$gameMode = $inputData['gameMode'];
$roomType = $inputData['roomType'];
$playerMode = $inputData['playerMode'];
$subject = $inputData['subject'];
$quizData = $inputData['quizData'];

// Insert the quiz settings (game mode, room type, player mode)
$sql = "INSERT INTO quizzes (subject, game_mode, room_type, player_mode) 
        VALUES ('$subject', '$gameMode', '$roomType', '$playerMode')";

if ($conn->query($sql) === TRUE) {
    $quizId = $conn->insert_id; // Get the inserted quiz ID

    // Insert each question in the quiz
    foreach ($quizData as $questionData) {
        $question = $conn->real_escape_string($questionData['question']);
        $points = $questionData['points'];
        $type = $questionData['type'];
        $answers = implode(',', $questionData['answers']);
        $correctAnswerIndex = $questionData['correctAnswerIndex'];
        $correctAnswer = $conn->real_escape_string($questionData['correctAnswer']);

        $sql = "INSERT INTO quiz_questions (quiz_id, question_text, points, type, answers, correct_answer_index, correct_answer) 
                VALUES ('$quizId', '$question', '$points', '$type', '$answers', '$correctAnswerIndex', '$correctAnswer')";

        if (!$conn->query($sql)) {
            echo json_encode(['success' => false, 'message' => 'Error inserting question: ' . $conn->error]);
            $conn->close();
            exit();
        }
    }

    // Respond with success message
    echo json_encode(['success' => true]);
} else {
    echo json_encode(['success' => false, 'message' => 'Error inserting quiz: ' . $conn->error]);
}

$conn->close();
?>
