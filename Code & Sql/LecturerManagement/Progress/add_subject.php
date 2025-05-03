<?php
// add_subject.php

// Database connection details
$host = 'localhost';
$db = 'quiz_creation';
$user = 'root';
$pass = '';

// Create a connection to the database
$conn = new mysqli($host, $user, $pass, $db);

// Check if the connection was successful
if ($conn->connect_error) {
  die("Connection failed: " . $conn->connect_error);
}

// Get the subject name from the request
$subjectName = isset($_POST['subject']) ? $_POST['subject'] : '';

// Ensure the subject name is not empty
if (!empty($subjectName)) {
  // Prepare an SQL query to insert the subject
  $stmt = $conn->prepare("INSERT INTO subjects (subject_name) VALUES (?)");
  $stmt->bind_param("s", $subjectName);

  // Execute the query
  if ($stmt->execute()) {
    echo "Subject added successfully!";
  } else {
    echo "Error: " . $stmt->error;
  }

  $stmt->close();
} else {
  echo "Error: Subject name cannot be empty.";
}

// Close the database connection
$conn->close();
?>
