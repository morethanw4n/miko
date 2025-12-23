<?php
include 'config.php';

$msg = "";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    $nama_pelapor = trim($_POST['nama_pelapor']);
    $no_hp        = trim($_POST['no_hp']); // VAR BARU
    $jenis        = $_POST['jenis'];
    $lokasi       = $_POST['lokasi'];
    $status       = $_POST['status'];
    $deskripsi    = trim($_POST['deskripsi']);
    $realtime     = date("Y-m-d H:i:s");

    $foto = "";
    if (!empty($_FILES['foto']['name'])) {
        if (!is_dir("uploads")) { mkdir("uploads", 0777, true); }

        $allowed_types = ['jpg','jpeg','png','gif'];
        $file_ext = strtolower(pathinfo($_FILES['foto']['name'], PATHINFO_EXTENSION));

        if(in_array($file_ext, $allowed_types)){
            $foto = "foto_" . time() . "_" . basename($_FILES['foto']['name']);
            move_uploaded_file($_FILES['foto']['tmp_name'], "uploads/" . $foto);
        }
    }

    if(empty($nama_pelapor) || empty($jenis) || empty($lokasi) || empty($status) || empty($deskripsi)){
        $msg = "<div class='alert'>Semua field harus diisi.</div>";
    } else {

        // QUERY INSERT UPDATE (Ada No HP)
        $sql = "INSERT INTO keluhan (nama_pelapor, no_hp, jenis, lokasi, status, realtime, deskripsi, foto, aksi)
                VALUES ('$nama_pelapor', '$no_hp', '$jenis', '$lokasi', '$status', '$realtime', '$deskripsi', '$foto', 'pending')";

        if (mysqli_query($conn, $sql)) {
            // FITUR: Jika User lapor rusak/offline, update tabel pengelolaan otomatis
            if($status == 'rusak' || $status == 'offline'){
                 $lokasi_esc = mysqli_real_escape_string($conn, $lokasi);
                 $status_esc = mysqli_real_escape_string($conn, $status);
                 mysqli_query($conn, "UPDATE pengelolaan SET status='$status_esc' WHERE lokasi='$lokasi_esc'");
            }

            $last_id = mysqli_insert_id($conn);
            $msg = "
            <div class='alert-success'>
                <b>Laporan Terkirim ✔</b><br>
                ID Tiket Anda: <b style='font-size:18px;'>$last_id</b><br>
                <small>Mohon simpan ID ini.</small>
            </div>";
        } else {
            $msg = "<div class='alert'>Error: " . mysqli_error($conn) . "</div>";
        }
    }
}
?>

<!DOCTYPE html>
<html>

<head>
    <title>Form Keluhan CCTV</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <style>
    body {
        margin: 0;
        font-family: Arial, sans-serif;
        background: #e8f0fb;
        padding: 20px;
    }

    .form-wrapper {
        max-width: 500px;
        /* Lebar maksimal agar rapi di HP */
        margin: auto;
        background: white;
        padding: 25px;
        border-radius: 12px;
        box-shadow: 0 2px 6px rgba(0, 0, 0, 0.15);
    }

    h2 {
        margin-bottom: 10px;
        color: #0b3d91;
        text-align: center;
    }

    label {
        font-weight: bold;
        margin-top: 12px;
        display: block;
    }

    input,
    select,
    textarea {
        width: 100%;
        padding: 10px;
        margin-top: 6px;
        border-radius: 8px;
        border: 1px solid #ccc;
        font-size: 14px;
        box-sizing: border-box;
    }

    textarea {
        height: 90px;
    }

    .btn-submit {
        margin-top: 20px;
        padding: 12px;
        background-color: #0b3d91;
        color: white;
        border: none;
        border-radius: 8px;
        width: 100%;
        font-size: 15px;
        font-weight: bold;
        cursor: pointer;
    }

    .alert-success {
        background: #d9ffe3;
        padding: 12px;
        border-left: 5px solid #28a745;
        margin-bottom: 15px;
        border-radius: 6px;
        color: #155724;
    }

    .alert {
        background: #ffd9d9;
        padding: 12px;
        border-radius: 6px;
        color: red;
        margin-bottom: 15px;
    }

    .back-btn {
        display: block;
        text-align: center;
        margin-top: 15px;
        color: #0b3d91;
        text-decoration: none;
    }
    </style>
</head>

<body>

    <div class="form-wrapper">
        <h2>Form Keluhan CCTV</h2>
         <?= $msg ?>

        <form method="POST" enctype="multipart/form-data">
            <label>Nama Pelapor</label>
            <input type="text" name="nama_pelapor" required>

            <label>Nomor HP (WhatsApp)</label>
            <input type="number" name="no_hp" placeholder="08..." required>

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
                <option>Lobby</option>
                <option>Ruang Kerja</option>
                <option>Ruang Monitoring CCTV</option>
                <option>Ruang Infrastruktur</option>
                <option>Ruang Jaringan</option>
                <option>Ruang Keamanan</option>
                <option>Ruang Server</option>
                <option>Ruang Meeting</option>
                <option>Ruang Kerja Praktik</option>
                <option>Ruang Programmer</option>
            </select>

            <label>Kondisi CCTV</label>
            <select name="status" required>
                <option value="rusak">Rusak</option>
                <option value="offline">Offline</option>
                <option value="online">Online (Masalah Lain)</option>
            </select>

            <label>Deskripsi Keluhan</label>
            <textarea name="deskripsi" required></textarea>

            <label>Ambil Foto</label>
            <input type="file" name="foto" accept="image/*" capture="environment">
            <small style="color:gray">*Otomatis buka kamera di HP</small>

            <button type="submit" class="btn-submit">Kirim Keluhan</button>
        </form>
        <a href="landing_page.php" class="back-btn">← Kembali ke Halaman Utama</a>
    </div>
</body>

</html>