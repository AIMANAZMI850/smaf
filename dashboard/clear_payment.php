<?php
include '../db_connection/db.php';

if (isset($_POST['id'])) {
    $studentId = $_POST['id'];

    // Reset all payments for the student
    $query = "UPDATE bayaran SET 
        jum_bayar_dana_pibg = 0, 
        jum_bayar_tuisyen = 0, 
        jum_bayar_massak = 0, 
        jum_bayar_majalah = 0, 
        jum_bayar_hac = 0, 
        jum_bayar_kertas_peperiksaan = 0, 
        jum_bayar_bas = 0, 
        jum_bayar_dobi = 0 
        WHERE id = '$studentId'";

    if (mysqli_query($conn, $query)) {
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false]);
    }
} else {
    echo json_encode(['success' => false]);
}
?>
