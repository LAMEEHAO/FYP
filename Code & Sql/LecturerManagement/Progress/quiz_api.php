<?php
header('Content-Type: application/json');
error_reporting(E_ALL);
ini_set('display_errors', 0);
ini_set('log_errors', 1);

// Database connection
$servername = "127.0.0.1";
$username = "root"; // Replace with your username
$password = "";     // Replace with your password
$dbname = "quiz_creation";

try {
    $conn = new mysqli($servername, $username, $password, $dbname);
    
    if ($conn->connect_error) {
        throw new Exception("Connection failed: " . $conn->connect_error);
    }

    // Get the request method and input data
    $method = $_SERVER['REQUEST_METHOD'];
    $input = json_decode(file_get_contents('php://input'), true) ?: $_POST;

    // Handle different actions
    switch ($method) {
        case 'GET':
            // Fetch all quizzes with their modes
            $result = $conn->query("
                SELECT q.id, q.quiz_title, s.subject_name, 
                       qm.room_type, qm.player_mode, qm.difficulty_level, 
                       qm.quiz_description AS description
                FROM quizzes q
                LEFT JOIN quiz_modes qm ON q.id = qm.quiz_id
                LEFT JOIN subjects s ON q.subject_id = s.id
                GROUP BY q.id
            ");
            
            $quizzes = [];
            while ($row = $result->fetch_assoc()) {
                $quizzes[] = [
                    'id' => $row['id'],
                    'title' => $row['quiz_title'],
                    'subject' => $row['subject_name'],
                    'roomType' => $row['room_type'],
                    'playerMode' => $row['player_mode'],
                    'difficulty' => $row['difficulty_level'],
                    'description' => $row['description']
                ];
            }
            echo json_encode(['success' => true, 'quizzes' => $quizzes]);
            break;

        case 'PUT':
            // Update quiz
            $quizId = intval($input['id']);
            $quizTitle = $conn->real_escape_string($input['title']);
            $roomType = $conn->real_escape_string($input['roomType']);
            $playerMode = $conn->real_escape_string($input['playerMode']);
            $difficulty = $conn->real_escape_string($input['difficulty']);
            $description = $conn->real_escape_string($input['description']);

            // Update quiz table
            $conn->query("UPDATE quizzes SET quiz_title = '$quizTitle' WHERE id = $quizId");
            
            // Update quiz_modes table
            $conn->query("
                UPDATE quiz_modes 
                SET room_type = '$roomType',
                    player_mode = '$playerMode',
                    difficulty_level = '$difficulty',
                    quiz_description = '$description'
                WHERE quiz_id = $quizId
            ");
            
            echo json_encode(['success' => true]);
            break;

        case 'DELETE':
            // Delete quiz
            $quizId = intval($input['id']);
            
            // First delete from quiz_modes (due to foreign key constraint)
            $conn->query("DELETE FROM quiz_modes WHERE quiz_id = $quizId");
            
            // Then delete from quizzes
            $conn->query("DELETE FROM quizzes WHERE id = $quizId");
            
            echo json_encode(['success' => true]);
            break;

        default:
            throw new Exception("Invalid request method");
    }
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
} finally {
    if (isset($conn)) {
        $conn->close();
    }
}
?>