<?php
session_start();
include 'koneksi.php';

if (!isset($_SESSION['is_logged_in']) || $_SESSION['user_role'] !== 'Admin') {
    die("Akses ditolak.");
}

if (isset($_GET['id']) && isset($_GET['aksi'])) {
    $id_pengajuan = (int)$_GET['id'];
    $aksi = $_GET['aksi'];
    $status_baru = ($aksi === 'setuju') ? 'Disetujui' : 'Ditolak';

    // Update status pengajuan di tabel pengajuan_izin
    $stmt_update = mysqli_prepare($koneksi, "UPDATE pengajuan_izin SET status_pengajuan = ? WHERE id_pengajuan = ?");
    mysqli_stmt_bind_param($stmt_update, "si", $status_baru, $id_pengajuan);
    mysqli_stmt_execute($stmt_update);
    mysqli_stmt_close($stmt_update);

    // Jika disetujui, masukkan data ke tabel absensi
    if ($status_baru === 'Disetujui') {
        // Ambil detail pengajuan
        $stmt_detail = mysqli_prepare($koneksi, "SELECT user_id, jenis_izin, tanggal_mulai, tanggal_selesai FROM pengajuan_izin WHERE id_pengajuan = ?");
        mysqli_stmt_bind_param($stmt_detail, "i", $id_pengajuan);
        mysqli_stmt_execute($stmt_detail);
        $result_detail = mysqli_stmt_get_result($stmt_detail);
        $pengajuan = mysqli_fetch_assoc($result_detail);
        mysqli_stmt_close($stmt_detail);

        if ($pengajuan) {
            $user_id = $pengajuan['user_id'];
            $status_absensi = $pengajuan['jenis_izin'];
            $tanggal_mulai = new DateTime($pengajuan['tanggal_mulai']);
            $tanggal_selesai = new DateTime($pengajuan['tanggal_selesai']);
            $tanggal_selesai->modify('+1 day'); 

            $interval = new DateInterval('P1D');
            $period = new DatePeriod($tanggal_mulai, $interval, $tanggal_selesai);

            // Perulangan untuk setiap hari dalam rentang tanggal
            foreach ($period as $tanggal) {
                $tanggal_str = $tanggal->format('Y-m-d');
                
                // menimpa data jika sudah ada or menyisipkan jika belum ada
                $stmt_absensi = mysqli_prepare($koneksi, "
                    REPLACE INTO absensi (user_id, tanggal_absen, status) 
                    VALUES (?, ?, ?)
                ");
                mysqli_stmt_bind_param($stmt_absensi, "iss", $user_id, $tanggal_str, $status_absensi);
                mysqli_stmt_execute($stmt_absensi);
                mysqli_stmt_close($stmt_absensi);
            }
        }
    }

    header("Location: index.php?page=persetujuan_izin");
    exit();
}