<?php
session_start();
include 'koneksi.php';

if (!isset($_SESSION['is_logged_in']) || $_SESSION['user_role'] !== 'Admin') {
    die("Akses ditolak.");
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Ambil data dari form
    $nama_lengkap = $_POST['nama_lengkap'];
    $jabatan = $_POST['jabatan'];
    $username = $_POST['username'];
    $password = $_POST['password'];
    $role = $_POST['role'];

    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

    //query untuk menyimpan karyawan baru
    $stmt = mysqli_prepare($koneksi, "INSERT INTO users (nama_lengkap, jabatan, username, password, role) VALUES (?, ?, ?, ?, ?)");
    mysqli_stmt_bind_param($stmt, "sssss", $nama_lengkap, $jabatan, $username, $hashed_password, $role);
    
    if (mysqli_stmt_execute($stmt)) {
        header("Location: index.php?page=manajemen_karyawan");
        exit();
    } else {
        echo "Error: Gagal menambahkan pengguna baru. " . mysqli_error($koneksi);
    }
    mysqli_stmt_close($stmt);
}
?>