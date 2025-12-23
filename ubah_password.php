<?php
session_start();
include 'config.php';

// Proteksi halaman: hanya user login
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}

$username = $_SESSION['username'];
$message = "";

// Proses form submit
if (isset($_POST['submit'])) {
    $password_lama = $_POST['password_lama'] ?? '';
    $password_baru = $_POST['password_baru'] ?? '';
    $konfirmasi_password = $_POST['konfirmasi_password'] ?? '';

    // Ambil password lama dari database
    $query = mysqli_query($conn, "SELECT password FROM users WHERE username='$username'");
    $user = mysqli_fetch_assoc($query);

    if (!$user) {
        $message = "User tidak ditemukan.";
    } elseif (!password_verify($password_lama, $user['password'])) {
        $message = "Password lama salah.";
    } elseif ($password_baru !== $konfirmasi_password) {
        $message = "Konfirmasi password baru tidak cocok.";
    } elseif (strlen($password_baru) < 6) {
        $message = "Password baru minimal 6 karakter.";
    } else {
        // Update password
        $password_hash = password_hash($password_baru, PASSWORD_DEFAULT);
        $update = mysqli_query($conn, "UPDATE users SET password='$password_hash' WHERE username='$username'");
        if ($update) {
            $message = "Password berhasil diubah.";
        } else {
            $message = "Terjadi kesalahan saat mengubah password.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<title>Ubah Password - PUSRI</title>
<style>
body {
    font-family: Arial, sans-serif;
    background: linear-gradient(to bottom right, #0057A8, #013567);
    margin: 0;
    padding: 0;
    height: 100vh;
    display: flex;
    justify-content: center;
    align-items: center;
    color: #fff;
}

.password-box {
    width: 380px;
    background: #ffffff;
    padding: 35px 30px;
    border-radius: 12px;
    text-align: center;
    box-shadow: 0 8px 20px rgba(0, 0, 0, 0.25);
    color: #333;
}

.password-box h2 {
    font-size: 22px;
    margin-bottom: 20px;
    font-weight: bold;
    color: #0057A8;
}

input[type="password"] {
    width: 100%;
    padding: 12px;
    margin: 10px 0 15px 0;
    border: 1px solid #ccc;
    border-radius: 6px;
    font-size: 15px;
}

button {
    width: 100%;
    padding: 12px;
    background: #0057A8;
    border: none;
    font-size: 16px;
    border-radius: 6px;
    color: #fff;
    cursor: pointer;
    transition: 0.3s;
    font-weight: bold;
}

button:hover {
    background: #003f7c;
}

.message {
    font-weight: bold;
    margin-bottom: 15px;
}

.message.error { color: red; }
.message.success { color: green; }

a.back {
    display: block;
    margin-top: 10px;
    color: #0057A8;
    text-decoration: none;
}

a.back:hover {
    text-decoration: underline;
}
</style>
</head>
<body>

<div class="password-box">

    <h2>Ubah Password</h2>

    <?php if($message): ?>
        <div class="message <?= strpos($message,'berhasil')!==false ? 'success':'error' ?>"><?= $message ?></div>
    <?php endif; ?>

    <form method="POST">
        <input type="password" name="password_lama" placeholder="Password Lama" required>
        <input type="password" name="password_baru" placeholder="Password Baru" required>
        <input type="password" name="konfirmasi_password" placeholder="Konfirmasi Password Baru" required>
        <button type="submit" name="submit">Simpan Password</button>
    </form>

    <a class="back" href="dashboard_admin.php">← Kembali ke Dashboard</a>
</div>

</body>
</html>
