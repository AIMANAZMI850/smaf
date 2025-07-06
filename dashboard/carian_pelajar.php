<?php
header('Content-Type: application/json');
session_start();
include '../db_connection/db.php';

$query = "SELECT * FROM daftar_pelajar WHERE 1=1"; // Always true condition
$params = [];
$types = "";

// Dynamic filters
if (!empty($_GET['noKad'])) {
    $query .= " AND noKad LIKE ?";
    $params[] = "%" . $_GET['noKad'] . "%";
    $types .= "s";
}

if (!empty($_GET['namaPelajar'])) {
    $query .= " AND namaPelajar = ?";
$params[] = $_GET['namaPelajar'];

    $types .= "s";
}

if (!empty($_GET['parent_ic'])) {
    $query .= " AND parent_ic = ?";
    $params[] = $_GET['parent_ic'];
    $types .= "s";
}

$stmt = $conn->prepare($query);

if (!empty($params)) {
    $stmt->bind_param($types, ...$params);
}

$stmt->execute();
$result = $stmt->get_result();

$searchResults = [];

while ($student = $result->fetch_assoc()) {
    // Fetch yuran data
    $yuranQuery = "SELECT * FROM bayaran WHERE noKad = ?";
    $yuranStmt = $conn->prepare($yuranQuery);
    $yuranStmt->bind_param("s", $student['noKad']);
    $yuranStmt->execute();
    $yuranResult = $yuranStmt->get_result();

    $yuranData = [];
    while ($row = $yuranResult->fetch_assoc()) {
        $yuranData[] = $row;
    }

    $student['bayaran'] = $yuranData;
    $searchResults[] = $student;
}

echo json_encode(!empty($searchResults) ? $searchResults : ["error" => "Tiada data dijumpai."]);
?>
