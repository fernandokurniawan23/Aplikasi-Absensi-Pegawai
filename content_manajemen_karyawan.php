<?php

if ($_SESSION['user_role'] !== 'Admin') {
    die("Akses ditolak.");
}

$result = mysqli_query($koneksi, "SELECT id, nama_lengkap, username, jabatan, role FROM users ORDER BY nama_lengkap ASC");
?>

<style>
    .page-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 1rem; }
    .btn-tambah { background-color: #28a745; color: white; padding: 10px 15px; text-decoration: none; border-radius: 5px; }
    .data-table { width: 100%; border-collapse: collapse; }
    .data-table th, .data-table td { border: 1px solid #ccc; padding: 10px; text-align: left; }
</style>

<div class="page-header">
    <h1>Manajemen Karyawan</h1>
    <a href="index.php?page=tambah_karyawan" class="btn-tambah">+ Karyawan Baru</a>
</div>

<table class="data-table">
    <thead>
        <tr>
            <th>Nama Lengkap</th>
            <th>Jabatan</th>
            <th>Username</th>
            <th>Role</th>
            </tr>
    </thead>
    <tbody>
        <?php while ($user = mysqli_fetch_assoc($result)): ?>
        <tr>
            <td><?= htmlspecialchars($user['nama_lengkap']) ?></td>
            <td><?= htmlspecialchars($user['jabatan']) ?></td>
            <td><?= htmlspecialchars($user['username']) ?></td>
            <td><?= htmlspecialchars($user['role']) ?></td>
        </tr>
        <?php endwhile; ?>
    </tbody>
</table>