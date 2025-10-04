<?php
// absensi.php
session_start();

// ===================================================
// LOGIKA PEMBATASAN AKSES (HANYA KARYAWAN)
// ===================================================

// Periksa apakah pengguna sudah login DAN memiliki peran Karyawan
if (!isset($_SESSION['is_logged_in']) || $_SESSION['is_logged_in'] !== true || ($_SESSION['user_role'] ?? '') !== 'Karyawan') {
    header('Location: login.php');
    exit();
}

// Data pengguna dari sesi
$user_name = $_SESSION['user_name'];
$user_id = $_SESSION['user_id'];
$message = ''; // Pesan feedback

// ===================================================
// LOGIKA PEMROSESAN PRESENSI
// ===================================================

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit_presensi'])) {
    $nama = $_POST['nama'] ?? '';
    $jabatan = $_POST['jabatan'] ?? '';
    $current_time = date('H:i:s');
    
    // --- SIMULASI PEMROSESAN DATA ---
    if (!empty($jabatan)) {
        $message = "Halo, " . htmlspecialchars($nama) . "! Presensi HADIR Anda untuk jabatan " . htmlspecialchars($jabatan) . " telah dicatat pada pukul " . $current_time . ".";
    } else {
        $message = "Gagal: Mohon masukkan Jabatan Anda.";
    }
}

?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Presensi Karyawan - PT.SINERGI</title>
    <link rel="stylesheet" href="css/style.css"> 
</head>
<body>

<div class="main-layout"> 
    
    <div class="sidebar">
        <div class="logo">PT.SINERGI</div>
        <div class="user-info">
            <div class="user-icon"></div>
            <p class="user-name"><?= htmlspecialchars($user_name) ?></p>
            <p class="user-id"><?= htmlspecialchars($user_id) ?></p>
        </div>
        <nav class="nav-menu">
            <ul>
                <li><a href="index.php?page=beranda">Beranda</a></li>
                <li class="active"><a href="absensi.php">Presensi</a></li>
                <li><a href="logout.php">Keluar</a></li>
            </ul>
        </nav>
    </div>

    <div class="content-area">
        <div class="content-body">
            <h1 class="page-title">PRESENSI KARYAWAN</h1>

            <?php if ($message): ?>
                <p class="feedback-message" style="color: green; font-weight: bold; margin-bottom: 20px;"><?= $message ?></p>
            <?php endif; ?>

            <div class="presensi-form-card">
                <form method="POST" action="absensi.php">
                    
                    <div class="form-row">
                        <label for="nama">Nama</label>
                        <input type="text" id="nama" name="nama" 
                               value="<?= htmlspecialchars($user_name) ?>" 
                               readonly> 
                    </div>

                    <div class="form-row">
                        <label for="jabatan">Jabatan</label>
                        <input type="text" id="jabatan" name="jabatan" placeholder="Masukkan Jabatan Anda" required>
                    </div>

                    <button type="submit" name="submit_presensi" class="btn-hadir">Hadir</button>
                    
                </form>
            </div>
        </div>
    </div>
</div> </body>
</html>