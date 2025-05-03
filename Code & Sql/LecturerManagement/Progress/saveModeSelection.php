<?php
// Database configuration
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "quiz_creation";

// Create connection with error handling
try {
    $conn = new mysqli($servername, $username, $password, $dbname);
    
    // Check connection
    if ($conn->connect_error) {
        throw new Exception("Connection failed: " . $conn->connect_error);
    }
    
    // Validate and sanitize POST data
    $required_fields = ['roomType', 'playerMode', 'difficultyLevel', 'quizDescription'];
    
    foreach ($required_fields as $field) {
        if (empty($_POST[$field])) {
            throw new Exception("Missing required field: " . $field);
        }
    }
    
    // Prepare and bind parameters to prevent SQL injection
    $stmt = $conn->prepare("INSERT INTO quiz_modes (room_type, player_mode, difficulty_level, quiz_description) VALUES (?, ?, ?, ?)");
    if (!$stmt) {
        throw new Exception("Prepare failed: " . $conn->error);
    }
    
    $stmt->bind_param("ssss", 
        $_POST['roomType'], 
        $_POST['playerMode'], 
        $_POST['difficultyLevel'], 
        $_POST['quizDescription']
    );
    
    // Execute the statement
    if ($stmt->execute()) {
        // Get the last inserted ID
        $last_id = $conn->insert_id;
        echo json_encode([
            'status' => 'success',
            'message' => 'Mode selection saved successfully!',
            'inserted_id' => $last_id
        ]);
    } else {
        throw new Exception("Execute failed: " . $stmt->error);
    }
    
    // Close statement and connection
    $stmt->close();
    $conn->close();
    
} catch (Exception $e) {
    // Return error response
    http_response_code(400);
    echo json_encode([
        'status' => 'error',
        'message' => $e->getMessage()
    ]);
    
    // Ensure connections are closed even if error occurs
    if (isset($stmt)) $stmt->close();
    if (isset($conn)) $conn->close();
}
?>