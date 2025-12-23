<?php
session_start();
include 'config.php';

if (!isset($_SESSION['username']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit();
}

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    die("ID tidak valid.");
}

$id = (int) $_GET['id'];

$query = mysqli_query(
    $conn,
    "SELECT foto FROM pengelolaan WHERE id_pengelolaan = $id"
);

if (mysqli_num_rows($query) === 0) {
    die("Data CCTV tidak ditemukan.");
}

$data = mysqli_fetch_assoc($query);

if (!empty($data['foto'])) {
    $path = "uploads/" . $data['foto'];
    if (file_exists($path)) {
        unlink($path);
    }
}

$delete = mysqli_query(
    $conn,
    "DELETE FROM pengelolaan WHERE id_pengelolaan = $id"
);

if ($delete) {
    echo "<script>
        alert('Data CCTV berhasil dihapus');
        window.location = 'pengelolaan_admin.php';
    </script>";
} else {
    echo "Gagal menghapus data: " . mysqli_error($conn);
}
?>
