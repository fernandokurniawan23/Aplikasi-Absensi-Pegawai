<?php

// semua data pengajuan berstatus 'Menunggu'
$query = "
    SELECT 
        p.id_pengajuan, u.nama_lengkap, p.jenis_izin, 
        p.tanggal_mulai, p.tanggal_selesai, p.keterangan, p.file_pendukung
    FROM 
        pengajuan_izin p
    JOIN 
        users u ON p.user_id = u.id
    WHERE 
        p.status_pengajuan = 'Menunggu'
    ORDER BY 
        p.tanggal_pengajuan ASC
";
$result = mysqli_query($koneksi, $query);
?>

<style>
    .izin-card { border: 1px solid #ddd; border-radius: 8px; padding: 1rem; margin-bottom: 1rem; background: #fff; }
    .izin-header { border-bottom: 1px solid #eee; padding-bottom: 0.5rem; margin-bottom: 1rem; }
    .izin-header h3 { margin: 0; }
    .izin-actions a { text-decoration: none; color: white; padding: 8px 12px; border-radius: 4px; margin-right: 10px; }
    .btn-setuju { background-color: #28a745; }
    .btn-tolak { background-color: #dc3545; }
</style>

<h1>Persetujuan Izin Karyawan</h1>
<p>Berikut adalah daftar pengajuan yang memerlukan tindakan Anda.</p>

<?php if (mysqli_num_rows($result) > 0): ?>
    <?php while ($data = mysqli_fetch_assoc($result)): ?>
        <div class="izin-card">
            <div class="izin-header">
                <h3><?= htmlspecialchars($data['nama_lengkap']) ?></h3>
                <small>Mengajukan: <b><?= htmlspecialchars($data['jenis_izin']) ?></b></small>
            </div>
            <p>
                <b>Tanggal:</b> 
                <?= date('d M Y', strtotime($data['tanggal_mulai'])) ?> s/d <?= date('d M Y', strtotime($data['tanggal_selesai'])) ?>
            </p>
            <p><b>Keterangan:</b> <?= nl2br(htmlspecialchars($data['keterangan'])) ?></p>
            <?php if ($data['file_pendukung']): ?>
                <p>
                    <b>File Pendukung:</b> 
                    <a href="uploads/<?= htmlspecialchars($data['file_pendukung']) ?>" target="_blank">Lihat File</a>
                </p>
            <?php endif; ?>
            <div class="izin-actions">
                <a href="proses_persetujuan_izin.php?id=<?= $data['id_pengajuan'] ?>&aksi=setuju" class="btn-setuju" onclick="return confirm('Anda yakin ingin menyetujui pengajuan ini?')">Setujui</a>
                <a href="proses_persetujuan_izin.php?id=<?= $data['id_pengajuan'] ?>&aksi=tolak" class="btn-tolak" onclick="return confirm('Anda yakin ingin menolak pengajuan ini?')">Tolak</a>
            </div>
        </div>
    <?php endwhile; ?>
<?php else: ?>
    <p>Tidak ada pengajuan izin yang menunggu persetujuan saat ini.</p>
<?php endif; ?>