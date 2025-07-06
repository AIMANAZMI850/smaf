<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Senarai Pelajar</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: rgb(215, 237, 247);
            margin: 0;
            padding: 20px;
            text-align: center;
        }
        .container {
            margin-left: 210px;
            width: calc(100% - 240px);
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
        .edit-btn {
            background-color: #28a745;
        }
        .edit-btn:hover {
            background-color: #218838;
        }
        .delete-btn {
            background-color: #dc3545;
        }
        .delete-btn:hover {
            background-color: #c82333;
        }
        .pagination {
            margin-top: 20px;
        }
        .pagination a {
            display: inline-block;
            padding: 10px 15px;
            margin: 5px;
            text-decoration: none;
            color: white;
            background-color: #007bff;
            border-radius: 5px;
        }
        .pagination a:hover {
            background-color: #0056b3;
        }
        .pagination .active {
            background-color: #0056b3;
            font-weight: bold;
        }
        .sidebar {
            width: 220px;
            background: #2c3e50;
            height: 100vh;
            padding-top: 20px;
            position: fixed;
            left: 0;
            top: 0;
            text-align: center;
            z-index: 999;
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
        .sidebar-logo {
            width: 100px;
            margin-bottom: 20px;
            transition: opacity 0.3s ease-in-out;
        }
    </style>
</head>
<body>

<div class="container">
    <div class="sidebar" id="sidebar">
        <img src="../images/logo.jpg" id="sidebar-logo" class="sidebar-logo" alt="Logo">
        <a href="daftar_pelajar.php">DAFTAR PELAJAR</a>
        <a href="kemaskini_pelajar.php">KEMASKINI PELAJAR</a>
        <a href="bayaran.php" class="btn">BAYARAN</a>
        <a href="../logout/logout.php" class="btn-red">LOG KELUAR</a>
    </div>

    <h2>Senarai Pelajar</h2>
    

   <input type="text" id="searchStudent" placeholder="Cari No. Kad Pengenalan atau Nama Penuh" onkeypress="handleSearchKey(event)">

<button onclick="handleSearchClick()">Cari</button>
<button onclick="window.location.href='kemaskini_pelajar.php'">Reset</button>



    <table>
        <thead>
            <tr>
                <th>Bil</th>
                <th>Tahun</th>
                <th>No. Kad Pengenalan</th>
                <th>Nama Pelajar</th>
                <th>Tingkatan</th>
                <th>Kelas</th>
                <th>Kategori</th>
                <th>Jumlah Yuran (RM)</th>
                <th>Tindakan</th>
            </tr>
        </thead>
        <tbody id="studentTable">
            <?php
            include '../db_connection/db.php';

            $searchNoKad = isset($_GET['noKad']) ? $_GET['noKad'] : null;

            if ($searchNoKad) {
                $stmt = $conn->prepare("SELECT * FROM daftar_pelajar WHERE noKad LIKE ?");
                $searchValue = "%" . $searchNoKad . "%";
                $stmt->bind_param("s", $searchValue);
                $stmt->execute();
                $result = $stmt->get_result();
                $bil = 1;
            } else {
                $studentsPerPage = 25;
                $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
                $offset = ($page - 1) * $studentsPerPage;

                $totalStudentsQuery = "SELECT COUNT(*) AS total FROM daftar_pelajar";
                $totalResult = $conn->query($totalStudentsQuery);
                $totalStudents = $totalResult->fetch_assoc()['total'];
                $totalPages = ceil($totalStudents / $studentsPerPage);

                $sql = "SELECT * FROM daftar_pelajar LIMIT $studentsPerPage OFFSET $offset";
                $result = $conn->query($sql);
                $bil = $offset + 1;
            }

            while ($row = $result->fetch_assoc()) {
                ?>
                <tr>
                    <td><?= $bil++; ?></td>
                    <td><?= $row['tahunPelajar']; ?></td>
                    <td><?= $row['noKad']; ?></td>
                    <td><?= $row['namaPelajar']; ?></td>
                    <td><?= $row['tingkatan']; ?></td>
                    <td><?= $row['selectTingkatan']; ?></td>
                    <td><?= $row['kategori']; ?></td>
                    <td><?= number_format($row['jumlahYuran'], 2); ?></td>
                    <td>
                        <a href="edit_pelajar.php?noKad=<?= $row['noKad']; ?>" class="btn edit-btn">Kemaskini</a>
                        <button class="btn delete-btn" onclick="confirmDelete('<?= $row['noKad']; ?>')">Padam</button>
                    </td>
                </tr>
                <?php
            }
            ?>
        </tbody>
    </table>

    <!-- Pagination (Hanya jika tiada carian) -->
    <?php if (!$searchNoKad && $totalPages > 1): ?>
        <div class="pagination">
            <?php if ($page > 1): ?>
                <a href="?page=<?= $page - 1; ?>">Sebelumnya</a>
            <?php endif; ?>

            <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                <a href="?page=<?= $i; ?>" class="<?= ($i == $page) ? 'active' : ''; ?>"><?= $i; ?></a>
            <?php endfor; ?>

            <?php if ($page < $totalPages): ?>
                <a href="?page=<?= $page + 1; ?>">Seterusnya</a>
            <?php endif; ?>
        </div>
    <?php endif; ?>
</div>

<script>
function handleSearchClick() {
    const searchValue = document.getElementById("searchStudent").value.trim();
    //const tingkatan = document.getElementById("filterTingkatan").value;
   // const kelas = document.getElementById("filterKelas").value;

    if (!searchValue) {
    alert("Sila masukkan sekurang-kurangnya satu kriteria carian.");
    return;
}


    const url = new URL('carian_pelajar.php', window.location.href);


    // Check if input is IC number (all digits or 12 digits)
    if (/^\d{6,}$/.test(searchValue)) {
        url.searchParams.append("noKad", searchValue);
    } else {
        url.searchParams.append("namaPelajar", searchValue);
    }

   // if (tingkatan) url.searchParams.append("tingkatan", tingkatan);
   // if (kelas) url.searchParams.append("kelas", kelas);

    fetch(url)
        .then(response => response.json())
        .then(data => {
            const tableBody = document.getElementById("studentTable");
            tableBody.innerHTML = "";

            if (data.error) {
                tableBody.innerHTML = `<tr><td colspan="9" style="color:red;">${data.error}</td></tr>`;
            } else {
                let bil = 1;
                data.forEach(row => {
                    const tr = document.createElement("tr");
                    tr.innerHTML = `
                        <td>${bil++}</td>
                        <td>${row.tahunPelajar}</td>
                        <td>${row.noKad}</td>
                        <td>${row.namaPelajar}</td>
                        <td>${row.tingkatan}</td>
                        <td>${row.selectTingkatan}</td>
                        <td>${row.kategori}</td>
                        <td>${parseFloat(row.jumlahYuran).toFixed(2)}</td>
                        <td>
                            <a href="edit_pelajar.php?noKad=${row.noKad}" class="btn edit-btn">Kemaskini</a>
                            <button class="btn delete-btn" onclick="confirmDelete('${row.noKad}')">Padam</button>
                        </td>
                    `;
                    tableBody.appendChild(tr);
                });
            }

            const pagination = document.querySelector(".pagination");
            if (pagination) {
                pagination.style.display = "none";
            }
        })
        .catch(error => {
            console.error("Error:", error);
        });
}


function handleSearchKey(event) {
    if (event.key === "Enter") {
        handleSearchClick();
    }
}

function confirmDelete(noKad) {
    if (confirm("Adakah anda pasti ingin menghapuskan pelajar ini?")) {
        window.location.href = "hapus_pelajar.php?noKad=" + noKad;
    }
}
</script>


</body>
</html>
