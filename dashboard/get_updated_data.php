<?php
include '../db_connection/db.php'; // Include your database connection

if (isset($_GET['id'])) {
    $id = $_GET['id'];

    // Fetch updated jumlahBayar and baki from the database
    $query = "SELECT jumlahBayar, baki FROM bayaran WHERE id = ?";
    $stmt = mysqli_prepare($conn, $query);
    if ($stmt) {
        mysqli_stmt_bind_param($stmt, "i", $id);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        $row = mysqli_fetch_assoc($result);

        if ($row) {
            // Return the updated data as JSON
            echo json_encode([
                'jumlahBayar' => (float)$row['jumlahBayar'],
                'baki' => (float)$row['baki']
            ]);
        } else {
            echo json_encode(['error' => 'Rekod tidak ditemui.']);
        }

        mysqli_stmt_close($stmt);
    } else {
        echo json_encode(['error' => 'Ralat: Persediaan query gagal.']);
    }
} else {
    echo json_encode(['error' => 'Ralat: ID tidak disediakan.']);
}

mysqli_close($conn);
?>