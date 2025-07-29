<?php
include '../db_connection/db.php'; // Include your database connection
$search = isset($_GET['search']) ? mysqli_real_escape_string($conn, $_GET['search']) : '';
$tingkatan = isset($_GET['tingkatan']) ? mysqli_real_escape_string($conn, $_GET['tingkatan']) : '';
$tingkatanCondition = $tingkatan !== '' ? " AND dp.tingkatan = '$tingkatan'" : '';


function getTotalPaidBySiblings($studentId) {
    global $conn;

    // Step 1: Get the student's IC (noKad)
    $queryIC = "SELECT noKad FROM bayaran WHERE id = ?";
    $stmtIC = mysqli_prepare($conn, $queryIC);
    mysqli_stmt_bind_param($stmtIC, "i", $studentId);
    mysqli_stmt_execute($stmtIC);
    mysqli_stmt_bind_result($stmtIC, $noKad);
    mysqli_stmt_fetch($stmtIC);
    mysqli_stmt_close($stmtIC);

    if (!$noKad) return 0;

    // Step 2: Get guardian name
    $queryWaris = "SELECT namaWarisPelajar FROM daftar_pelajar WHERE noKad = ?";
    $stmtWaris = mysqli_prepare($conn, $queryWaris);
    mysqli_stmt_bind_param($stmtWaris, "s", $noKad);
    mysqli_stmt_execute($stmtWaris);
    mysqli_stmt_bind_result($stmtWaris, $namaWaris);
    mysqli_stmt_fetch($stmtWaris);
    mysqli_stmt_close($stmtWaris);

    if (!$namaWaris) return 0;

    // Step 3: Get all siblings’ noKad with the same guardian
    $querySiblings = "SELECT noKad FROM daftar_pelajar WHERE namaWarisPelajar = ?";
    $stmtSiblings = mysqli_prepare($conn, $querySiblings);
    mysqli_stmt_bind_param($stmtSiblings, "s", $namaWaris);
    mysqli_stmt_execute($stmtSiblings);
    $resultSiblings = mysqli_stmt_get_result($stmtSiblings);

    $totalPaid = 0;

    while ($row = mysqli_fetch_assoc($resultSiblings)) {
        $siblingNoKad = $row['noKad'];

        // Step 4: Get total Dana PIBG paid by each sibling
        $queryBayar = "SELECT jumlahBayar_dana FROM bayaran WHERE noKad = ?";
        $stmtBayar = mysqli_prepare($conn, $queryBayar);
        mysqli_stmt_bind_param($stmtBayar, "s", $siblingNoKad);
        mysqli_stmt_execute($stmtBayar);
        mysqli_stmt_bind_result($stmtBayar, $jumlah);
        while (mysqli_stmt_fetch($stmtBayar)) {
            $totalPaid += floatval($jumlah);
        }
        mysqli_stmt_close($stmtBayar);
    }

    mysqli_stmt_close($stmtSiblings);

    return $totalPaid;
}




// Set number of records per page
$records_per_page = 20;

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
    WHERE (
    dp.namaPelajar LIKE '%$search%' 
    OR dp.namaWarisPelajar LIKE '%$search%' 
    OR b.noKad LIKE '%$search%'
)
$tingkatanCondition

";


// Execute the main query
$query = "
    SELECT 
        b.*, 
        dp.namaWarisPelajar, 
        dp.namaPelajar,
        dp.tingkatan,
        dp.jumlahYuran,
        (
            SELECT SUM(b2.jum_bayar_dana_pibg)
            FROM bayaran b2
            JOIN daftar_pelajar dp2 ON b2.nokad = dp2.noKad
            WHERE dp2.namaWarisPelajar = dp.namaWarisPelajar
        ) AS total_paid_by_siblings
    FROM bayaran b
    JOIN daftar_pelajar dp ON b.nokad = dp.noKad
   WHERE (
    dp.namaPelajar LIKE '%$search%' 
    OR dp.namaWarisPelajar LIKE '%$search%' 
    OR b.noKad LIKE '%$search%'
)
$tingkatanCondition

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
            background-color:rgb(215, 237, 247);
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
            width: 85%;
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
       .sidebar {
        width: 185px;
        background: #2c3e50;
        height: 100vh;
        padding-top: 20px;
        position: fixed;
        left: 0;
        top: 0;
        text-align: center;
        z-index: 999;
    }

    .container {
        margin-left: 200px;
        width: calc(100% - 240px);
        background: white;
        padding: 20px;
        border-radius: 10px;
        box-shadow: 5px 5px 10px rgba(0, 0, 0, 0.1);
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
            margin-top: 10px;
            text-align: center;
        }
        .pay-btn {
            background: #007bff;
            color: white;
            border: none;
            padding: 10px 10px;
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
        <a href="kemaskini_pelajar.php" class="btn">SENARAI PELAJAR</a>
        <a href="bayaran.php" class="btn">BAYARAN</a>
      
        <a href="../logout/logout.php" class="btn btn-red">LOG KELUAR</a>
    </div>
    
        <h2>Senarai Bayaran</h2>
        <form method="get" action="">
    <input 
        type="text" 
        name="search" 
        placeholder="Masukkan nama waris atau nama pelajar" 
        value="<?= isset($_GET['search']) ? htmlspecialchars($_GET['search']) : '' ?>" 
        style="width:70%; padding:10px; font-size:16px;"
    >
    <select name="tingkatan" style="padding:10px;">
    <option value="">Semua Tingkatan</option>
    <option value="1" <?= (isset($_GET['tingkatan']) && $_GET['tingkatan'] == '1') ? 'selected' : '' ?>>Tingkatan 1</option>
    <option value="2" <?= (isset($_GET['tingkatan']) && $_GET['tingkatan'] == '2') ? 'selected' : '' ?>>Tingkatan 2</option>
    <option value="3" <?= (isset($_GET['tingkatan']) && $_GET['tingkatan'] == '3') ? 'selected' : '' ?>>Tingkatan 3</option>
    <option value="4" <?= (isset($_GET['tingkatan']) && $_GET['tingkatan'] == '4') ? 'selected' : '' ?>>Tingkatan 4</option>
    <option value="5" <?= (isset($_GET['tingkatan']) && $_GET['tingkatan'] == '5') ? 'selected' : '' ?>>Tingkatan 5</option>
    <option value="6" <?= (isset($_GET['tingkatan']) && $_GET['tingkatan'] == '6') ? 'selected' : '' ?>>Tingkatan 6</option>
</select>

    
    <button type="submit" style="padding:10px;">Cari</button>
</form>


        <table>
            <thead>
                <tr>
                    <th>Bil</th>
                    <th>No. Kad Pengenalan</th>
                    <th>Nama Pelajar</th>
                    <th>Tingkatan</th>
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
                   $dana_pibg = $row['dana_pibg'];
$actualPaid = $row['jum_bayar_dana_pibg'];
$siblingsPaid = getTotalPaidBySiblings($row['noKad']);

// Determine display-only value (max 30 between own or siblings)
$displayedPaidDana = max($actualPaid, min($siblingsPaid, 30));
$displayedBakiDana = ($dana_pibg > $displayedPaidDana) ? $dana_pibg - $displayedPaidDana : 0;

// Compute full total paid (only actual payments by this student)
$jumlahBayar = $row['jum_bayar_dana_pibg'] + $row['jum_bayar_tuisyen'] + $row['jum_bayar_massak'] + 
               $row['jum_bayar_majalah'] + $row['jum_bayar_hac'] + $row['jum_bayar_kertas_peperiksaan'] + 
               $row['jum_bayar_bas'] + $row['jum_bayar_dobi'];

$bakiKeseluruhan = $row['jumlahYuran'] - $jumlahBayar;


                    echo "<tr>";
                    echo "<td>" . $bil++ . "</td>";
                    echo "<td>" . $row['noKad'] . "</td>";
                    echo "<td>" . $row['namaPelajar'] . "</td>";
                    echo "<td>" . $row['tingkatan'] . "</td>";
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
                            <div style='padding: 5px; margin-top: 5px; border-radius: 5px; background: #f8f9fa; text-align: left; max-height: 280px; overflow-y: auto;'>

                                <table style='width: 100%; border-collapse: collapse; font-size: 13px;'>
                                    <tbody>
                                            <tr>
                                                <td style='padding: 3px;'><strong>Dana PIBG</strong></td>
                                                <td><input type='number' value='" . $row['dana_pibg'] . "' class='fee-input' data-type='dana_pibg' data-id='" . $row['id'] . "'></td>
                                                <td><input type='number' value='" . $row['jum_bayar_dana_pibg'] . "' class='fee-input' data-type='jum_bayar_dana_pibg' data-id='" . $row['id'] . "'></td>
                                                <td><input type='number' value='" . ($row['dana_pibg'] - $row['jum_bayar_dana_pibg'] - getTotalPaidBySiblings($row['noKad']) + $row['jum_bayar_dana_pibg']) . "' readonly></td>
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
        <a href="?page=1&search=<?= urlencode($search) ?>&tingkatan=<?= urlencode($tingkatan) ?>">First</a>
        <a href="?page=<?= $page - 1 ?>&search=<?= urlencode($search) ?>&tingkatan=<?= urlencode($tingkatan) ?>">Prev</a>
    <?php endif; ?>

    <?php for ($i = 1; $i <= $total_pages; $i++): ?>
        <a href="?page=<?= $i ?>&search=<?= urlencode($search) ?>&tingkatan=<?= urlencode($tingkatan) ?>" class="<?= ($i == $page) ? 'active' : '' ?>">
            <?= $i ?>
        </a>
    <?php endfor; ?>

    <?php if ($page < $total_pages): ?>
        <a href="?page=<?= $page + 1 ?>&search=<?= urlencode($search) ?>&tingkatan=<?= urlencode($tingkatan) ?>">Next</a>
        <a href="?page=<?= $total_pages ?>&search=<?= urlencode($search) ?>&tingkatan=<?= urlencode($tingkatan) ?>">Last</a>
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