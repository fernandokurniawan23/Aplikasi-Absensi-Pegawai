<?php
session_start();
include 'koneksi.php';

if (!isset($_SESSION['is_logged_in']) || $_SESSION['user_role'] !== 'Admin') {
    die("Akses ditolak.");
}

if (isset($_GET['aksi']) && isset($_GET['id'])) {
    $aksi = $_GET['aksi'];
    $user_id = (int)$_GET['id'];
    $tanggal_absen = date('Y-m-d');

    if ($aksi == 'hadir') {
        $jam_masuk = date('H:i:s');
        $stmt_cek = mysqli_prepare($koneksi, "SELECT id_absensi FROM absensi WHERE user_id = ? AND tanggal_absen = ?");
        mysqli_stmt_bind_param($stmt_cek, "is", $user_id, $tanggal_absen);
        mysqli_stmt_execute($stmt_cek);
        $result_cek = mysqli_stmt_get_result($stmt_cek);

        if (mysqli_num_rows($result_cek) == 0) {
            $stmt_insert = mysqli_prepare($koneksi, "INSERT INTO absensi (user_id, tanggal_absen, jam_masuk) VALUES (?, ?, ?)");
            mysqli_stmt_bind_param($stmt_insert, "iss", $user_id, $tanggal_absen, $jam_masuk);
            mysqli_stmt_execute($stmt_insert);
        }

    } elseif ($aksi == 'batal') {
        $stmt_delete = mysqli_prepare($koneksi, "DELETE FROM absensi WHERE user_id = ? AND tanggal_absen = ?");
        mysqli_stmt_bind_param($stmt_delete, "is", $user_id, $tanggal_absen);
        mysqli_stmt_execute($stmt_delete);
    }
}

header("Location: index.php?page=presensi");
exit();
?>