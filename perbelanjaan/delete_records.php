<?php
include '../db_connection/db.php'; // Include database connection

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['clear'])) {
    // Delete all records from pembayaran
    $conn->query("DELETE FROM pembayaran"); 
    
    // Reset auto-increment (assuming 'id' or 'refno' is the primary key)
    $conn->query("ALTER TABLE pembayaran AUTO_INCREMENT = 1000001"); 

    echo "success"; // Return success response
} else {
    echo "error";
}
?>
