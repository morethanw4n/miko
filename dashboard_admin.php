<?php
if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}
include 'config.php';

if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}

$resStats = mysqli_query($conn, "
    SELECT 
        (SELECT COUNT(*) FROM pengelolaan) AS totalKamera,
        (SELECT COUNT(*) FROM pengelolaan WHERE status='online') AS kameraAktif,
        (SELECT COUNT(*) FROM pengelolaan WHERE status='offline') AS kameraOffline,
        (SELECT COUNT(*) FROM keluhan WHERE aksi='pending') AS keluhanPending,
        (SELECT COUNT(*) FROM keluhan WHERE aksi='selesai') AS keluhanSelesai
");
$rowStats = mysqli_fetch_assoc($resStats);

$totalKamera    = (int)$rowStats['totalKamera'];
$kameraAktif    = (int)$rowStats['kameraAktif'];
$kameraOffline  = (int)$rowStats['kameraOffline'];
$keluhanBaru    = (int)$rowStats['keluhanPending'];
$keluhanSelesai = (int)$rowStats['keluhanSelesai'];

/* Bulan & Tahun */
$bulanTahun = date('F Y'); 
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<title>Dashboard CCTV</title>

<style>
body { margin:0; font-family: Arial, sans-serif; background:#e8f0fb; }

/* SIDEBAR */
.sidebar {
    width: 250px; height: 100vh;
    background-color: #0b3d91; color: white;
    position: fixed; top:0; left:0; padding-top: 30px;
    text-align: center;
}
.sidebar img { width: 80px; }
.sidebar .company { margin-top: 15px; font-weight: bold; font-size: 13px; line-height:1.3; }
.menu { margin-top: 30px; }
.menu a {
    display:block; padding:12px 20px; text-decoration:none;
    color:white; font-size:15px; text-align:left;
    margin:5px 15px; border-radius:8px;
}
.menu a.active, .menu a:hover { background-color: #1b5fcc; }

/* MAIN AREA */
.main-area { margin-left: 250px; padding: 0; }

/* HEADER */
.header {
    background: white; padding:15px 25px;
    display:flex; justify-content:space-between; align-items:center;
    box-shadow: 0 2px 5px rgba(0,0,0,0.1);
    font-size:20px; font-weight:bold;
    margin-bottom: 20px;
}
/* USER DROPDOWN */
.user-dropdown { position: relative; cursor:pointer; font-size:16px; font-weight:bold; }
#user-btn {
    padding: 8px 12px; background: #f0f4ff; border-radius: 8px; color: #0b3d91;
}
#user-btn:hover { background:#dbe7ff; }
#dropdown-menu {
    display:none; position:absolute; right:0; top:42px;
    background:white; border-radius:8px; width:160px;
    overflow:hidden; box-shadow: 0 4px 10px rgba(0,0,0,0.15);
}
#dropdown-menu a {
    display:block; padding:12px; text-decoration:none;
    color:#333; font-size:14px; border-bottom:1px solid #eee;
}
#dropdown-menu a:hover { background:#f3f7ff; }

/* STATS */
.stats {
    display:grid;
    grid-template-columns: repeat(auto-fit, minmax(200px,1fr));
    gap:20px;
    margin-top:30px;
}
.card {
    background:white;
    padding:16px;
    border-radius:12px;
    box-shadow:0 2px 6px rgba(0,0,0,.15);
    text-align:center;
}
.card h4 { margin:5px 0; font-size:15px; }
.card p { margin:0; font-size:24px; font-weight:bold; color:#0b3d91; }
.card.keluhan-pending p { color:#ff9800; font-size:20px; }
.card.keluhan-selesai p { color:#4caf50; font-size:20px; }

/* CHART */
#chartKeluhan {
    margin:40px auto 0;
    background:white;
    padding:20px;
    border-radius:12px;
    max-width:700px;
    box-shadow:0 2px 6px rgba(0,0,0,.15);
}
</style>
</head>

<body>

<!-- SIDEBAR -->
<div class="sidebar">
    <img src="images/logo_pusri.png">
    <div class="company">PT PUPUK SRIWIDJAJA</div>
    <div class="menu">
        <a href="dashboard_admin.php" class="active">Dashboard</a>
        <a href="pengelolaan_admin.php">Pengelolaan</a>
        <a href="tambah_keluhan.php">Tambah Keluhan</a>
        <a href="daftar_keluhan_admin.php">Daftar Keluhan</a>
    </div>
</div>

<!-- MAIN -->
<div class="main-area">
    <div class="header">
        📊 Dashboard CCTV
        <div class="user-dropdown">
            <span id="user-btn">Hi! <?= htmlspecialchars($_SESSION['username']) ?> ▼</span>
            <div id="dropdown-menu">
                <a href="ubah_password.php">Ubah Password</a>
                <a href="logout.php">Logout</a>
            </div>
        </div>
    </div>

    <div class="content-wrapper">

        <!-- STAT CARD -->
        <div class="stats">
            <div class="card"><h4>Total Kamera</h4><p><?= $totalKamera ?></p></div>
            <div class="card"><h4>Kamera Aktif</h4><p><?= $kameraAktif ?></p></div>
            <div class="card"><h4>Kamera Offline</h4><p><?= $kameraOffline ?></p></div>
            <div class="card keluhan-pending"><h4>Keluhan Pending</h4><p><?= $keluhanBaru ?></p></div>
            <div class="card keluhan-selesai"><h4>Keluhan Selesai</h4><p><?= $keluhanSelesai ?></p></div>
        </div>

        <!-- CHART -->
        <div id="chartKeluhan">
            <h4 style="text-align:center; margin-bottom:15px; color:#0b3d91;">
                Grafik Keluhan Bulan <?= $bulanTahun ?>
            </h4>
            <canvas id="chartKeluhanCanvas" height="90"></canvas>
        </div>

    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
const btn = document.getElementById('user-btn');
const menu = document.getElementById('dropdown-menu');
btn.onclick = () => menu.style.display = menu.style.display === 'block' ? 'none' : 'block';
document.addEventListener('click', e => {
    if (!btn.contains(e.target) && !menu.contains(e.target)) menu.style.display='none';
});

new Chart(document.getElementById('chartKeluhanCanvas'), {
    type:'bar',
    data:{
        labels:[
            'Pending (<?= $bulanTahun ?>)',
            'Selesai (<?= $bulanTahun ?>)'
        ],
        datasets:[{
            data:[<?= $keluhanBaru ?>, <?= $keluhanSelesai ?>],
            backgroundColor:['#ff9800','#4caf50'],
            barThickness:90
        }]
    },
    options:{
        plugins:{ legend:{ display:false }},
        scales:{ y:{ beginAtZero:true, ticks:{ stepSize:1 }}}
    }
});
</script>

</body>
</html>
