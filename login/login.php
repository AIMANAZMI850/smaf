<?php
session_start();
include_once "../db_connection/db.php";

if (!$conn) {
    die("Database connection failed!");
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = trim($_POST['kata_nama'] ?? "");
    $password = trim($_POST['kata_laluan'] ?? "");
    $action = $_POST['action'] ?? "";  // Check which button was clicked

    if (empty($username) || empty($password)) {
        $_SESSION['error'] = "Sila isi semua maklumat!";
        header("Location: index.php");
        exit();
    }

    $stmt = $conn->prepare("SELECT kata_nama, kata_laluan FROM pengguna WHERE kata_nama = ?");
    if ($stmt) {
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows === 1) {
            $user = $result->fetch_assoc();
            if ($password === $user['kata_laluan'] || password_verify($password, $user['kata_laluan'])) {
                $_SESSION['kata_nama'] = $user['kata_nama'];
                
                // Redirect based on button clicked
                if ($action === "penerimaan") {
                    header("Location: ../dashboard/kemaskini_pelajar.php");
                } elseif ($action === "perbelanjaan") {
                    header("Location: ../perbelanjaan/dashboard.php");  // Change this to your desired page
                }
                exit();
            } else {
                $_SESSION['error'] = "Kata laluan salah!";
            }
        } else {
            $_SESSION['error'] = "Pengguna tidak dijumpai!";
        }
        $stmt->close();
    } else {
        $_SESSION['error'] = "Ralat pangkalan data!";
    }

    header("Location: index.php");
    exit();
}
?>
