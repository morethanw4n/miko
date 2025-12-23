<?php
session_start();
include 'config.php';

if (!isset($_SESSION['username']) || $_SESSION['role'] != 'teknisi') {
    header("Location: login.php");
    exit();
}

$sql = "SELECT id_keluhan, nama_pelapor, jenis, lokasi, status, deskripsi, foto, aksi 
        FROM keluhan 
        ORDER BY id_keluhan ASC";
$result = mysqli_query($conn, $sql);
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<title>Daftar Keluhan CCTV</title>

<style>
body {
    margin:0;
    font-family: Arial, sans-serif;
    background: #e8f0fb;
}

/* SIDEBAR */
.sidebar {
    width:250px;
    height:100vh;
    background:#0b3d91;
    color:white;
    position:fixed;
    top:0; left:0;
    padding-top:30px;
    text-align:center;
}
.sidebar img { width:80px; }
.sidebar .company {
    margin-top:15px;
    font-weight:bold;
    font-size:13px;
}
.menu { margin-top:30px; }
.menu a {
    display:block;
    padding:12px 20px;
    text-decoration:none;
    color:white;
    font-size:15px;
    text-align:left;
    margin:5px 15px;
    border-radius:8px;
}
.menu a.active, .menu a:hover { background:#1b5fcc; }

/* MAIN */
.main-area { margin-left:250px; }

/* HEADER */
.header {
    background:white;
    padding:15px 25px;
    display:flex;
    justify-content:space-between;
    align-items:center;
    box-shadow:0 2px 5px rgba(0,0,0,0.1);
    font-size:18px;
    font-weight:bold;
}

/* USER */
.user-dropdown { position:relative; }
#user-btn {
    padding:9px 13px;
    background:#f0f4ff;
    border-radius:7px;
    color:#0b3d91;
    cursor:pointer;
}
#dropdown-menu {
    display:none;
    position:absolute;
    right:0;
    top:48px;
    background:white;
    border-radius:9px;
    width:170px;
    box-shadow:0 4px 10px rgba(0,0,0,0.15);
}
#dropdown-menu a {
    display:block;
    padding:13px;
    text-decoration:none;
    color:#333;
    border-bottom:1px solid #eee;
}

/* CONTENT */
.content-wrapper {
    width:95%;
    margin:25px auto;
}

/* TABLE */
.table-container {
    background:white;
    padding:30px;
    border-radius:14px;
    box-shadow:0 2px 6px rgba(0,0,0,0.15);
}
table {
    width:100%;
    border-collapse:collapse;
    text-align:center;
}
th {
    background:#0b3d91;
    color:white;
    padding:14px;
}
td {
    padding:14px;
    border-bottom:1px solid #ddd;
}
.foto-keluhan {
    width:90px;
    height:90px;
    object-fit:cover;
    border-radius:6px;
    border:1px solid #ccc;
}

/* BUTTON */
.btn {
    padding:10px 14px;
    border-radius:6px;
    text-decoration:none;
    color:white;
    font-size:14px;
    display:inline-block;
}
.btn-proses { background:#f39c12; }
.btn-selesai { background:#27ae60; }
.btn-disabled {
    background:#aaa;
    cursor:not-allowed;
}
.btn-excel { background:#0b3d91; }
.btn-print { background:#28a745; }

/* ACTION BOTTOM */
.action-bottom {
    display:flex;
    justify-content:flex-end;
    gap:10px;
    margin-top:15px;
}

@media print{
    .sidebar,
    .header,
    .action-bottom{
        display:none !important;
    }

    .main-area{
        margin-left:0 !important;
    }

    /* SEMBUNYIKAN KOLOM HAPUS SAAT CETAK */
    table th:nth-child(9),
    table td:nth-child(9){
        display:none !important;
    }
}
</style>
</head>

<body>

<!-- SIDEBAR -->
<div class="sidebar">
    <img src="images/logo_pusri.png">
    <div class="company">PT PUPUK SRIWIDJAJA</div>
    <div class="menu">
        <a href="dashboard_teknisi.php">Dashboard</a>
        <a href="pengelolaan_teknisi.php">Pengelolaan</a>
        <a href="tambah_keluhan.php">Tambah Keluhan</a>
        <a href="daftar_keluhan_teknisi.php" class="active">Daftar Keluhan</a>
    </div>
</div>

<!-- MAIN -->
<div class="main-area">

<div class="header">
    📋 Daftar Keluhan CCTV
    <div class="user-dropdown">
        <span id="user-btn">Hi <?= htmlspecialchars($_SESSION['username']) ?> ▼</span>
        <div id="dropdown-menu">
            <a href="ubah_password.php">Ubah Password</a>
            <a href="logout.php">Logout</a>
        </div>
    </div>
</div>

<div class="content-wrapper">
<div class="table-container">

<h2>Daftar Keluhan</h2>

<table>
<tr>
    <th>No</th>
    <th>Pelapor</th>
    <th>Jenis</th>
    <th>Lokasi</th>
    <th>Status</th>
    <th>Deskripsi</th>
    <th>Foto</th>
    <th>Aksi Teknisi</th>
</tr>

<?php $no=1; while ($row = mysqli_fetch_assoc($result)) : ?>
<tr>
    <td><?= $no++ ?></td>
    <td><?= htmlspecialchars($row['nama_pelapor']) ?></td>
    <td><?= htmlspecialchars($row['jenis']) ?></td>
    <td><?= htmlspecialchars($row['lokasi']) ?></td>
    <td><?= htmlspecialchars($row['status']) ?></td>
    <td><?= htmlspecialchars($row['deskripsi']) ?></td>
    <td>
        <?php if ($row['foto']): ?>
            <img src="uploads/<?= htmlspecialchars($row['foto']) ?>" class="foto-keluhan">
        <?php else: ?>
            Tidak ada
        <?php endif; ?>
    </td>
    <td>
        <?php if ($row['aksi'] === 'pending'): ?>
            <a href="update_status.php?id=<?= $row['id_keluhan'] ?>&status=proses" class="btn btn-proses">Proses</a>
        <?php elseif ($row['aksi'] === 'proses'): ?>
            <a href="update_status.php?id=<?= $row['id_keluhan'] ?>&status=selesai" class="btn btn-selesai">Selesai</a>
        <?php else: ?>
            <span class="btn btn-disabled">Selesai</span>
        <?php endif; ?>
    </td>
</tr>
<?php endwhile; ?>
</table>

<!-- ACTION BOTTOM -->
<div class="action-bottom">
    <a href="keluhan_excel.php" class="btn btn-excel">📊 Unduh Excel</a>
    <button onclick="window.print()" class="btn btn-print">🖨️ Cetak</button>
</div>

</div>
</div>
</div>

<script>
const btn = document.getElementById('user-btn');
const menu = document.getElementById('dropdown-menu');

btn.onclick = () => {
    menu.style.display = menu.style.display === 'block' ? 'none' : 'block';
};

document.addEventListener('click', e => {
    if (!btn.contains(e.target) && !menu.contains(e.target)) {
        menu.style.display = 'none';
    }
});
</script>

</body>
</html>
