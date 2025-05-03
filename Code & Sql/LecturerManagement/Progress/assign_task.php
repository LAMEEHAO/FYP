<?php
header('Content-Type: application/json');

// Database connection configuration
$host = '127.0.0.1';
$user = 'root';
$pass = '';
$dbname = 'assign_task';
$quiz_dbname = 'quiz_creation';

try {
    $conn = new PDO("mysql:host=$host;dbname=$dbname", $user, $pass);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    echo json_encode(['error' => 'Connection failed: ' . $e->getMessage()]);
    exit;
}

// Handle different actions
$action = isset($_GET['action']) ? $_GET['action'] : '';

if ($action === 'add_task' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);
    $quiz_id = $data['quiz_id'] ?? '';
    $student_id = $data['student_id'] ?? '';
    $description = $data['description'] ?? '';
    $type = $data['type'] ?? '';

    if (empty($quiz_id) || empty($student_id) || empty($description) || empty($type)) {
        echo json_encode(['error' => 'All fields are required']);
        exit;
    }

    try {
        $stmt = $conn->prepare("INSERT INTO tasks (quiz_id, student_id, description, type) VALUES (:quiz_id, :student_id, :description, :type)");
        $stmt->execute([
            ':quiz_id' => $quiz_id,
            ':student_id' => $student_id,
            ':description' => $description,
            ':type' => $type
        ]);
        echo json_encode(['success' => 'Task added successfully']);
    } catch (PDOException $e) {
        echo json_encode(['error' => 'Failed to add task: ' . $e->getMessage()]);
    }
} elseif ($action === 'get_tasks') {
    try {
        $stmt = $conn->query("
            SELECT t.id, t.description, t.type, t.created_at, q.quiz_title, s.name AS student_name
            FROM $dbname.tasks t
            JOIN $quiz_dbname.quizzes q ON t.quiz_id = q.id
            JOIN $dbname.students s ON t.student_id = s.id
            ORDER BY t.created_at DESC
        ");
        $tasks = $stmt->fetchAll(PDO::FETCH_ASSOC);
        echo json_encode($tasks);
    } catch (PDOException $e) {
        echo json_encode(['error' => 'Failed to fetch tasks: ' . $e->getMessage()]);
    }
} elseif ($action === 'delete_task' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);
    $id = $data['id'] ?? 0;

    try {
        $stmt = $conn->prepare("DELETE FROM tasks WHERE id = :id");
        $stmt->execute([':id' => $id]);
        echo json_encode(['success' => 'Task deleted successfully']);
    } catch (PDOException $e) {
        echo json_encode(['error' => 'Failed to delete task: ' . $e->getMessage()]);
    }
} else {
    echo json_encode(['error' => 'Invalid action']);
}
?>