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

// Get the action from the query string
$action = isset($_GET['action']) ? $_GET['action'] : '';

try {
    if ($action === 'list') {
        // List all subjects
        $stmt = $pdo->prepare("SELECT id, subject_name AS name FROM subjects ORDER BY subject_name");
        $stmt->execute();
        $subjects = $stmt->fetchAll();
        echo json_encode(['success' => true, 'subjects' => $subjects]);
    } elseif ($action === 'add') {
        // Add a new subject
        $input = file_get_contents('php://input');
        $data = json_decode($input, true);

        if (!$data || !isset($data['subject']) || !trim($data['subject'])) {
            error_log('Invalid or missing subject name');
            echo json_encode(['success' => false, 'message' => 'Invalid or missing subject name']);
            exit;
        }

        $subjectName = trim($data['subject']);

        // Check if subject already exists
        $stmt = $pdo->prepare("SELECT id FROM subjects WHERE subject_name = ?");
        $stmt->execute([$subjectName]);
        if ($stmt->fetchColumn()) {
            error_log("Subject already exists: $subjectName");
            echo json_encode(['success' => false, 'message' => 'Subject already exists']);
            exit;
        }

        // Insert new subject
        $stmt = $pdo->prepare("INSERT INTO subjects (subject_name) VALUES (?)");
        $stmt->execute([$subjectName]);
        $subjectId = $pdo->lastInsertId();
        error_log("Inserted subject: $subjectName, ID: $subjectId");

        echo json_encode(['success' => true, 'message' => 'Subject added successfully']);
    } elseif ($action === 'delete') {
        // Delete a subject
        $input = file_get_contents('php://input');
        $data = json_decode($input, true);

        if (!$data || !isset($data['subject']) || !trim($data['subject'])) {
            error_log('Invalid or missing subject name');
            echo json_encode(['success' => false, 'message' => 'Invalid or missing subject name']);
            exit;
        }

        $subjectName = trim($data['subject']);

        // Check if subject exists
        $stmt = $pdo->prepare("SELECT id FROM subjects WHERE subject_name = ?");
        $stmt->execute([$subjectName]);
        $subjectId = $stmt->fetchColumn();

        if (!$subjectId) {
            error_log("Subject not found: $subjectName");
            echo json_encode(['success' => false, 'message' => 'Subject not found']);
            exit;
        }

        // Delete subject (quizzes, questions, and options will be deleted via CASCADE)
        $stmt = $pdo->prepare("DELETE FROM subjects WHERE id = ?");
        $stmt->execute([$subjectId]);
        error_log("Deleted subject: $subjectName, ID: $subjectId");

        echo json_encode(['success' => true, 'message' => 'Subject deleted successfully']);
    } else {
        error_log('Invalid action: ' . $action);
        echo json_encode(['success' => false, 'message' => 'Invalid action']);
    }
} catch (PDOException $e) {
    error_log('PDO Error: ' . $e->getMessage());
    echo json_encode(['success' => false, 'message' => 'Database error: ' . $e->getMessage()]);
} catch (Exception $e) {
    error_log('General Error: ' . $e->getMessage());
    echo json_encode(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
}
?>