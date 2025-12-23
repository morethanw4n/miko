<?php
session_start();
include 'config.php';

if (!isset($_SESSION['username']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit();
}

$sql = "SELECT id_keluhan, nama_pelapor, jenis, lokasi, status, deskripsi, foto, aksi 
        FROM keluhan 
        ORDER BY id_keluhan ASC";
$result = mysqli_query($conn, $sql);
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Daftar Keluhan CCTV - Admin</title>

    <style>
    body {
        margin: 0;
        font-family: Arial, sans-serif;
        background: #e8f0fb;
    }

    /* SIDEBAR */
    .sidebar {
        width: 250px;
        height: 100vh;
        background: #0b3d91;
        color: #fff;
        position: fixed;
        top: 0;
        left: 0;
        padding-top: 30px;
        text-align: center;
    }

    .sidebar img {
        width: 80px
    }

    .company {
        margin-top: 15px;
        font-weight: bold;
        font-size: 13px
    }

    .menu {
        margin-top: 30px
    }

    .menu a {
        display: block;
        padding: 12px 20px;
        color: #fff;
        text-decoration: none;
        margin: 5px 15px;
        border-radius: 8px;
        text-align: left;
        font-size: 15px
    }

    .menu a.active,
    .menu a:hover {
        background: #1b5fcc
    }

    /* MAIN */
    .main-area {
        margin-left: 250px
    }

    /* HEADER */
    .header {
        background: #fff;
        padding: 15px 25px;
        display: flex;
        justify-content: space-between;
        align-items: center;
        box-shadow: 0 2px 5px rgba(0, 0, 0, .1);
        font-size: 18px;
        font-weight: bold;
    }

    /* USER */
    .user-dropdown {
        position: relative
    }

    #user-btn {
        padding: 8px 12px;
        background: #f0f4ff;
        border-radius: 7px;
        color: #0b3d91;
        cursor: pointer
    }

    #dropdown-menu {
        display: none;
        position: absolute;
        right: 0;
        top: 45px;
        background: #fff;
        border-radius: 8px;
        width: 160px;
        box-shadow: 0 4px 10px rgba(0, 0, 0, .15)
    }

    #dropdown-menu a {
        display: block;
        padding: 12px;
        text-decoration: none;
        color: #333;
        border-bottom: 1px solid #eee
    }

    /* CONTENT */
    .content-wrapper {
        width: 95%;
        margin: 25px auto
    }

    .table-container {
        background: #fff;
        padding: 30px;
        border-radius: 14px;
        box-shadow: 0 2px 6px rgba(0, 0, 0, .15)
    }

    /* TABLE */
    table {
        width: 100%;
        border-collapse: collapse;
        text-align: center
    }

    th {
        background: #0b3d91;
        color: #fff;
        padding: 14px
    }

    td {
        padding: 12px;
        border-bottom: 1px solid #ddd
    }

    .foto {
        width: 90px;
        height: 90px;
        object-fit: cover;
        border-radius: 6px;
        border: 1px solid #ccc
    }

    /* STATUS */
    .status-pending {
        color: #f39c12;
        font-weight: bold
    }

    .status-proses {
        color: #3498db;
        font-weight: bold
    }

    .status-selesai {
        color: #27ae60;
        font-weight: bold
    }

    /* BUTTON */
    .btn {
        padding: 10px 14px;
        border-radius: 6px;
        text-decoration: none;
        color: white;
        font-size: 14px;
        display: inline-block;
    }

    .btn-hapus {
        background: #dc3545
    }

    .btn-excel {
        background: #0b3d91
    }

    .btn-print {
        background: #28a745
    }

    /* ACTION BOTTOM */
    .action-bottom {
        display: flex;
        justify-content: flex-end;
        gap: 10px;
        margin-top: 15px;
    }

    @media print {

        .sidebar,
        .header,
        .action-bottom {
            display: none !important;
        }

        .main-area {
            margin-left: 0 !important;
        }

        /* SEMBUNYIKAN KOLOM HAPUS SAAT CETAK */
        table th:nth-child(9),
        table td:nth-child(9) {
            display: none !important;
        }
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
            <a href="pengelolaan_admin.php">Pengelolaan</a>
            <a href="tambah_keluhan.php">Tambah Keluhan</a>
            <a href="daftar_keluhan_admin.php" class="active">Daftar Keluhan</a>
        </div>
    </div>

    <!-- MAIN -->
    <div class="main-area">

        <div class="header">
            📋 Daftar Keluhan CCTV
            <div class="user-dropdown">
                <span id="user-btn">Hi <?= htmlspecialchars($_SESSION['username']) ?> ▼</span>
                <div id="dropdown-menu">
                    <a href="ubah_password.php">Ubah Password</a>
                    <a href="logout.php">Logout</a>
                </div>
            </div>
        </div>

        <div class="content-wrapper">
            <div class="table-container">

                <h2>Daftar Keluhan</h2>

                <div style="overflow-x:auto;">
                    <table>
                        <tr>
                            <th>No</th>
                            <th>Pelapor</th>
                            <th>No HP</th>
                            <th>Lokasi</th>
                            <th>Status CCTV</th>
                            <th>Deskripsi</th>
                            <th>Foto</th>
                            <th>Status Pengerjaan</th>
                            <th>Aksi</th>
                        </tr>

                        <?php $no=1; while($row=mysqli_fetch_assoc($result)): ?>
                        <tr>
                            <td><?= $no++ ?></td>
                            <td><?= htmlspecialchars($row['nama_pelapor']) ?></td>

                            <td>
                                <?php if(!empty($row['no_hp'])): ?>
                                <a href="https://wa.me/<?= preg_replace('/^0/', '62', $row['no_hp']) ?>" target="_blank"
                                    style="color:green; text-decoration:none; font-weight:bold;">
                                    <?= htmlspecialchars($row['no_hp']) ?> 📞
                                </a>
                                <?php else: ?> - <?php endif; ?>
                            </td>

                            <td><?= htmlspecialchars($row['lokasi']) ?></td>
                            <td><?= htmlspecialchars($row['status']) ?></td>
                            <td><?= htmlspecialchars($row['deskripsi']) ?></td>
                            <td>
                                <?php if($row['foto']): ?>
                                <img src="uploads/<?= htmlspecialchars($row['foto']) ?>" class="foto"
                                    onclick="window.open(this.src)">
                                <?php else: ?> - <?php endif; ?>
                            </td>
                            <td>
                                <?php
        if ($row['aksi']=='pending') echo "<span class='status-pending'>Pending</span>";
        elseif ($row['aksi']=='proses') echo "<span class='status-proses'>Proses</span>";
        else echo "<span class='status-selesai'>Selesai</span>";
        ?>
                            </td>
                            <td>
                                <a href="hapus_keluhan.php?id=<?= $row['id_keluhan'] ?>" class="btn btn-hapus"
                                    onclick="return confirm('Hapus?')">Hapus</a>
                            </td>
                        </tr>
                        <?php endwhile; ?>
                    </table>
                </div>
                <div class="action-bottom">
                    <a href="keluhan_excel.php" class="btn btn-excel">📊 Excel</a>
                    <a href="cetak_laporan.php" target="_blank" class="btn btn-print">🖨️ PDF / Cetak</a>
                </div>

            </div>
        </div>
    </div>

    <script>
    const btn = document.getElementById('user-btn');
    const menu = document.getElementById('dropdown-menu');
    btn.onclick = () => menu.style.display = menu.style.display === 'block' ? 'none' : 'block';
    document.addEventListener('click', e => {
        if (!btn.contains(e.target) && !menu.contains(e.target))
            menu.style.display = 'none';
    });
    </script>

</body>

</html>