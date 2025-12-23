<?php
session_start();
include 'config.php';

// Proteksi admin
if (!isset($_SESSION['username']) || $_SESSION['role'] != 'admin') {
    header("Location: login.php");
    exit();
}

// Jika submit
if (isset($_POST['submit'])) {

    $nama     = $_POST['nama'];
    $status   = $_POST['status'];
    $jenis    = $_POST['jenis'];
    $lokasi   = $_POST['lokasi'];
    $realtime = $_POST['realtime'];

    // Upload foto
    $fotoName = $_FILES['foto']['name'];
    $fotoTmp  = $_FILES['foto']['tmp_name'];

    if ($fotoName != "") {
        // Buat nama unik
        $uniqName = time() . "_" . $fotoName;
        move_uploaded_file($fotoTmp, "uploads/" . $uniqName);
    } else {
        $uniqName = "";
    }

    $sql = "INSERT INTO pengelolaan (nama, status, jenis, lokasi, realtime, foto)
            VALUES ('$nama', '$status', '$jenis', '$lokasi', '$realtime', '$uniqName')";

    if (mysqli_query($conn, $sql)) {
        echo "<script>alert('CCTV berhasil ditambahkan!'); window.location='pengelolaan_admin.php';</script>";
    } else {
        echo "<script>alert('Gagal menambahkan CCTV');</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<title>Tambah CCTV</title>
<style>
body { margin:0; font-family: Arial, sans-serif; background:#e8f0fb; }

/* SIDEBAR */
.sidebar {
    width:250px; height:100vh; background:#0b3d91; color:white;
    position:fixed; top:0; left:0; padding-top:30px; text-align:center;
}
.sidebar img { width:80px; margin-bottom:10px; }
.company { margin-top:10px; font-size:14px; font-weight:bold; }
.menu { margin-top:30px; }
.menu a { display:block; padding:12px 20px; text-decoration:none; color:white; font-size:15px; text-align:left; margin:5px 15px; border-radius:8px; }
.menu a:hover, .menu .active { background-color:#1b5fcc; }

/* HEADER */
.header {
    margin-left:250px; background:white; padding:15px 25px;
    display:flex; justify-content:space-between; align-items:center;
    box-shadow:0 2px 5px rgba(0,0,0,0.1); font-size:18px; font-weight:bold;
}

/* USER DROPDOWN */
.user-dropdown { position: relative; cursor:pointer; }
#user-btn { padding:8px 12px; background:#f0f4ff; border-radius:8px; color:#0b3d91; }
#user-btn:hover { background:#dbe7ff; }
#dropdown-menu {
    display:none; position:absolute; right:0; top:42px; background:white;
    border-radius:8px; width:160px; overflow:hidden; box-shadow:0 4px 10px rgba(0,0,0,0.15); z-index:1000;
}
#dropdown-menu a { display:block; padding:12px; text-decoration:none; color:#333; font-size:14px; border-bottom:1px solid #eee; }
#dropdown-menu a:hover { background:#f3f7ff; }
#dropdown-menu a:last-child { border-bottom:none; }

/* FORM */
.form-wrapper { margin-left:250px; width:calc(100%-250px); display:flex; justify-content:center; margin-top:40px; margin-bottom:40px; }
.form-container { width:50%; min-width:420px; background:white; padding:28px 32px; border-radius:12px; box-shadow:0 2px 6px rgba(0,0,0,0.15); }
h2 { text-align:center; margin-bottom:20px; color:#0b3d91; font-size:20px; border-bottom:2px solid #0b3d91; padding-bottom:8px; }
label { font-weight:bold; font-size:14px; }
input, select { width:100%; padding:10px; border-radius:8px; border:1px solid #ccc; margin-top:6px; margin-bottom:15px; font-size:14px; }
.btn-submit { background:#0b3d91; padding:12px 20px; border:none; border-radius:8px; color:white; cursor:pointer; font-size:15px; width:100%; margin-top:10px; }
.btn-submit:hover { background:#1b5fcc; }
.btn-back { background:#777; padding:10px 18px; color:white; border-radius:8px; text-decoration:none; font-size:14px; display:inline-block; margin-bottom:15px; }
.btn-back:hover { background:#555; }
</style>
</head>
<body>

<!-- SIDEBAR -->
<div class="sidebar">
    <img src="images/logo_pusri.png" alt="Logo">
    <div class="company">PT PUPUK SRIWIDJAJA</div>
    <div class="menu">
        <a href="dashboard_admin.php">Dashboard</a>
        <a href="pengelolaan_admin.php" class="active">Pengelolaan</a>
        <a href="tambah_keluhan.php">Tambah Keluhan</a>
        <a href="daftar_keluhan_admin.php">Daftar Keluhan</a>
        <a href="laporan.php">Laporan</a>
    </div>
</div>

<!-- HEADER -->
<div class="header">
    <span>Tambah CCTV</span>
    <div class="user-dropdown">
        <span id="user-btn">Hi! <?= htmlspecialchars($_SESSION['username']) ?> ▼</span>
        <div id="dropdown-menu">
            <a href="ubah_password.php">Ubah Password</a>
            <a href="logout.php">Logout</a>
        </div>
    </div>
</div>

<!-- FORM -->
<div class="form-wrapper">
<div class="form-container">
    <h2>Form Tambah CCTV</h2>

    <form method="POST" enctype="multipart/form-data">

        <label>Nama CCTV</label>
        <input type="text" name="nama" required>

        <label>Status CCTV</label>
        <select name="status" required>
            <option value="online">Online</option>
            <option value="offline">Offline</option>
            <option value="rusak">Rusak</option>
        </select>

        <label>Jenis CCTV</label>
        <select name="jenis" required>
            <option value="">-- Pilih Jenis --</option>
            <option value="night and day">Night and Day</option>
            <option value="IP">IP</option>
            <option value="HD">HD</option>
            <option value="bullet">Bullet</option>
            <option value="dome">Dome</option>
            <option value="PTZ">PTZ</option>
        </select>

        <label>Lokasi CCTV</label>
        <select name="lokasi" required>
            <option value="">-- Pilih Lokasi --</option>
            <option value="Lobby">Lobby</option>
            <option value="Ruang Kerja">Ruang Kerja</option>
            <option value="Ruang Monitoring CCTV">Ruang Monitoring CCTV</option>
            <option value="Ruang Infrastruktur">Ruang Infrastruktur</option>
            <option value="Ruang Jaringan">Ruang Jaringan</option>
            <option value="Ruang Keamanan">Ruang Keamanan</option>
            <option value="Ruang Server">Ruang Server</option>
            <option value="Ruang Meeting">Ruang Meeting</option>
            <option value="Ruang Kerja Praktik">Ruang Kerja Praktik</option>
            <option value="Ruang Programmer">Ruang Programmer</option>
        </select>

        <label>Realtime</label>
        <input type="datetime-local" name="realtime" required>

        <label>Foto CCTV</label>
        <input type="file" name="foto" accept="image/*">

        <a href="pengelolaan_admin.php" class="btn-back">Kembali</a>
        <button type="submit" name="submit" class="btn-submit">Tambah CCTV</button>

    </form>
</div>
</div>

<script>
const userBtn = document.getElementById('user-btn');
const dropdownMenu = document.getElementById('dropdown-menu');

userBtn.addEventListener('click', () => {
    dropdownMenu.style.display = (dropdownMenu.style.display==='block')?'none':'block';
});

document.addEventListener('click', function(event){
    if(!userBtn.contains(event.target) && !dropdownMenu.contains(event.target)){
        dropdownMenu.style.display='none';
    }
});
</script>

</body>
</html>
