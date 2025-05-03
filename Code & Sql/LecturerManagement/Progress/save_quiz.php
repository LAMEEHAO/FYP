<?php
header('Content-Type: application/json');

// Database connection settings
$host = 'localhost';
$dbname = 'quiz_creation';
$username = 'root'; // Replace with your MySQL username
$password = ''; // Replace with your MySQL password

// Enable error logging
ini_set('display_errors', 0);
ini_set('log_errors', 1);
ini_set('error_log', 'php_errors.log');

try {
    // Create a PDO connection
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    error_log('Database connection error: ' . $e->getMessage());
    echo json_encode(['success' => false, 'message' => 'Database connection failed: ' . $e->getMessage()]);
    exit;
}

// Get the JSON data from the request
$input = file_get_contents('php://input');
error_log('Received input: ' . $input);
$data = json_decode($input, true);

// Validate input data
if (!$data || !isset($data['subject']) || !isset($data['quizTitle']) || !isset($data['questions']) || !is_array($data['questions'])) {
    error_log('Invalid input data');
    echo json_encode(['success' => false, 'message' => 'Invalid or missing data: subject, quizTitle, or questions']);
    exit;
}

$subjectName = trim($data['subject']);
$quizTitle = trim($data['quizTitle']);
$questions = $data['questions'];

// Validate questions and options
foreach ($questions as $index => $question) {
    if (!isset($question['question']) || !trim($question['question'])) {
        error_log('Question text missing for question ' . ($index + 1));
        echo json_encode(['success' => false, 'message' => 'Question text missing for question ' . ($index + 1)]);
        exit;
    }
    if (!isset($question['points']) || !is_numeric($question['points'])) {
        error_log('Invalid points for question ' . ($index + 1));
        echo json_encode(['success' => false, 'message' => 'Invalid points for question ' . ($index + 1)]);
        exit;
    }
    if (!isset($question['options']) || !is_array($question['options']) || count($question['options']) !== 4) {
        error_log('Invalid options for question ' . ($index + 1));
        echo json_encode(['success' => false, 'message' => 'Question ' . ($index + 1) . ' must have exactly four options']);
        exit;
    }
    if (!isset($question['correctAnswerIndex']) || !is_numeric($question['correctAnswerIndex']) || $question['correctAnswerIndex'] < 0 || $question['correctAnswerIndex'] >= 4) {
        error_log('Invalid correct answer index for question ' . ($index + 1));
        echo json_encode(['success' => false, 'message' => 'Invalid correct answer index for question ' . ($index + 1)]);
        exit;
    }
    foreach ($question['options'] as $optIndex => $option) {
        if (!trim($option)) {
            error_log('Empty option for question ' . ($index + 1));
            echo json_encode(['success' => false, 'message' => 'Option ' . ($optIndex + 1) . ' is empty for question ' . ($index + 1)]);
            exit;
        }
    }
    // Check for duplicate options
    $uniqueOptions = array_unique($question['options']);
    if (count($uniqueOptions) !== count($question['options'])) {
        error_log('Duplicate options for question ' . ($index + 1));
        echo json_encode(['success' => false, 'message' => 'Question ' . ($index + 1) . ' contains duplicate options']);
        exit;
    }
}

try {
    // Start a transaction
    $pdo->beginTransaction();

    // Step 1: Check if subject exists, if not, create it
    $stmt = $pdo->prepare("SELECT id FROM subjects WHERE subject_name = ?");
    $stmt->execute([$subjectName]);
    $subjectId = $stmt->fetchColumn();

    if (!$subjectId) {
        $stmt = $pdo->prepare("INSERT INTO subjects (subject_name) VALUES (?)");
        $stmt->execute([$subjectName]);
        $subjectId = $pdo->lastInsertId();
        error_log("Inserted subject: $subjectName, ID: $subjectId");
    }

    // Step 2: Check if quiz exists, if so, clear existing questions and options
    $stmt = $pdo->prepare("SELECT id FROM quizzes WHERE quiz_title = ? AND subject_id = ?");
    $stmt->execute([$quizTitle, $subjectId]);
    $quizId = $stmt->fetchColumn();

    if ($quizId) {
        // Clear existing questions (options will be deleted via CASCADE)
        $stmt = $pdo->prepare("DELETE FROM questions WHERE quiz_id = ?");
        $stmt->execute([$quizId]);
        error_log("Cleared existing questions for quiz ID: $quizId");
    } else {
        // Insert new quiz
        $stmt = $pdo->prepare("INSERT INTO quizzes (quiz_title, subject_id) VALUES (?, ?)");
        $stmt->execute([$quizTitle, $subjectId]);
        $quizId = $pdo->lastInsertId();
        error_log("Inserted quiz: $quizTitle, ID: $quizId");
    }

    // Step 3: Insert questions and options
    $stmtQuestion = $pdo->prepare("INSERT INTO questions (quiz_id, question_text, points, type, correct_answer) VALUES (?, ?, ?, 'objective', ?)");
    $stmtOption = $pdo->prepare("INSERT INTO options (question_id, option_text, is_correct) VALUES (?, ?, ?)");

    foreach ($questions as $index => $question) {
        $questionText = trim($question['question']);
        $points = (int)$question['points'];
        $options = array_map('trim', $question['options']);
        $correctAnswerIndex = (int)$question['correctAnswerIndex'];
        $correctAnswerText = $options[$correctAnswerIndex];

        // Insert question
        $stmtQuestion->execute([$quizId, $questionText, $points, $correctAnswerText]);
        $questionId = $pdo->lastInsertId();
        error_log("Inserted question ID: $questionId, Text: $questionText");

        // Insert options
        foreach ($options as $optIndex => $optionText) {
            $isCorrect = ($optIndex === $correctAnswerIndex) ? 1 : 0;
            $stmtOption->execute([$questionId, $optionText, $isCorrect]);
            $optionId = $pdo->lastInsertId();
            error_log("Inserted option ID: $optionId, Text: $optionText, is_correct: $isCorrect for question ID: $questionId");
        }
    }

    // Commit the transaction
    $pdo->commit();
    echo json_encode(['success' => true, 'message' => 'Quiz saved successfully']);
} catch (PDOException $e) {
    // Roll back the transaction on error
    if ($pdo->inTransaction()) {
        $pdo->rollBack();
    }
    error_log('PDO Error: ' . $e->getMessage());
    echo json_encode(['success' => false, 'message' => 'Database error: ' . $e->getMessage()]);
} catch (Exception $e) {
    // Roll back the transaction on error
    if ($pdo->inTransaction()) {
        $pdo->rollBack();
    }
    error_log('General Error: ' . $e->getMessage());
    echo json_encode(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
}
?>