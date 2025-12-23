<?php
session_start();
include 'config.php';

/* Proteksi admin */
if (!isset($_SESSION['username']) || $_SESSION['role'] != 'admin') {
    header("Location: login.php");
    exit();
}

/* Validasi ID */
if (!isset($_GET['id'])) {
    die("ID CCTV tidak ditemukan.");
}
$id = (int)$_GET['id'];

/* Ambil data CCTV */
$query = "SELECT * FROM pengelolaan WHERE id_pengelolaan = $id LIMIT 1";
$result = mysqli_query($conn, $query);

if (mysqli_num_rows($result) === 0) {
    die("Data CCTV tidak ditemukan.");
}
$data = mysqli_fetch_assoc($result);

/* Ambil enum */
function getEnum($conn, $table, $column) {
    $q = mysqli_query($conn, "SHOW COLUMNS FROM $table LIKE '$column'");
    $row = mysqli_fetch_assoc($q);
    preg_match("/^enum\((.*)\)$/", $row['Type'], $matches);
    return str_getcsv($matches[1], ',', "'");
}

$enum_status = getEnum($conn, "pengelolaan", "status");
$enum_jenis  = getEnum($conn, "pengelolaan", "jenis");
$enum_lokasi = getEnum($conn, "pengelolaan", "lokasi");

/* Proses update */
if (isset($_POST['submit'])) {

    $nama     = mysqli_real_escape_string($conn, $_POST['nama']);
    $status   = $_POST['status'];
    $jenis    = $_POST['jenis'];
    $lokasi   = $_POST['lokasi'];
    $realtime = $_POST['realtime'];

    $foto = $data['foto'];

    if (!empty($_FILES['foto']['name'])) {
        $foto = time() . "_" . basename($_FILES['foto']['name']);
        move_uploaded_file($_FILES['foto']['tmp_name'], "uploads/" . $foto);
    }

    $update = "
        UPDATE pengelolaan SET
            nama       = '$nama',
            status     = '$status',
            jenis      = '$jenis',
            lokasi     = '$lokasi',
            realtime   = '$realtime',
            foto       = '$foto'
        WHERE id_pengelolaan = $id
    ";

    if (mysqli_query($conn, $update)) {
        echo "<script>alert('Data CCTV berhasil diperbarui'); window.location='pengelolaan_admin.php';</script>";
        exit();
    } else {
        echo "<script>alert('Gagal memperbarui data');</script>";
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<title>Edit CCTV</title>

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
    color:white; text-decoration:none;
    margin:5px 15px; border-radius:8px;
    text-align:left;
}
.menu a.active, .menu a:hover { background:#1b5fcc; }

/* HEADER */
.header {
    margin-left:250px;
    background:white;
    padding:15px 25px;
    display:flex;
    justify-content:space-between;
    align-items:center;
    box-shadow:0 2px 5px rgba(0,0,0,.1);
    font-weight:bold;
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
    right:0; top:42px;
    background:white;
    border-radius:8px;
    width:160px;
    box-shadow:0 4px 10px rgba(0,0,0,.15);
}
#dropdown-menu a {
    display:block;
    padding:12px;
    text-decoration:none;
    color:#333;
    border-bottom:1px solid #eee;
}
#dropdown-menu a:hover { background:#f3f7ff; }

/* FORM */
.form-wrapper {
    margin-left:250px;
    margin-top:40px;
    display:flex;
    justify-content:center;
}
.form-container {
    max-width:600px;
    width:100%;
    background:white;
    padding:25px;
    border-radius:12px;
    box-shadow:0 2px 6px rgba(0,0,0,.15);
}
label { font-weight:bold; }
input, select {
    width:100%;
    padding:10px;
    border:1px solid #ccc;
    border-radius:8px;
    margin:5px 0 15px;
}
.btn-save {
    width:100%;
    background:#28a745;
    color:white;
    padding:12px;
    border:none;
    border-radius:8px;
    font-size:16px;
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

<!-- HEADER -->
<div class="header">
    ✏️ Edit CCTV
    <div class="user-dropdown">
        <span id="user-btn">Hi <?= htmlspecialchars($_SESSION['username']) ?> ▼</span>
        <div id="dropdown-menu">
            <a href="ubah_password.php">Ubah Password</a>
            <a href="logout.php">Logout</a>
        </div>
    </div>
</div>

<!-- FORM -->
<div class="form-wrapper">
<div class="form-container">

<form method="POST" enctype="multipart/form-data">

<label>Nama CCTV</label>
<input type="text" name="nama" value="<?= htmlspecialchars($data['nama']) ?>" required>

<label>Status</label>
<select name="status">
<?php foreach ($enum_status as $s): ?>
<option value="<?= $s ?>" <?= $data['status']==$s?'selected':'' ?>><?= ucfirst($s) ?></option>
<?php endforeach; ?>
</select>

<label>Jenis</label>
<select name="jenis">
<?php foreach ($enum_jenis as $j): ?>
<option value="<?= $j ?>" <?= $data['jenis']==$j?'selected':'' ?>><?= $j ?></option>
<?php endforeach; ?>
</select>

<label>Lokasi</label>
<select name="lokasi">
<?php foreach ($enum_lokasi as $l): ?>
<option value="<?= $l ?>" <?= $data['lokasi']==$l?'selected':'' ?>><?= $l ?></option>
<?php endforeach; ?>
</select>

<label>Realtime</label>
<input type="datetime-local" name="realtime"
value="<?= date('Y-m-d\TH:i', strtotime($data['realtime'])) ?>">

<label>Foto</label>
<?php if ($data['foto']): ?>
<img src="uploads/<?= $data['foto'] ?>" width="120" style="border-radius:8px;"><br><br>
<?php endif; ?>
<input type="file" name="foto">

<button type="submit" name="submit" class="btn-save">Simpan Perubahan</button>

</form>
</div>
</div>

<script>
const btn = document.getElementById('user-btn');
const menu = document.getElementById('dropdown-menu');
btn.onclick = ()=> menu.style.display = menu.style.display==='block'?'none':'block';
document.addEventListener('click', e=>{
    if(!btn.contains(e.target) && !menu.contains(e.target)) menu.style.display='none';
});
</script>

</body>
</html>
