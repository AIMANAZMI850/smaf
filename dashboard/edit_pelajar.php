<?php
include '../db_connection/db.php';

if (!isset($_GET['noKad'])) {
    echo "<script>alert('No. Kad Pengenalan tidak ditemui!'); window.history.back();</script>";
    exit;
}

$noKad = $_GET['noKad'];
$sql = "SELECT * FROM daftar_pelajar WHERE noKad = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $noKad);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows == 0) {
    echo "<script>alert('Pelajar tidak dijumpai!'); window.history.back();</script>";
    exit;
}

$row = $result->fetch_assoc();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kemaskini Pelajar</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 20px;
            text-align: center;
        }
        .container {
            width: 50%;
            background: white;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0px 0px 10px rgba(0, 0, 0, 0.1);
            margin: auto;
        }
        h2 {
            color: #333;
        }
        label {
            display: block;
            font-weight: bold;
            margin-top: 10px;
            text-align: left;
        }
        input, select {
            width: 100%;
            max-width: 1200px; /* or 300px, depending on your layout */
            padding: 10px;
            margin: 5px 0 0px;
            border: 1px solid #ccc;
            border-radius: 5px;
            font-size: 16px;
        }
        
        
        .btn {
            background-color: #007bff;
            color: white;
            padding: 10px 15px;
            border: none;
            border-radius: 5px;
            font-size: 16px;
            cursor: pointer;
            width: 100%;
            margin-top: 10px;
        }
        .btn:hover {
            background-color: #0056b3;
        }
        .sidebar {
    width: 180px;
    background: #2c3e50;
    height: 100vh;
    padding-top: 20px;
    position: fixed;
    top: 0;
    left: 0;
    text-align: center;
    z-index: 999;
}

.sidebar a {
    display: block;
    padding: 15px;
    color: white;
    text-decoration: none;
    font-size: 16px;
    background: #34495e;
    border-radius: 5px;
    width: 79%;
    margin: 10px auto;
    text-align: center;
}
.sidebar a:hover {
    background: #1abc9c;
}

.sidebar-logo {
    width: 100px;
    margin-bottom: 20px;
}

.container {
    margin-left: 180px; /* Create space for sidebar */
    width: calc(100% - 240px);
    background: white;
    padding: 20px;
    border-radius: 10px;
    box-shadow: 0px 0px 10px rgba(0, 0, 0, 0.1);
}

        .sidebar-logo {
            width: 100px;
            margin-bottom: 20px;
            transition: opacity 0.3s ease-in-out;
        }
        
    </style>
</head>
<body>

<div class="container">
<div class="sidebar" id="sidebar">
        <img src="../images/logo.jpg" id="sidebar-logo" class="sidebar-logo" alt="Logo">
        
        <a href="daftar_pelajar.php">DAFTAR PELAJAR</a>
        <a href="kemaskini_pelajar.php">KEMASKINI PELAJAR</a>
        <a href="bayaran.php" class="btn">BAYARAN</a>
        <a href="../logout/logout.php" class="btn-red">LOG KELUAR</a>
    </div>
    <div class="sidebar-footer">
    &copy; <?= date('Y') ?> SMA Fauzi
</div>

    
    <h2>Kemaskini Pelajar</h2>

    <form id="editStudentForm" action="update_pelajar.php" method="POST">
        <input type="hidden" name="noKad" value="<?= htmlspecialchars($row['noKad']); ?>">

        <label for="noKad">No.Kad Pengenalan <span style="color: red;">*</span></label>
        <input type="text" id="noKad" name="noKad" value="<?= htmlspecialchars($row['noKad']); ?>" required>

        <label for="namaPelajar">Nama Pelajar <span style="color: red;">*</span></label>
        <input type="text" id="namaPelajar" name="namaPelajar" value="<?= htmlspecialchars($row['namaPelajar']); ?>" required>

        <label for="alamat">Alamat <span style="color: red;">*</span></label>
        <input type="text" id="alamat" name="alamat" value="<?= htmlspecialchars($row['alamat']); ?>" required>

        <label for="namaWarisPelajar">Nama Waris <span style="color: red;">*</span></label>
        <input type="text" id="namaWarisPelajar" name="namaWarisPelajar" value="<?= htmlspecialchars($row['namaWarisPelajar']); ?>" required>

        <label for="tingkatan">Tingkatan <span style="color: red;">*</span></label>
        <input type="number" id="tingkatan" name="tingkatan" value="<?= htmlspecialchars($row['tingkatan']); ?>" required oninput="setFee()">

        <label for="selectTingkatan">Kelas <span style="color: red;">*</span></label>
        <select name="selectTingkatan" id="selectTingkatan" onchange="setFee()">
            <option value="abqori" <?= strtolower($row['selectTingkatan']) == 'abqori' ? 'selected' : ''; ?>>Abqori</option>
            <option value="fatonah" <?= strtolower($row['selectTingkatan']) == 'fatonah' ? 'selected' : ''; ?>>Fatonah</option>
            <option value="gemilang" <?= strtolower($row['selectTingkatan']) == 'gemilang' ? 'selected' : ''; ?>>Gemilang</option>

        </select>

        <label for="kategori">Kategori <span style="color: red;">*</span></label>
        <select name="kategori" id="kategori" onchange="setFee()">
            <option value="harian" <?= strtolower($row['kategori']) == 'harian' ? 'selected' : ''; ?>>Harian</option>
            <option value="asrama" <?= strtolower($row['kategori']) == 'asrama' ? 'selected' : ''; ?>>Asrama</option>

        </select>

        <label for="jumlahYuran">Jumlah Yuran (RM) <span style="color: red;">*</span></label>
        <input type="text" id="jumlahYuran" name="jumlahYuran" value="<?= number_format($row['jumlahYuran'], 2); ?>" required readonly>

        <button type="submit" class="btn">Simpan Perubahan</button>

    </form>
</div>

<script>
     
function setFee() {
    let kategori = document.getElementById("kategori").value;
    let tingkatan = document.getElementById("tingkatan").value;
    let yuranField = document.getElementById("jumlahYuran");

    let fee = 0;
    let tingkatanNumber = parseInt(tingkatan) || 0;

    if (kategori === "asrama") {
        fee = tingkatanNumber >= 3 ? 530.00 : 520.00;
    } else {
        fee = tingkatanNumber >= 3 ? 110.00 : 100.00;
    }

    yuranField.value = fee.toFixed(2);
}
</script>

</body>
</html>
