<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pembayaran</title>
    <style>
    /* General Layout */
    body {
        font-family: Arial, sans-serif;
        margin: 0;
        padding: 20px;
        background-color: #f4f6f9;
        color: #333;
    }

    .container {
        max-width: 900px;
        margin: auto;
        background: white;
        padding: 20px;
        box-shadow: 0px 4px 8px rgba(0, 0, 0, 0.1);
        border-radius: 8px;
    }

    h2 { text-align: center; color: #3b71ca; }

    /* Table */
    table {
        width: 100%;
        border-collapse: collapse;
        margin-top: 20px;
        background: white;
        border-radius: 5px;
        overflow: hidden;
    }

    th, td {
        border: 1px solid #ddd;
        padding: 12px;
        text-align: left;
    }

    th {
        background: #3b71ca;
        color: white;
        text-transform: uppercase;
        position: sticky;
        top: 0;
    }

    tr:nth-child(even) {
        background-color: #f9f9f9;
    }

    /* Form Design */
    .form-container {
        background: #fff;
        padding: 20px;
        border-radius: 5px;
        box-shadow: 0px 2px 5px rgba(0, 0, 0, 0.1);
        margin-bottom: 20px;
    }

    .form-section {
        display: flex;
        align-items: center;
        gap: 15px;
        margin-bottom: 15px;
    }

    .form-section label {
        flex: 1;
        font-weight: bold;
        text-align: right;
        color: #333;
    }

    .form-section select, .form-section input {
        flex: 2;
        padding: 10px;
        font-size: 16px;
        border: 1px solid #ccc;
        border-radius: 5px;
    }

    /* Buttons */
    .btn-save {
        background: #3b71ca;
        color: white;
        border: none;
        padding: 12px 20px;
        cursor: pointer;
        font-size: 16px;
        border-radius: 5px;
        transition: background 0.3s;
        width: 100%;
        max-width: 200px;
        display: block;
        margin: auto;
    }

    .btn-save:hover {
        background: #2a5db0;
    }

    .btn-delete {
        background: red;
        color: white;
        padding: 5px 10px;
        border: none;
        cursor: pointer;
        border-radius: 4px;
    }

    /* Search Bar */
    .search-box {
        display: flex;
        justify-content: center;
        margin-bottom: 20px;
    }

    .search-box input {
        width: 250px;
        padding: 10px;
        border: 1px solid #ccc;
        border-radius: 5px;
    }

    .search-box button {
        margin-left: 10px;
    }

    /* Responsive */
    @media (max-width: 768px) {
        .form-section {
            flex-direction: column;
            align-items: flex-start;
        }
        .form-section label {
            text-align: left;
        }
    }
</style>
</head>
<body>

    <div class="container">
        <div class="search-box">
            <input type="text" id="searchRefNo" placeholder="Cari No Rujukan">
            <button class="btn-save" onclick="cariDanCetak()">Cari & Cetak</button>
        </div>

        <!-- Table -->
        <table>
            <thead>
                <tr>
                    <th>Tarikh</th>
                    <th>No Ref</th>
                    <th>Bayar Kepada</th>
                    <th>Akaun Pengeluaran</th>
                   <th>Catatan</th>
                    <th>Jumlah</th>
                    <th>Cara Bayaran</th>
                </tr>
            </thead>
            <tbody id="data-table">
                <!-- Data will be inserted here -->
            </tbody>
        </table>

        <!-- Input Form -->
        <div class="form-container">
            <div class="form-section">
                <label>Tarikh:</label>
                <input type="date" id="tarikh" value="">
            </div>

            <div class="form-section">
                <label>Ref. No:</label>
                <input type="text" id="refNo" value="" readonly>
            </div>

            <div class="form-section">
                <label>Bayar Kepada:</label>
                <input type="text" id="bayarKepada" placeholder="Masukkan nama penerima">
            </div>
           

            <div class="form-section">
                <label>Sila Pilih Akaun Pengeluaran:</label>
                <select id="transferDari">
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
            </div>

            <div class="form-section">
                <label>Catatan:</label>
                <input type="text" id="catatan" placeholder="Masukkan catatan">
            </div>

            <div class="form-section">
                <label>Jumlah:</label>
                <input type="text" id="jumlah" placeholder="Masukkan jumlah">
            </div>

            <div class="form-section">
                <label>Cara Bayaran:</label>
                <select id="caraBayaran">
                    <option value="">-- Pilih Cara Bayaran --</option>
                    <option value="Bank">Bank</option>
                    <option value="Tunai">Tunai</option>
                </select>
            </div>

            <button class="btn-save" onclick="simpanData('akaun')">Simpan </button>
            <button class="btn-save" style="background: red; margin-top: 10px;" onclick="clearData()">Clear Data</button>

        </div>
    </div>

    <script>
    let refNoCounter = parseInt(localStorage.getItem("refNoCounter")) || 1000001;
    document.getElementById("refNo").value = refNoCounter;

    function simpanData() {
    let tarikh = document.getElementById("tarikh").value;
    let refNo = refNoCounter.toString(); // Ensure it's stored as a string
    let bayarKepada = document.getElementById("bayarKepada").value;
    let transferDari = document.getElementById("transferDari").value;
    let catatan = document.getElementById("catatan").value;
    let jumlah = document.getElementById("jumlah").value;
    let caraBayaran = document.getElementById("caraBayaran").value;

    if (!tarikh || !transferDari || !catatan || !jumlah || !bayarKepada || !caraBayaran) {
        alert("Sila isi semua maklumat sebelum menyimpan!");
        return;
    }

    let pembayaran = JSON.parse(localStorage.getItem("pembayaran_transaksi")) || {};

    if (!pembayaran[transferDari]) {
        pembayaran[transferDari] = [];
    }

    pembayaran[transferDari].push({ tarikh, refNo, bayarKepada, transferDari, jumlah, caraBayaran, catatan });

    localStorage.setItem("pembayaran_transaksi", JSON.stringify(pembayaran));
    localStorage.setItem("refNoCounter", refNoCounter + 1);

    refNoCounter++;
    document.getElementById("refNo").value = refNoCounter;

    loadTable();
    alert("Data berjaya disimpan!");
}

function loadTable() {
    let tableBody = document.getElementById("data-table");
    tableBody.innerHTML = "";

    let pembayaran = JSON.parse(localStorage.getItem("pembayaran_transaksi")) || {};

    Object.keys(pembayaran).forEach(account => {
        pembayaran[account].forEach(item => {
            let newRow = tableBody.insertRow();
            newRow.innerHTML = `
                <td>${item.tarikh}</td>
                <td>${item.refNo}</td>
                <td>${item.bayarKepada}</td>
                <td>${item.transferDari}</td> 
                <td>${item.catatan}</td>
                <td>${item.jumlah}</td>
                <td>${item.caraBayaran}</td>
            `;
        });
    });
}

window.onload = function() {
    loadTable();
    document.getElementById("refNo").value = refNoCounter;
};

function clearData() {
    let confirmation = prompt("Taip 'DELETE' untuk mengesahkan pengosongan data:");

    if (confirmation === "DELETE") {
        if (confirm("Adakah anda benar-benar pasti ingin mengosongkan semua data? Tindakan ini tidak boleh dibuat asal!")) {
            
            // Backup before deletion (optional)
            let backupData = {
                pembayaran_transaksi: localStorage.getItem("pembayaran_transaksi"),
                transaksi: localStorage.getItem("transaksi"),
                refNoCounter: localStorage.getItem("refNoCounter")
            };
            localStorage.setItem("backup_data", JSON.stringify(backupData));

            // Proceed with deletion
            localStorage.removeItem("pembayaran_transaksi");
            localStorage.removeItem("transaksi");
            localStorage.removeItem("refNoCounter");

            // Reset reference number counter
            refNoCounter = 1000001;
            document.getElementById("refNo").value = refNoCounter;

            // Clear table UI and reset transactionsData array
            transactionsData = [];
            let tableBody = document.querySelector("tbody");
            tableBody.innerHTML = "";

            alert("Data telah dikosongkan!");

            // Optional: Reset dropdown selection
            document.getElementById("account-select").value = "";

            // Reload the page to ensure all data is removed from memory
            location.reload();
        }
    } else {
        alert("Pengosongan data dibatalkan.");
    }
}



function cariDanCetak() {
    let searchRefNo = document.getElementById("searchRefNo").value;
    let transaksiObj = JSON.parse(localStorage.getItem("pembayaran_transaksi")) || {};

    // Flatten object into an array
    let transaksi = Object.values(transaksiObj).flat();

    let found = transaksi.find(item => item.refNo == searchRefNo);

    if (found) {
        let receiptContent = `
            <div id="receipt-section">
                <div style="border: 1px solid #333; padding: 20px; width: 300px; margin: auto; text-align: center;">
                    <h2 style="color: #3b71ca;">PIBG SEK.MEN.AGAMA.FAUZI</h2>
                    <h3>06900 YAN KEDAH</h3>
                    <table style="width: 100%; margin-top: 10px; border-collapse: collapse;">
                        <tr><td><strong>Tarikh:</strong></td><td>${found.tarikh}</td></tr>
                        <tr><td><strong>No Rujukan:</strong></td><td>${found.refNo}</td></tr>
                        <tr><td><strong>Bayar Kepada:</strong></td><td>${found.bayarKepada}</td></tr>
                        <tr><td><strong>Transfer Dari:</strong></td><td>${found.transferDari}</td></tr>
                        <tr><td><strong>Catatan:</strong></td><td>${found.catatan}</td></tr>
                        <tr><td><strong>Jumlah:</strong></td><td>RM ${found.jumlah}</td></tr>
                        <tr><td><strong>Cara Bayaran:</strong></td><td>${found.caraBayaran}</td></tr>
                    </table>
                </div>
            </div>
        `;

        let printWindow = window.open("", "_blank");
        printWindow.document.write(`
            <html>
            <head>
                <title>Cetak Resit</title>
                <style>
                    body { font-family: Arial, sans-serif; padding: 20px; text-align: center; }
                    .receipt-box { border: 1px solid #333; padding: 20px; width: 300px; margin: auto; }
                    h2 { color: #3b71ca; }
                    table { width: 100%; margin-top: 10px; border-collapse: collapse; }
                    td { padding: 8px; border-bottom: 1px solid #ddd; text-align: left; }
                </style>
            </head>
            <body>
                ${receiptContent}
                <script>
                    window.onload = function() {
                        window.print();
                        window.onafterprint = function() { window.close(); };
                    };
                <\/script>
            </body>
            </html>
        `);
        printWindow.document.close();

    } else {
        alert("No Rujukan tidak ditemui!");
    }
}



</script>


</body>
</html>
