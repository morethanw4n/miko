<?php
session_start();
include 'config.php'; 

$error = "";

if (isset($_SESSION['username']) && isset($_SESSION['role'])) {
    if ($_SESSION['role'] === 'admin') {
        header("Location: dashboard_admin.php");
    } elseif ($_SESSION['role'] === 'teknisi') {
        header("Location: dashboard_teknisi.php");
    }
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['login'])) {

    $username = trim(mysqli_real_escape_string($conn, $_POST['username']));
    $password = md5(trim($_POST['password']));

    $query = "SELECT * FROM users WHERE username='$username' AND password='$password' LIMIT 1";
    $result = mysqli_query($conn, $query);

    if ($result && mysqli_num_rows($result) === 1) {
        $row = mysqli_fetch_assoc($result);

        $_SESSION['username'] = $row['username'];
        $_SESSION['role'] = $row['role'];

        if ($row['role'] === 'admin') {
            header("Location: dashboard_admin.php");
        } else {
            header("Location: dashboard_teknisi.php");
        }
        exit();
    } else {
        $error = "Username atau password salah!";
    }
}
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Login Sistem CCTV - PUSRI</title>

    <style>
        body {
            font-family: Arial, sans-serif;
            background: linear-gradient(to bottom right, #062439);
            margin: 0;
            padding: 0;
            height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            color: #fff;
        }

        .login-box {
            width: 380px;
            background: #ffffff;
            padding: 35px 30px;
            border-radius: 12px;
            text-align: center;
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.25);
            color: #333;
        }

        .login-box img {
            width: 100px;
            margin-bottom: 15px;
        }

        .login-box h2 {
            font-size: 22px;
            margin-bottom: 20px;
            font-weight: bold;
            color: #0057A8;
        }

        input[type="text"],
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

        .error {
            color: red;
            font-weight: bold;
            margin-bottom: 15px;
        }

        .footer-text {
            margin-top: 10px;
            font-size: 13px;
            color: #444;
        }
    </style>
</head>

<body>

    <div class="login-box">

        <!-- LOGO PUSRI -->
        <img src="images/logo_pusri.png" alt="Logo PUSRI">

        <h2>Sistem Pengelolaan CCTV</h2>

        <?php if (!empty($error)) : ?>
            <div class="error"><?= $error ?></div>
        <?php endif; ?>

        <form method="POST">
            <input type="text" name="username" placeholder="Username" required autofocus>
            <input type="password" name="password" placeholder="Password" required>
            <button type="submit" name="login">Masuk</button>
        </form>

        <div class="footer-text">
            © 2025 PT Pupuk Sriwidjaja Palembang
        </div>
    </div>

</body>

</html>
