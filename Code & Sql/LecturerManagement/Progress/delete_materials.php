<?php
// delete_materials.php
include('config.php'); // Include your DB connection

$data = json_decode(file_get_contents("php://input"), true);
$ids = $data['ids'];

$idsToDelete = implode(',', $ids);
$query = "DELETE FROM learning_materials WHERE id IN ($idsToDelete)";
$result = mysqli_query($conn, $query);

if ($result) {
    echo json_encode(['success' => true]);
} else {
    echo json_encode(['success' => false]);
}
?>
