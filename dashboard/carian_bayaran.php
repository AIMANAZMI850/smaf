<?php
include '../db_connection/db.php'; // Make sure this file connects to your MySQL database

if (isset($_GET['search'])) {
    $search = $_GET['search'];
    
    // Query to search students by IC, name, or guardian name
    $query = "SELECT b.noKad, b.nama_pelajar, b.jumlahYuran, b.jumlahBayar, 
                     (b.jumlahYuran - b.jumlahBayar) AS baki, b.namaWarisPelajar 
              FROM bayaran b 
              WHERE b.noKad LIKE ? OR b.nama_pelajar LIKE ? OR b.namaWarisPelajar LIKE ?";

    $stmt = $conn->prepare($query);
    $searchTerm = "%$search%";
    $stmt->bind_param("sss", $searchTerm, $searchTerm, $searchTerm);
    $stmt->execute();
    $result = $stmt->get_result();

    $data = [];
    while ($row = $result->fetch_assoc()) {
        $data[] = [
            'noKad' => $row['noKad'],
            'namaPelajar' => $row['nama_pelajar'],
            'jumlahYuran' => $row['jumlahYuran'],
            'jumlahBayar' => $row['jumlahBayar'],
            'baki' => $row['baki'],
            'namaWarisPelajar' => $row['namaWarisPelajar']
        ];
    }

    echo json_encode($data);
}
?>
