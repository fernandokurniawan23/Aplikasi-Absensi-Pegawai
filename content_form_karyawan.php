<?php
if ($_SESSION['user_role'] !== 'Admin') {
    die("Akses ditolak.");
}
?>

<style>
    .form-karyawan { max-width: 600px; }
    .form-group { margin-bottom: 1rem; }
    .form-group label { display: block; margin-bottom: 5px; font-weight: bold; }
    .form-group input, .form-group select { width: 100%; padding: 8px; box-sizing: border-box; border: 1px solid #ccc; border-radius: 4px; }
    .btn-simpan { background-color: #007bff; color: white; padding: 10px 15px; border: none; border-radius: 4px; cursor: pointer; }
</style>

<h1>Formulir Tambah Pengguna Baru</h1>

<form action="proses_tambah_karyawan.php" method="POST" class="form-karyawan">
    <div class="form-group">
        <label for="nama_lengkap">Nama Lengkap</label>
        <input type="text" id="nama_lengkap" name="nama_lengkap" required>
    </div>
    <div class="form-group">
        <label for="jabatan">Jabatan</label>
        <input type="text" id="jabatan" name="jabatan" required>
    </div>
    <div class="form-group">
        <label for="username">Username</label>
        <input type="text" id="username" name="username" required>
    </div>
    <div class="form-group">
        <label for="password">Password</label>
        <input type="password" id="password" name="password" required>
    </div>
    <div class="form-group">
        <label for="role">Role</label>
        <select id="role" name="role" required>
            <option value="Karyawan">Karyawan</option>
            <option value="Admin">Admin</option>
        </select>
    </div>
    <button type="submit" class="btn-simpan">Simpan Pengguna</button>
</form>