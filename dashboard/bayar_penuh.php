<?php
include '../db_connection/db.php';

$query = "
    SELECT 
        p.namaPelajar, 
        p.noKad, 
        b.jumlahBayar, 
        b.baki,
        b.id AS bayaran_id
    FROM bayaran b
    JOIN daftar_pelajar p ON b.noKad = p.noKad
    WHERE b.baki = 0 AND b.jumlahBayar > 0
";

$result = mysqli_query($conn, $query);

// Prepare a lookup for transactions
$transactionsLookup = [];
$transaksiResult = mysqli_query($conn, "SELECT * FROM transaksi_bayaran");
while ($tr = mysqli_fetch_assoc($transaksiResult)) {
    $bayaranId = $tr['bayaran_id'];
    $feeLabel = ucwords(str_replace('jum_bayar_', '', $tr['fee_type']));
    $transactionsLookup[$bayaranId][] = "<span class='badge'>{$feeLabel}: RM " . number_format($tr['jumlah_bayar'], 2) . "</span>";
}
?>

<!DOCTYPE html>
<html lang="ms">
<head>
    <meta charset="UTF-8">
    <title>Senarai Pelajar Bayar Penuh</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            margin: 0;
            padding: 30px;
            background: #f4f8fc;
        }

        h2 {
            text-align: center;
            margin-bottom: 30px;
            color: #2c3e50;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            background: white;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        }

        th, td {
            padding: 15px;
            text-align: left;
        }

        th {
            background-color: #007bff;
            color: white;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        tr:nth-child(even) {
            background-color: #f9f9f9;
        }

        tr:hover {
            background-color: #f1f1f1;
        }

        .badge {
            display: inline-block;
            background-color: #eaf1ff;
            color: #007bff;
            padding: 6px 10px;
            margin: 2px;
            border-radius: 20px;
            font-size: 13px;
            white-space: nowrap;
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
                margin-bottom: 10px;
                border: none;
                border-bottom: 1px solid #ddd;
            }

            td::before {
                position: absolute;
                top: 12px;
                left: 15px;
                width: 45%;
                padding-right: 10px;
                white-space: nowrap;
                font-weight: bold;
                color: #333;
            }

            td:nth-of-type(1)::before { content: "No Kad"; }
            td:nth-of-type(2)::before { content: "Nama Pelajar"; }
            td:nth-of-type(4)::before { content: "Yuran Dibayar"; }
            td:nth-of-type(3)::before { content: "Jumlah Bayar (RM)"; }
        }
    </style>
</head>
<body>

    <h2>Senarai Pelajar Bayar Penuh</h2>

    <table>
        <thead>
            <tr>
                <th>No Kad Pengenalan</th>
                <th>Nama Pelajar</th>
                <th>Yuran Dibayar</th>
                <th>Jumlah Bayar (RM)</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($row = mysqli_fetch_assoc($result)) {
                $bayaranId = $row['bayaran_id'];
                $feesPaid = isset($transactionsLookup[$bayaranId]) 
                            ? implode(' ', $transactionsLookup[$bayaranId]) 
                            : '<span style="color: #999;">-</span>';
            ?>
            <tr>
                <td><?= $row['noKad']; ?></td>
                <td><?= $row['namaPelajar']; ?></td>
                <td><?= $feesPaid; ?></td>
                    <td>RM <?= number_format($row['jumlahBayar'], 2); ?></td>
            </tr>
            <?php } ?>
        </tbody>
    </table>

</body>
</html>
