<?php
include '../db_connection/db.php';

if (isset($_GET['id'])) {
    $id = $_GET['id'];

    $query = "SELECT p.namaPelajar, p.noKad, p.tingkatan, p.selectTingkatan, b.*, 
                k.dana_pibg, k.tuisyen, k.massak, 
                k.majalah, k.hac, k.kertas_peperiksaan, 
                k.bas, k.dobi, p.kategori, p.namaWarisPelajar
              FROM bayaran b 
              JOIN daftar_pelajar p ON b.noKad = p.noKad 
              JOIN keterangan_yuran k ON p.tingkatan = k.tingkatan AND p.kategori = k.kategori
              WHERE b.id = $id";

    $result = mysqli_query($conn, $query);
    $row = mysqli_fetch_assoc($result);

    if ($row) {
        $kategoriPelajar = $row['kategori'];
        $namaWarisPelajar = $row['namaWarisPelajar'];

        // Semak adik-beradik yang sudah bayar Dana PIBG
        $checkSiblingPaymentQuery = "
            SELECT dp.namaPelajar 
            FROM bayaran b
            JOIN daftar_pelajar dp ON b.noKad = dp.noKad
            WHERE dp.namaWarisPelajar = ? 
              AND b.jum_bayar_dana_pibg > 0
              AND dp.noKad != ?
        ";
        $checkStmt = $conn->prepare($checkSiblingPaymentQuery);
        $checkStmt->bind_param("ss", $namaWarisPelajar, $row['noKad']);
        $checkStmt->execute();
        $checkResult = $checkStmt->get_result();

        $siblings = [];
        while ($sibling = $checkResult->fetch_assoc()) {
            $siblings[] = $sibling['namaPelajar'];
        }

        $invoiceNumber = date("Y") . str_pad($id, 4, "0", STR_PAD_LEFT);
        ?>

<!DOCTYPE html>
<html lang="ms">
<head>
    <meta charset="UTF-8">
    <title>Resit Pembayaran</title>
    <style>
        body { font-family: Arial, sans-serif; padding: 20px; text-align: center; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { border: 1px solid black; padding: 8px; text-align: left; }
        th { background-color: #f2f2f2; }
        .print-btn { margin-top: 20px; }
        .details-container {
            display: flex;
            flex-wrap: wrap;
            justify-content: space-between;
            max-width: 600px;
            margin: auto;
        }
        .details-column {
            width: 100%;
            display: flex;
            justify-content: space-between;
            padding: 2px 0;
        }
        .details-label {
            width: 60%;
            font-weight: bold;
            text-align: left;
        }
        .details-value {
            width: 150%;
            text-align: left;
        }
        .signature-container {
            margin-top: 40px;
            text-align: left;
            width: 300px;
        }
        .signature-container p {
            margin: 20px 0;
        }
        .small-text { font-size: 10px; }
        .receipt {
            page-break-inside: avoid;
            max-width: 750px;
            margin: auto;
            min-height: 5cm;
        }
       @media print {
    body {
        transform: scale(1.00); /* Auto kecilkan ke 74% */
        transform-origin: top left;
    }

    .print-btn {
        display: none;
    }
}

    </style>
</head>
<body>

<?php
function renderReceipt($label, $row, $invoiceNumber, $kategoriPelajar, $siblings, $conn)

{
?>
<div class="receipt">
    <h4><?php echo $label; ?><br>Resit Pembayaran SMAF Bagi Tahun <?php echo date("Y"); ?></h4>

    <div class="details-container">
        <div class="details-column"><span class="details-label">No. Resit:</span><span class="details-value"><?php echo $invoiceNumber; ?></span></div>
        <div class="details-column"><span class="details-label">Nama Pelajar:</span><span class="details-value"><?php echo $row['namaPelajar']; ?></span></div>
        <div class="details-column"><span class="details-label">No Kad Pengenalan:</span><span class="details-value"><?php echo $row['noKad']; ?></span></div>
        <div class="details-column"><span class="details-label">Tingkatan:</span><span class="details-value"><?php echo $row['tingkatan']; ?></span></div>
        <div class="details-column"><span class="details-label">Kelas:</span><span class="details-value"><?php echo $row['selectTingkatan']; ?></span></div>
        <div class="details-column"><span class="details-label">Kategori:</span><span class="details-value"><?php echo ucfirst($kategoriPelajar); ?></span></div>
        <div class="details-column"><span class="details-label">Kaedah Bayaran:</span><span class="details-value"><?php echo ucfirst($row['caraBayaran']); ?></span></div>
        <div class="details-column"><span class="details-label">Tarikh Bayaran:</span>
            <span class="details-value">
                <input type="date" id="tarikhBayaran_<?php echo $label; ?>" value="<?php echo date('Y-m-d'); ?>" style="border: none; background: transparent;" onchange="formatTarikh('<?php echo $label; ?>')" />
                <span id="tarikhFormatted_<?php echo $label; ?>" style="display: none;"></span>
            </span>
        </div>
    </div>

    <table>
        <tr>
            <th>Yuran</th>
            <th>Yuran Asal (RM)</th>
            <th>Jumlah Bayar (RM)</th>
            <th>Baki (RM)</th>
        </tr>
        <?php
        $fees = [
            'Dana PIBG' => ['dana_pibg', 'jum_bayar_dana_pibg', 'baki_dana_pibg'],
            'Tuisyen' => ['tuisyen', 'jum_bayar_tuisyen', 'baki_tuisyen'],
            'Massak' => ['massak', 'jum_bayar_massak', 'baki_massak'],
            'Majalah' => ['majalah', 'jum_bayar_majalah', 'baki_majalah'],
            'HAC' => ['hac', 'jum_bayar_hac', 'baki_hac'],
            'Kertas Peperiksaan' => ['kertas_peperiksaan', 'jum_bayar_kertas_peperiksaan', 'baki_kertas_peperiksaan'],
        ];

        if ($kategoriPelajar === 'Asrama') {
            $fees['Bas'] = ['bas', 'jum_bayar_bas', 'baki_bas'];
            $fees['Dobi'] = ['dobi', 'jum_bayar_dobi', 'baki_dobi'];
        }

        foreach ($fees as $feeName => $columns) {
    // Skip Dana PIBG if siblings have already paid
    if ($feeName === 'Dana PIBG' && !empty($siblings)) {
        continue;
    }

    $originalFee = floatval($row[$columns[0]]);
    $amountPaid = floatval($row[$columns[1]]);
    $balance = $originalFee - $amountPaid;

    echo "<tr>
        <td>$feeName</td>
        <td>RM " . number_format($originalFee, 2) . "</td>
        <td>RM " . number_format($amountPaid, 2) . "</td>
        <td>RM " . number_format($balance, 2) . "</td>
    </tr>";
}

        ?>
    </table>

   <?php
// Adjust Dana PIBG only if no siblings have paid
$actualDanaPibgPaid = empty($siblings) ? floatval($row['jum_bayar_dana_pibg']) : 0.00;

$manualJumlahBayar = 
    $actualDanaPibgPaid +
    floatval($row['jum_bayar_tuisyen']) +
    floatval($row['jum_bayar_massak']) +
    floatval($row['jum_bayar_majalah']) +
    floatval($row['jum_bayar_hac']) +
    floatval($row['jum_bayar_kertas_peperiksaan']);

if ($kategoriPelajar === 'Asrama') {
    $manualJumlahBayar += floatval($row['jum_bayar_bas']) + floatval($row['jum_bayar_dobi']);
}

$manualJumlahYuran = 
    floatval(empty($siblings) ? $row['dana_pibg'] : 0.00) +
    floatval($row['tuisyen']) +
    floatval($row['massak']) +
    floatval($row['majalah']) +
    floatval($row['hac']) +
    floatval($row['kertas_peperiksaan']);

if ($kategoriPelajar === 'Asrama') {
    $manualJumlahYuran += floatval($row['bas']) + floatval($row['dobi']);
}

$bakiKeseluruhan = $manualJumlahYuran - $manualJumlahBayar;
?>

<h4>Jumlah Bayar Keseluruhan: RM <?php echo number_format($manualJumlahBayar, 2); ?></h4>
<h4>Baki Keseluruhan: RM <?php echo number_format($bakiKeseluruhan, 2); ?></h4>


   <?php
// Lookup name of the payer (if it's a sibling)
$dibayarOlehName = '';
if (!empty($row['dibayar_oleh_noKad'])) {
    $payerIC = mysqli_real_escape_string($conn, $row['dibayar_oleh_noKad']);
    $payerQuery = "SELECT namaPelajar FROM daftar_pelajar WHERE noKad = '$payerIC' LIMIT 1";
    $payerResult = mysqli_query($conn, $payerQuery);
    if ($payerResult && mysqli_num_rows($payerResult) > 0) {
        $payerData = mysqli_fetch_assoc($payerResult);
        $dibayarOlehName = $payerData['namaPelajar'];
    }
}
?>

<?php if (!empty($dibayarOlehName)): ?>
    <p><strong>Dibayar oleh:</strong> <?= htmlspecialchars($dibayarOlehName) ?></p>
<?php endif; ?>




    <div class="signature-container">
        <p><strong>Dikeluarkan Oleh:</strong></p>
        <p>__________________________</p>
        <p><em>Tandatangan & Cop Rasmi</em></p>
        <p class="small-text"><strong><em>Ini adalah resit janaan komputer, tidak perlu tandatangan.</em></strong></p>
    </div>
</div>
<?php
} // end of function
?>

<?php
renderReceipt("Salinan Sekolah", $row, $invoiceNumber, $kategoriPelajar, $siblings, $conn);
renderReceipt("Salinan Ibu Bapa", $row, $invoiceNumber, $kategoriPelajar, $siblings, $conn);

?>

<button class="print-btn" onclick="window.print()">Cetak Resit</button>
<button class="print-btn" onclick="window.location.href='bayaran.php'">Kembali</button>

<script>
    function formatTarikh(label) {
        const input = document.getElementById('tarikhBayaran_' + label);
        const span = document.getElementById('tarikhFormatted_' + label);
        const date = new Date(input.value);
        const formatted = date.toLocaleDateString('ms-MY', {
            day: '2-digit',
            month: '2-digit',
            year: 'numeric'
        });

        span.textContent = formatted;
    }

    window.onbeforeprint = () => {
        ['Salinan Sekolah', 'Salinan Ibu Bapa'].forEach(label => {
            formatTarikh(label);
            document.getElementById('tarikhBayaran_' + label).style.display = 'none';
            document.getElementById('tarikhFormatted_' + label).style.display = 'inline';
        });
    };

    window.onafterprint = () => {
        ['Salinan Sekolah', 'Salinan Ibu Bapa'].forEach(label => {
            document.getElementById('tarikhBayaran_' + label).style.display = 'inline';
            document.getElementById('tarikhFormatted_' + label).style.display = 'none';
        });
    };
</script>

</body>
</html>

<?php
    } else {
        echo "Maklumat tidak dijumpai.";
    }
}
?>
