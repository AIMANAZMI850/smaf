<?php session_start(); ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - PIBG System</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background: url('../images/background.jpg') no-repeat center center fixed; 
            background-size: cover;
            text-align: center;
            margin: 0;
            padding: 0;
        }
        .container {
            width: 400px;
            margin: 100px auto;
            padding: 20px;
            background: white;
            border-radius: 10px;
            box-shadow: 0px 0px 10px 0px gray;
        }
        input {
            width: 90%;
            padding: 10px;
            margin: 10px;
            border: 1px solid #ccc;
            border-radius: 5px;
        }
        .btn {
            width: 100%;
            padding: 10px;
            margin: 10px 0;
            cursor: pointer;
            font-size: 16px;
        }
        .btn-green { background: green; color: white; }
        .btn-red { background: darkred; color: white; }
    </style>
</head>
<body>
    <div class="container">
        <h2>SISTEM MAKLUMAT YURAN PELAJAR PIBG</h2>
        <?php if(isset($_SESSION['error'])): ?>
            <p style="color: red;"><?php echo $_SESSION['error']; unset($_SESSION['error']); ?></p>
        <?php endif; ?>
        <form action="login.php" method="POST">
            <input type="text" name="kata_nama" placeholder="Kata Nama" required><br>
            <input type="password" name="kata_laluan" placeholder="Kata Laluan" required><br>
            <button type="submit" name="action" value="penerimaan" class="btn btn-green">Penerimaan</button>
            <button type="submit" name="action" value="perbelanjaan" class="btn btn-red">Perbelanjaan</button>
            <p>Belum ada akaun? <a href="../register/register.php">Daftar di sini</a></p> 

        </form>
    </div>
</body>
</html>
