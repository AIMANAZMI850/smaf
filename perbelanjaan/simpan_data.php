<?php
include '../db_connection/db.php';

// Retrieve data
$tarikh = $_POST['tarikh'];
$bayarKepada = $_POST['bayarKepada'];
$transferDari = $_POST['transferDari'];
$catatan = $_POST['catatan'];
$jumlah = $_POST['jumlah'];
$caraBayaran = $_POST['caraBayaran'];

$year = date("Y"); // Get current year

// Get last refNo for this year
$query = "SELECT ref_no FROM pembayaran WHERE ref_no LIKE '$year%' ORDER BY id DESC LIMIT 1";
$result = $conn->query($query);
$row = $result->fetch_assoc();

if ($row) {
    $last_number = intval(substr($row['ref_no'], 4)) + 1; // Extract last 4 digits and increment
} else {
    $last_number = 1; // Start with 0001 if no records exist
}

// Format refNo as `20240001`
$refNo = $year . str_pad($last_number, 4, "0", STR_PAD_LEFT);

// Insert into database
$stmt = $conn->prepare("INSERT INTO pembayaran (tarikh, refNo, bayarKepada, akaunPengeluaran, catatan, jumlah, caraBayaran) VALUES (?, ?, ?, ?, ?, ?, ?)");
$stmt->bind_param("sssssss", $tarikh, $refNo, $bayarKepada, $transferDari, $catatan, $jumlah, $caraBayaran);

if ($stmt->execute()) {
    echo json_encode(["status" => "success", "refNo" => $refNo]);
} else {
    echo json_encode(["status" => "error", "message" => $conn->error]);
}

$stmt->close();
$conn->close();
?>
