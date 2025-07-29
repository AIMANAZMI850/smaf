<?php
include '../db_connection/db.php';

$query = "
    SELECT p.namaPelajar, p.noKad, b.jumlahBayar, b.baki, b.id AS bayaran_id
    FROM bayaran b
    JOIN daftar_pelajar p ON b.noKad = p.noKad
    WHERE b.jumlahBayar > 0 AND b.baki > 0
";
$result = mysqli_query($conn, $query);

// Fetch all transactions into a lookup table
$transactionsLookup = [];
$transaksiResult = mysqli_query($conn, "SELECT * FROM transaksi_bayaran");
while ($tr = mysqli_fetch_assoc($transaksiResult)) {
    $bayaranId = $tr['bayaran_id'];
    $feeLabel = ucwords(str_replace("jum_bayar_", "", $tr['fee_type']));
    $transactionsLookup[$bayaranId][] = "<span class='badge'>{$feeLabel}: RM " . number_format($tr['jumlah_bayar'], 2) . "</span>";
}
?>

<!DOCTYPE html>
<html lang="ms">
<head>
    <meta charset="UTF-8">
    <title>Senarai Bayar Sebahagian</title>
    <style>
        body {
            font-family: 'Segoe UI', sans-serif;
            background-color: #f0f4f8;
            padding: 30px;
        }

        h2 {
            text-align: center;
            color: #2c3e50;
            margin-bottom: 30px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            background: white;
            border-radius: 8px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.08);
            overflow: hidden;
        }

        th, td {
            padding: 15px;
            text-align: left;
        }

        th {
            background-color: #ffc107;
            color: white;
            text-transform: uppercase;
            font-size: 14px;
        }

        tr:nth-child(even) {
            background-color: #fafafa;
        }

        tr:hover {
            background-color: #f1f1f1;
        }

        .badge {
            display: inline-block;
            background-color: #fff3cd;
            color: #856404;
            padding: 5px 10px;
            margin: 2px;
            border-radius: 12px;
            font-size: 12px;
        }

        @media (max-width: 768px) {
            table, thead, tbody, th, td, tr {
                display: block;
            }

            thead tr {
                display: none;
            }

            td {
                position: relative;
                padding-left: 50%;
                border: none;
                border-bottom: 1px solid #ddd;
            }

            td::before {
                position: absolute;
                top: 10px;
                left: 15px;
                width: 45%;
                white-space: nowrap;
                font-weight: bold;
                color: #333;
            }

            td:nth-of-type(1)::before { content: "No Kad"; }
            td:nth-of-type(2)::before { content: "Nama Pelajar"; }
            td:nth-of-type(3)::before { content: "Jumlah Bayar"; }
            td:nth-of-type(4)::before { content: "Baki"; }
            td:nth-of-type(5)::before { content: "Yuran Dibayar"; }
        }
    </style>
</head>
<body>

<h2>Senarai Pelajar Bayar Sebahagian</h2>

<table>
    <thead>
        <tr>
            <th>No Kad</th>
            <th>Nama Pelajar</th>
            <th>Jumlah Bayar (RM)</th>
            <th>Baki (RM)</th>
            <th>Yuran Dibayar</th>
        </tr>
    </thead>
    <tbody>
        <?php while ($row = mysqli_fetch_assoc($result)): 
            $bayaranId = $row['bayaran_id'];
            $feesPaid = isset($transactionsLookup[$bayaranId]) 
                ? implode(" ", $transactionsLookup[$bayaranId]) 
                : "<span style='color: #999;'>-</span>";
        ?>
        <tr>
            <td><?= $row['noKad']; ?></td>
            <td><?= $row['namaPelajar']; ?></td>
            <td>RM <?= number_format($row['jumlahBayar'], 2); ?></td>
            <td>RM <?= number_format($row['baki'], 2); ?></td>
            <td><?= $feesPaid; ?></td>
        </tr>
        <?php endwhile; ?>
    </tbody>
</table>

</body>
</html>
