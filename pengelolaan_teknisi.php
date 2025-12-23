<?php
session_start();
include 'config.php';

if (!isset($_SESSION['username']) || $_SESSION['role'] != 'teknisi') {
    header("Location: login.php");
    exit();
}

$search = isset($_GET['search']) ? mysqli_real_escape_string($conn, trim($_GET['search'])) : "";

if ($search != "") {
    $sql = "SELECT * FROM pengelolaan
            WHERE nama LIKE '%$search%'
               OR lokasi LIKE '%$search%'
            ORDER BY id_pengelolaan ASC";
} else {
    $sql = "SELECT * FROM pengelolaan ORDER BY id_pengelolaan ASC";
}

$result = mysqli_query($conn, $sql);
if (!$result) {
    die("Query error: " . mysqli_error($conn));
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<title>Pengelolaan CCTV (Teknisi)</title>

<style>
body { margin:0; font-family: Arial, sans-serif; background:#e8f0fb; }

/* SIDEBAR */
.sidebar {
    width:250px; height:100vh;
    background:#0b3d91; color:white;
    position:fixed; top:0; left:0;
    padding-top:30px; text-align:center;
}
.sidebar img { width:80px; }
.company { margin-top:15px; font-weight:bold; font-size:13px; }

.menu { margin-top:30px; }
.menu a {
    display:block; padding:12px 20px;
    text-decoration:none; color:white;
    font-size:15px; text-align:left;
    margin:5px 15px; border-radius:8px;
}
.menu a.active, .menu a:hover { background:#1b5fcc; }

/* MAIN */
.main-area { margin-left:250px; }

/* HEADER */
.header {
    background:white; padding:15px 25px;
    display:flex; justify-content:space-between; align-items:center;
    box-shadow:0 2px 5px rgba(0,0,0,.1);
    font-size:18px; font-weight:bold;
}

/* USER DROPDOWN */
.user-dropdown { position:relative; cursor:pointer; }
#user-btn {
    padding:8px 12px; background:#f0f4ff;
    border-radius:8px; color:#0b3d91;
}
#dropdown-menu {
    display:none; position:absolute; right:0; top:42px;
    background:white; width:160px; border-radius:8px;
    box-shadow:0 4px 10px rgba(0,0,0,.15);
}
#dropdown-menu a {
    display:block; padding:12px;
    text-decoration:none; color:#333;
    border-bottom:1px solid #eee;
}

/* CONTENT */
.content-wrapper { width:95%; margin:25px auto; }

/* TABLE */
.table-container {
    background:white; padding:25px;
    border-radius:12px;
    box-shadow:0 2px 6px rgba(0,0,0,.15);
}
table { width:100%; border-collapse:collapse; margin-top:15px; }
th { background:#0b3d91; color:white; padding:12px; text-align:left; }
td { padding:10px; border-bottom:1px solid #ddd; }
tr:hover td { background:#f5f7ff; }

.foto-pengelolaan {
    width:50px; height:50px;
    border-radius:6px; object-fit:cover;
}

.status-online { color:green; font-weight:bold; }
.status-offline { color:orange; font-weight:bold; }
.status-rusak { color:red; font-weight:bold; }
</style>
</head>

<body>

<!-- SIDEBAR -->
<div class="sidebar">
    <img src="images/logo_pusri.png">
    <div class="company">PT PUPUK SRIWIDJAJA</div>
    <div class="menu">
        <a href="dashboard_teknisi.php">Dashboard</a>
        <a href="pengelolaan_teknisi.php" class="active">Pengelolaan</a>
        <a href="tambah_keluhan.php">Tambah Keluhan</a>
        <a href="daftar_keluhan_teknisi.php">Daftar Keluhan</a>
    </div>
</div>

<!-- MAIN -->
<div class="main-area">

<div class="header">
    🛠️ Pengelolaan CCTV
    <div class="user-dropdown">
        <span id="user-btn">Hi <?= htmlspecialchars($_SESSION['username']) ?> ▼</span>
        <div id="dropdown-menu">
            <a href="ubah_password.php">Ubah Password</a>
            <a href="logout.php">Logout</a>
        </div>
    </div>
</div>

<div class="content-wrapper">

<form method="GET" style="margin-bottom:20px;">
    <input type="text" name="search" value="<?= htmlspecialchars($search) ?>"
           placeholder="Cari nama / lokasi..."
           style="padding:10px; width:260px; border-radius:8px; border:1px solid #ccc;">
</form>


<div class="table-container">
<h3>Daftar CCTV</h3>

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
</tr>
</thead>
<tbody>

<?php if (mysqli_num_rows($result) > 0): ?>
<?php while ($row = mysqli_fetch_assoc($result)): ?>
<tr>
    <td><?= $row['id_pengelolaan'] ?></td>
    <td><?= htmlspecialchars($row['nama']) ?></td>
    <td>
        <?php
        if ($row['status'] == 'online') echo "<span class='status-online'>Online</span>";
        elseif ($row['status'] == 'offline') echo "<span class='status-offline'>Offline</span>";
        else echo "<span class='status-rusak'>Rusak</span>";
        ?>
    </td>
    <td><?= htmlspecialchars($row['jenis']) ?></td>
    <td><?= htmlspecialchars($row['lokasi']) ?></td>
    <td><?= htmlspecialchars($row['realtime']) ?></td>
    <td>
        <?php if ($row['foto']) : ?>
            <img src="uploads/<?= htmlspecialchars($row['foto']) ?>" class="foto-pengelolaan">
        <?php else : ?>
            -
        <?php endif; ?>
    </td>
</tr>
<?php endwhile; ?>
<?php else: ?>
<tr><td colspan="7" style="text-align:center;">Tidak ada data</td></tr>
<?php endif; ?>

</tbody>
</table>
</div>

</div>
</div>

<script>
const btn = document.getElementById('user-btn');
const menu = document.getElementById('dropdown-menu');
btn.onclick = () => menu.style.display = menu.style.display === 'block' ? 'none' : 'block';
document.addEventListener('click', e => {
    if (!btn.contains(e.target) && !menu.contains(e.target)) {
        menu.style.display = 'none';
    }
});
</script>

</body>
</html>
