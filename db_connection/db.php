<?php
$host = "localhost";
$user = "root"; // Change this if you have a different MySQL username
$pass = "";     // Change this if you have a MySQL password
$dbname = "smaf";

$conn = new mysqli($host, $user, $pass, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>
