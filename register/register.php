<?php
session_start();
include_once "../db_connection/db.php";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = trim($_POST['kata_nama']);
    $password = trim($_POST['kata_laluan']);

    if (empty($username) || empty($password)) {
        $_SESSION['error'] = "Sila isi semua maklumat!";
        header("Location: register.php");
        exit();
    }

    $stmt = $conn->prepare("SELECT * FROM pengguna WHERE kata_nama = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $_SESSION['error'] = "Nama pengguna sudah wujud!";
        header("Location: register.php");
        exit();
    }

    $stmt = $conn->prepare("INSERT INTO pengguna (kata_nama, kata_laluan) VALUES (?, ?)");
    $stmt->bind_param("ss", $username, $password);

    if ($stmt->execute()) {
        $_SESSION['success'] = "Pendaftaran berjaya! Sila log masuk.";
        header("Location: register.php");
        exit();
    } else {
        $_SESSION['error'] = "Ralat pendaftaran!";
        header("Location: register.php");
        exit();
    }
}
?>

<!DOCTYPE html>
<html lang="ms">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftar Pengguna</title>
    <style>
        body {
            font-family: 'Poppins', sans-serif;
            background: url('../images/background.jpg') no-repeat center center fixed; 
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
        }
        .container {
            width: 380px;
            background: white;
            padding: 30px;
            border-radius: 12px;
            box-shadow: 0px 4px 10px rgba(0, 0, 0, 0.1);
            text-align: center;
        }
        h2 {
            margin-bottom: 15px;
            color: #333;
        }
        input {
            width: 90%;
            padding: 12px;
            margin: 10px 0;
            border: 1px solid #ddd;
            border-radius: 6px;
            font-size: 16px;
            transition: 0.3s;
        }
        input:focus {
            border-color: #007bff;
            outline: none;
            box-shadow: 0px 0px 5px rgba(0, 123, 255, 0.3);
        }
        .btn {
            width: 100%;
            padding: 12px;
            margin: 10px 0;
            border: none;
            border-radius: 6px;
            font-size: 16px;
            cursor: pointer;
            transition: 0.3s;
        }
        .btn-green {
            background: #28a745;
            color: white;
        }
        .btn-green:hover {
            background: #218838;
        }
        .error, .success {
            padding: 10px;
            border-radius: 6px;
            margin-bottom: 15px;
        }
        .error {
            background: #ffdddd;
            color: #d8000c;
        }
        .success {
            background: #ddffdd;
            color: #008000;
        }
        p {
            margin-top: 10px;
        }
        a {
            color: #007bff;
            text-decoration: none;
        }
        a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>Daftar Pengguna</h2>

        <?php if (isset($_SESSION['error'])) { echo "<div class='error'>" . $_SESSION['error'] . "</div>"; unset($_SESSION['error']); } ?>
        <?php if (isset($_SESSION['success'])) { echo "<div class='success'>" . $_SESSION['success'] . "</div>"; unset($_SESSION['success']); } ?>

        <form action="../register/register.php" method="POST">
            <input type="text" name="kata_nama" placeholder="Nama Pengguna" required>
            <input type="password" name="kata_laluan" placeholder="Kata Laluan" required>
            <button type="submit" class="btn btn-green">Daftar</button>
        </form>
        <p>Sudah ada akaun? <a href="../login/index.php">Log masuk</a></p>
    </div>
</body>
</html>
