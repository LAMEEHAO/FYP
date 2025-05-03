<?php
include('db.php');

$stmt = $pdo->prepare('SELECT * FROM subjects');
$stmt->execute();
$subjects = $stmt->fetchAll(PDO::FETCH_ASSOC);

echo json_encode($subjects);
?>
