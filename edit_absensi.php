<?php
session_start();
include 'koneksi.php';

// pastikan admin yang bisa akses
if (!isset($_SESSION['is_logged_in']) || $_SESSION['user_role'] !== 'Admin') {
    die("Akses ditolak. Halaman ini hanya untuk Admin.");
}

$user_id = $_GET['user_id'] ?? 0;
$tanggal = $_GET['tanggal'] ?? date('Y-m-d');
$data_absensi = null;
$nama_karyawan = '';

// nama karyawan
$stmt_user = mysqli_prepare($koneksi, "SELECT nama_lengkap FROM users WHERE id = ?");
mysqli_stmt_bind_param($stmt_user, "i", $user_id);
mysqli_stmt_execute($stmt_user);
$result_user = mysqli_stmt_get_result($stmt_user);
if ($user = mysqli_fetch_assoc($result_user)) {
    $nama_karyawan = $user['nama_lengkap'];
}
mysqli_stmt_close($stmt_user);

// data absensi
$stmt_absensi = mysqli_prepare($koneksi, "SELECT * FROM absensi WHERE user_id = ? AND tanggal_absen = ?");
mysqli_stmt_bind_param($stmt_absensi, "is", $user_id, $tanggal);
mysqli_stmt_execute($stmt_absensi);
$result_absensi = mysqli_stmt_get_result($stmt_absensi);
if ($data = mysqli_fetch_assoc($result_absensi)) {
    $data_absensi = $data;
}
mysqli_stmt_close($stmt_absensi);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Edit Absensi Karyawan</title>
    <style>
        body { font-family: sans-serif; padding: 2rem; }
        .form-edit { width: 500px; margin: auto; border: 1px solid #ccc; padding: 1.5rem; border-radius: 8px; }
        .form-group { margin-bottom: 1rem; }
        .form-group label { display: block; margin-bottom: 5px; }
        .form-group input, .form-group select { width: 100%; padding: 8px; box-sizing: border-box; }
        .btn-simpan { background-color: #28a745; color: white; padding: 10px 15px; border: none; border-radius: 4px; cursor: pointer; }
        .link-kembali { display: inline-block; margin-top: 1rem; }
    </style>
</head>
<body>
    <div class="form-edit">
        <h2>Edit Absensi Karyawan</h2>
        <form action="proses_edit_absensi.php" method="POST">
            <input type="hidden" name="user_id" value="<?= htmlspecialchars($user_id) ?>">
            <input type="hidden" name="tanggal" value="<?= htmlspecialchars($tanggal) ?>">
            
            <div class="form-group">
                <label>Nama Karyawan</label>
                <input type="text" value="<?= htmlspecialchars($nama_karyawan) ?>" readonly>
            </div>
            <div class="form-group">
                <label>Tanggal</label>
                <input type="text" value="<?= date('d F Y', strtotime($tanggal)) ?>" readonly>
            </div>
            <div class="form-group">
                <label for="jam_masuk">Jam Masuk (Contoh: 08:30:00)</label>
                <input type="text" id="jam_masuk" name="jam_masuk" value="<?= $data_absensi['jam_masuk'] ?? '' ?>">
            </div>
            <div class="form-group">
                <label for="jam_pulang">Jam Pulang (Contoh: 17:00:00)</label>
                <input type="text" id="jam_pulang" name="jam_pulang" value="<?= $data_absensi['jam_pulang'] ?? '' ?>">
            </div>
            <div class="form-group">
                <label for="status">Status</label>
                <select id="status" name="status" required>
                    <option value="Hadir" <?= (($data_absensi['status'] ?? '') == 'Hadir') ? 'selected' : '' ?>>Hadir</option>
                    <option value="Terlambat" <?= (($data_absensi['status'] ?? '') == 'Terlambat') ? 'selected' : '' ?>>Terlambat</option>
                    <option value="Sakit" <?= (($data_absensi['status'] ?? '') == 'Sakit') ? 'selected' : '' ?>>Sakit</option>
                    <option value="Izin" <?= (($data_absensi['status'] ?? '') == 'Izin') ? 'selected' : '' ?>>Izin</option>
                    <option value="Absen" <?= (($data_absensi['status'] ?? '') == '') ? 'selected' : '' ?>>Absen</option>
                </select>
            </div>
            <button type="submit" class="btn-simpan">Simpan Perubahan</button>
        </form>
        <a href="index.php?page=presensi" class="link-kembali">Kembali ke Rekapitulasi</a>
    </div>
</body>
</html>