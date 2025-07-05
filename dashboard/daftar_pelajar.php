<?php
include '../db_connection/db.php'; // Panggil fail sambungan database

if ($_SERVER["REQUEST_METHOD"] == "POST") {
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

    $sql = "INSERT INTO daftar_pelajar (tahunPelajar, noKad, namaPelajar, alamat, namaWarisPelajar, tingkatan, selectTingkatan,  noTel, kategori, jumlahYuran, catatan) 
            VALUES ('$tahunPelajar', '$noKad', '$namaPelajar', '$alamat', '$namaWarisPelajar', '$tingkatan', '$selectTingkatan',  '$noTel', '$kategori', '$jumlahYuran', '$catatan')";

    if ($conn->query($sql) === TRUE) {
        echo "<script>alert('Pendaftaran berjaya!'); window.location.href='kemaskini_pelajar.php';</script>";
    } else {
        echo "Ralat: " . $sql . "<br>" . $conn->error;
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pendaftaran Pelajar Baru</title>
    <style>
        {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Arial', sans-serif;
        }
        body {
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            background-color:rgb(215, 237, 247);
        }
        .container {
            width: 90%;
            max-width: 600px;
            background: white;
            padding: 25px;
            border-radius: 12px;
            box-shadow: 10px 10px 10px gray;
        }
        h2 {
            text-align: center;
            color: #333;
            margin-bottom: 15px;
        }
        .form-group {
            display: flex;
            align-items: center;
            gap: 10px; /* Adjust spacing between input and select */
            margin-bottom: 15px; /* Adds more space between input fields */
        }
        .form-group label {
            flex: 1; /* Makes label take appropriate space */
        }

        .form-group input,
.form-group select,
.form-group textarea {
    flex: 2;
    padding: 8px;
    border: 1px solid #ccc;
    border-radius: 5px;
    font-size: 14px;
    outline: none;
}
input[type="text"] {
    text-transform: uppercase;
}


        label {
            font-weight: bold;
            display: block;
            margin-bottom: 5px;
            color: #333;
        }
        input, select, textarea {
            width: 95%;
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 5px;
            font-size: 14px;
            outline: none;
            transition: all 0.3s ease-in-out;
        }
        input:focus, select:focus, textarea:focus {
            border-color: #007bff;
            box-shadow: 0px 0px 5px rgba(0, 123, 255, 0.3);
        }
        .uppercase {
    text-transform: uppercase;
}

        .radio-group {
            display: flex;
            align-items: center;
            gap: 5px;
        }
        .btn {
            background-color: #007bff;
            color: white;
            padding: 12px;
            border: none;
            border-radius: 5px;
            font-size: 16px;
            cursor: pointer;
            width: 100%;
            transition: 0.3s;
        }
        .btn:hover {
            background-color: #0056b3;
        }
        .sidebar {
    width: 220px;
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
    transition: 0.3s;
    background: #34495e;
    border-radius: 5px;
    width: 90%;
    margin: 10px auto;
    text-align: center;
    box-sizing: border-box;
}

.sidebar a:hover {
    background: #1abc9c;
}

.sidebar-logo {
    width: 100px;
    margin-bottom: 20px;
}

.container {
    margin-left: 240px; /* Leave space for sidebar */
    width: calc(100% - 240px);
    background: white;
    padding: 25px;
    border-radius: 12px;
    box-shadow: 10px 10px 10px gray;
}

        .sidebar-logo {
            width: 100px; /* Adjust size as needed */
            margin-bottom: 20px;
            transition: opacity 0.3s ease-in-out;
        }
    </style>
</head>
<body>

<div class="container">
    <h2>Pendaftaran Pelajar Baru</h2>
        <form id="studentForm" action="daftar_pelajar.php" method="POST">
    <div class="form-group">
        <label for="tahunPelajar">Tahun Pelajar:</label>
        <select name="tahunPelajar" id="tahunPelajar">
            <option value="2025">2025</option>
            <option value="2026">2026</option>
            <option value="2027">2027</option>
        </select>
    </div>
    <div class="form-group">
        <label for="noKad">No. Kad Pengenalan:</label>
        <input type="text" name="noKad" id="noKad" 
       placeholder="Masukkan No. Kad Pengenalan" 
       required 
       maxlength="12" 
       oninput="this.value = this.value.replace(/[^0-9]/g, '')">

    </div>

    <div class="form-group">
        <label for="namaPelajar">Nama Pelajar:</label>
        <input type="text" name="namaPelajar" id="namaPelajar" class="uppercase" placeholder="Masukkan Nama Pelajar" required>

    </div>

    <div class="form-group">
        <label for="alamat">Alamat:</label>
        <textarea name="alamat" id="alamat" class="uppercase" placeholder="Masukkan Alamat" rows="2" required></textarea>
    </div>

    <div class="form-group">
        <label for="namaWarisPelajar">Nama Waris Pelajar:</label>
        <input type="text" name="namaWarisPelajar" id="namaWarisPelajar" placeholder="Masukkan Nama Waris Pelajar" required>
    </div>

    <div class="form-group">
        <label for="tingkatan">Tingkatan:</label>
        <input type="text" name="tingkatan" id="tingkatan" 
       placeholder="Masukkan Tingkatan" 
       required 
       oninput="this.value = this.value.replace(/[^0-9]/g, ''); setFee();">

    </div>

    <div class="form-group">
        <label for="selectTingkatan">Pilih Kelas:</label>
        <select name="selectTingkatan" id="selectTingkatan" required>
            <option value="">-- Pilih Kelas --</option>
            <option value="Abqori">Abqori</option>
            <option value="Fatonah">Fatonah</option>
            <option value="Cemerlang">Cemerlang</option>
            <option value="Gemilang">Gemilang</option>
            <option value="Imtiyaz">Imtiyaz</option>
        </select>
    </div>


    <div class="form-group">
        <label for="noTel">No. Telefon:</label>
        <input type="text" name="noTel" id="noTel" placeholder="Masukkan No. Telefon" required>
    </div>

    <div class="form-group">
        <label for="kategori">Kategori:</label>
                <select name="kategori" id="kategori" onchange="setFee()">
                    <option value="Sila Pilih Kategori">Pilih Kategori</option>
                    <option value="harian">Harian</option>
                    <option value="asrama">Asrama</option>
                </select>

    <div id="fee-structure"></div>

    </div>

    <div class="form-group">
        <label for="jumlahYuran">Jumlah Yuran (RM):</label>
        <input type="text" name="jumlahYuran" id="jumlahYuran" placeholder="Masukkan Jumlah Yuran" value="00.00" required>
    </div>

    <div class="form-group">
        <label for="catatan">Catatan:</label>
        <input type="text" name="catatan" id="catatan" placeholder="Masukkan Catatan">
    </div>

    <button type="submit" class="btn">Daftar Pelajar</button>
</form>

    </div>

        <div class="sidebar" id="sidebar">
        
        <img src="../images/logo.jpg" id="sidebar-logo" class="sidebar-logo" alt="Logo">
            <a href="daftar_pelajar.php" class="btn">DAFTAR PELAJAR</a>
            <a href="kemaskini_pelajar.php" class="btn">KEMASKINI PELAJAR</a>
            <a href="bayaran.php" class="btn">BAYARAN</a>
           
            <a href="../logout/logout.php" class="btn btn-red">LOG KELUAR</a>
    </div>
    

<script>

function setFee() {
    let kategori = document.getElementById("kategori").value;
    let tingkatan = document.getElementById("tingkatan").value;
    let yuranField = document.getElementById("jumlahYuran");

    let fee = 0;

    // Convert tingkatan to integer for comparison
    let tingkatanNumber = parseInt(tingkatan);

    if (kategori === "asrama") {
        if (tingkatanNumber >= 1 && tingkatanNumber <= 2) {
            fee = 520.00;
        } else if (tingkatanNumber >= 3 && tingkatanNumber <= 6) {
            fee = 530.00;
        }
    } else if (kategori === "harian") {
        if (tingkatanNumber >= 1 && tingkatanNumber <= 2) {
            fee = 100.00;
        } else if (tingkatanNumber >= 3 && tingkatanNumber <= 6) {
            fee = 110.00;
        }
    }

    yuranField.value = fee.toFixed(2); // Ensure 2 decimal places
}
</script>


</body>
</html>
