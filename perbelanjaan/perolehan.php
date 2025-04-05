<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Perolehan</title>
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
                    <th>Terima Daripada</th>
                    <th>Catatan</th>
                    <th>Akaun Pendapatan</th>
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
                <label>Terima Daripada:</label>
                <input type="text" id="terimaDaripada" placeholder="Masukkan nama penerima">
            </div>

            <div class="form-section">
                <label>Catatan:</label>
                <input type="text" id="catatan" placeholder="Masukkan catatan">
            </div>

            <div class="form-section">
                <label>Sila Pilih Akaun Pendapatan:</label>
                <select id="transferDari">
                    <option value="">SILA PILIH</option>
                    <option value="BANK(CAJ & HIBAH)">BANK(CAJ & HIBAH)</option>
                    <option value="LAIN-LAIN">LAIN-LAIN</option>
                </select>
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

            <button class="btn-save" onclick="simpanData()">Simpan</button>
            <button class="btn-save" style="background: red; margin-top: 10px;" onclick="clearData()">Clear Data</button>
        </div>
    </div>

    <script>
    let refNoCounter = parseInt(localStorage.getItem("refNoCounter")) || 1000001;


    document.addEventListener("DOMContentLoaded", function () {
        loadSavedData();
    });

    function simpanData() {
    let tarikh = document.getElementById("tarikh").value;
    let refNo = refNoCounter;
    let terimaDaripada = document.getElementById("terimaDaripada").value;
    let catatan = document.getElementById("catatan").value;
    let akaunPendapatan = document.getElementById("transferDari").value;
    let jumlah = document.getElementById("jumlah").value;
    let caraBayaran = document.getElementById("caraBayaran").value;

    // Validate required fields
    if (!tarikh || !akaunPendapatan || !jumlah || !terimaDaripada || !caraBayaran) {
        alert("Sila isi semua maklumat sebelum menyimpan!");
        return;
    }

    // Validate jumlah (must be a number and greater than zero)
    if (isNaN(jumlah) || jumlah <= 0) {
        alert("Jumlah mestilah nombor yang sah!");
        return;
    }

    let transaksi = JSON.parse(localStorage.getItem("perolehan_transaksi")) || {};
    
    // Ensure akaunPendapatan key exists
    if (!transaksi[akaunPendapatan]) {
        transaksi[akaunPendapatan] = [];
    }

    transaksi[akaunPendapatan].push({ tarikh, refNo, terimaDaripada, jumlah, caraBayaran, catatan });

    // Save updated data
    localStorage.setItem("perolehan_transaksi", JSON.stringify(transaksi));

    let newEntry = {
        tarikh,
        refNo,
        terimaDaripada,
        catatan,
        akaunPendapatan,
        jumlah,
        caraBayaran
    };

    let savedData = JSON.parse(localStorage.getItem("paymentRecords")) || [];
    savedData.push(newEntry);
    localStorage.setItem("paymentRecords", JSON.stringify(savedData));

    insertRow(newEntry);

    refNoCounter++;
    document.getElementById("refNo").value = refNoCounter;

    // Reset form fields
    document.getElementById("terimaDaripada").value = "";
    document.getElementById("catatan").value = "";
    document.getElementById("jumlah").value = "";
    document.getElementById("caraBayaran").selectedIndex = 0;  // Reset dropdown
    document.getElementById("transferDari").selectedIndex = 0;  // Reset dropdown
}


function insertRow(data) {
    let tableBody = document.getElementById("data-table");
    let newRow = tableBody.insertRow();
    newRow.innerHTML = `
        <td>${data.tarikh}</td>
        <td>${data.refNo}</td>
        <td>${data.terimaDaripada}</td>
        <td>${data.catatan}</td>
        <td>${data.akaunPendapatan}</td>
        <td>${data.jumlah}</td>
        <td>${data.caraBayaran}</td>
    `;
}


    function loadSavedData() {
        let savedData = JSON.parse(localStorage.getItem("paymentRecords")) || [];
        savedData.forEach(entry => {
            insertRow(entry);
        });

        if (savedData.length > 0) {
            refNoCounter = savedData[savedData.length - 1].refNo + 1;
        }
        document.getElementById("refNo").value = refNoCounter;
    }

   function clearData() {
    let confirmation = prompt("Taip 'DELETE' untuk mengesahkan pengosongan data:");

    if (confirmation === "DELETE") {
        if (confirm("Adakah anda benar-benar pasti ingin mengosongkan semua data? Tindakan ini tidak boleh dibuat asal!")) {
            
            // Backup data before deletion (optional)
            let backupData = {
                paymentRecords: localStorage.getItem("paymentRecords"),
                refNoCounter: localStorage.getItem("refNoCounter"),
                perolehan_transaksi: localStorage.getItem("perolehan_transaksi")
            };
            localStorage.setItem("backup_data", JSON.stringify(backupData));

            // Proceed with data clearing
            localStorage.removeItem("paymentRecords");
            localStorage.removeItem("refNoCounter");
            localStorage.removeItem("perolehan_transaksi");

            /// Reset reference number counter
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
    let transaksi = JSON.parse(localStorage.getItem("paymentRecords")) || [];

    let found = transaksi.find(item => item.refNo == searchRefNo);

    if (found) {
        let receiptContent = `
            <div style="border: 1px solid #333; padding: 20px; width: 300px; margin: auto; text-align: center;">
                <h2 style="color: #3b71ca;">PIBG SEK. MEN. AGAMA FAUZI</h2>
                <h3>06900 YAN, KEDAH</h3>
                <table style="width: 100%; margin-top: 10px; border-collapse: collapse;">
                    <tr><td><strong>Tarikh:</strong></td><td>${found.tarikh}</td></tr>
                    <tr><td><strong>No Rujukan:</strong></td><td>${found.refNo}</td></tr>
                    <tr><td><strong>Terima Daripada:</strong></td><td>${found.terimaDaripada}</td></tr>
                    <tr><td><strong>Catatan:</strong></td><td>${found.catatan}</td></tr>
                    <tr><td><strong>Akaun Pendapatan:</strong></td><td>${found.akaunPendapatan}</td></tr>
                    <tr><td><strong>Jumlah:</strong></td><td>RM ${found.jumlah}</td></tr>
                    <tr><td><strong>Cara Bayaran:</strong></td><td>${found.caraBayaran}</td></tr>
                </table>
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
