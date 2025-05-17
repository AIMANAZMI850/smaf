<?php
session_start();
if (!isset($_SESSION['kata_nama'])) {
    header("Location: ../index.php"); // Redirect to login page if session is not set
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<script src="https://cdn.jsdelivr.net/npm/exceljs@4.3.0/dist/exceljs.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/FileSaver.js/2.0.5/FileSaver.min.js"></script>



    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bahagian Akaun Perbelanjaan PIBG</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f4f4f4;
        }

        .container {
            width: 90%;
            margin: 20px auto;
            background: white;
            border: 1px solid #ccc;
            box-shadow: 2px 2px 10px rgba(0, 0, 0, 0.1);
        }

        header {
            background: #3b71ca;
            color: white;
            padding: 10px;
            font-size: 18px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .close-btn {
            background: red;
            color: white;
            border: none;
            padding: 5px 10px;
            cursor: pointer;
        }
        .menu {
    background: #dcdcdc;
    padding: 10px;
    display: flex;
    gap: 8px;
    justify-content: center; /* Centers items horizontally */
    align-items: center; /* Aligns items vertically */
}


        /* Modified Buttons */
        .btn {
            background: white;
            border: 1px solid #bbb;
            padding: 12px 20px; /* Increased padding */
            cursor: pointer;
            display: flex;
            align-items: center;
            gap: 10px;
            font-size: 16px; /* Increased text size */
        }

        .btn img {
            width: 24px; /* Increased icon size */
            height: 24px;
        }

        .btn.active {
            background: red;
            color: white;
        }

        .btn.red {
            background: #ff5c5c;
            color: white;
        }

        .btn.green {
            background: #4caf50;
            color: white;
        }

        .content {
            padding: 15px;
        }

        label {
            font-weight: bold;
        }

        #account-select {
            margin: 10px 0;
            padding: 5px;
        }

        .bayaran-text {
            color: red;
            font-weight: bold;
            margin-left: 10px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }

        th, td {
            border: 1px solid #ccc;
            padding: 8px;
            text-align: left;
        }

        th {
            background: #f0f0f0;
        }
        @media print {
    .menu, .close-btn, header { 
        display: none !important; /* Hide buttons and header */
    }

    #account-select {
        display: none !important; /* Hide the dropdown */
    }

    body.print-mode .container {
        width: 100%;
        box-shadow: none; /* Remove box shadow for better print appearance */
    }

    table {
        width: 100%;
        border-collapse: collapse;
        margin-top: 10px;
    }

    th, td {
        border: 1px solid black;
        padding: 8px;
        text-align: left;
    }
    .account-controls {
    display: flex;
    align-items: center;
    justify-content: flex-end; /* Move to right */
    gap: 10px;
    margin-bottom: 10px;
}
@media print {
    /* Hide all elements not needed in print */
    .menu, .close-btn, header, 
    label[for="account-select"], #account-select, 
    label[for="recordsPerPageSelect"], #recordsPerPageSelect,
    span {
        display: none !important;
    }

    /* Ensure print-specific header is visible */
    #print-header {
        display: block !important;
        font-size: 18px;
        font-weight: bold;
        text-align: center;
        margin-bottom: 15px;
    }
}
}
    </style>
</head>
<body>
<div class="container">
    <header>
        <span class="title">Bahagian Akaun Perbelanjaan</span>
        <button class="close-btn" onclick="logout()">Log Keluar</button>
    </header>

    <nav class="menu">
        <button class="btn" onclick="printTable()"><img src="../images/print.jpg" alt="Print"> Cetak</button>
        <button class="btn" onclick="exportToExcel()"><img src="../images/excel.png" alt="Report"> Laporan</button>

        <button class="btn" onclick="openTransaksiWindow()"><img src="../images/transaction.png" alt="Transaction"> Transaksi</button>
        <button class="btn" onclick="openAkaunWindow()"><img src="../images/account.webp" alt="Account"> Akaun</button>
        <button class="btn" onclick="openPembayaranWindow()"> <img src="../images/bayaran.png" alt="Bayaran">Bayaran</button>
        <button class="btn" onclick="openPerolehanWindow()"> <img src="../images/04ikon-perolehan.png" alt="Acquisition"> Perolehan</button>
    </nav>
    <div id="print-header" style="text-align: center; margin-bottom: 10px; display: none;"></div>


    <section class="content">
    <h2 id="print-header" style="display: none;"></h2>

        <label for="account-select">Sila Pilih Akaun:</label>
    <select id="account-select">
        <option value="">-- Pilih Akaun --</option>
        <option value="Dana PIBG">Dana PIBG</option>
        <option value="AKADEMIK & SAHSIAH">AKADEMIK & SAHSIAH</option>
        <option value="MASSAK">MASSAK</option>
        <option value="MAJALAH">MAJALAH</option>
        <option value="HAC">HAC</option>
        <option value="BAS">BAS</option>
        <option value="DOBI">DOBI</option>
        <option value="BANK(CAJ & HIBAH)">BANK(CAJ & HIBAH)</option>
        <option value="LAIN-LAIN">LAIN-LAIN</option>
    </select>

    <label for="recordsPerPageSelect">Lihat:</label>
    <select id="recordsPerPageSelect">
        <option value="20" selected>20</option>
        <option value="50">50</option>
        <option value="100">100</option>
        <option value="150">150</option>
    </select>
    <span>rekod setiap halaman</span>


        <table id="transaction-table">
            <thead>
                <tr>
                    <th>Tarikh</th>
                    <th>No Ref</th>
                    <th>Perkara</th>
                    <th>TUNAI (RM)</th>
                    <th>BANK (RM)</th>
                </tr>
            </thead>
            <tbody>
                <!-- Dynamic rows will be added here -->
            </tbody>
        </table>
    </section>
</div>




    <script>
      let currentPage = 1;
    const recordsPerPage = 20;
    let transactionsData = [];

    async function exportToExcel() {
    const ExcelJS = window.ExcelJS; // Assuming ExcelJS is loaded in your project

    const workbook = new ExcelJS.Workbook();
    const now = new Date().toLocaleDateString("ms-MY");

    const summarySheet = workbook.addWorksheet("SUMMARY");
    summarySheet.pageSetup = {
  paperSize: 9,
  orientation: 'landscape',
  fitToPage: true,
  fitToWidth: 1,
  fitToHeight: 0,
  horizontalCentered: true
};


// Set column widths for a nicer layout
summarySheet.columns = [
    { width: 20 },    // Column A (empty or numbering, if needed)
    { width: 30 },   // Column B - PERKARA
    { width: 15 },   // Column C - B/F 2023
    { width: 15 },   // Column D - TERIMA
    { width: 15 },   // Column E - JUMLAH
    { width: 15 },   // Column F - BELANJA
    { width: 15 }    // Column G - BAKI
];

// Add title and date
summarySheet.addRow([]);
summarySheet.addRow(["", "SUMMARY 2025"]);
summarySheet.addRow(["", `TARIKH DI KELUARKAN ${now}`]);
summarySheet.addRow([]);

// Header row
const sumHeader = summarySheet.addRow([
    "", "PERKARA", "B/F 2025", "TERIMA", "JUMLAH", "BELANJA", "BAKI"
]);

// Add categories
const categories = [
    "DANA PIBG",
    "PEMBANGUNAN (TUISYEN)",
    "MASAK",
    "MAJALAH",
    "HAC",
    "KERTAS PEPERIKSAAN",
    "BAS",
    "DOBI",
    "BANK (CAJ & HIBAH)",
    "LAIN-LAIN"
];
categories.forEach(name => summarySheet.addRow(["", name]));

// Add final "JUMLAH" row
const totalRow = summarySheet.addRow(["", "JUMLAH RM"]);
totalRow.getCell(2).font = { bold: true };

// Define row range
const firstTableRow = sumHeader.number;
const lastTableRow = totalRow.number;

// Apply border, alignment, and color formatting
for (let rowNum = firstTableRow; rowNum <= lastTableRow; rowNum++) {
    const row = summarySheet.getRow(rowNum);
    for (let colNum = 2; colNum <= 7; colNum++) {
        const cell = row.getCell(colNum);

        // Border
        cell.border = {
            top: { style: 'thin' },
            left: { style: 'thin' },
            bottom: { style: 'thin' },
            right: { style: 'thin' }
        };

        // Center for numeric columns, left for PERKARA
        cell.alignment = {
            horizontal: colNum === 2 ? 'left' : 'center',
            vertical: 'middle'
        };

        // Header row style
        if (rowNum === sumHeader.number) {
            cell.font = { bold: true };
            cell.fill = {
                type: 'pattern',
                pattern: 'solid',
                fgColor: { argb: 'FFFFA500' } // Orange
            };
        }

        // "JUMLAH" row style
        if (rowNum === totalRow.number) {
            cell.font = { bold: true };
            cell.fill = {
                type: 'pattern',
                pattern: 'solid',
                fgColor: { argb: 'FFFFA500' } // Orange
            };
        }
    }
}
summarySheet.addRow([]); // spacing row

summarySheet.addRow(["", "", "", "", "", "Dalam Tangan", ""]);
summarySheet.addRow(["", "", "", "", "", "Dalam Bank", ""]);

// Add JUMLAH RM row and store the row in a variable
const jumlahRow = summarySheet.addRow(["", "", "", "", "", "JUMLAH RM", ""]);

// Style the "JUMLAH RM" cell (6th column)
jumlahRow.getCell(6).font = { bold: true };
jumlahRow.getCell(6).fill = {
  type: 'pattern',
  pattern: 'solid',
  fgColor: { argb: 'FFFFA500' } // Orange
};
jumlahRow.getCell(6).alignment = { horizontal: 'center' };
jumlahRow.getCell(6).border = {
  top: { style: 'thin' },
  left: { style: 'thin' },
  bottom: { style: 'thin' },
  right: { style: 'thin' }
};

summarySheet.addRow([]); // another spacing row

// Add date row
summarySheet.addRow(["", `TARIKH : ${now}`]);

// First, create the workbook and BANK sheet as you already have
const createSheetWithHeader = (sheetName, title, headers, openingBalance) => {
    const sheet = workbook.addWorksheet(sheetName);

    const boldCenter = { bold: true, alignment: { horizontal: 'center' } };
    const grayFill = { type: 'pattern', pattern: 'solid', fgColor: { argb: 'FFD9D9D9' } };
    const orangeFill = { type: 'pattern', pattern: 'solid', fgColor: { argb: 'FFFFA500' } };
    const thinBorder = {
        top: { style: 'thin' },
        left: { style: 'thin' },
        bottom: { style: 'thin' },
        right: { style: 'thin' }
    };

    // Title and print date
    sheet.addRow([]);
    sheet.addRow(['', '', '', `Tarikh Cetak: ${now}`]);
    sheet.addRow([]);
    const headerRow = sheet.addRow(['', ...headers]);
    const bfrRow = sheet.addRow(['', '', '', `B/F 01.01.2024`, ...openingBalance]);

    headerRow.eachCell((cell, colNumber) => {
        if (colNumber > 1) {
            cell.font = boldCenter;
            cell.fill = grayFill;
            cell.border = thinBorder;
            cell.alignment = { horizontal: 'center' };
        }
    });

    bfrRow.eachCell((cell, colNumber) => {
        if (colNumber > 1) cell.border = thinBorder;
    });

    const perihalItems = [
        "Dana PIBG", "Pembangunan", "Massak", "Majalah",
        "HAC", "Kertas Peperiksaan", "Bas", "Dobi",
        "Bank (Caj & Hibah)", "Lain-lain"
    ];

    const dataRows = [];

    perihalItems.forEach((item, index) => {
        const row = sheet.addRow(['', index + 1, '', item, '', '']);
        row.eachCell((cell, colNumber) => {
            if (colNumber > 1) cell.border = thinBorder;
        });
        dataRows.push(row); // collect reference to the row for later update
    });

    const subTotalARow = sheet.addRow(['', '', '', '# Sub total (A) RM:', '', '']);
    subTotalARow.eachCell((cell, colNumber) => {
        if (colNumber > 3) {
            cell.font = { bold: true };
            cell.fill = orangeFill;
            cell.border = thinBorder;
            cell.alignment = { horizontal: 'center' };
        }
    });

    for (let i = 0; i < 10; i++) {
        const row = sheet.addRow(['', '', '', '', '', '']);
        row.eachCell((cell, colNumber) => {
            if (colNumber > 1) cell.border = thinBorder;
        });
    }

    const subTotalBRow = sheet.addRow(['', '', '', '# Sub total (B) RM:', '', '']);
    subTotalBRow.eachCell((cell, colNumber) => {
        if (colNumber > 3) {
            cell.font = { bold: true };
            cell.fill = orangeFill;
            cell.border = thinBorder;
            cell.alignment = { horizontal: 'center' };
        }
    });

    const bakiRow = sheet.addRow(['', '', '', 'BAKI SEBENAR RM:', '', '']);
    bakiRow.eachCell((cell, colNumber) => {
        if (colNumber > 3) {
            cell.font = { bold: true };
            cell.fill = orangeFill;
            cell.border = thinBorder;
            cell.alignment = { horizontal: 'center' };
        }
    });

    sheet.columns = [
        { width: 3 },
        { width: 10 },
        { width: 15 },
        { width: 35 },
        { width: 15 },
        { width: 15 }
    ];

    return { sheet, dataRows }; // return rows for later update
};

const { sheet: bankSheet, dataRows: bankDataRows } = createSheetWithHeader(
    "BANK",
    "BANK",
    ["NO.", "TARIKH", "PERIHAL", "MASUK", "KELUAR (CEK)"],
    [0, ""]
);

bankSheet.pageSetup = {
    paperSize: 9,
    orientation: 'landscape',
    fitToPage: true,
    fitToWidth: 1,
    fitToHeight: 0,
    horizontalCentered: true
};


// === FETCH DATA FROM PHP AND FILL IN BANK SHEET ===
fetch('fetchData.php')
  .then(response => response.json())
  .then(async data => {
    // Prepare maps for bank and tunai (petty cash)
    const paymentMapBank = {};
    const paymentMapTunai = {};

    data.forEach(d => {
      if (d.caraBayaran === 'bank') {
        paymentMapBank["Dana PIBG"] = parseFloat(d.total_dana_pibg) || 0;
        paymentMapBank["Pembangunan"] = parseFloat(d.total_tuisyen) || 0;
        paymentMapBank["Massak"] = parseFloat(d.total_massak) || 0;
        paymentMapBank["Majalah"] = parseFloat(d.total_majalah) || 0;
        paymentMapBank["HAC"] = parseFloat(d.total_hac) || 0;
        paymentMapBank["Kertas Peperiksaan"] = parseFloat(d.total_kertas_peperiksaan) || 0;
        paymentMapBank["Bas"] = parseFloat(d.total_bas) || 0;
        paymentMapBank["Dobi"] = parseFloat(d.total_dobi) || 0;
      } else if (d.caraBayaran === 'tunai') {
        paymentMapTunai["Dana PIBG"] = parseFloat(d.total_dana_pibg) || 0;
        paymentMapTunai["Pembangunan"] = parseFloat(d.total_tuisyen) || 0;
        paymentMapTunai["Massak"] = parseFloat(d.total_massak) || 0;
        paymentMapTunai["Majalah"] = parseFloat(d.total_majalah) || 0;
        paymentMapTunai["HAC"] = parseFloat(d.total_hac) || 0;
        paymentMapTunai["Kertas Peperiksaan"] = parseFloat(d.total_kertas_peperiksaan) || 0;
        paymentMapTunai["Bas"] = parseFloat(d.total_bas) || 0;
        paymentMapTunai["Dobi"] = parseFloat(d.total_dobi) || 0;
      }
    });

    // Get your sheets from the workbook
    const bankSheet = workbook.getWorksheet('BANK');
    const pettyCashSheet = workbook.getWorksheet('PETTYCASH');

    // Helper function to update rows on a sheet
    // Assumes data rows start from row 2 (adjust if needed)
    function updateSheet(sheet, paymentMap) {
  let subtotalRow = null;
  let subtotalSum = 0;

  for (let rowNum = 2; rowNum <= sheet.actualRowCount; rowNum++) {
    const row = sheet.getRow(rowNum);

    // Skip empty or non-data rows
    if (!row.hasValues) continue;

    const categoryName = row.getCell(4).value; // "PERIHAL" is in column 4 (D)
    if (!categoryName || typeof categoryName !== "string") continue;

    // Check if this is the subtotal row
    if (categoryName.trim() === '# Sub total (A) RM:') {
      subtotalRow = row;
      continue; // skip updating subtotal row for payment values
    }

    // If category exists in paymentMap, update "MASUK" value (column 5 = E)
    if (paymentMap.hasOwnProperty(categoryName)) {
      const value = paymentMap[categoryName];
      row.getCell(5).value = value;
      row.getCell(5).numFmt = '0.00';
      subtotalSum += value;
    }

    row.commit(); // Save changes
  }

  // After looping, update subtotal row value if it exists
  if (subtotalRow) {
    subtotalRow.getCell(5).value = subtotalSum;
    subtotalRow.getCell(5).numFmt = '0.00';  // RM currency format
    subtotalRow.commit();
  }
}



    updateSheet(bankSheet, paymentMapBank);
    updateSheet(pettyCashSheet, paymentMapTunai);

    // Now generate the Excel buffer AFTER updating all cells
    const buffer = await workbook.xlsx.writeBuffer();

    // Create a Blob and trigger download
    const blob = new Blob([buffer], { type: "application/vnd.openxmlformats-officedocument.spreadsheetml.sheet" });
    const fileName = `Laporan_Kewangan_${new Date().toISOString().slice(0, 10)}.xlsx`;
    saveAs(blob, fileName);
  })
  .catch(err => {
    console.error("Error fetching or generating Excel:", err);
  });

    const pettySheet = createSheetWithHeader(
        "PETTYCASH",
        "PETTYCASH",
        ["NO.", "TARIKH", "PERIHAL", "MASUK", "KELUAR (TUNAI)"],
        [0, ""]
    );

    pettySheet.pageSetup = {
  paperSize: 9,
  orientation: 'landscape',
  fitToPage: true,
  fitToWidth: 1,
  fitToHeight: 0,
  horizontalCentered: true
};

    

    const balanceSheet = workbook.addWorksheet("BalanceSheet");
    balanceSheet.pageSetup = {
  paperSize: 9,
  orientation: 'landscape',
  fitToPage: true,
  fitToWidth: 1,
  fitToHeight: 0,
  horizontalCentered: true
};

// Title
balanceSheet.addRow(["", "", "", "PENYATA AKAUN BERAKHIR PADA 31.12.2024"]);
balanceSheet.addRow([]);
balanceSheet.addRow([]);

// Header
balanceSheet.addRow(["", "", "", "PENDAPATAN", "", "", "", "PERBELANJAAN"]);

// Data rows (PENDAPATAN vs PERBELANJAAN)
balanceSheet.addRow([
    "", "", "a.", "BAKI DALAM BANK BERAKHIR 31.12.2023", 10574.55,
    "", "a.", "PENGURUSAN PIBG", 7485.20
]);
balanceSheet.addRow([
    "", "", "b.", "TUNAI DALAM TANGAN PADA 31.12.2023", 1689.77,
    "", "b.", "SISWAH PELAJAR", 20706.00
]);
balanceSheet.addRow([
    "", "", "c.", "KUTIPAN YURAN PADA 1 Jan - 31 Dec 2024", 73437.00,
    "", "c.", "MASAK", 2653.00
]);
balanceSheet.addRow([
    "", "", "d.", "KUTIPAN DERMA/LAIN2 PADA 2024", 25935.92,
    "", "d.", "MAJALAH", 7385.00
]);
balanceSheet.addRow([
    "", "", "e.", "KUTIPAN YURAN 2025 (PADA DECEMBER 2024)", 0.00,
    "", "e.", "HAC", 0.00
]);
balanceSheet.addRow([
    "", "", "f.", "KUTIPAN YURAN ONLINE 1 Jan - 31 Dec 2024", 41915.50,
    "", "f.", "KERTAS PEPERIKSAAN", 0.00
]);
balanceSheet.addRow([
    "", "", "g.", "KUTIPAN YURAN ONLINE 2025 (PADA DECEMBER 2024)", 0.00,
    "", "g.", "BAS", 50279.76
]);
balanceSheet.addRow([
    "", "", "h.", "HIBAH", 9.73,
    "", "h.", "DOBI", 33085.00
]);
balanceSheet.addRow([
    "", "", "", "", "",
    "", "i.", "BANK(CAJ)", 1163.91
]);
balanceSheet.addRow([
    "", "", "", "", "",
    "", "j.", "LAIN", 17284.18
]);

// Sub totals
balanceSheet.addRow([
    "", "", "", "JUMLAH RM", 153562.47,
    "", "", "JUMLAH RM", 153562.47
]);

balanceSheet.addRow([]);
balanceSheet.addRow(["", "", "", "Disediakan Oleh", "", "", "Disahkan Oleh"]);
balanceSheet.addRow(["", "", "", "", "", "", ""]);
balanceSheet.addRow(["", "", "", "Bendahari PIBG SMAF", "", "", "YDP PIBG SMAF"]);
balanceSheet.addRow(["", "", "", "Tarikh:", "", "", "Tarikh:"]);
balanceSheet.addRow([]);
balanceSheet.addRow([
    "", "",
    "", 
    "Adalah diakui bahawa kami telah menyemak akaun PIBG SMAF bermula dari 01 Januari hingga 31 Disember.", "", "", 
    ""
]);

// Optional: Set column widths for better formatting
balanceSheet.columns = [
    { width: 5 },
    { width: 5 },
    { width: 5 },
    { width: 45 },
    { width: 15 },
    { width: 5 },
    { width: 5 },
    { width: 45 },
    { width: 15 }
];


    const incomeSheet = workbook.addWorksheet("INCOME");
    incomeSheet.pageSetup = {
  paperSize: 9,
  orientation: 'landscape',
  fitToPage: true,
  fitToWidth: 1,
  fitToHeight: 0,
  horizontalCentered: true
};
    incomeSheet.addRow([]);
    incomeSheet.addRow([]);
    incomeSheet.addRow(["", "YURAN PIBG TAHUN 2024 - KUTIP PADA 2024"]);
    incomeSheet.addRow([]);
    incomeSheet.addRow(["", "", "", "", "TUNAI", "BANK", "JUMLAH", "", "", "TUNAI", "BANK", "JUMLAH"]);
    incomeSheet.addRow(["", "", "", "", "", "", "", "YURAN PIBG TAHUN 2025"]);

    
}



    document.addEventListener("DOMContentLoaded", function () {
        const accountSelect = document.getElementById("account-select");
        const tableBody = document.querySelector("tbody");

        accountSelect.addEventListener("change", function () {
            console.log("Selected account:", this.value);
            currentPage = 1;
            displayTransactions(this.value);
        });
        recordsSelect.addEventListener("change", function () {
        recordsPerPage = parseInt(this.value); // Update records per page
        currentPage = 1; // Reset to first page
        updateTable();
    });

        function displayTransactions(account) {
            tableBody.innerHTML = "";
            const pembayaranTransaksi = JSON.parse(localStorage.getItem("pembayaran_transaksi")) || {};
            const perolehanTransaksi = JSON.parse(localStorage.getItem("perolehan_transaksi")) || {};
            
            let pembayaranData = pembayaranTransaksi[account] || [];
            let transaksiData = perolehanTransaksi[account] || [];

            // Combine both arrays
            transactionsData = [...pembayaranData, ...transaksiData];

            if (transactionsData.length === 0) {
                const row = document.createElement("tr");
                row.innerHTML = `<td colspan="5" style="text-align: center;">Tiada rekod</td>`;
                tableBody.appendChild(row);
            } else {
                updateTable();
            }
        }


        function updateTable() {
            tableBody.innerHTML = "";
            let start = (currentPage - 1) * recordsPerPage;
            let end = start + recordsPerPage;
            let paginatedTransactions = transactionsData.slice(start, end);

            paginatedTransactions.forEach(tx => {
                const row = document.createElement("tr");

                let bayarKepadaDisplay = tx.bayarKepada ? `${tx.bayarKepada} (${tx.catatan || '-'})` : '';
                let terimaDaripadaDisplay = tx.bayarKepada ? '' : `${tx.terimaDaripada || '-'} (${tx.catatan || '-'})`;

                let tunaiAmount = tx.caraBayaran === "Tunai" ? tx.jumlah : "0.00";
                let bankAmount = tx.caraBayaran === "Bank" ? tx.jumlah : "0.00";

                row.innerHTML = `
                    <td>${tx.tarikh}</td>
                    <td>${tx.refNo}</td>
                    <td>${bayarKepadaDisplay || terimaDaripadaDisplay}</td>
                    <td>${tunaiAmount}</td>
                    <td>${bankAmount}</td>
                `;

                tableBody.appendChild(row);
            });

            updatePagination();
        }

        function updatePagination() {
            const totalPages = Math.ceil(transactionsData.length / recordsPerPage);
            document.getElementById("pageInfo").textContent = `Halaman ${currentPage} daripada ${totalPages || 1}`;
            document.getElementById("prevPage").disabled = currentPage === 1;
            document.getElementById("nextPage").disabled = currentPage === totalPages || totalPages === 0;
        }
    });

    function changePage(step) {
        currentPage += step;
        updateTable();
    }



function openTransaksiWindow() {
    window.open("transaksi.php", "TransaksiWindow", "width=1100,height=600");
}

function openAkaunWindow() {
    window.open("akaun.php", "AkaunWindow", "width=1100,height=600");
}

function openPembayaranWindow() {
    window.open("pembayaran.php", "PembayaranWindow", "width=1100,height=600");
}

function openPerolehanWindow() {
    window.open("perolehan.php", "PerolehanWindow", "width=1100,height=600");
}

function logout() {
    if (confirm("Adakah anda pasti mahu log keluar?")) {
        window.location.href = " ../login/index.php"; // Redirect to logout.php
    }
}
function printTable() {
    const accountSelect = document.getElementById("account-select");
    const selectedAccount = accountSelect.value;

    if (!selectedAccount) {
        alert("Sila pilih akaun sebelum mencetak.");
        return;
    }

    // Retrieve transactions correctly
    const pembayaranTransaksi = JSON.parse(localStorage.getItem("pembayaran_transaksi")) || {};
    const perolehanTransaksi = JSON.parse(localStorage.getItem("perolehan_transaksi")) || {};

    let pembayaranData = pembayaranTransaksi[selectedAccount] || [];
    let perolehanData = perolehanTransaksi[selectedAccount] || [];

    // Merge both datasets
    let filteredTransactions = [...pembayaranData, ...perolehanData];

    if (filteredTransactions.length === 0) {
        alert("Tiada rekod untuk dicetak.");
        return;
    }

    // Set selected account name in the print header
    document.getElementById("print-header").textContent = `Rekod Akaun: ${selectedAccount}`;

    // Add print-mode class to hide elements during print
    document.body.classList.add("print-mode");

    // Trigger print
    window.print();

    // Remove the class after printing to restore visibility
    document.body.classList.remove("print-mode");
}









    </script>
</body>
</html>
