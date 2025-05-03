<?php
header('Content-Type: application/json');

// Database connection settings
$host = 'localhost';
$dbname = 'quiz_creation';
$username = 'root'; // Replace with your MySQL username
$password = ''; // Replace with your MySQL password

try {
    // Create a PDO connection
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'message' => 'Database connection failed: ' . $e->getMessage()]);
    exit;
}

// Get the JSON data from the request
$input = file_get_contents('php://input');
$data = json_decode($input, true);

// Validate input data
if (!$data || !isset($data['subject']) || !trim($data['subject'])) {
    echo json_encode(['success' => false, 'message' => 'Invalid or missing subject name']);
    exit;
}

$subjectName = trim($data['subject']);

try {
    // Start a transaction
    $pdo->beginTransaction();

    // Check if subject exists
    $stmt = $pdo->prepare("SELECT id FROM subjects WHERE subject_name = ?");
    $stmt->execute([$subjectName]);
    $subjectId = $stmt->fetchColumn();

    if (!$subjectId) {
        echo json_encode(['success' => false, 'message' => 'Subject not found']);
        $pdo->rollBack();
        exit;
    }

    // Delete the subject
    $stmt = $pdo->prepare("DELETE FROM subjects WHERE id = ?");
    $stmt->execute([$subjectId]);

    // Commit the transaction
    $pdo->commit();
    echo json_encode(['success' => true, 'message' => 'Subject deleted successfully']);
} catch (PDOException $e) {
    // Roll back the transaction on error
    if ($pdo->inTransaction()) {
        $pdo->rollBack();
    }
    echo json_encode(['success' => false, 'message' => 'Database error: ' . $e->getMessage()]);
} catch (Exception $e) {
    // Roll back the transaction on error
    if ($pdo->inTransaction()) {
        $pdo->rollBack();
    }
    echo json_encode(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
}
?>