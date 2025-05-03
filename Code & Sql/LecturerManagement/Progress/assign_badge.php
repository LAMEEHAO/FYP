<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, GET');
header('Access-Control-Allow-Headers: Content-Type');

$host = '127.0.0.1';
$db = 'badges_achievement';
$user = 'root'; // Adjust as per your database credentials
$pass = ''; // Adjust as per your database credentials
$charset = 'utf8mb4';

$dsn = "mysql:host=$host;dbname=$db;charset=$charset";
$options = [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES => false,
];

try {
    $pdo = new PDO($dsn, $user, $pass, $options);

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        // Handle badge assignment
        $data = json_decode(file_get_contents('php://input'), true);
        $student_name = $data['name'] ?? '';
        $student_score = $data['score'] ?? 0;
        $badge_title = $data['badge'] ?? '';
        $quiz_id = $data['quiz_id'] ?? null;

        if (empty($student_name) || !is_numeric($student_score) || empty($badge_title) || empty($quiz_id)) {
            http_response_code(400);
            echo json_encode(['error' => 'Invalid input']);
            exit;
        }

        $stmt = $pdo->prepare('INSERT INTO badge_assignments (student_name, student_score, badge_title, quiz_id) VALUES (?, ?, ?, ?)');
        $stmt->execute([$student_name, $student_score, $badge_title, $quiz_id]);

        http_response_code(201);
        echo json_encode(['message' => 'Badge assigned successfully']);
    } elseif ($_SERVER['REQUEST_METHOD'] === 'GET') {
        if (isset($_GET['action']) && $_GET['action'] === 'quizzes') {
            // Fetch all quizzes from quiz_creation database
            $quiz_dsn = "mysql:host=$host;dbname=quiz_creation;charset=$charset";
            $quiz_pdo = new PDO($quiz_dsn, $user, $pass, $options);
            $stmt = $quiz_pdo->query('SELECT id, quiz_title FROM quizzes');
            $quizzes = $stmt->fetchAll();

            echo json_encode($quizzes);
        } else {
            // Fetch all badges with quiz titles
            $stmt = $pdo->prepare('
                SELECT ba.id, ba.student_name AS name, ba.student_score AS score, ba.badge_title AS badge, q.quiz_title
                FROM badge_assignments ba
                LEFT JOIN quiz_creation.quizzes q ON ba.quiz_id = q.id
            ');
            $stmt->execute();
            $badges = $stmt->fetchAll();

            echo json_encode($badges);
        }
    } else {
        http_response_code(405);
        echo json_encode(['error' => 'Method not allowed']);
    }
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Database error: ' . $e->getMessage()]);
}
?>