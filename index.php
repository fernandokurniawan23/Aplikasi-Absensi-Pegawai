<?php
session_start();
include 'koneksi.php';

if (!isset($_SESSION['is_logged_in']) || $_SESSION['is_logged_in'] !== true) {
    header('Location: login.php');
    exit();
}

$user_role = $_SESSION['user_role'] ?? 'Guest';
$page = $_GET['page'] ?? 'beranda';
$content_file = '';

switch ($page) {
    case 'presensi':
        if ($user_role === 'Admin') $content_file = 'content_presensi_karyawan.php';
        elseif ($user_role === 'Karyawan') $content_file = 'content_form_presensi_karyawan.php';
        break;
    case 'riwayat':
        if ($user_role === 'Karyawan') $content_file = 'content_history_pribadi.php';
        break;
    case 'persetujuan_izin':
        if ($user_role === 'Admin') $content_file = 'content_persetujuan_izin.php';
        break;
    case 'ajukan_izin':
        if ($user_role === 'Karyawan') $content_file = 'content_form_izin.php';
        break;
    case 'manajemen_karyawan':
        if ($user_role === 'Admin') $content_file = 'content_manajemen_karyawan.php';
        break;
    case 'tambah_karyawan':
        if ($user_role === 'Admin') $content_file = 'content_form_karyawan.php';
        break;
    case 'beranda':
    default:
        $content_file = 'content_beranda.php';
        break;
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sistem Absensi</title>
    <style>
        body { font-family: sans-serif; margin: 0; }
        .layout { display: flex; }
        .sidebar { width: 220px; background: #2c3e50; color: white; min-height: 100vh; padding: 1rem; }
        .sidebar h3 { text-align: center; }
        .sidebar ul { list-style: none; padding: 0; }
        .sidebar ul li a { color: white; text-decoration: none; display: block; padding: 10px 15px; border-radius: 4px; }
        .sidebar ul li a:hover, .sidebar ul li.active a { background: #34495e; }
        .content { flex-grow: 1; padding: 2rem; }
    </style>
</head>
<body>
    <div class="layout">
        <div class="sidebar">
            <h3>Absensi Pegawai</h3>
            <p>Halo, <?= htmlspecialchars($_SESSION['user_name']) ?> (<?= htmlspecialchars($_SESSION['user_role']) ?>)</p>
            <ul>
                <li class="<?= $page == 'beranda' ? 'active' : '' ?>"><a href="index.php?page=beranda">Beranda</a></li>
                <li class="<?= $page == 'presensi' ? 'active' : '' ?>"><a href="index.php?page=presensi">
                    <?= ($user_role === 'Admin') ? 'Rekapitulasi' : 'Presensi' ?>
                </a></li>
                
                <?php if ($user_role === 'Karyawan'): ?>
                    <li class="<?= $page == 'riwayat' ? 'active' : '' ?>"><a href="index.php?page=riwayat">Riwayat Absensi</a></li>
                    <li class="<?= $page == 'ajukan_izin' ? 'active' : '' ?>"><a href="index.php?page=ajukan_izin">Ajukan Izin/Cuti</a></li>
                <?php endif; ?>
                
                <?php if ($user_role === 'Admin'): ?>
                    <li class="<?= $page == 'persetujuan_izin' ? 'active' : '' ?>"><a href="index.php?page=persetujuan_izin">Persetujuan Izin</a></li>
                    <li class="<?= ($page == 'manajemen_karyawan' || $page == 'tambah_karyawan') ? 'active' : '' ?>"><a href="index.php?page=manajemen_karyawan">Manajemen Karyawan</a></li>
                <?php endif; ?>

                <li><a href="logout.php">Keluar</a></li>
            </ul>
        </div>
        <div class="content">
            <?php
            if (!empty($content_file) && file_exists($content_file)) {
                include $content_file;
            } else {
                echo "<h2>Halaman Tidak Ditemukan atau Akses Ditolak.</h2>";
            }
            ?>
        </div>
    </div>
</body>
</html>