<?php
require __DIR__ . '/../vendor/autoload.php';

include '../db_connection/db.php'; // adjust this path if needed

use PhpOffice\PhpSpreadsheet\IOFactory;

$spreadsheet = IOFactory::load('SENARAI MURID 2025.xlsx');
$sheetNames = $spreadsheet->getSheetNames();
$tahunPelajar = '2025';

foreach ($sheetNames as $sheetName) {
    if (strtolower($sheetName) === 'kadar bayaran') continue;

    $sheet = $spreadsheet->getSheetByName($sheetName);
    $rows = $sheet->toArray();

    for ($i = 6; $i < count($rows); $i++) { // Skip headers
        $row = $rows[$i];

        $namaPelajar = isset($row[2]) ? trim($row[2]) : '';
        $noKad = isset($row[3]) ? trim($row[3]) : '';
        $tingkatan = isset($row[4]) ? trim($row[4]) : '';
        $kategori = isset($row[5]) ? trim($row[5]) : '';

        if ($namaPelajar === '' || $noKad === '' || $tingkatan === '') continue;

        // Simulate $_POST variables
        $_POST['tahunPelajar'] = $tahunPelajar;
        $_POST['noKad'] = $noKad;
        $_POST['namaPelajar'] = $namaPelajar;
        $_POST['alamat'] = '';
        $_POST['namaWarisPelajar'] = '';
        $_POST['tingkatan'] = $tingkatan;
        $_POST['selectTingkatan'] = $tingkatan;
        $_POST['noTel'] = '';
        $_POST['kategori'] = $kategori;
        $_POST['jumlahYuran'] = '';
        $_POST['catatan'] = '';

        // Use your original code logic:
        $tahunPelajar = $_POST['tahunPelajar'];
        $noKad = $_POST['noKad'];
        $namaPelajar = $_POST['namaPelajar'];
        $alamat = $_POST['alamat'];
        $namaWarisPelajar = $_POST['namaWarisPelajar'];
        $tingkatan = $_POST['tingkatan'];
        $selectTingkatan = $_POST['selectTingkatan'];
        $noTel = $_POST['noTel'];
        $kategori = $_POST['kategori'];
        $jumlahYuran = $_POST['jumlahYuran'];
        $catatan = $_POST['catatan'];

        $sql = "INSERT INTO daftar_pelajar (
                    tahunPelajar, noKad, namaPelajar, alamat, namaWarisPelajar,
                    tingkatan, selectTingkatan, noTel, kategori, jumlahYuran, catatan
                ) VALUES (
                    '$tahunPelajar', '$noKad', '$namaPelajar', '$alamat', '$namaWarisPelajar',
                    '$tingkatan', '$selectTingkatan', '$noTel', '$kategori', '$jumlahYuran', '$catatan'
                )";

        if ($conn->query($sql) !== TRUE) {
            echo "Ralat: " . $conn->error . "<br>";
        }
    }
}

echo "<script>alert('Import selesai!'); window.location.href='kemaskini_pelajar.php';</script>";
?>
