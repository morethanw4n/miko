<?php
session_start();
include 'config.php';

// Cek Login (Bisa Admin atau Teknisi)
if (!isset($_SESSION['username'])) { header("Location: login.php"); exit(); }

$sql = "SELECT * FROM keluhan ORDER BY id_keluhan DESC";
$result = mysqli_query($conn, $sql);
?>
<!DOCTYPE html>
<html>

<head>
    <title>Cetak Laporan Keluhan</title>
    <style>
    body {
        font-family: Arial, sans-serif;
        padding: 20px;
        font-size: 12px;
    }

    .header {
        text-align: center;
        margin-bottom: 20px;
        border-bottom: 2px solid black;
        padding-bottom: 10px;
    }

    .header h2 {
        margin: 0;
        font-size: 18px;
        text-transform: uppercase;
    }

    .header p {
        margin: 5px 0;
    }

    table {
        width: 100%;
        border-collapse: collapse;
        margin-top: 10px;
    }

    th,
    td {
        border: 1px solid black;
        padding: 6px;
        text-align: left;
    }

    th {
        background-color: #f2f2f2;
    }

    /* Hilangkan tombol saat mode print */
    @media print {
        .no-print {
            display: none;
        }

        @page {
            margin: 1cm;
            size: landscape;
        }

        /* Cetak Landscape agar muat */
    }
    </style>
</head>

<body onload="window.print()">
    <div class="no-print" style="margin-bottom:15px;">
        <button onclick="window.print()" style="padding:10px; font-weight:bold;">🖨️ Cetak / Simpan PDF</button>
        <button onclick="window.history.back()" style="padding:10px;">Kembali</button>
    </div>

    <div class="header">
        <h2>Laporan Rekapitulasi Keluhan CCTV</h2>
        <p>PT PUPUK SRIWIDJAJA PALEMBANG</p>
        <p>Dicetak Tanggal: <?= date('d-m-Y H:i') ?></p>
    </div>

    <table>
        <thead>
            <tr>
                <th width="5%">ID</th>
                <th width="15%">Waktu Lapor</th>
                <th width="15%">Pelapor</th>
                <th width="10%">No HP</th>
                <th width="15%">Lokasi</th>
                <th width="25%">Masalah</th>
                <th width="10%">Status</th>
            </tr>
        </thead>
        <tbody>
            <?php while($row = mysqli_fetch_assoc($result)): ?>
            <tr>
                <td><?= $row['id_keluhan'] ?></td>
                <td><?= $row['realtime'] ?></td>
                <td><?= htmlspecialchars($row['nama_pelapor']) ?></td>
                <td><?= htmlspecialchars($row['no_hp'] ?? '-') ?></td>
                <td><?= htmlspecialchars($row['lokasi']) ?></td>
                <td><?= htmlspecialchars($row['deskripsi']) ?></td>
                <td style="text-transform:uppercase; font-weight:bold;">
                    <?= $row['aksi'] ?>
                </td>
            </tr>
            <?php endwhile; ?>
        </tbody>
    </table>

    <div style="margin-top: 40px; float: right; text-align: center; margin-right: 50px;">
        <p>Mengetahui,</p>
        <br><br><br>
        <p><b>( Admin / Teknisi IT )</b></p>
    </div>

</body>

</html>