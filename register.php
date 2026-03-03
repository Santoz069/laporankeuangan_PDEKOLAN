<?php
session_start();
if (!isset($_SESSION['username']) || $_SESSION['level'] != 'admin') {
    header("Location: login.php");
    exit;
}

include 'koneksi.php';

if (isset($_POST['register'])) {
    $username = $_POST['username'];
    $nama = $_POST['nama_lengkap'];
    $password = md5($_POST['password']);
    $level = $_POST['level'];

    $query = mysqli_query($koneksi, "INSERT INTO users (username, nama_lengkap, password, level) VALUES ('$username', '$nama', '$password', '$level')");

    if ($query) {
        echo "<script>alert('Registrasi berhasil!'); window.location='admin_home.php';</script>";
    } else {
        echo "<script>alert('Gagal registrasi. Username mungkin sudah dipakai.');</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Register User - PD. EKOLAN</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f1f6f9;
        }
        .container {
            max-width: 500px;
            margin-top: 50px;
            background-color: #fff;
            padding: 30px;
            border-radius: 12px;
            box-shadow: 0 4px 20px rgba(0,0,0,0.1);
        }
        .btn-back {
            margin-top: 20px;
        }
    </style>
</head>
<body>

<div class="container">
    <h4 class="mb-4 text-center text-primary">Registrasi User Baru</h4>
    <form method="POST">
        <div class="mb-3">
            <label for="nama_lengkap" class="form-label">Nama Lengkap</label>
            <input type="text" name="nama_lengkap" class="form-control" id="nama_lengkap" required>
        </div>
        <div class="mb-3">
            <label for="username" class="form-label">Username</label>
            <input type="text" name="username" class="form-control" id="username" required>
        </div>
        <div class="mb-3">
            <label for="password" class="form-label">Password</label>
            <input type="password" name="password" class="form-control" id="password" required>
        </div>
        <div class="mb-3">
            <label for="level" class="form-label">Level</label>
            <select name="level" class="form-select" id="level" required>
                <option value="" disabled selected>Jabatan</option>
                <option value="admin">Admin</option>
                <option value="kasir">Kasir</option>
            </select>
        </div>
        <button type="submit" name="register" class="btn btn-primary w-100">Daftar</button>
    </form>
    <a href="admin_home.php" class="btn btn-secondary w-100 btn-back">Kembali ke Dashboard</a>
</div>

</body>
</html>
