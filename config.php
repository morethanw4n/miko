<?php
// Konfigurasi koneksi database
$host = "localhost";
$user = "root";
$pass = "";
$db   = "db_cctv";

// Membuat koneksi
$conn = mysqli_connect($host, $user, $pass, $db);

// Cek koneksi
if (!$conn) {
    die("Koneksi gagal: " . mysqli_connect_error());
}

mysqli_set_charset($conn, "utf8mb4");

?>
