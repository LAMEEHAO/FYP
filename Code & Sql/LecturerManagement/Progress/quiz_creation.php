<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "quiz_creation";  // Updated database name

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Get the raw POST data
$rawData = file_get_contents("php://input");
$data = json_decode($rawData, true);  // Decode the JSON data

if ($data) {
    // Assuming you have the subject_id from the client-side and quiz title
    $subjectId = 1; // Example subject ID (you may pass this from the client)
    $quizTitle = "New Quiz"; // Example quiz title (you can set a title dynamically)

    // Insert quiz into the quizzes table
    $stmt = $conn->prepare("INSERT INTO quizzes (subject_id, title) VALUES (?, ?)");
    $stmt->bind_param("is", $subjectId, $quizTitle);
    $stmt->execute();
    $quizId = $conn->insert_id;  // Get the ID of the newly inserted quiz
    $stmt->close();

    // Insert each question and its options (if any)
    foreach ($data as $questionData) {
        $question = $questionData['question'];
        $points = $questionData['points'];
        $type = $questionData['type'];

        // Insert question into the questions table
        $stmt = $conn->prepare("INSERT INTO questions (quiz_id, question, points, type) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("isis", $quizId, $question, $points, $type);
        $stmt->execute();
        $questionId = $conn->insert_id;  // Get the ID of the newly inserted question
        $stmt->close();

        // Insert options if the question is of type 'objective'
        if ($type === 'objective') {
            foreach ($questionData['answers'] as $index => $option) {
                $isCorrect = ($index === $questionData['correctAnswerIndex']) ? 1 : 0;
                
                $stmt = $conn->prepare("INSERT INTO options (question_id, option_text, is_correct) VALUES (?, ?, ?)");
                $stmt->bind_param("isi", $questionId, $option, $isCorrect);
                $stmt->execute();
                $stmt->close();
            }
        } else {
            // Insert the correct answer for subjective questions
            $stmt = $conn->prepare("INSERT INTO options (question_id, option_text, is_correct) VALUES (?, ?, ?)");
            $stmt->bind_param("ssi", $questionId, $questionData['correctAnswer'], 1);
            $stmt->execute();
            $stmt->close();
        }
    }

    // Return success response
    echo json_encode(['success' => true]);
} else {
    // Return failure response if data is not valid
    echo json_encode(['success' => false]);
}

$conn->close();
?>
