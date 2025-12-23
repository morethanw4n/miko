<?php
session_start();
include 'config.php';

if (!isset($_SESSION['username']) || $_SESSION['role'] != 'admin') {
    header("Location: login.php");
    exit();
}

$search = isset($_GET['search']) ? trim($_GET['search']) : "";

$sql = "SELECT id_pengelolaan, nama, status, jenis, lokasi, realtime, foto 
        FROM pengelolaan 
        WHERE nama LIKE '%$search%' 
           OR lokasi LIKE '%$search%'
        ORDER BY id_pengelolaan ASC";

$result = mysqli_query($conn, $sql);
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<title>Pengelolaan CCTV (Admin)</title>

<style>
body{
    margin:0;
    font-family:Arial,sans-serif;
    background:#e8f0fb;
}

/* ===== SIDEBAR ===== */
.sidebar{
    width:250px;
    height:100vh;
    background:#0b3d91;
    color:#fff;
    position:fixed;
    top:0; left:0;
    padding-top:30px;
    text-align:center;
}
.sidebar img{width:80px}
.company{
    margin-top:15px;
    font-weight:bold;
    font-size:13px;
}
.menu{margin-top:30px}
.menu a{
    display:block;
    padding:12px 20px;
    color:white;
    text-decoration:none;
    font-size:15px;
    margin:5px 15px;
    border-radius:8px;
    text-align:left;
}
.menu a.active,
.menu a:hover{background:#1b5fcc}

/* ===== MAIN ===== */
.main-area{margin-left:250px}
.content-wrapper{
    width:95%;
    margin:auto;
    padding-bottom:40px;
}

/* ===== HEADER ===== */
.header{
    background:white;
    padding:15px 25px;
    display:flex;
    justify-content:space-between;
    align-items:center;
    box-shadow:0 2px 5px rgba(0,0,0,.1);
    margin-bottom:20px;
    font-size:18px;
    font-weight:bold;
}

/* ===== USER DROPDOWN ===== */
.user-dropdown{
    position:relative;
    cursor:pointer;
    font-size:15px;
    font-weight:bold;
}
#user-btn{
    padding:8px 12px;
    background:#f0f4ff;
    border-radius:8px;
    color:#0b3d91;
}
#user-btn:hover{background:#dbe7ff}
#dropdown-menu{
    display:none;
    position:absolute;
    right:0;
    top:42px;
    background:white;
    border-radius:8px;
    width:160px;
    overflow:hidden;
    box-shadow:0 4px 10px rgba(0,0,0,.15);
    z-index:99;
}
#dropdown-menu a{
    display:block;
    padding:12px;
    text-decoration:none;
    color:#333;
    font-size:14px;
    border-bottom:1px solid #eee;
}
#dropdown-menu a:hover{background:#f3f7ff}

/* ===== TOOLBAR ===== */
.toolbar{
    display:flex;
    justify-content:space-between;
    margin-bottom:15px;
}
.search-box input{
    padding:10px;
    width:260px;
    border-radius:8px;
    border:1px solid #ccc;
}
.btn-add{
    background:#0b3d91;
    color:white;
    padding:10px 18px;
    border-radius:8px;
    text-decoration:none;
}

/* ===== TABLE ===== */
.table-container{
    background:white;
    padding:25px;
    border-radius:12px;
    box-shadow:0 2px 6px rgba(0,0,0,.15);
}
table{
    width:100%;
    border-collapse:collapse;
}
th, td{
    padding:12px;
    border-bottom:1px solid #ddd;
    text-align:left;
    vertical-align:middle;
}
th{
    background:#0b3d91;
    color:white;
}
tr:hover td{background:#f5f7ff}

.foto-pengelolaan{
    width:55px;
    height:55px;
    border-radius:6px;
    object-fit:cover;
}

.status-online{color:green;font-weight:bold}
.status-offline{color:orange;font-weight:bold}
.status-rusak{color:red;font-weight:bold}

/* ===== BUTTON ===== */
.btn{
    padding:6px 12px;
    border-radius:6px;
    color:white;
    text-decoration:none;
    font-size:14px;
}
.edit{background:#28a745}
.delete{background:#dc3545}

/* ===== BOTTOM ACTION ===== */
.action-bottom{
    display:flex;
    justify-content:flex-end;
    gap:10px;
    margin-top:5px;
}

/* ===== PRINT ===== */
@media print{
    .sidebar,.header,.toolbar,.action-bottom,.aksi{display:none!important}
    .main-area{margin-left:0!important}
    body{background:white}
    th{
        background:black!important;
        color:white!important;
        -webkit-print-color-adjust:exact;
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
        <a href="dashboard_admin.php">Dashboard</a>
        <a href="pengelolaan_admin.php" class="active">Pengelolaan</a>
        <a href="tambah_keluhan.php">Tambah Keluhan</a>
        <a href="daftar_keluhan_admin.php">Daftar Keluhan</a>
    </div>
</div>

<!-- MAIN -->
<div class="main-area">

<div class="header">
    🛠️ Pengelolaan CCTV
    <div class="user-dropdown">
        <span id="user-btn">Hi! <?= htmlspecialchars($_SESSION['username']) ?> ▼</span>
        <div id="dropdown-menu">
            <a href="ubah_password.php">Ubah Password</a>
            <a href="logout.php">Logout</a>
        </div>
    </div>
</div>

<div class="content-wrapper">

<div class="toolbar">
    <form class="search-box">
        <input type="text" name="search" value="<?= $search ?>" placeholder="Cari nama / lokasi...">
    </form>
    <a href="tambah_cctv.php" class="btn-add">+ Tambah CCTV</a>
</div>

<div class="table-container">
<table>
<thead>
<tr>
    <th>ID</th>
    <th>Nama</th>
    <th>Status</th>
    <th>Jenis</th>
    <th>Lokasi</th>
    <th>Realtime</th>
    <th>Foto</th>
    <th class="aksi">Aksi</th>
</tr>
</thead>
<tbody>

<?php if(mysqli_num_rows($result) > 0): ?>
<?php while($row = mysqli_fetch_assoc($result)): ?>
<tr>
    <td><?= $row['id_pengelolaan'] ?></td>
    <td><?= htmlspecialchars($row['nama']) ?></td>
    <td class="status-<?= $row['status'] ?>"><?= ucfirst($row['status']) ?></td>
    <td><?= htmlspecialchars($row['jenis']) ?></td>
    <td><?= htmlspecialchars($row['lokasi']) ?></td>
    <td><?= htmlspecialchars($row['realtime']) ?></td>
    <td>
        <?php if($row['foto']): ?>
            <img src="uploads/<?= $row['foto'] ?>" class="foto-pengelolaan">
        <?php else: ?>-
        <?php endif; ?>
    </td>
    <td class="aksi">
        <a href="edit_cctv.php?id=<?= $row['id_pengelolaan'] ?>" class="btn edit">Edit</a>
        <a href="hapus_cctv.php?id=<?= $row['id_pengelolaan'] ?>" class="btn delete"
           onclick="return confirm('Yakin hapus data?')">Hapus</a>
    </td>
</tr>
<?php endwhile; ?>
<?php else: ?>
<tr><td colspan="8" style="text-align:center">Data tidak ditemukan</td></tr>
<?php endif; ?>

</tbody>
</table>

<div class="action-bottom">
    <a href="pengelolaan_excel.php" class="btn" style="background:#0b3d91">📊 Unduh</a>
    <button onclick="window.print()" class="btn edit">🖨️ Cetak</button>
</div>

</div>
</div>
</div>

<script>
const btn = document.getElementById('user-btn');
const menu = document.getElementById('dropdown-menu');
btn.onclick = () => menu.style.display = menu.style.display === 'block' ? 'none' : 'block';
document.addEventListener('click', e => {
    if (!btn.contains(e.target) && !menu.contains(e.target)) menu.style.display='none';
});
</script>

</body>
</html>
