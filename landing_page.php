<?php session_start(); ?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<title>Sistem Pelaporan CCTV - PT PUSRI</title>

<link href="https://fonts.googleapis.com/css2?family=League+Spartan:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

<style>

body{
    margin:0;
    font-family: "League Spartan", sans-serif;
    background:#062439; 
    color:white;
}

/* HEADER */
.header{
    padding:35px 65px;
    display:flex;
    align-items:center;
    justify-content:space-between;
}

.header img{
    height:90px;
}

.login-btn{
    background:#0056ff;
    padding:12px 35px;
    border-radius:20px;
    font-weight:700;
    color:white;
    text-decoration:none;
    font-size:15px;
    transition:.25s;
}
.login-btn:hover{
    opacity:.8;
    transform:scale(1.05);
}

/* HERO */
.hero{
    text-align:center;
    margin-top:70px;
}

.hero h1{
    font-size:62px;
    font-weight:800;
    margin-bottom:10px;
}

.hero h3{
    font-size:21px;
    font-weight:500;
    margin-bottom:35px;
}

/* paragraf */
.hero p{
    font-size:18px;
    max-width:820px;
    margin:auto;
    line-height:1.6;
    color:white;
}

/* tombol bawah */
.button-group{
    margin-top:45px;
}

.btn{
    padding:15px 45px;
    border-radius:16px;
    display:inline-block;
    font-size:18px;
    font-weight:700;
    text-decoration:none;
    margin:10px;
}

/* tombol sesuai contoh */
.btn-green{
    background:#7ea7c3;
    color:white;
}
.btn-blue{
    background:#0056ff;
    color:white;
}

.btn:hover{
    transform:translateY(-3px);
}

</style>
</head>

<body>

<!-- HEADER -->
<div class="header">
    <img src="images/logo_pusri.png" alt="">
    <a href="login.php" class="login-btn">LOGIN</a>
</div>

<!-- HERO -->
<div class="hero">

    <h1>Sistem Pelaporan CCTV</h1>

    <h3>Departemen Teknologi Informasi – PT Pupuk Sriwidjaja</h3>

    <p>
        Laporkan permasalahan CCTV agar tim Teknologi Informasi dapat segera melakukan perbaikan secara efektif dan terkoordinasi.
        Pantau juga perkembangan laporan Anda secara transparan melalui sistem ini.
    </p>

    <div class="button-group">
        <a href="keluhan_user.php" class="btn btn-green">Ajukan Keluhan</a>
        <a href="status_keluhan.php" class="btn btn-blue">Cek Status Keluhan</a>
    </div>

</div>

</body>
</html>
