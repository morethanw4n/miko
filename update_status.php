<?php
session_start();
include 'config.php';

if (!isset($_SESSION['username']) || $_SESSION['role'] !== 'teknisi') {
    header("Location: login.php");
    exit();
}

if (!isset($_GET['id'], $_GET['status'])) {
    die("Parameter URL tidak lengkap.");
}

$id_keluhan = (int)$_GET['id'];
$aksi_baru  = strtolower($_GET['status']);

// Validasi input status
if (!in_array($aksi_baru, ['proses','selesai'])) {
    die("Aksi tidak valid.");
}

// Ambil data keluhan lama untuk mendapatkan Lokasi CCTV
$q = mysqli_query($conn, "SELECT aksi, lokasi FROM keluhan WHERE id_keluhan=$id_keluhan LIMIT 1");
$data = mysqli_fetch_assoc($q);

if (!$data) { die("Data keluhan tidak ditemukan."); }

$aksi_lama = strtolower($data['aksi']);
$lokasi_target = mysqli_real_escape_string($conn, $data['lokasi']); 

// Validasi urutan status
$allowed = [ 'pending' => 'proses', 'proses'  => 'selesai' ];

if (!isset($allowed[$aksi_lama]) || $allowed[$aksi_lama] !== $aksi_baru) {
    echo "<script>alert('Alur status salah. Harus Pending -> Proses -> Selesai'); window.history.back();</script>";
    exit();
}

// 1. UPDATE Status Keluhan
$update_keluhan = mysqli_query($conn, "UPDATE keluhan SET aksi='$aksi_baru' WHERE id_keluhan=$id_keluhan");

// 2. OTOMATIS Update Status CCTV jadi 'Online' jika keluhan 'Selesai'
if ($update_keluhan && $aksi_baru == 'selesai') {
    mysqli_query($conn, "UPDATE pengelolaan SET status='online' WHERE lokasi='$lokasi_target'");
}

// Redirect kembali
header("Location: daftar_keluhan_teknisi.php?msg=updated");
exit();
?>