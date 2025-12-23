<?php
session_start();
include 'config.php';

// ===== CEK LOGIN =====
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}

$role = $_SESSION['role'];

if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    $nama_pelapor = trim($_POST['nama_pelapor']);
    $no_hp        = trim($_POST['no_hp']); // FITUR BARU: NOMOR HP
    $jenis        = $_POST['jenis'];
    $lokasi       = $_POST['lokasi'];
    $status       = $_POST['status'];
    $deskripsi    = trim($_POST['deskripsi']);
    $realtime     = date("Y-m-d H:i:s");

    // ================= UPLOAD FOTO =================
    $foto = "";
    if (!empty($_FILES['foto']['name'])) {

        if (!is_dir("uploads")) {
            mkdir("uploads", 0777, true);
        }

        $allowed_types = ['jpg','jpeg','png','gif'];
        $file_ext = strtolower(pathinfo($_FILES['foto']['name'], PATHINFO_EXTENSION));

        if(in_array($file_ext, $allowed_types)){
            // Nama file unik
            $foto = "foto_" . time() . "_" . basename($_FILES['foto']['name']);
            move_uploaded_file($_FILES['foto']['tmp_name'], "uploads/" . $foto);
        }
    }   

    // ================= VALIDASI =================
    if(empty($nama_pelapor) || empty($jenis) || empty($lokasi) || empty($status) || empty($deskripsi)){
        echo "<script>alert('Semua field harus diisi');</script>";
    } else {

        // QUERY INSERT (Sudah termasuk kolom no_hp)
        // Pastikan kamu sudah menambahkan kolom 'no_hp' di database!
        $sql = "INSERT INTO keluhan (nama_pelapor, no_hp, jenis, lokasi, status, realtime, deskripsi, foto, aksi)
                VALUES ('$nama_pelapor', '$no_hp', '$jenis', '$lokasi', '$status', '$realtime', '$deskripsi', '$foto', 'pending')";

        if (mysqli_query($conn, $sql)) {

            // FITUR TAMBAHAN: Update status CCTV di tabel pengelolaan jika laporannya 'rusak' atau 'offline'
            if ($status == 'rusak' || $status == 'offline') {
                $lokasi_esc = mysqli_real_escape_string($conn, $lokasi);
                $status_esc = mysqli_real_escape_string($conn, $status);
                mysqli_query($conn, "UPDATE pengelolaan SET status='$status_esc' WHERE lokasi='$lokasi_esc'");
            }

            $_SESSION['last_id'] = mysqli_insert_id($conn);
        } else {
            echo "<script>alert('Gagal menyimpan: " . mysqli_error($conn) . "');</script>";
        }
    }
}
?>

<!DOCTYPE html>
<html>

<head>
    <title>Tambah Keluhan CCTV</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <style>
    body {
        margin: 0;
        font-family: Arial, sans-serif;
        background: #e8f0fb;
    }

    /* SIDEBAR (Desktop) */
    .sidebar {
        width: 250px;
        height: 100vh;
        background-color: #0b3d91;
        color: #fff;
        position: fixed;
        top: 0;
        left: 0;
        padding-top: 30px;
        text-align: center;
        transition: 0.3s;
    }

    .sidebar img {
        width: 80px;
    }

    .company {
        margin-top: 15px;
        font-weight: bold;
        font-size: 13px;
    }

    .menu {
        margin-top: 30px;
    }

    .menu a {
        display: block;
        padding: 12px 20px;
        text-decoration: none;
        color: white;
        font-size: 15px;
        text-align: left;
        margin: 5px 15px;
        border-radius: 8px;
    }

    .menu a.active,
    .menu a:hover {
        background-color: #1b5fcc;
    }

    /* HEADER */
    .header {
        margin-left: 250px;
        /* Sesuai lebar sidebar */
        background: white;
        padding: 15px 25px;
        display: flex;
        justify-content: space-between;
        align-items: center;
        box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
        font-size: 18px;
        font-weight: bold;
        transition: 0.3s;
    }

    .user-dropdown {
        position: relative;
        cursor: pointer;
        font-size: 15px;
        background: #f0f4ff;
        padding: 8px 12px;
        border-radius: 8px;
        color: #0b3d91;
    }

    /* FORM AREA */
    .center-wrapper {
        margin-left: 250px;
        display: flex;
        justify-content: center;
        padding: 40px 20px;
        /* Tambah padding kiri-kanan */
    }

    .form-container {
        width: 100%;
        max-width: 500px;
        /* Batasi lebar maksimal agar enak dilihat */
        background: white;
        padding: 25px;
        border-radius: 12px;
        box-shadow: 0 2px 6px rgba(0, 0, 0, 0.15);
    }

    label {
        font-weight: bold;
        margin-top: 15px;
        display: block;
        color: #333;
    }

    input,
    select,
    textarea {
        width: 100%;
        padding: 12px;
        /* Padding lebih besar untuk sentuhan jari */
        margin-top: 6px;
        border-radius: 8px;
        border: 1px solid #ccc;
        font-size: 14px;
        box-sizing: border-box;
        /* Agar padding tidak merusak lebar */
    }

    textarea {
        height: 100px;
        resize: vertical;
    }

    .btn-submit {
        margin-top: 25px;
        padding: 14px;
        background-color: #0b3d91;
        color: white;
        border: none;
        border-radius: 8px;
        width: 100%;
        font-size: 16px;
        font-weight: bold;
        cursor: pointer;
    }

    .btn-submit:hover {
        background-color: #1b5fcc;
    }

    /* ALERT SUKSES */
    .alert-success {
        background: #d4f6d1;
        padding: 15px;
        border-radius: 8px;
        margin-bottom: 20px;
        border-left: 6px solid #28a745;
        font-size: 15px;
        color: #155724;
    }

    /* ===== MOBILE RESPONSIVE (MEDIA QUERY) ===== */
    @media only screen and (max-width: 768px) {
        .sidebar {
            left: -250px;
            /* Sembunyikan sidebar di HP */
            z-index: 999;
        }

        .header {
            margin-left: 0;
            font-size: 16px;
        }

        .center-wrapper {
            margin-left: 0;
            padding-top: 20px;
        }

        /* Tampilkan hamburger menu jika perlu (opsional), 
           tapi untuk sekarang fokus agar form tidak zoom-out */
    }
    </style>
</head>

<body>

    <div class="sidebar">
        <img src="images/logo_pusri.png">
        <div class="company">PT PUPUK SRIWIDJAJA</div>

        <div class="menu">
            <?php if($role=="admin"){ ?>
            <a href="dashboard_admin.php">Dashboard</a>
            <a href="pengelolaan_admin.php">Pengelolaan</a>
            <a href="tambah_keluhan.php" class="active">Tambah Keluhan</a>
            <a href="daftar_keluhan_admin.php">Daftar Keluhan</a>
            <?php } else { ?>
            <a href="dashboard_teknisi.php">Dashboard</a>
            <a href="pengelolaan_teknisi.php">Pengelolaan</a>
            <a href="tambah_keluhan.php" class="active">Tambah Keluhan</a>
            <a href="daftar_keluhan_teknisi.php">Daftar Keluhan</a>
            <?php } ?>
        </div>
    </div>

    <div class="header">
        <div>
            <span style="margin-right:10px;">✚</span> Tambah Keluhan
        </div>
        <div class="user-dropdown">
            Hi <?= ucfirst($role) ?>!
        </div>
    </div>

    <div class="center-wrapper">
        <div class="form-container">

            <?php if(isset($_SESSION['last_id']) && $_SESSION['last_id'] != 0): ?>
            <div class="alert-success">
                <b>Laporan Terkirim ✔</b><br><br>
                ID Tiket: <b style="font-size:18px;"><?= $_SESSION['last_id'] ?></b><br>
                <small>Simpan ID ini untuk pengecekan status.</small>
            </div>
            <?php unset($_SESSION['last_id']); ?>
            <?php endif; ?>

            <form method="POST" enctype="multipart/form-data">

                <label>Nama Pelapor</label>
                <input type="text" name="nama_pelapor" placeholder="Nama lengkap Anda" required>

                <label>Nomor HP (WhatsApp)</label>
                <input type="number" name="no_hp" placeholder="Contoh: 081234567890" required>

                <label>Jenis CCTV</label>
                <select name="jenis" required>
                    <option value="">-- Pilih Jenis --</option>
                    <option value="night and day">Night and Day</option>
                    <option value="IP">IP Camera</option>
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

                <label>Status CCTV Saat Ini</label>
                <select name="status" required>
                    <option value="">-- Pilih Status --</option>
                    <option value="rusak">Rusak</option>
                    <option value="offline">Offline / Mati</option>
                    <option value="online">Online (Keluhan Lain)</option>
                </select>

                <label>Deskripsi Masalah</label>
                <textarea name="deskripsi" placeholder="Jelaskan detail kerusakan..." required></textarea>

                <label>Ambil Foto Bukti</label>
                <input type="file" name="foto" accept="image/*" capture="environment">
                <small style="color:#666;">*Pada HP, tombol ini akan langsung membuka kamera.</small>

                <button type="submit" class="btn-submit">Kirim Laporan</button>

            </form>
        </div>
    </div>

</body>

</html>