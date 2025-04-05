<?php
include '../db_connection/db.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $id = $_POST['id'];
    $feeType = $_POST['feeType'];
    $value = $_POST['value'];

    // Update the specific fee directly
    $query = "UPDATE bayaran SET $feeType = ? WHERE id = ?";

    $stmt = mysqli_prepare($conn, $query);

    if ($stmt) {
        mysqli_stmt_bind_param($stmt, "di", $value, $id);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);

        // Fetch the **updated** jumlahBayar and jumlahYuran
        $query = "SELECT 
                    (jum_bayar_dana_pibg + jum_bayar_tuisyen + jum_bayar_massak + 
                     jum_bayar_majalah + jum_bayar_hac + jum_bayar_kertas_peperiksaan + 
                     jum_bayar_bas + jum_bayar_dobi) AS jumlahBayar,
                    jumlahYuran 
                  FROM bayaran 
                  WHERE id = ?";
        $stmt = mysqli_prepare($conn, $query);
        mysqli_stmt_bind_param($stmt, "i", $id);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        $row = mysqli_fetch_assoc($result);

        // Use the fetched jumlahBayar directly (no manual addition)
       // $jumlahBayar = $row['jumlahBayar'];
       // $baki = $row['jumlahYuran'] - $jumlahBayar;

        // Update jumlahBayar and baki in the database
        $updateQuery = "UPDATE bayaran SET jumlahBayar = ?, baki = ? WHERE id = ?";
        $updateStmt = mysqli_prepare($conn, $updateQuery);
        mysqli_stmt_bind_param($updateStmt, "ddi", $jumlahBayar, $baki, $id);
        mysqli_stmt_execute($updateStmt);
        mysqli_stmt_close($updateStmt);

        echo "success"; // Return success message
    } else {
        echo "Ralat: Persediaan query gagal.";
    }
}
?>
