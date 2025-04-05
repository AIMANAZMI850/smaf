<?php
session_start();
include '../db_connection/db.php';

$searchResults = [];

if (!empty($_GET['noKad']) || !empty($_GET['parent_ic']) || !empty($_GET['namaPelajar'])) {
    if (!empty($_GET['noKad'])) {
        $searchField = "noKad";
        $searchValue = "%" . $_GET['noKad'] . "%"; // Allow partial match
        $query = "SELECT * FROM daftar_pelajar WHERE $searchField LIKE ?";
    } elseif (!empty($_GET['parent_ic'])) {
        // If searching by parent IC, find all students with the same parent IC
        $searchValue = $_GET['parent_ic'];
        $query = "SELECT * FROM daftar_pelajar WHERE parent_ic = ?";
    } elseif (!empty($_GET['namaPelajar'])) {
        $searchField = "namaPelajar";
        $searchValue = "%" . $_GET['namaPelajar'] . "%"; // Allow partial match
        $query = "SELECT * FROM daftar_pelajar WHERE $searchField LIKE ?";
    }

    $stmt = $conn->prepare($query);
    $stmt->bind_param("s", $searchValue);
    $stmt->execute();
    $result = $stmt->get_result();

    while ($student = $result->fetch_assoc()) {
        // Fetch fees based on student's IC number
        $yuranQuery = "SELECT * FROM bayaran WHERE noKad = ?";
        $yuranStmt = $conn->prepare($yuranQuery);
        $yuranStmt->bind_param("s", $student['noKad']);
        $yuranStmt->execute();
        $yuranResult = $yuranStmt->get_result();

        $yuranData = [];
        while ($row = $yuranResult->fetch_assoc()) {
            $yuranData[] = $row;
        }

        // Add fee data to the student details
        $student['bayaran'] = $yuranData;
        $searchResults[] = $student;
    }

    if (!empty($searchResults)) {
        echo json_encode($searchResults);
    } else {
        echo json_encode(["error" => "Tiada data dijumpai."]);
    }
} else {
    echo json_encode(["error" => "Sila masukkan data untuk carian."]);
}
?>
