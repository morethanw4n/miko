<?php
include 'config.php';

$keluhan = null;
$error = null;

if (isset($_GET['id'])) {
    $id = intval($_GET['id']);

    $query = "SELECT * FROM keluhan WHERE id_keluhan = $id LIMIT 1";
    $result = mysqli_query($conn, $query);

    if ($result && mysqli_num_rows($result) > 0) {
        $keluhan = mysqli_fetch_assoc($result);
    } else {
        $error = "ID keluhan tidak ditemukan.";
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<title>Status Keluhan CCTV</title>

<style>
body {
    font-family: Arial, sans-serif;
    background: #e8f0fb;
    margin: 0;
    padding: 0;
}

.container {
    max-width: 700px;
    background: white;
    margin: 40px auto;
    padding: 25px;
    border-radius: 12px;
    box-shadow: 0 2px 8px rgba(0,0,0,0.15);
}

h2 {
    text-align: center;
    color: #0b3d91;
    margin-bottom: 20px;
}

input[type="number"] {
    width: 100%;
    padding: 10px;
    border-radius: 8px;
    border: 1px solid #ccc;
    margin-bottom: 15px;
    font-size: 15px;
}

button {
    width: 100%;
    padding: 12px;
    background: #0b3d91;
    color: white;
    font-weight: bold;
    border: none;
    border-radius: 8px;
    cursor: pointer;
    font-size: 15px;
}
button:hover {
    background: #124eb8;
}

.detail-box {
    margin-top: 25px;
    padding: 20px;
    background: #f8faff;
    border-radius: 10px;
    border: 1px solid #d8e4ff;
}

.detail-box p {
    margin: 8px 0;
    font-size: 15px;
}

.status-label {
    padding: 6px 10px;
    border-radius: 6px;
    color: white;
    font-weight: bold;
    font-size: 14px;
}

img.foto-keluhan {
    width: 140px;
    border-radius: 8px;
    margin-top: 10px;
    border: 1px solid #ccc;
}

.notfound {
    padding: 12px;
    background: #ffdddd;
    color: #b30000;
    border-radius: 8px;
    margin-top: 15px;
    text-align: center;
}
</style>
</head>

<body>

<div class="container">

<h2>🔍 Cek Status Keluhan CCTV</h2>

<!-- FORM PENCARIAN -->
<form method="GET">
    <input type="number" name="id" placeholder="Masukkan ID Keluhan..." required>
    <button type="submit">Cari Keluhan</button>
</form>

<?php if ($error): ?>
    <div class="notfound"><?= $error ?></div>
<?php endif; ?>

<?php if ($keluhan): ?>

<?php
// ================= STATUS CCTV =================
$status_cctv = strtolower(trim($keluhan['status']));
$warna_status_cctv = match ($status_cctv) {
    'online'  => '#28a745',
    'offline' => '#fd7e14',
    'rusak'   => '#dc3545',
    default   => '#6c757d',
};

// ================= STATUS KELUHAN =================
$status_keluhan = strtolower(trim($keluhan['aksi']));
$warna_status_keluhan = match ($status_keluhan) {
    'pending' => '#ffc107',
    'proses'  => '#0b3d91',
    'selesai' => '#28a745',
    default   => '#6c757d',
};
?>

<div class="detail-box">
    <p><strong>ID Keluhan:</strong> <?= $keluhan['id_keluhan'] ?></p>
    <p><strong>Nama Pelapor:</strong> <?= htmlspecialchars($keluhan['nama_pelapor']) ?></p>
    <p><strong>Jenis CCTV:</strong> <?= htmlspecialchars($keluhan['jenis']) ?></p>
    <p><strong>Lokasi:</strong> <?= htmlspecialchars($keluhan['lokasi']) ?></p>

    <p>
        <strong>Status CCTV:</strong>
        <span class="status-label" style="background: <?= $warna_status_cctv ?>;">
            <?= ucfirst($status_cctv) ?>
        </span>
    </p>

    <p>
        <strong>Status Keluhan:</strong>
        <span class="status-label" style="background: <?= $warna_status_keluhan ?>;">
            <?= ucfirst($status_keluhan) ?>
        </span>
    </p>

    <p>
        <strong>Deskripsi Keluhan:</strong><br>
        <?= nl2br(htmlspecialchars($keluhan['deskripsi'])) ?>
    </p>

    <?php if (!empty($keluhan['foto'])): ?>
        <p><strong>Foto Keluhan:</strong></p>
        <img src="uploads/<?= htmlspecialchars($keluhan['foto']) ?>" class="foto-keluhan">
    <?php else: ?>
        <p><strong>Foto Keluhan:</strong> <span style="color:red;">Tidak ada</span></p>
    <?php endif; ?>
</div>

<?php endif; ?>

</div>

</body>
</html>
