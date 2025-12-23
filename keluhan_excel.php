<?php
session_start();
include 'config.php';

if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}

if (!in_array($_SESSION['role'], ['admin', 'teknisi'])) {
    die("Akses ditolak.");
}

header("Content-Type: application/vnd.ms-excel");
header("Content-Disposition: attachment; filename=laporan_keluhan_cctv.xls");
header("Pragma: no-cache");
header("Expires: 0");

$sql = "SELECT 
            id_keluhan,
            nama_pelapor,
            jenis,
            lokasi,
            status,
            deskripsi,
            aksi,
            realtime
        FROM keluhan
        ORDER BY id_keluhan DESC";

$result = mysqli_query($conn, $sql);
?>

<table border="1">
    <thead>
        <tr style="background:#0b3d91;color:white;font-weight:bold;">
            <th>No</th>
            <th>ID Keluhan</th>
            <th>Nama Pelapor</th>
            <th>Jenis</th>
            <th>Lokasi</th>
            <th>Status CCTV</th>
            <th>Deskripsi</th>
            <th>Aksi Teknisi</th>
            <th>Waktu Lapor</th>
        </tr>
    </thead>
    <tbody>
        <?php $no=1; while ($row = mysqli_fetch_assoc($result)): ?>
        <tr>
            <td><?= $no++ ?></td>
            <td><?= $row['id_keluhan'] ?></td>
            <td><?= htmlspecialchars($row['nama_pelapor']) ?></td>
            <td><?= htmlspecialchars($row['jenis']) ?></td>
            <td><?= htmlspecialchars($row['lokasi']) ?></td>
            <td><?= htmlspecialchars($row['status']) ?></td>
            <td><?= htmlspecialchars($row['deskripsi']) ?></td>
            <td><?= ucfirst($row['aksi']) ?></td>
            <td><?= $row['realtime'] ?></td>
        </tr>
        <?php endwhile; ?>
    </tbody>
</table>
