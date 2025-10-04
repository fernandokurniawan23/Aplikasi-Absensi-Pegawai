<?php

$tanggal_terpilih = $_GET['tanggal'] ?? date('Y-m-d');

$query = "
    SELECT 
        u.id, u.nama_lengkap, u.jabatan, 
        a.id_absensi, a.jam_masuk, a.jam_pulang, a.foto_masuk, a.foto_pulang, a.status, a.status_lembur
    FROM 
        users u
    LEFT JOIN 
        absensi a ON u.id = a.user_id AND a.tanggal_absen = ?
    WHERE 
        u.role = 'Karyawan'
    ORDER BY 
        u.nama_lengkap ASC
";

$stmt = mysqli_prepare($koneksi, $query);

mysqli_stmt_bind_param($stmt, "s", $tanggal_terpilih);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
?>

<style>
    .data-table { width: 100%; border-collapse: collapse; margin-top: 1rem;}
    .data-table th, .data-table td { border: 1px solid #ccc; padding: 10px; text-align: left; vertical-align: middle; }
    .status-hadir { color: green; }
    .status-terlambat { color: orange; }
    .status-absen { color: red; }
    .foto-absen { max-width: 60px; height: auto; border-radius: 4px; }
    .btn-edit { background-color: #007bff; color: white; padding: 6px 12px; text-decoration: none; border-radius: 4px; font-size: 12px; }
    .lembur-ya { background-color: #d4edda; color: #155724; padding: 4px; border-radius: 4px; font-weight: bold; }
    .filter-form { margin-bottom: 1rem; }
</style>

<h1>Rekapitulasi Absensi Harian</h1>

<div class="filter-form">
    <form method="GET" action="index.php">
        <input type="hidden" name="page" value="presensi">
        <label for="tanggal"><b>Pilih Tanggal:</b></label>
        <input type="date" id="tanggal" name="tanggal" value="<?= htmlspecialchars($tanggal_terpilih) ?>">
        <button type="submit">Tampilkan</button>
    </form>
</div>
<p>Menampilkan data untuk tanggal: <strong><?= date('d F Y', strtotime($tanggal_terpilih)) ?></strong></p>
<table class="data-table">
    <thead>
        <tr>
            <th>NAMA KARYAWAN</th>
            <th>STATUS</th>
            <th>JAM MASUK</th>
            <th>FOTO MASUK</th>
            <th>JAM PULANG</th>
            <th>FOTO PULANG</th>
            <th>LEMBUR</th>
            <th>AKSI</th>
        </tr>
    </thead>
    <tbody>
        <?php while ($data = mysqli_fetch_assoc($result)): ?>
            <tr>
                <td><?= htmlspecialchars($data['nama_lengkap']) ?><br><small><?= htmlspecialchars($data['jabatan']) ?></small></td>
                <td>
                    <?php
                        // Logika status
                        if ($data['status'] === null) {
                            echo "<b class='status-absen'>Absen</b>";
                        } elseif ($data['status'] === 'Hadir') {
                            echo "<b class='status-hadir'>Hadir</b>";
                        } elseif ($data['status'] === 'Terlambat') {
                            echo "<b class='status-terlambat'>Terlambat</b>";
                        } else {
                            //status Sakit, Izin, dll.
                            echo "<b>" . htmlspecialchars($data['status']) . "</b>";
                        }
                    ?>
                </td>
                <td><?= $data['jam_masuk'] ? date('H:i:s', strtotime($data['jam_masuk'])) : '-' ?></td>
                <td>
                    <?php if ($data['foto_masuk']): ?>
                        <a href="uploads/<?= htmlspecialchars($data['foto_masuk']) ?>" target="_blank">
                            <img src="uploads/<?= htmlspecialchars($data['foto_masuk']) ?>" alt="Foto Masuk" class="foto-absen">
                        </a>
                    <?php else: ?> - <?php endif; ?>
                </td>
                <td><?= $data['jam_pulang'] ? date('H:i:s', strtotime($data['jam_pulang'])) : '-' ?></td>
                <td>
                    <?php if ($data['foto_pulang']): ?>
                        <a href="uploads/<?= htmlspecialchars($data['foto_pulang']) ?>" target="_blank">
                            <img src="uploads/<?= htmlspecialchars($data['foto_pulang']) ?>" alt="Foto Pulang" class="foto-absen">
                        </a>
                    <?php else: ?> - <?php endif; ?>
                </td>
                <td>
                    <?php if ($data['status_lembur'] === 'Ya'): ?>
                        <span class="lembur-ya">Ya</span>
                    <?php else: ?> - <?php endif; ?>
                </td>
                <td>
                    <a href="edit_absensi.php?user_id=<?= $data['id'] ?>&tanggal=<?= $tanggal_terpilih ?>" class="btn-edit">Edit</a>
                </td>
            </tr>
        <?php endwhile; ?>
    </tbody>
</table>