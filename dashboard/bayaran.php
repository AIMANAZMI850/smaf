<?php

function getTotalPaidBySiblings($studentId) {
    global $conn;
    
    // Get the student's guardian name
    $query = "SELECT namaWarisPelajar FROM daftar_pelajar WHERE noKad = (SELECT noKad FROM bayaran WHERE id = $studentId)";
    $result = mysqli_query($conn, $query);
    if ($result && mysqli_num_rows($result) > 0) {
        $row = mysqli_fetch_assoc($result);
        $guardianName = $row['namaWarisPelajar'];
        
        // Get the total amount paid by siblings (same guardian)
        $query = "
            SELECT SUM(b.jum_bayar_dana_pibg) AS total_paid
            FROM bayaran b
            JOIN daftar_pelajar dp ON b.noKad = dp.noKad
            WHERE dp.namaWarisPelajar = '$guardianName'
        ";
        $result = mysqli_query($conn, $query);
        $row = mysqli_fetch_assoc($result);
        
        return floatval($row['total_paid']);
    }
    
    return 0;
}

include '../db_connection/db.php'; // Include your database connection

// Set number of records per page
$records_per_page = 50;

// Get the current page number
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
if ($page < 1) $page = 1;

// Calculate the starting point
$start_from = ($page - 1) * $records_per_page;

// Fetch student data with pagination and calculate total payment made by siblings
// Fetch student data with pagination and calculate total payment made by siblings
// Define the total query for counting the rows
$total_query = "
    SELECT COUNT(*) 
    FROM bayaran b
    JOIN daftar_pelajar dp ON b.nokad = dp.noKad
";

// Execute the main query
$query = "
    SELECT 
        b.*, 
        dp.namaWarisPelajar, 
        b.dana_pibg,
        b.jum_bayar_dana_pibg,
        (
            SELECT SUM(b2.jum_bayar_dana_pibg)
            FROM bayaran b2
            JOIN daftar_pelajar dp2 ON b2.nokad = dp2.noKad
            WHERE dp2.namaWarisPelajar = dp.namaWarisPelajar
        ) AS total_paid_by_siblings,
        (
            b.dana_pibg - (
                SELECT SUM(b2.jum_bayar_dana_pibg)
                FROM bayaran b2
                JOIN daftar_pelajar dp2 ON b2.nokad = dp2.noKad
                WHERE dp2.namaWarisPelajar = dp.namaWarisPelajar
            ) 
        ) AS baki
    FROM bayaran b
    JOIN daftar_pelajar dp ON b.nokad = dp.noKad
    LIMIT $start_from, $records_per_page
";

// Execute the query to fetch the result
$result = mysqli_query($conn, $query);

// Execute the total query for pagination
$total_result = mysqli_query($conn, $total_query);
$total_rows = mysqli_fetch_array($total_result)[0];
$total_pages = ceil($total_rows / $records_per_page);

?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bayaran</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 20px;
            text-align: center;
        }
        .container {
            overflow-x: auto;
            width: 80%;
            margin: auto;
            background: white;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 5px 5px 10px rgba(0, 0, 0, 0.1);
        }
        h2 {
            color: #333;
        }
        input {
            width: 50%;
            padding: 10px;
            margin-bottom: 20px;
            border: 1px solid #ccc;
            border-radius: 5px;
            font-size: 16px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
            background: white;
            border: 2px solid black;
        }
        th, td {
            padding: 12px;
            text-align: center;
            border: 2px solid black;
        }
        th {
            background-color: #007bff;
            color: white;
        }
        tr:nth-child(even) {
            background-color: #f9f9f9;
        }
        tr:hover {
            background-color: #f1f1f1;
        }
        .btn {
            text-decoration: none;
            color: white;
            padding: 8px 12px;
            border-radius: 5px;
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
        .sidebar a {
            display: block;
            padding: 15px;
            color: white;
            text-decoration: none;
            font-size: 16px;
            transition: 0.3s;
            background: #34495e;
            border-radius: 5px;
            width: 79%;
            margin: 10px auto;
            text-align: center;
        }
        .sidebar a:hover {
            background: #1abc9c;
        }
        .toggle-btn {
            position: fixed;
            top: 10px;
            left: 10px;
            background: #2c3e50;
            color: white;
            padding: 10px 15px;
            border: none;
            cursor: pointer;
            border-radius: 5px;
            z-index: 1000;
        }
        .sidebar.open {
            left: 0;
        }
        .container.shift {
            margin-left: 230px;
            width: calc(100% - 230px);
            transition: margin-left 0.3s ease-in-out, width 0.3s ease-in-out;
        }
        .sidebar-logo {
            width: 100px;
            margin-bottom: 20px;
            transition: opacity 0.3s ease-in-out;
        }
        .pagination {
            margin-top: 20px;
            text-align: center;
        }
        .pagination a {
            text-decoration: none;
            padding: 8px 12px;
            margin: 0 5px;
            border: 1px solid #007bff;
            border-radius: 5px;
            color: #007bff;
        }
        .pagination a.active {
            background-color: #007bff;
            color: white;
        }
        .pagination a:hover {
            background-color: #0056b3;
            color: white;
        }
        .payment-options {
            display: none;
            margin-top: 5px;
            text-align: center;
        }
        .pay-btn {
            background: #007bff;
            color: white;
            border: none;
            padding: 5px 8px;
            cursor: pointer;
            border-radius: 3px;
        }
        

    </style>
</head>
<body>
    <div class="container">
        <div class="sidebar" id="sidebar">
        <img src="../images/logo.jpg" id="sidebar-logo" class="sidebar-logo" alt="Logo">
        <a href="daftar_pelajar.php" class="btn">DAFTAR PELAJAR</a>
        <a href="kemaskini_pelajar.php" class="btn">KEMASKINI PELAJAR</a>
        <a href="bayaran.php" class="btn">BAYARAN</a>
        <a href="setting.php" class="btn">SETTING</a>
        <a href="../logout/logout.php" class="btn btn-red">LOG KELUAR</a>
    </div>
    <button class="toggle-btn" onclick="toggleSidebar()">☰</button>
        <h2>Senarai Bayaran</h2>
        <input type="text" id="searchPayment" placeholder="Masukkan nama waris atau nama pelajar" onkeyup="filterPayments()">

        <table>
            <thead>
                <tr>
                    <th>Bil</th>
                    <th>No. Kad Pengenalan</th>
                    <th>Nama Pelajar</th>
                    <th>Nama Waris</th>
                    <th>Jumlah Yuran (RM)</th>
                    <th>Jumlah Bayar (RM)</th>
                    <th>Baki (RM)</th>
                    <th>Butiran</th>
                </tr>
            </thead>
            <tbody id="paymentTable">
                <?php
                $bil = $start_from + 1;
                while ($row = mysqli_fetch_assoc($result)) {
                    echo "<tr>";
                    echo "<td>" . $bil++ . "</td>";
                    echo "<td>" . $row['noKad'] . "</td>";
                    echo "<td>" . $row['namaPelajar'] . "</td>";
                    echo "<td>" . $row['namaWarisPelajar'] . "</td>";
                    echo "<td>" . number_format($row['jumlahYuran'], 2) . "</td>";
                    
                    // Calculate jumlahBayar based on the sum of the fees that the parent is willing to pay
                    $jumlahBayar = $row['jum_bayar_dana_pibg'] + $row['jum_bayar_tuisyen'] + $row['jum_bayar_massak'] + 
                                   $row['jum_bayar_majalah'] + $row['jum_bayar_hac'] + $row['jum_bayar_kertas_peperiksaan'] + 
                                   $row['jum_bayar_bas'] + $row['jum_bayar_dobi'];
                    
                    echo "<td>" . number_format($jumlahBayar, 2) . "</td>";
                    echo "<td>" . number_format($row['jumlahYuran'] - $jumlahBayar, 2) . "</td>";
                    echo "<td>
                        <details>
                            <summary style='cursor: pointer; font-weight: bold; color: #007bff;'>Lihat Butiran</summary>
                            <div style='padding: 5px; margin-top: 5px; border-radius: 5px; background: #f8f9fa; text-align: left;'>
                                <table style='width: 100%; border-collapse: collapse; font-size: 13px;'>
                                    <tbody>
                                            <tr>
                                                <td style='padding: 3px;'><strong>Dana PIBG</strong></td>
                                                <td><input type='number' value='" . $row['dana_pibg'] . "' class='fee-input' data-type='dana_pibg' data-id='" . $row['id'] . "'></td>
                                                <td><input type='number' value='" . $row['jum_bayar_dana_pibg'] . "' class='fee-input' data-type='jum_bayar_dana_pibg' data-id='" . $row['id'] . "'></td>
                                                <td><input type='number' value='" . ($row['dana_pibg'] - $row['jum_bayar_dana_pibg'] - getTotalPaidBySiblings($row['id']) + $row['jum_bayar_dana_pibg']) . "' readonly></td>
                                            </tr>


                                            <tr>
                                                <td style='padding: 3px;'><strong>Tuisyen</strong></td>
                                                <td><input type='number' value='" . $row['tuisyen'] . "' class='fee-input' data-type='tuisyen' data-id='" . $row['id'] . "'></td>
                                                <td><input type='number' value='" . $row['jum_bayar_tuisyen'] . "' class='fee-input' data-type='jum_bayar_tuisyen' data-id='" . $row['id'] . "'></td>
                                                <td><input type='number' value='" . ($row['tuisyen'] - $row['jum_bayar_tuisyen']) . "' readonly></td>
                                            </tr>
                                            <tr>
                                                <td style='padding: 3px;'><strong>MASSAK</strong></td>
                                                <td><input type='number' value='" . $row['massak'] . "' class='fee-input' data-type='massak' data-id='" . $row['id'] . "'></td>
                                                <td><input type='number' value='" . $row['jum_bayar_massak'] . "' class='fee-input' data-type='jum_bayar_massak' data-id='" . $row['id'] . "'></td>
                                                <td><input type='number' value='" . ($row['massak'] - $row['jum_bayar_massak']) . "' readonly></td>
                                            </tr>
                                            <tr>
                                                <td style='padding: 3px;'><strong>Majalah</strong></td>
                                                <td><input type='number' value='" . $row['majalah'] . "' class='fee-input' data-type='majalah' data-id='" . $row['id'] . "'></td>
                                                <td><input type='number' value='" . $row['jum_bayar_majalah'] . "' class='fee-input' data-type='jum_bayar_majalah' data-id='" . $row['id'] . "'></td>
                                                <td><input type='number' value='" . ($row['majalah'] - $row['jum_bayar_majalah']) . "' readonly></td>
                                            </tr>
                                            <tr>
                                                <td style='padding: 3px;'><strong>HAC</strong></td>
                                                <td><input type='number' value='" . $row['hac'] . "' class='fee-input' data-type='hac' data-id='" . $row['id'] . "'></td>
                                                <td><input type='number' value='" . $row['jum_bayar_hac'] . "' class='fee-input' data-type='jum_bayar_hac' data-id='" . $row['id'] . "'></td>
                                                <td><input type='number' value='" . ($row['hac'] - $row['jum_bayar_hac']) . "' readonly></td>
                                            </tr>
                                            <tr>
                                                <td style='padding: 3px;'><strong>Kertas Peperiksaan</strong></td>
                                                <td><input type='number' value='" . $row['kertas_peperiksaan'] . "' class='fee-input' data-type='kertas_peperiksaan' data-id='" . $row['id'] . "'></td>
                                                <td><input type='number' value='" . $row['jum_bayar_kertas_peperiksaan'] . "' class='fee-input' data-type='jum_bayar_kertas_peperiksaan' data-id='" . $row['id'] . "'></td>
                                                <td><input type='number' value='" . ($row['kertas_peperiksaan'] - $row['jum_bayar_kertas_peperiksaan']) . "' readonly></td>
                                            </tr>
                                            <tr>
                                                <td style='padding: 3px;'><strong>Bas</strong></td>
                                                <td><input type='number' value='" . $row['bas'] . "' class='fee-input' data-type='bas' data-id='" . $row['id'] . "'></td>
                                                <td><input type='number' value='" . $row['jum_bayar_bas'] . "' class='fee-input' data-type='jum_bayar_bas' data-id='" . $row['id'] . "'></td>
                                                <td><input type='number' value='" . ($row['bas'] - $row['jum_bayar_bas']) . "' readonly></td>
                                            </tr>
                                            <tr>
                                                <td style='padding: 3px;'><strong>Dobi</strong></td>
                                                <td><input type='number' value='" . $row['dobi'] . "' class='fee-input' data-type='dobi' data-id='" . $row['id'] . "'></td>
                                                <td><input type='number' value='" . $row['jum_bayar_dobi'] . "' class='fee-input' data-type='jum_bayar_dobi' data-id='" . $row['id'] . "'></td>
                                                <td><input type='number' value='" . ($row['dobi'] - $row['jum_bayar_dobi']) . "' readonly></td>
                                            </tr>
                                        
                                    </tbody>
                                </table>
                                <!-- Add Payment Method Dropdown -->
                                    <label for='caraBayaran'>Cara Bayar:</label>
                                    <select id='caraBayaran_".$row['id']."' class='caraBayaran' required>
                                        <option value='' disabled selected>Pilih</option>
                                        <option value='tunai'>Tunai</option>
                                        <option value='bank'>Bank</option>
                                        <option value='online'>Online</option>
                                    </select>

                                
                                <button class='bayar-btn' data-id='" . $row['id'] . "' style='margin-top: 5px; padding: 3px 8px; background: #007bff; color: white; border: none; border-radius: 4px; cursor: pointer;'>Bayar</button>
                                <button class='reset-btn' data-id='" . $row['id'] . "'>reset</button>

                            </div>
                        </details>
                      </td>";
                }
                ?>
            </tbody>
        </table>
        
        <!-- Pagination Links -->
        <div class="pagination">
            <?php if ($page > 1): ?>
                <a href="?page=1">First</a>
                <a href="?page=<?php echo $page - 1; ?>">Prev</a>
            <?php endif; ?>

            <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                <a href="?page=<?php echo $i; ?>" class="<?php echo ($i == $page) ? 'active' : ''; ?>">
                    <?php echo $i; ?>
                </a>
            <?php endfor; ?>

            <?php if ($page < $total_pages): ?>
                <a href="?page=<?php echo $page + 1; ?>">Next</a>
                <a href="?page=<?php echo $total_pages; ?>">Last</a>
            <?php endif; ?>
        </div>
    </div>

    </div>

    <script>
   document.addEventListener("DOMContentLoaded", function () {
    // Save Button Functionality
    document.querySelectorAll(".save-btn").forEach(button => {
        button.addEventListener("click", function () {
            let id = this.getAttribute("data-id");
            let feeInputs = this.closest("div").querySelectorAll(".fee-input");
            let feeData = { id: id };

            feeInputs.forEach(input => {
                let type = input.getAttribute("data-type");
                feeData[type] = input.value;
            });

            fetch("update_yuran.php", {
                method: "POST",
                headers: { "Content-Type": "application/json" },
                body: JSON.stringify(feeData)
            })
            .then(response => response.text())
            .then(() => {
                alert("Yuran telah dikemaskini.");
            })
            .catch(() => alert("Ralat kemaskini yuran!"));
        });
    });

    // Reset Button Functionality
    document.querySelectorAll(".reset-btn").forEach(button => {
        button.addEventListener("click", function () {
            let studentId = this.getAttribute("data-id");
            let row = this.closest("tr");

            if (confirm("Adakah anda pasti ingin reset semua yuran untuk pelajar ini?")) {
                fetch("reset_yuran.php", {
                    method: "POST",
                    headers: { "Content-Type": "application/json" },
                    body: JSON.stringify({ reset: true, id: studentId })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.status === "success") {
                        let detailsDiv = row.querySelector("details div");
                        detailsDiv.querySelectorAll(".fee-input").forEach(input => {
                            let feeType = input.getAttribute("data-type");
                            if (data[feeType] !== undefined) {
                                input.value = parseFloat(data[feeType]).toFixed(2);
                            }
                        });

                        row.cells[4].textContent = "0.00";
                        row.cells[5].textContent = parseFloat(data.jumlahYuran).toFixed(2);

                        alert("Semua yuran untuk pelajar ini telah berjaya direset.");
                        location.reload();
                    } else {
                        throw new Error(data.message);
                    }
                })
                .catch(error => {
                    console.error("Error resetting fees:", error);
                    alert("Ralat semasa reset yuran: " + error.message);
                });
            }
        });
    });

    // Bayar Button Functionality
    document.querySelectorAll(".bayar-btn").forEach(button => {
        button.addEventListener("click", function () {
            let id = this.getAttribute("data-id");
            let caraBayaran = document.getElementById(`caraBayaran_${id}`).value;

            if (!caraBayaran) {
                alert("Sila pilih cara bayaran sebelum membuat pembayaran!");
                return;
            }

            let feeData = { id: id, caraBayaran: caraBayaran };
            let feeInputs = this.closest("div").querySelectorAll(".fee-input");

            feeInputs.forEach(input => {
                let type = input.getAttribute("data-type");
                if (type.startsWith("jum_bayar_")) {
                    feeData[type] = parseFloat(input.value) || 0;
                }
            });

            this.disabled = true; // Prevent duplicate clicks

            fetch("proses_pembayaran.php", {
                method: "POST",
                headers: { "Content-Type": "application/json" },
                body: JSON.stringify(feeData)
            })
            .then(response => response.json())
            .then(result => {
                if (result.status === "success") {
                    let row = this.closest("tr");
                    row.cells[4].textContent = result.jumlahBayar;
                    row.cells[5].textContent = result.baki;

                    alert(`Pembayaran melalui ${caraBayaran} berjaya!`);

                    if (result.redirect) {
                       window.location.href = result.redirect;

                    }
                } else {
                    throw new Error(result.message);
                }
            })
            .catch(error => {
                console.error("Error processing payment:", error);
                alert("Ralat semasa memproses pembayaran: " + error.message);
            })
            .finally(() => {
                this.disabled = false; // Enable button after response
            });
        });
    });

    // Sidebar Toggle
    function toggleSidebar() {
        let sidebar = document.getElementById("sidebar");
        let container = document.querySelector(".container");

        if (sidebar && container) {
            sidebar.classList.toggle("open");
            container.classList.toggle("shift");
        }
    }

    let toggleBtn = document.querySelector(".toggle-btn");
    if (toggleBtn) {
        toggleBtn.addEventListener("click", toggleSidebar);
    }
  document.addEventListener("DOMContentLoaded", function() {
    let searchInput = document.getElementById("searchPayment");
    if (searchInput) {
        searchInput.addEventListener("keyup", filterPayments);
    }
});

function filterPayments() {
    let input = document.getElementById("searchPayment").value.trim().toLowerCase();
    let searchTerms = input.split(" ");
    let rows = document.querySelectorAll("#paymentTable tr");

    if (input === "") {
        rows.forEach(row => row.style.display = ""); // Show all rows again
        return;
    }

    rows.forEach(row => {
        if (row.cells.length > 2) {
            let noKad = row.cells[1].textContent.toLowerCase();
            let nama_pelajar = row.cells[2].textContent.toLowerCase();
            let parent_ic = row.cells.length > 5 ? row.cells[5].textContent.toLowerCase() : "";

            let match = searchTerms.every(term => 
                noKad.includes(term) || nama_pelajar.includes(term) || parent_ic.includes(term)
            );

            row.style.display = match ? "" : "none";
        }
    });
}



let searchInput = document.getElementById("searchPayment");
if (searchInput) {
    searchInput.addEventListener("input", filterPayments); // Changed from "keyup" to "input"
}


});

    </script>
</body>
</html>
