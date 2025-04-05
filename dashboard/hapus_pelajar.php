<?php
include '../db_connection/db.php';

if (isset($_GET['noKad'])) {
    $noKad = $_GET['noKad'];

    // Start transaction
    mysqli_begin_transaction($conn);

    try {
        // Step 1: Delete related transactions first
        $sql1 = "DELETE FROM transaksi_bayaran WHERE bayaran_id IN (SELECT id FROM bayaran WHERE noKad = ?)";
        $stmt1 = $conn->prepare($sql1);
        $stmt1->bind_param("s", $noKad);
        $stmt1->execute();

        // Step 2: Delete payment records
        $sql2 = "DELETE FROM bayaran WHERE noKad = ?";
        $stmt2 = $conn->prepare($sql2);
        $stmt2->bind_param("s", $noKad);
        $stmt2->execute();

        // Step 3: Delete student record
        $sql3 = "DELETE FROM daftar_pelajar WHERE noKad = ?";
        $stmt3 = $conn->prepare($sql3);
        $stmt3->bind_param("s", $noKad);
        $stmt3->execute();

        // Commit transaction if all queries succeed
        mysqli_commit($conn);

        echo "<script>
            alert('Pelajar dan semua rekod bayaran berjaya dihapuskan!');
            window.location.href = 'kemaskini_pelajar.php';
        </script>";
    } catch (Exception $e) {
        // Rollback transaction if any query fails
        mysqli_rollback($conn);
        echo "<script>
            alert('Gagal menghapuskan pelajar! " . addslashes($e->getMessage()) . "');
            window.history.back();
        </script>";
    }

    // Close statements
    $stmt1->close();
    $stmt2->close();
    $stmt3->close();
    $conn->close();
} else {
    echo "<script>
        alert('ID Pelajar tidak sah!');
        window.history.back();
    </script>";
}
?>
