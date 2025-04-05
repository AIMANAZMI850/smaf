<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sistem Bayaran Yuran PIBG SMAF</title>
    <link rel="stylesheet" href="styles.css">
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color:rgb(207, 237, 247);
            margin: 0;
            padding: 20px;
        }
        .container {
            max-width: 950px;
            background: white;
            padding: 20px;
            margin: auto;
            border-radius: 8px;
            box-shadow: 10px 10px 10px gray;
        }
        h2 {
            text-align: center;
            color: #333;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
        }
        table, th, td {
            border: 2px solid black;
        }
        th, td {
            padding: 10px;
            text-align: center;
        }
        th {
            background-color: #007bff;
            color: white;
        }
        .bank-info, .invoice-section {
            margin: 20px 20px;
        }
        .bank-info input {
            width: 100%;
            padding: 8px;
            font-size: 16px;
        }

        .controls {
            display: flex;
            justify-content: space-between;
        }
        button {
            background-color: #007bff;
            color: white;
            border: none;
            padding: 10px 15px;
            cursor: pointer;
            border-radius: 5px;
        }
        button:hover {
            background-color: #0056b3;
        }
        .invoice-section {
            font-weight: bold;
            color: red;
        }
        .sidebar {
            width: 220px;
            background: #2c3e50;
            height: 100vh;
            padding-top: 20px;
            position: fixed;
            left: -220px;
            top: 0;
            transition: left 0.3s ease-in-out;
            text-align: center;
        }

        .sidebar.open {
            left: 0;
        }

        .sidebar.open .toggle-btn {
            right: 170px;
        }

        .sidebar a {
            display: block;
            padding: 15px;
            color: white;
            text-decoration: none;
            font-size: 16px;
            transition: 0.3s;
            background: #34495e;
            border-radius: 5px;
            width: 90%;
            margin: 10px auto;
            text-align: center;
            box-sizing: border-box;
        }

        .sidebar a:hover {
            background: #1abc9c;
        }

        .toggle-btn {
            position: absolute;
            top: 10px;
            right: -50px;
            background: #2c3e50;
            color: white;
            padding: 10px 15px;
            border: none;
            cursor: pointer;
            border-radius: 5px;
            z-index: 1000;
            transition: right 0.3s ease-in-out;
        }

        .container.shift {
            margin-left: 230px;
            width: calc(100% - 230px);
        }

        .sidebar-logo {
            width: 100px; /* Adjust size as needed */
            margin-bottom: 20px;
            transition: opacity 0.3s ease-in-out;
        }

    </style>
</head>
<body>
    <div class="container">
        <h2>Yuran Pelajar</h2>
        <table>
            <thead>
                <tr>
                    <th>Yuran</th>
                    <th>Jumlah (RM)</th>
                    <th>1</th>
                    <th>2</th>
                    <th>3</th>
                    <th>4</th>
                    <th>5</th>
                    <th>6</th>
                    <th>Asrama</th>
                    <th>X Asrama</th>
                    <th>Jelas Oleh</th>
                    <th>Kumpulan</th>
                    <th>Data Yuran</th>
                </tr>
            </thead>
            <tbody id="yuranTableBody">
                <!-- Rows will be inserted here by JavaScript -->
            </tbody>
        </table>

        <div class="bank-info">
            <label for="bankName">Nama Bank:</label>
            <input type="text" id="bankName" value="BANK ISLAM CAWANGAN GUAR CHEMPEDAK" readonly>
        </div>

        <div class="invoice-section">
            <label>Auto Print Resit Bayaran</label>
            <button id="printReceipt">Print</button>
            <br>
            <label>No Invoice: </label>
            <span id="invoiceNumber">1004581</span> <!-- Static Version -->
            <!-- <span id="invoiceNumber">(Generated by JS)</span> --> <!-- Uncomment for Dynamic Version -->
            <br>
            <label for="portSMS">Port SMS:</label>
            <input type="text" id="portSMS" value="0">
        </div>

        <div class="controls">
            <button id="updateButton">Kemaskini</button>
            <button id="colorButton">Warna</button>
            <button id="registerButton">Daftar Sekolah</button>
        </div>
        
        <div class="sidebar" id="sidebar">
            <button class="toggle-btn" onclick="toggleSidebar()">☰</button>
            <img src="../images/logo.jpg" id="sidebar-logo" class="sidebar-logo" alt="Logo">
           
            <a href="daftar_pelajar.php" class="btn">DAFTAR PELAJAR</a>
            <a href="kemaskini_pelajar.php" class="btn">KEMASKINI PELAJAR</a>
            <a href="bayaran.php" class="btn">BAYARAN</a>
           
            <a href="setting.php" class="btn">SETTING</a>
            <a href="../logout/logout.php" class="btn btn-red">LOG KELUAR</a>
        </div>

    </div>

    <script>
        function toggleSidebar() {
            let sidebar = document.getElementById("sidebar");
            let logo = document.getElementById("sidebar-logo");
            let container = document.querySelector(".container");

            if (sidebar.classList.contains("open")) {
                sidebar.classList.remove("open");
                container.classList.remove("shift");
                logo.style.opacity = "0"; // Hide logo
            } else {
                sidebar.classList.add("open");
                container.classList.add("shift");
                logo.style.opacity = "1"; // Show logo
            }
        }
        document.getElementById("printReceipt").addEventListener("click", function() {
            window.print(); // This will open the print dialog
        });

        document.addEventListener("DOMContentLoaded", function() {
            const yuranData = [
                { yuran: "DANA PIBG", jumlah: 30.00, values: [1,1,1,1,1,1], asrama: 1, x_asrama: 1, jelas: 1, kumpulan: "DANA PIBG", dataYuran: "Y_DANA" },
                { yuran: "TUISYEN TING 1-2", jumlah: 50.00, values: [1,1,0,0,0,0], asrama: 1, x_asrama: 1, jelas: 0, kumpulan: "PEMBANGUNAN", dataYuran: "Y_PEMBANGUNAN" },
                { yuran: "TUISYEN TING 3-5", jumlah: 60.00, values: [0,0,1,1,1,0], asrama: 1, x_asrama: 1, jelas: 0, kumpulan: "PEMBANGUNAN", dataYuran: "Y_PEMBANGUNAN" },
                { yuran: "TUISYEN TING 6", jumlah: 60.00, values: [0,0,0,0,0,1], asrama: 1, x_asrama: 1, jelas: 0, kumpulan: "PEMBANGUNAN", dataYuran: "Y_PEMBANGUNAN" },
                { yuran: "MASSAK", jumlah: 10.00, values: [1,1,1,1,1,1], asrama: 1, x_asrama: 1, jelas: 0, kumpulan: "MASSAK", dataYuran: "Y_MASSAK" },
                { yuran: "MAJALAH", jumlah: 0.00, values: [0,0,0,0,0,0], asrama: 0, x_asrama: 0, jelas: 0, kumpulan: "MAJALAH", dataYuran: "Y_MAJALAH" },
                { yuran: "HAC", jumlah: 10.00, values: [1,1,1,1,1,1], asrama: 1, x_asrama: 1, jelas: 0, kumpulan: "HAC", dataYuran: "Y_HAC" },
                { yuran: "KERTAS PEPERIKSAAN", jumlah: 0.00, values: [0,0,0,0,0,0], asrama: 0, x_asrama: 0, jelas: 0, kumpulan: "KERTAS PEPERIKSAAN", dataYuran: "Y_KERTAS" },
                { yuran: "BAS", jumlah: 260.00, values: [1,1,1,1,1,0], asrama: 1, x_asrama: 0, jelas: 0, kumpulan: "BAS", dataYuran: "Y_BAS" },
                { yuran: "DOBI", jumlah: 160.00, values: [1,1,1,1,1,0], asrama: 1, x_asrama: 0, jelas: 0, kumpulan: "DOBI", dataYuran: "Y_DOBI" }
            ];

            const tableBody = document.getElementById("yuranTableBody");
            yuranData.forEach(data => {
                const row = document.createElement("tr");
                row.innerHTML = `<td>${data.yuran}</td><td contenteditable="true">${data.jumlah.toFixed(2)}</td>${data.values.map(value => `<td>${value}</td>`).join('')}<td>${data.asrama}</td><td>${data.x_asrama}</td><td>${data.jelas}</td><td>${data.kumpulan}</td><td>${data.dataYuran}</td>`;
                tableBody.appendChild(row);
            });
        });
    </script>
</body>
</html>
