<?php
include '../db_connection/db.php';

if (isset($_GET['id'])) {
    $id = $_GET['id'];

    // Fetch payment details along with original fees
    $query = "SELECT p.namaPelajar, p.noKad, p.tingkatan, p.selectTingkatan, b.*, 
                k.dana_pibg, k.tuisyen, k.massak, 
                k.majalah, k.hac, k.kertas_peperiksaan, 
                k.bas, k.dobi, p.kategori, p.namaWarisPelajar
              FROM bayaran b 
              JOIN daftar_pelajar p ON b.noKad = p.noKad 
              JOIN keterangan_yuran k ON p.tingkatan = k.tingkatan
              WHERE b.id = $id";

    $result = mysqli_query($conn, $query);
    $row = mysqli_fetch_assoc($result);

    if ($row) {
        $kategoriPelajar = $row['kategori']; 
        $namaWarisPelajar = $row['namaWarisPelajar']; // Using namaWarisPelajar for identifying the family

        // Check if any sibling has already paid for Dana PIBG and fetch their names
        $checkSiblingPaymentQuery = "
            SELECT dp.namaPelajar FROM bayaran b
            JOIN daftar_pelajar dp ON b.noKad = dp.noKad
            WHERE dp.namaWarisPelajar = ? AND b.jum_bayar_dana_pibg > 0
        ";
        $checkStmt = $conn->prepare($checkSiblingPaymentQuery);
        $checkStmt->bind_param("s", $namaWarisPelajar);
        $checkStmt->execute();
        $checkResult = $checkStmt->get_result();
        
        // Collect sibling names who have paid for Dana PIBG
        $siblings = [];
        while ($sibling = $checkResult->fetch_assoc()) {
            $siblings[] = $sibling['namaPelajar'];
        }

        // Generate Invoice Number using ID and Year
        $invoiceNumber = "INV" . date("Y") . str_pad($id, 4, "0", STR_PAD_LEFT);
        ?>

        <html>
        <head>
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
                    width: 58%;
                    display: flex;
                    justify-content: space-between;
                    padding: 5px 0;
                }
                .details-label {
                    width: 60%; /* Ensures uniform alignment */
                    font-weight: bold;
                    text-align: left;
                }
                .details-value {
                    width: 50%;
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
                @media print {
                    .print-btn {
                        display: none;
                        size: A5 potrait;
                        margin: 10mm;
                    }
                }
                .small-text {
                    font-size: 10px; /* Adjust the size as needed */
                }
            </style>
        </head>
        <body>
            <h2>Resit Pembayaran SMAF Bagi Tahun <span id="year"></span></h2>

            <div class="details-container">
                <div class="details-column">
                    <span class="details-label">No. Invois:</span>
                    <span class="details-value"><?php echo $invoiceNumber; ?></span>
                </div>
                <div class="details-column">
                    <span class="details-label">Nama Pelajar:</span>
                    <span class="details-value"><?php echo $row['namaPelajar']; ?></span>
                </div>
                <div class="details-column">
                    <span class="details-label">No Kad Pengenalan:</span>
                    <span class="details-value"><?php echo $row['noKad']; ?></span>
                </div>
                <div class="details-column">
                    <span class="details-label">Tingkatan:</span>
                    <span class="details-value"><?php echo $row['tingkatan']; ?></span>
                </div>
                <div class="details-column">
                    <span class="details-label">Kelas:</span>
                    <span class="details-value"><?php echo $row['selectTingkatan']; ?></span>
                </div>
                <div class="details-column">
                    <span class="details-label">Kategori:</span>
                    <span class="details-value"><?php echo ucfirst($kategoriPelajar); ?></span>
                </div>
                <div class="details-column">
                    <span class="details-label">Kaedah Bayaran:</span>
                    <span class="details-value"><?php echo $row['caraBayaran']; ?></span>
                </div>
                <div class="details-column">
                    <span class="details-label">Tarikh Bayaran:</span>
                    <span class="details-value"><?php echo date('d/m/Y'); ?></span>
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
                // Fee structure
                $fees = [
                    'Dana PIBG' => ['dana_pibg', 'jum_bayar_dana_pibg', 'baki_dana_pibg'],
                    'Tuisyen' => ['tuisyen', 'jum_bayar_tuisyen', 'baki_tuisyen'],
                    'Massak' => ['massak', 'jum_bayar_massak', 'baki_massak'],
                    'Majalah' => ['majalah', 'jum_bayar_majalah', 'baki_majalah'],
                    'HAC' => ['hac', 'jum_bayar_hac', 'baki_hac'],
                    'Kertas Peperiksaan' => ['kertas_peperiksaan', 'jum_bayar_kertas_peperiksaan', 'baki_kertas_peperiksaan'],
                ];

                // Include Bas & Dobi fees ONLY if student is "Asrama"
                if ($kategoriPelajar === 'Asrama') {
                    $fees['Bas'] = ['bas', 'jum_bayar_bas', 'baki_bas'];
                    $fees['Dobi'] = ['dobi', 'jum_bayar_dobi', 'baki_dobi'];
                }

                foreach ($fees as $feeName => $columns) {
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

            <h3>Jumlah Bayar Keseluruhan: RM <?php echo number_format($row['jumlahBayar'], 2); ?></h3>
            <h3>Baki Keseluruhan: RM <?php echo number_format($row['baki'], 2); ?></h3>

            <?php if (!empty($siblings)): ?>
                <p><strong>Nota:</strong> Dana PIBG telah dibayar oleh adik-beradik berikut: <?php echo implode(", ", $siblings); ?>.</p>
            <?php endif; ?>

            <div class="signature-container">
                <p><strong>Dikeluarkan Oleh:</strong></p>
                <p>__________________________</p>
                <p><em>Tandatangan & Cop Rasmi</em></p>
                <p class="small-text"><strong><em>Ini adalah resit janaan komputer, tidak perlu tandatangan.</em></strong></p>
            </div>

            <button class="print-btn" onclick="window.print()">Cetak Resit</button>
            <button class="print-btn" onclick="window.location.href='bayaran.php'">Kembali</button>

            <script>
                document.addEventListener("DOMContentLoaded", function () {
                    document.getElementById("year").textContent = new Date().getFullYear();
                });
            </script>
        </body>
        </html>

        <?php
    } else {
        echo "Maklumat tidak dijumpai.";
    }
}
?>
