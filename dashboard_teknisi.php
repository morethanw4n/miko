<?php
if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}

include 'config.php';

if (!isset($_SESSION['username']) || $_SESSION['role'] != 'teknisi') {
    header("Location: login.php");
    exit();
}

$qRusak = mysqli_query($conn, "SELECT COUNT(*) AS cnt FROM pengelolaan WHERE status='rusak'");
$rusak = $qRusak ? (int)mysqli_fetch_assoc($qRusak)['cnt'] : 0;

$qOffline = mysqli_query($conn, "SELECT COUNT(*) AS cnt FROM pengelolaan WHERE status='offline'");
$offline = $qOffline ? (int)mysqli_fetch_assoc($qOffline)['cnt'] : 0;

$cctvBermasalah = $rusak + $offline;

$keluhanBaruQuery = mysqli_query(
    $conn,
    "SELECT id_keluhan, nama_pelapor, lokasi, deskripsi, aksi 
     FROM keluhan 
     ORDER BY id_keluhan ASC
     LIMIT 5"
);
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<title>Dashboard Teknisi</title>

<style>
body { margin:0; font-family: Arial, sans-serif; background: #e8f0fb; }

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
    line-height:1.3;
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
.menu a.active,
.menu a:hover { background:#1b5fcc; }

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
    font-size:20px;
    font-weight:bold;
    margin-bottom:20px;
}

/* USER DROPDOWN */
.user-dropdown { position:relative; cursor:pointer; }
#user-btn {
    padding:8px 12px;
    background:#f0f4ff;
    border-radius:8px;
    color:#0b3d91;
}
#dropdown-menu {
    display:none;
    position:absolute;
    right:0;
    top:42px;
    background:white;
    width:160px;
    border-radius:8px;
    box-shadow:0 4px 10px rgba(0,0,0,0.15);
}
#dropdown-menu a {
    display:block;
    padding:12px;
    text-decoration:none;
    color:#333;
    border-bottom:1px solid #eee;
}
#dropdown-menu a:hover { background:#f3f7ff; }

/* CONTENT */
.content-wrapper { width:95%; margin:auto; }

/* CARDS */
.stats {
    display:grid;
    grid-template-columns: repeat(auto-fit, minmax(250px,1fr));
    gap:20px;
}
.card {
    background:white;
    padding:20px;
    border-radius:12px;
    box-shadow:0 2px 6px rgba(0,0,0,0.15);
    text-align:center;
}
.card h4 { margin:5px 0; font-size:17px; color:#444; }
.card p { font-size:30px; font-weight:bold; color:#0b3d91; margin:0; }

/* TABLE */
.table-container {
    background:white;
    margin-top:25px;
    padding:25px;
    border-radius:12px;
    box-shadow:0 2px 6px rgba(0,0,0,0.15);
}
.table-container h3 { margin-bottom:15px; }

table {
    width:100%;
    border-collapse:collapse;
    font-size:14px;
}
th {
    background:#0b3d91;
    color:white;
    padding:12px;
    text-align:center;
}
td {
    padding:12px;
    border-bottom:1px solid #ddd;
    vertical-align:middle;
}
tr:hover { background:#f5f8ff; }

.status-pending { color:orange; font-weight:bold; }
.status-proses { color:#0b3d91; font-weight:bold; }
.status-selesai { color:green; font-weight:bold; }

td {
    max-width:260px;
    white-space:nowrap;
    overflow:hidden;
    text-overflow:ellipsis;
}
</style>
</head>

<body>

<!-- SIDEBAR -->
<div class="sidebar">
    <img src="images/logo_pusri.png">
    <div class="company">PT PUPUK SRIWIDJAJA</div>
    <div class="menu">
        <a href="dashboard_teknisi.php" class="active">Dashboard</a>
        <a href="pengelolaan_teknisi.php">Pengelolaan</a>
        <a href="tambah_keluhan.php">Tambah Keluhan</a>
        <a href="daftar_keluhan_teknisi.php">Daftar Keluhan</a>
    </div>
</div>

<!-- MAIN -->
<div class="main-area">

<div class="header">
    🛠 Dashboard Teknisi
    <div class="user-dropdown">
        <span id="user-btn">Hi! <?= htmlspecialchars($_SESSION['username']) ?> ▼</span>
        <div id="dropdown-menu">
            <a href="ubah_password.php">Ubah Password</a>
            <a href="logout.php">Logout</a>
        </div>
    </div>
</div>

<div class="content-wrapper">

<!-- CARD -->
<div class="stats">
    <div class="card">
        <h4>CCTV Bermasalah</h4>
        <p><?= $cctvBermasalah ?></p>
    </div>
</div>

<!-- TABLE -->
<div class="table-container">
<h3>Keluhan Terbaru</h3>

<table>
<thead>
<tr>
    <th>ID</th>
    <th>Pelapor</th>
    <th>Lokasi</th>
    <th>Keluhan</th>
    <th>Status</th>
</tr>
</thead>
<tbody>

<?php while ($k = mysqli_fetch_assoc($keluhanBaruQuery)): ?>
<tr>
    <td><?= $k['id_keluhan'] ?></td>
    <td><?= htmlspecialchars($k['nama_pelapor']) ?></td>
    <td><?= htmlspecialchars($k['lokasi']) ?></td>
    <td><?= htmlspecialchars($k['deskripsi']) ?></td>
    <td class="status-<?= strtolower($k['aksi']) ?>">
        <?= ucfirst($k['aksi']) ?>
    </td>
</tr>
<?php endwhile; ?>

</tbody>
</table>
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
