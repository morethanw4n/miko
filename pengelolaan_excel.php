<?php
session_start();
include 'config.php';

if (!isset($_SESSION['username']) || $_SESSION['role'] != 'admin') {
    header("Location: login.php");
    exit();
}

header("Content-Type: application/vnd.ms-excel");
header("Content-Disposition: attachment; filename=pengelolaan_cctv.xls");

echo "ID\tNama\tStatus\tJenis\tLokasi\tRealtime\n";

$query = mysqli_query($conn, "SELECT * FROM pengelolaan ORDER BY id_pengelolaan ASC");

while($row = mysqli_fetch_assoc($query)){
    echo $row['id_pengelolaan']."\t".
         $row['nama']."\t".
         $row['status']."\t".
         $row['jenis']."\t".
         $row['lokasi']."\t".
         $row['realtime']."\n";
}
