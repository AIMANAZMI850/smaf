<?php
include '../db_connection/db.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Get form data
    $noKad = $_POST['noKad'];
    $namaPelajar = $_POST['namaPelajar'];
    $alamat = $_POST['alamat'];
    $namaWarisPelajar = $_POST['namaWarisPelajar'];
    $tingkatan = $_POST['tingkatan'];
    $selectTingkatan = $_POST['selectTingkatan'];
    $kategori = $_POST['kategori'];
    
    // Remove commas from jumlahYuran if formatted (e.g., "1,200.00")
    $jumlahYuran = str_replace(',', '', $_POST['jumlahYuran']);

    // SQL query with placeholders
    $sql = "UPDATE daftar_pelajar SET 
                namaPelajar = ?, 
                alamat = ?, 
                namaWarisPelajar = ?, 
                tingkatan = ?, 
                selectTingkatan = ?, 
                kategori = ?, 
                jumlahYuran = ?
            WHERE noKad = ?";

    // Prepare statement
    $stmt = $conn->prepare($sql);

    // Bind parameters (s = string, i = integer, d = double)
    $stmt->bind_param("ssssssds", 
        $namaPelajar, 
        $alamat, 
        $namaWarisPelajar, 
        $tingkatan, 
        $selectTingkatan, 
        $kategori, 
        $jumlahYuran, 
        $noKad
    );

    // Execute the statement
    if ($stmt->execute()) {
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
