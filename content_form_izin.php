<?php
$user_id = $_SESSION['user_id'];
$message = '';

if (isset($_POST['submit_pengajuan'])) {
    $jenis_izin = $_POST['jenis_izin'];
    $tanggal_mulai = $_POST['tanggal_mulai'];
    $tanggal_selesai = $_POST['tanggal_selesai'];
    $keterangan = $_POST['keterangan'];
    $nama_file_unik = null;

    // upload file pendukung
    if (isset($_FILES['file_pendukung']) && $_FILES['file_pendukung']['error'] === 0) {
        $file_name = $_FILES['file_pendukung']['name'];
        $file_tmp = $_FILES['file_pendukung']['tmp_name'];
        $file_extension = pathinfo($file_name, PATHINFO_EXTENSION);
        $nama_file_unik = 'izin_' . uniqid() . '.' . $file_extension;
        move_uploaded_file($file_tmp, 'uploads/' . $nama_file_unik);
    }

    $stmt = mysqli_prepare($koneksi, "INSERT INTO pengajuan_izin (user_id, jenis_izin, tanggal_mulai, tanggal_selesai, keterangan, file_pendukung) VALUES (?, ?, ?, ?, ?, ?)");
    mysqli_stmt_bind_param($stmt, "isssss", $user_id, $jenis_izin, $tanggal_mulai, $tanggal_selesai, $keterangan, $nama_file_unik);
    
    if (mysqli_stmt_execute($stmt)) {
        $message = "<p style='color:green;'>Pengajuan Anda telah berhasil dikirim dan sedang menunggu persetujuan.</p>";
    } else {
        $message = "<p style='color:red;'>Gagal mengirim pengajuan.</p>";
    }
}
?>

<style>
    .form-izin { max-width: 600px; }
    .form-group { margin-bottom: 1rem; }
    .form-group label { display: block; margin-bottom: 5px; font-weight: bold; }
    .form-group input, .form-group select, .form-group textarea { width: 100%; padding: 8px; box-sizing: border-box; border: 1px solid #ccc; border-radius: 4px; }
    .btn-kirim { background-color: #007bff; color: white; padding: 10px 15px; border: none; border-radius: 4px; cursor: pointer; }
</style>

<h1>Formulir Pengajuan Izin / Cuti / Sakit</h1>
<?php if ($message) echo $message; ?>

<form method="POST" class="form-izin" enctype="multipart/form-data">
    <div class="form-group">
        <label for="jenis_izin">Jenis Pengajuan</label>
        <select id="jenis_izin" name="jenis_izin" required>
            <option value="Sakit">Sakit</option>
            <option value="Izin">Izin</option>
            <option value="Cuti">Cuti</option>
        </select>
    </div>
    <div class="form-group">
        <label for="tanggal_mulai">Tanggal Mulai</label>
        <input type="date" id="tanggal_mulai" name="tanggal_mulai" required>
    </div>
    <div class="form-group">
        <label for="tanggal_selesai">Tanggal Selesai</label>
        <input type="date" id="tanggal_selesai" name="tanggal_selesai" required>
    </div>
    <div class="form-group">
        <label for="keterangan">Keterangan</label>
        <textarea id="keterangan" name="keterangan" rows="4" required></textarea>
    </div>
    <div class="form-group">
        <label for="file_pendukung">File Pendukung (Opsional, cth: Surat Dokter)</label>
        <input type="file" id="file_pendukung" name="file_pendukung">
    </div>
    <button type="submit" name="submit_pengajuan" class="btn-kirim">Kirim Pengajuan</button>
</form>