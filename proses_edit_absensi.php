<?php
session_start();
include 'koneksi.php';

// memastikan admin yang bisa proses
if (!isset($_SESSION['is_logged_in']) || $_SESSION['user_role'] !== 'Admin') {
    die("Akses ditolak.");
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Ambil data dari form
    $user_id = $_POST['user_id'];
    $tanggal = $_POST['tanggal'];
    $jam_masuk = !empty($_POST['jam_masuk']) ? $_POST['jam_masuk'] : null;
    $jam_pulang = !empty($_POST['jam_pulang']) ? $_POST['jam_pulang'] : null;
    $status = $_POST['status'];

    // Cek apakah data untuk user & tanggal ini sudah ada
    $stmt_cek = mysqli_prepare($koneksi, "SELECT id_absensi FROM absensi WHERE user_id = ? AND tanggal_absen = ?");
    mysqli_stmt_bind_param($stmt_cek, "is", $user_id, $tanggal);
    mysqli_stmt_execute($stmt_cek);
    $result_cek = mysqli_stmt_get_result($stmt_cek);
    $existing_absensi = mysqli_fetch_assoc($result_cek);
    mysqli_stmt_close($stmt_cek);

    if ($existing_absensi) {
        // Jika sudah ada, update data
        $id_absensi = $existing_absensi['id_absensi'];
        $stmt_update = mysqli_prepare($koneksi, "UPDATE absensi SET jam_masuk = ?, jam_pulang = ?, status = ? WHERE id_absensi = ?");
        mysqli_stmt_bind_param($stmt_update, "sssi", $jam_masuk, $jam_pulang, $status, $id_absensi);
        mysqli_stmt_execute($stmt_update);
        mysqli_stmt_close($stmt_update);
    } else {
        // Jika belum, inset data baru (kecuali "absen")
        if ($status !== 'Absen') {
            $stmt_insert = mysqli_prepare($koneksi, "INSERT INTO absensi (user_id, tanggal_absen, jam_masuk, jam_pulang, status) VALUES (?, ?, ?, ?, ?)");
            mysqli_stmt_bind_param($stmt_insert, "issss", $user_id, $tanggal, $jam_masuk, $jam_pulang, $status);
            mysqli_stmt_execute($stmt_insert);
            mysqli_stmt_close($stmt_insert);
        }
    }

    // Arahkan kembali ke halaman rekapitulasi
    header("Location: index.php?page=presensi");
    exit();
}
?>