<?php
include '../db_connection/db.php';

$query = "SELECT * FROM pembayaran ORDER BY id DESC";
$result = $conn->query($query);

$data = [];

while ($row = $result->fetch_assoc()) {
    $data[] = $row;
}

echo json_encode($data);

$conn->close();
?>
