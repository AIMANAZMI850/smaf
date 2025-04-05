<?php
include '../db_connection/db.php'; // Include your database connection

$studentId = intval($_GET['id']); // Get the student ID from the request

// Fetch student details
$query = "SELECT * FROM bayaran WHERE id = $studentId";
$result = mysqli_query($conn, $query);
$row = mysqli_fetch_assoc($result);

// Return the data as JSON
header('Content-Type: application/json');
echo json_encode($row);
?>