<?php
session_start();
include 'config.php';

if (!isset($_SESSION['username']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit();
}

if (!isset($_GET['id'])) {
    die("ID keluhan tidak ditemukan.");
}

$id_keluhan = intval($_GET['id']);

$cek = mysqli_query(
    $conn,
    "SELECT foto FROM keluhan WHERE id_keluhan = $id_keluhan LIMIT 1"
);

if (!$cek || mysqli_num_rows($cek) === 0) {
    die("Data keluhan tidak ditemukan.");
}

$data = mysqli_fetch_assoc($cek);

if (!empty($data['foto']) && file_exists("uploads/" . $data['foto'])) {
    unlink("uploads/" . $data['foto']);
}

$hapus = mysqli_query(
    $conn,
    "DELETE FROM keluhan WHERE id_keluhan = $id_keluhan"
);

if (!$hapus) {
    die("Gagal menghapus keluhan: " . mysqli_error($conn));
}

header("Location: daftar_keluhan_admin.php?msg=deleted");
exit();
