<?php
include '../db_connection/db.php';

$query = "
    SELECT p.namaPelajar, p.noKad, b.jumlahBayar
    FROM bayaran b
    JOIN daftar_pelajar p ON b.noKad = p.noKad
    WHERE b.jumlahBayar = 0
";
$result = mysqli_query($conn, $query);
?>

<!DOCTYPE html>
<html lang="ms">
<head>
    <meta charset="UTF-8">
    <title>Senarai Pelajar Tidak Bayar</title>
    <style>
        body {
            font-family: 'Segoe UI', sans-serif;
            background-color: #f4f6f8;
            padding: 30px;
        }

        h2 {
            text-align: center;
            color: #c0392b;
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
            background-color: #e74c3c;
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

        td:last-child {
            color: #999;
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
        }
    </style>
</head>
<body>

<h2>Senarai Pelajar Tidak Bayar</h2>

<table>
    <thead>
        <tr>
            <th>No Kad</th>
            <th>Nama Pelajar</th>
            <th>Jumlah Bayar (RM)</th>
        </tr>
    </thead>
    <tbody>
        <?php while ($row = mysqli_fetch_assoc($result)): ?>
            <tr>
                <td><?= $row['noKad']; ?></td>
                <td><?= $row['namaPelajar']; ?></td>
                <td>RM <?= number_format($row['jumlahBayar'], 2); ?></td>
            </tr>
        <?php endwhile; ?>
    </tbody>
</table>

</body>
</html>
