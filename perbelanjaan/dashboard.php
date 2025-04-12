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
                fgColor: { argb: 'FFFFA500' } // orange
            };
        }
    }
}




    const createSheetWithHeader = (sheetName, title, headers, openingBalance) => {
        const sheet = workbook.addWorksheet(sheetName);

        // Style helpers
        const boldCenter = { bold: true, alignment: { horizontal: 'center' } };
        const grayFill = { type: 'pattern', pattern: 'solid', fgColor: { argb: 'FFD9D9D9' } };
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

        // Style header
        headerRow.eachCell((cell, colNumber) => {
            if (colNumber > 1) {
                cell.font = boldCenter;
                cell.fill = grayFill;
                cell.border = thinBorder;
                cell.alignment = { horizontal: 'center' };
            }
        });

        // Style B/F row
        bfrRow.eachCell((cell, colNumber) => {
            if (colNumber > 1) cell.border = thinBorder;
        });

        // Adjust column width
        sheet.columns = [
            { width: 5 }, { width: 10 }, { width: 15 }, { width: 35 },
            { width: 15 }, { width: 15 }
        ];

        return sheet;
    };

    // Sheets
    const bankSheet = createSheetWithHeader(
        "BANK",
        "BANK",
        ["NO.", "TARIKH", "PERIHAL", "MASUK", "KELUAR (CEK)"],
        [10574.55, ""]
    );

    const pettySheet = createSheetWithHeader(
        "PETTYCASH",
        "PETTYCASH",
        ["NO.", "TARIKH", "PERIHAL", "MASUK", "KELUAR (TUNAI)"],
        [1689.77, ""]
    );

    

    const balanceSheet = workbook.addWorksheet("BalanceSheet");
    balanceSheet.addRow(["", "", "", "PENYATA AKAUN BERAKHIR PADA 31.12.2024"]);
    balanceSheet.addRow([]);
    balanceSheet.addRow([]);
    balanceSheet.addRow(["", "", "", "PENDAPATAN", "", "", "", "PERBELANJAAN"]);
    balanceSheet.addRow([
        "", "", "a.", "BAKI DALAM BANK BERAKHIR 31.12.2023", 10574.55,
        "", "a.", "PENGURUSAN PIBG", 7485.20
    ]);

    const incomeSheet = workbook.addWorksheet("INCOME");
    incomeSheet.addRow([]);
    incomeSheet.addRow([]);
    incomeSheet.addRow(["", "YURAN PIBG TAHUN 2024 - KUTIP PADA 2024"]);
    incomeSheet.addRow([]);
    incomeSheet.addRow(["", "", "", "", "TUNAI", "BANK", "JUMLAH", "", "", "TUNAI", "BANK", "JUMLAH"]);
    incomeSheet.addRow(["", "", "", "", "", "", "", "YURAN PIBG TAHUN 2025"]);

    // Save the file
    const buffer = await workbook.xlsx.writeBuffer();
    const blob = new Blob([buffer], { type: "application/vnd.openxmlformats-officedocument.spreadsheetml.sheet" });
    const link = document.createElement("a");
    link.href = URL.createObjectURL(blob);
    link.download = "Laporan_Kewangan_Format_Tepat.xlsx";
    document.body.appendChild(link);
    link.click();
    document.body.removeChild(link);
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
