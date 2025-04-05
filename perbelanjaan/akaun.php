<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Transfer Akaun</title>
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
                    <th>Transfer Dari</th>
                    <th>Akaun Penerima</th>
                    <th>Catatan</th>
                    <th>Jumlah</th>
                    
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
                <label>Sila Pilih Akaun Penerimaan:</label>
                <select id="akaunPenerima">
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

           

            <button class="btn-save" onclick="simpanData()">Simpan</button>
            <button class="btn-save" style="background: red; margin-top: 10px;" onclick="clearData()">Clear Data</button>
        </div>
    </div>

    <script>
document.addEventListener("DOMContentLoaded", function () {
    loadTransactions();
});

// Initialize Reference Number from localStorage or set default
let refNoCounter = localStorage.getItem("refNoCounter") 
    ? parseInt(localStorage.getItem("refNoCounter")) 
    : 1000001;

document.getElementById("refNo").value = refNoCounter;

function simpanData() {
    let tarikh = document.getElementById("tarikh").value;
    let refNo = refNoCounter;
    let transferDari = document.getElementById("transferDari").value;
    let akaunPenerima = document.getElementById("akaunPenerima").value;
    let catatan = document.getElementById("catatan").value;
    let jumlah = document.getElementById("jumlah").value;
   

    if (!tarikh || !transferDari || !akaunPenerima || !catatan || !jumlah) {
        alert("Sila isi semua maklumat sebelum menyimpan!");
        return;
    }

    let transaction = {
        tarikh, refNo, transferDari, akaunPenerima, catatan, jumlah
    };

    // Get existing transactions from localStorage
    let transactions = localStorage.getItem("transactions")
        ? JSON.parse(localStorage.getItem("transactions"))
        : [];

    // Add new transaction
    transactions.push(transaction);
    localStorage.setItem("transactions", JSON.stringify(transactions));

    // Update reference number in localStorage
    refNoCounter++;
    localStorage.setItem("refNoCounter", refNoCounter);
    document.getElementById("refNo").value = refNoCounter;

    // Refresh the table
    loadTransactions();

    // Clear input fields after saving
    clearForm();
}
function clearData() {
    if (confirm("Adakah anda pasti ingin mengosongkan semua data?")) {
        localStorage.removeItem("transactions");  // Clear transactions data
        localStorage.removeItem("refNoCounter"); // Reset reference number counter
        document.getElementById("data-table").innerHTML = ""; // Clear table

        // Reset reference number to initial value
        refNoCounter = 1000001;
        document.getElementById("refNo").value = refNoCounter;

        alert("Data telah dikosongkan!");
    }
}


function loadTransactions() {
    let tableBody = document.getElementById("data-table");
    tableBody.innerHTML = ""; // Clear table

    let transactions = localStorage.getItem("transactions")
        ? JSON.parse(localStorage.getItem("transactions"))
        : [];

    transactions.forEach(data => {
        let newRow = tableBody.insertRow();
        newRow.innerHTML = `
            <td>${data.tarikh}</td>
            <td>${data.refNo}</td>
            <td>${data.transferDari}</td>
            <td>${data.akaunPenerima}</td>
            <td>${data.catatan}</td>
            <td>${data.jumlah}</td>
            
        `;
    });
}

function clearForm() {
    document.getElementById("transferDari").value = "";
    document.getElementById("akaunPenerima").value = "";
    document.getElementById("catatan").value = "";
    document.getElementById("jumlah").value = "";
   
}

function cariDanCetak() {
    let searchRefNo = document.getElementById("searchRefNo").value;
    let transactions = localStorage.getItem("transactions")
        ? JSON.parse(localStorage.getItem("transactions"))
        : [];

    let selectedData = transactions.find(data => data.refNo.toString() === searchRefNo);

    if (selectedData) {
        cetakResit(selectedData);
    } else {
        alert("No Rujukan tidak ditemui!");
    }
}

function cetakResit(data) {
    let receiptWindow = window.open("", "_blank");
    receiptWindow.document.write(`
        <html>
        <head>
            <title>Resit Transaksi</title>
            <style>
                body { font-family: Arial, sans-serif; padding: 20px; text-align: center; }
                .receipt-box { border: 1px solid #333; padding: 20px; width: 300px; margin: auto; }
                h2 { color: #3b71ca; }
                table { width: 100%; margin-top: 10px; border-collapse: collapse; }
                td { padding: 8px; border-bottom: 1px solid #ddd; text-align: left; }
                .print-btn { margin-top: 20px; padding: 10px 15px; background: #3b71ca; color: white; border: none; cursor: pointer; }
                .print-btn:hover { background: #2a5db0; }
            </style>
        </head>
        <body>
            <div class="receipt-box">
                <h2>PIBG SEK.MEN.AGAMA.FAUZI</h2>
                <h2><p>06900 YAN KEDAH</p></h2>
                <table>
                    <tr><td><strong>Tarikh:</strong></td><td>${data.tarikh}</td></tr>
                    <tr><td><strong>No Rujukan:</strong></td><td>${data.refNo}</td></tr>
                    <tr><td><strong>Transfer Dari:</strong></td><td>${data.transferDari}</td></tr>
                    <tr><td><strong>Akaun Penerima:</strong></td><td>${data.akaunPenerima}</td></tr>
                    <tr><td><strong>Catatan:</strong></td><td>${data.catatan}</td></tr>
                    <tr><td><strong>Jumlah:</strong></td><td>RM ${data.jumlah}</td></tr>
                   
                </table>
                <button class="print-btn" onclick="window.print()">Cetak</button>
            </div>
        </body>
        </html>
    `);
    receiptWindow.document.close();
}
</script>


</body>
</html>
