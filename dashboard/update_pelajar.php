<?php
include '../db_connection/db.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $noKad = $_POST['noKad'];
    $namaPelajar = $_POST['namaPelajar'];
    $alamat = $_POST['alamat'];
    $namaWarisPelajar = $_POST['namaWarisPelajar'];
    $tingkatan = intval($_POST['tingkatan']);
    $selectTingkatan = $_POST['selectTingkatan'];
    $kategori = strtolower($_POST['kategori']);
    $dibayar_oleh_noKad = $_POST['dibayar_oleh_noKad'] ?? null;

    // Default yuran
    $fee = 0;
    if ($kategori === 'asrama') {
        $fee = $tingkatan >= 3 ? 530.00 : 520.00;
    } else {
        $fee = $tingkatan >= 3 ? 110.00 : 100.00;
    }

    $jumlahBayarSibling = 0;

    if (!empty($dibayar_oleh_noKad)) {
        $stmtCheck = $conn->prepare("SELECT jum_bayar_dana_pibg FROM bayaran WHERE noKad = ? AND jum_bayar_dana_pibg >= 30");
        $stmtCheck->bind_param("s", $dibayar_oleh_noKad);
        $stmtCheck->execute();
        $res = $stmtCheck->get_result();
        if ($res->num_rows > 0) {
            $fee -= 30.00;
            $jumlahBayarSibling = 30.00;
        }
        $stmtCheck->close();
    }

    $jumlahYuran = $fee;

    // Update daftar_pelajar
    $sql = "UPDATE daftar_pelajar SET 
                namaPelajar = ?, 
                alamat = ?, 
                namaWarisPelajar = ?, 
                tingkatan = ?, 
                selectTingkatan = ?, 
                kategori = ?, 
                jumlahYuran = ?,
                dibayar_oleh_noKad = ?
            WHERE noKad = ?";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sssissdss", 
        $namaPelajar, 
        $alamat, 
        $namaWarisPelajar, 
        $tingkatan, 
        $selectTingkatan, 
        $kategori, 
        $jumlahYuran, 
        $dibayar_oleh_noKad,
        $noKad
    );

    if ($stmt->execute()) {
        // Update or insert bayaran record
        $checkBayaran = $conn->prepare("SELECT * FROM bayaran WHERE noKad = ?");
        $checkBayaran->bind_param("s", $noKad);
        $checkBayaran->execute();
        $resultBayaran = $checkBayaran->get_result();

        if ($resultBayaran->num_rows > 0) {
            $bayaranData = $resultBayaran->fetch_assoc();
            $existingBayar = floatval($bayaranData['jumlahBayar']);
            $newBayar = max($existingBayar, $jumlahBayarSibling); // Ensure tidak kurang dari sibling amount
            $baki = $jumlahYuran - $newBayar;

          $updateBayaran = $conn->prepare("UPDATE bayaran SET jumlahYuran = ?, jumlahBayar = ?, baki = ?, jum_bayar_dana_pibg = ?, dibayar_oleh_noKad = ? WHERE noKad = ?");
$updateBayaran->bind_param("ddddss", $jumlahYuran, $newBayar, $baki, $jumlahBayarSibling, $dibayar_oleh_noKad, $noKad);

            $updateBayaran->execute();
            $updateBayaran->close();
        } else {
            $baki = $jumlahYuran - $jumlahBayarSibling;
            $insertBayaran = $conn->prepare("INSERT INTO bayaran (noKad, jumlahYuran, jumlahBayar, baki, jum_bayar_dana_pibg, dibayar_oleh_noKad) VALUES (?, ?, ?, ?, ?, ?)");
$insertBayaran->bind_param("sdddds", $noKad, $jumlahYuran, $jumlahBayarSibling, $baki, $jumlahBayarSibling, $dibayar_oleh_noKad);

            $insertBayaran->execute();
            $insertBayaran->close();
        }

        $checkBayaran->close();

        echo "<script>
                alert('Tunggu sebentar, data pelajar sedang dikemaskini...');
                setTimeout(function() {
                    alert('Data pelajar berjaya dikemaskini!');
                    window.location.href = 'kemaskini_pelajar.php';
                }, 1000);
              </script>";
    } else {
        echo "Ralat semasa mengemaskini data: " . $stmt->error;
    }

    $stmt->close();
}
$conn->close();
?>
