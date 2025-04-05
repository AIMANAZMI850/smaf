<?php
include '../db_connection/db.php'; // Include your database connection

header('Content-Type: application/json'); // Set the content type to JSON

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Decode the JSON data
    $data = json_decode(file_get_contents("php://input"), true);

    // Check if reset flag is set
    if (isset($data['reset']) && $data['reset'] === true) {
        // Get the student ID
        $studentId = $data['id'];

        // Start transaction to ensure atomicity
        mysqli_begin_transaction($conn);

        try {
            // Delete all transactions related to this student
            $deleteQuery = "DELETE FROM transaksi_bayaran WHERE bayaran_id = ?";
            $deleteStmt = mysqli_prepare($conn, $deleteQuery);
            if ($deleteStmt) {
                mysqli_stmt_bind_param($deleteStmt, "i", $studentId);
                mysqli_stmt_execute($deleteStmt);
                mysqli_stmt_close($deleteStmt);
            } else {
                throw new Exception("Gagal memadam transaksi: " . mysqli_error($conn));
            }

            // Reset all fee-related fields in the bayaran table
            $query = "UPDATE bayaran SET 
                      jum_bayar_dana_pibg = 0, baki_dana_pibg = 0,
                      jum_bayar_tuisyen = 0, baki_tuisyen = 0,
                      jum_bayar_massak = 0, baki_massak = 0,
                      jum_bayar_majalah = 0, baki_majalah = 0,
                      jum_bayar_hac = 0, baki_hac = 0,
                      jum_bayar_kertas_peperiksaan = 0, baki_kertas_peperiksaan = 0,
                      jum_bayar_bas = 0, baki_bas = 0,
                      jum_bayar_dobi = 0, baki_dobi = 0,
                      jumlahBayar = 0, 
                      caraBayaran = NULL,
                      payment_time = NULL,
                      status_bayaran = NULL,
                      baki = jumlahYuran 
                      WHERE id = ?";

            $stmt = mysqli_prepare($conn, $query);
            if ($stmt) {
                mysqli_stmt_bind_param($stmt, "i", $studentId);
                mysqli_stmt_execute($stmt);
                mysqli_stmt_close($stmt);
            } else {
                throw new Exception("Gagal reset yuran: " . mysqli_error($conn));
            }

            // Commit transaction if everything is successful
            mysqli_commit($conn);

            // Return a success message as JSON
            echo json_encode(['status' => 'success', 'message' => 'Yuran dan transaksi telah berjaya direset.']);
        } catch (Exception $e) {
            // Rollback changes if an error occurs
            mysqli_rollback($conn);
            echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
        }
    } else {
        // Return error message as JSON
        echo json_encode(['status' => 'error', 'message' => 'Ralat: Permintaan tidak sah.']);
    }
} else {
    // Return error message as JSON
    echo json_encode(['status' => 'error', 'message' => 'Ralat: Permintaan tidak sah.']);
}

mysqli_close($conn); // Close the database connection
?>
