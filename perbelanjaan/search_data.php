<?php
include '../db_connection/db.php';

$refNo = $_GET['refNo'];

$stmt = $conn->prepare("SELECT * FROM pembayaran WHERE ref_no = ?");
$stmt->bind_param("s", $refNo);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    echo json_encode($result->fetch_assoc());
} else {
    echo json_encode(["status" => "not_found"]);
}

$stmt->close();
$conn->close();
?>
