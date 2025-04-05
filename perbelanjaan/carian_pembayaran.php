<!DOCTYPE html>
<html lang="ms">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Carian Rekod Pembayaran</title>
    <style>
        body {
            font-family: 'Arial', sans-serif;
            background-color: #f4f4f4;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
        }

        .container {
            background: white;
            padding: 20px;
            width: 600px; /* Increased width */
            border-radius: 8px;
            box-shadow: 0px 4px 10px rgba(0, 0, 0, 0.1);
        }

        .search-section {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 10px;
            margin-bottom: 15px;
        }

        input[type="text"] {
            flex: 1;
            padding: 8px;
            font-size: 14px;
            border: 1px solid #ccc;
            border-radius: 5px;
            outline: none;
        }

        .search-btn {
            padding: 8px 15px;
            border: none;
            background-color: #007bff;
            color: white;
            cursor: pointer;
            font-weight: bold;
            border-radius: 5px;
            transition: 0.3s;
        }

        .search-btn:hover {
            background-color: #0056b3;
        }

        .tahun-section {
            display: flex;
            align-items: center;
            gap: 15px;
        }

        .tahun-section label {
            font-weight: bold;
        }

        .tahun-section input[type="radio"] {
            margin-left: 5px;
        }

        .tahun-section span {
            color: red;
            font-weight: bold;
        }

        .labels div {
            font-weight: bold;
            margin-bottom: 5px;
        }

        .table-section {
            margin-top: 10px;
            background: #f9f9f9;
            padding: 10px;
            border-radius: 5px;
            border: 1px solid #ddd;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        th, td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: center;
        }

        th {
            background-color: #007bff;
            color: white;
        }

        .footer {
            display: flex;
            justify-content: flex-end;
            margin-top: 10px;
        }

        .print-btn {
            padding: 8px 15px;
            border: none;
            background-color: #28a745;
            color: white;
            cursor: pointer;
            font-weight: bold;
            border-radius: 5px;
            transition: 0.3s;
        }

        .print-btn:hover {
            background-color: #218838;
        }
    </style>
</head>
<body>

    <div class="container">
        <!-- Search Section with Tahun Selection -->
        <div class="search-section">
            <input type="text" id="searchQuery" placeholder="Masukkan kata kunci...">
            <button class="search-btn" onclick="searchRecords()">Cari</button>
            <div class="tahun-section">
                <label>Tahun:</label>
                <input type="radio" name="tahun" value="2025" id="tahun2025"> <span>2025</span>
                <input type="radio" name="tahun" value="2026" id="tahun2026"> <span>2026</span>
            </div>
        </div>

        <!-- Labels -->
        <div class="labels">
            <div>Database</div>
            <div>Tarikh</div>
            <div>Perkara</div>
        </div>

        <!-- Table -->
        <div class="table-section">
            <table>
                <thead>
                    <tr>
                        <th>Tunai</th>
                        <th>Bank</th>
                    </tr>
                </thead>
                <tbody id="resultsTable">
                    <!-- Dynamic Data Here -->
                </tbody>
            </table>
        </div>

        <!-- Footer -->
        <div class="footer">
            <button class="print-btn" onclick="printRecords()">Print</button>
        </div>
    </div>

    <script>
        function searchRecords() {
            let query = document.getElementById("searchQuery").value.trim();
            let selectedYear = document.querySelector('input[name="tahun"]:checked');
            let year = selectedYear ? selectedYear.value : "Semua Tahun";

            // Simulate search results
            let resultsTable = document.getElementById("resultsTable");
            resultsTable.innerHTML = ""; // Clear previous results

            if (query === "") {
                alert("Sila masukkan kata kunci carian!");
                return;
            }

            let newRow = `
                <tr>
                    <td>RM 100</td>
                    <td>RM 250</td>
                </tr>
            `;

            resultsTable.innerHTML = newRow;
            alert("Carian untuk '" + query + "' dalam tahun " + year);
        }

        function printRecords() {
            window.print();
        }
    </script>

</body>
</html>
