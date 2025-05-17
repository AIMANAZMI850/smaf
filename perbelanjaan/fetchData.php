<?php
header('Content-Type: application/json');

// Include the connection file
include_once "../db_connection/db.php";

// SQL query to get summed payment data grouped by caraBayaran
$sql = "
    SELECT caraBayaran, 
           SUM(jum_bayar_dana_pibg) AS total_dana_pibg, 
           SUM(jum_bayar_tuisyen) AS total_tuisyen, 
           SUM(jum_bayar_massak) AS total_massak, 
           SUM(jum_bayar_majalah) AS total_majalah, 
           SUM(jum_bayar_hac) AS total_hac, 
           SUM(jum_bayar_kertas_peperiksaan) AS total_kertas_peperiksaan, 
           SUM(jum_bayar_bas) AS total_bas, 
           SUM(jum_bayar_dobi) AS total_dobi 
    FROM bayaran 
    GROUP BY caraBayaran;
";

$result = $conn->query($sql);

$data = [];
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $data[] = $row;
    }
}

echo json_encode($data);
$conn->close();
?>
