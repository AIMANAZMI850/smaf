<!DOCTYPE html>
<html lang="ms">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Transfer Bank <-> Tunai</title>
    <style>
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

    .form-container {
    background: #fff;
    padding: 20px;
    border-radius: 8px;
    box-shadow: 0px 2px 5px rgba(0, 0, 0, 0.1);
    margin-bottom: 100px;
    display: flex;
    flex-direction: column;
    gap: 10px; /* Adds spacing between fields */
}

.form-container label {
    font-weight: bold;
    color: #333;
    display: flex;
    flex-direction: column;
    font-size: 14px;
}

.form-container input {
    width: 95%;
    padding: 10px;
    font-size: 16px;
    border: 1px solid #ccc;
    border-radius: 6px;
    outline: none;
    transition: border-color 0.3s ease-in-out;
}

.form-container input:focus {
    border-color: #3b71ca;
    box-shadow: 0 0 5px rgba(59, 113, 202, 0.5);
}


    .btn-save{
    background: #3b71ca;
    color: white;
    border: none;
    padding: 12px 20px;
    cursor: pointer;
    font-size: 16px;
    border-radius: 5px;
    transition: background 0.3s;
    display: block;
    margin-top: 15px; /* Adds space below input fields */
    margin-left: auto;
    margin-right: auto;
    width: 100%;
    max-width: 200px;
    text-align: center;
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
    /* Transaction Summary */
.total {
    text-align: center;
    font-weight: bold;
    margin-top: 20px;
}

.total label {
    margin-right: 10px;
    font-size: 16px;
    cursor: pointer;
}

.total span {
    font-size: 16px;
    font-weight: bold;
    color: red;
    display: block;
    margin-top: 5px;
}
    </style>
</head>
<body>

<h3>Transfer Bank <-> Tunai</h3>
<table>
    <thead>
        <tr>
            <th>Tarikh</th>
            <th>No Ref</th>
            <th>Perkara</th>
            <th>Jumlah</th>
        </tr>
    </thead>
    <tbody id="transaksi-table">
         <!-- Data will be inserted here -->
    </tbody>
</table>

<p class="total">
    <label>
        <input type="radio" name="transaksi" value="Dari Bank Pindah Ke Tunai -" onclick="setPerkara(this.value)"> 
        Dari Bank Pindah Ke Tunai
    </label>
    <label>
        <input type="radio" name="transaksi" value="Dari Tunai Pindah Ke Bank -" onclick="setPerkara(this.value)"> 
        Dari Tunai Pindah Ke Bank
    </label>
    <span style="color: red;" id="bank-tunai">Bank-Tunai 0.00</span>
    <span style="color: red;" id="tunai-bank">Tunai-Bank 0.00</span>
</p>


<div class="form-container">
    <label>Tarikh: <input type="date" id="tarikh"></label>
    <label>No. Resit: <input type="text" id="no-resit" value=""></label>
    <label>Ref. No: <input type="text" id="ref-no"></label>
    <label>Perkara:
    <input type="text" id="perkara" readonly>
</label>

    <label>Jumlah: <input type="text" id="jumlah"></label>
    <button class="btn-save" onclick="simpanTransaksi()">Simpan</button>
</div>

<script>
    function setPerkara(value) {
        document.getElementById("perkara").value = value;
    }

    function simpanTransaksi() {
    let tarikh = document.getElementById("tarikh").value;
    let refNo = document.getElementById("ref-no").value;
    let perkara = document.getElementById("perkara").value;
    let jumlah = parseFloat(document.getElementById("jumlah").value) || 0;

    if (!tarikh || !refNo || !perkara || jumlah <= 0) {
        alert("Sila isi semua maklumat sebelum menyimpan!");
        return;
    }

    // Get existing transactions from localStorage or initialize an empty array
    let transaksi = JSON.parse(localStorage.getItem("transaksi")) || [];

    // Add new transaction
    transaksi.push({ tarikh, refNo, perkara, jumlah });

    // Save back to localStorage
    localStorage.setItem("transaksi", JSON.stringify(transaksi));

    // Reload table with new data
    loadTransaksi();
}

function loadTransaksi() {
    let transaksi = JSON.parse(localStorage.getItem("transaksi")) || [];
    let table = document.getElementById("transaksi-table");
    table.innerHTML = ""; // Clear table before loading data

    transaksi.forEach(data => {
        let newRow = table.insertRow();
        newRow.innerHTML = `
            <td>${data.tarikh}</td>
            <td>${data.refNo}</td>
            <td>${data.perkara}</td>
            <td>${data.jumlah.toFixed(2)}</td>
        `;
    });

    updateTotals();
}

// Run function when the page loads
window.onload = loadTransaksi;


window.onload = loadTransaksi; // Load stored data when page loads
function updateTotals() {
    let transaksi = JSON.parse(localStorage.getItem("transaksi")) || [];
    let bankTunaiTotal = 0, tunaiBankTotal = 0;

    transaksi.forEach(data => {
        if (data.perkara.includes("Dari Bank Pindah Ke Tunai")) {
            bankTunaiTotal += data.jumlah;
        } else if (data.perkara.includes("Dari Tunai Pindah Ke Bank")) {
            tunaiBankTotal += data.jumlah;
        }
    });

    document.getElementById("bank-tunai").innerHTML = `Bank-Tunai ${bankTunaiTotal.toFixed(2)}`;
    document.getElementById("tunai-bank").innerHTML = `Tunai-Bank ${tunaiBankTotal.toFixed(2)}`;
}

</script>


</body>
</html>
