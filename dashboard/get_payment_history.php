<?php
include '../db_connection/db.php';

$id = $_GET['id'];

$query = "SELECT feeType, amount, payment_time FROM bayaran_transaksi WHERE bayaran_id = ?";
$stmt = mysqli_prepare($conn, $query);
mysqli_stmt_bind_param($stmt, "i", $id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

$payments = [];
while ($row = mysqli_fetch_assoc($result)) {
    $payments[] = $row;
}

echo json_encode($payments);

?>
