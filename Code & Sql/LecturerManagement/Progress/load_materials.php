<?php
// load_materials.php
include('config.php'); // Include your DB connection

$query = "SELECT * FROM learning_materials";
$result = mysqli_query($conn, $query);

$materials = [];
while ($row = mysqli_fetch_assoc($result)) {
    $materials[] = $row;
}

echo json_encode($materials);
?>
