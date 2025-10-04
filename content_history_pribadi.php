<?php

// Ambil ID user login
$user_id = $_SESSION['user_id'];

$bulan_terpilih = $_GET['bulan'] ?? date('m');
$tahun_terpilih = $_GET['tahun'] ?? date('Y');


$query = "
    SELECT 
        tanggal_absen, jam_masuk, jam_pulang, foto_masuk, foto_pulang, status, status_lembur 
    FROM 
        absensi 
    WHERE 
        user_id = ? AND 
        MONTH(tanggal_absen) = ? AND 
        YEAR(tanggal_absen) = ?
    ORDER BY 
        tanggal_absen DESC
";

$stmt = mysqli_prepare($koneksi, $query);
mysqli_stmt_bind_param($stmt, "iss", $user_id, $bulan_terpilih, $tahun_terpilih);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

$nama_bulan = [
    '01' => 'Januari', '02' => 'Februari', '03' => 'Maret', '04' => 'April', 
    '05' => 'Mei', '06' => 'Juni', '07' => 'Juli', '08' => 'Agustus', 
    '09' => 'September', '10' => 'Oktober', '11' => 'November', '12' => 'Desember'
];
?>

<style>

    .data-table { width: 100%; border-collapse: collapse; margin-top: 1rem; }
    .data-table th, .data-table td { border: 1px solid #ccc; padding: 10px; text-align: left; vertical-align: middle; }
    .status-hadir { color: green; }
    .status-terlambat { color: orange; }
    .foto-absen { max-width: 60px; height: auto; border-radius: 4px; }
    .lembur-ya { background-color: #d4edda; color: #155724; padding: 4px; border-radius: 4px; font-weight: bold; }
    .filter-form { margin-bottom: 1rem; background-color: #f8f9fa; padding: 1rem; border-radius: 8px; }
</style>

<h1>Riwayat Absensi Anda</h1>

<div class="filter-form">
    <form method="GET" action="index.php">
        <input type="hidden" name="page" value="riwayat">
        <label for="bulan"><b>Pilih Periode:</b></label>
        <select name="bulan" id="bulan">
            <?php foreach ($nama_bulan as $nomor => $nama): ?>
                <option value="<?= $nomor ?>" <?= ($nomor == $bulan_terpilih) ? 'selected' : '' ?>><?= $nama ?></option>
            <?php endforeach; ?>
        </select>
        <select name="tahun" id="tahun">
            <?php for ($i = date('Y'); $i >= date('Y') - 5; $i--): ?>
                <option value="<?= $i ?>" <?= ($i == $tahun_terpilih) ? 'selected' : '' ?>><?= $i ?></option>
            <?php endfor; ?>
        </select>
        <button type="submit">Tampilkan</button>
    </form>
</div>

<p>Menampilkan riwayat untuk: <strong><?= $nama_bulan[$bulan_terpilih] ?> <?= $tahun_terpilih ?></strong></p>

<table class="data-table">
    <thead>
        <tr>
            <th>TANGGAL</th>
            <th>STATUS</th>
            <th>JAM MASUK</th>
            <th>JAM PULANG</th>
            <th>LEMBUR</th>
            <th>FOTO MASUK</th>
            <th>FOTO PULANG</th>
        </tr>
    </thead>
    <tbody>
        <?php if (mysqli_num_rows($result) > 0): ?>
            <?php while ($data = mysqli_fetch_assoc($result)): ?>
                <tr>
                    <td><?= date('d F Y', strtotime($data['tanggal_absen'])) ?></td>
                    <td>
                        <?php
                            if ($data['status'] === 'Hadir') { echo "<b class='status-hadir'>Hadir</b>"; }
                            elseif ($data['status'] === 'Terlambat') { echo "<b class='status-terlambat'>Terlambat</b>"; }
                            else { echo "<b>" . htmlspecialchars($data['status']) . "</b>"; }
                        ?>
                    </td>
                    <td><?= $data['jam_masuk'] ? date('H:i:s', strtotime($data['jam_masuk'])) : '-' ?></td>
                    <td><?= $data['jam_pulang'] ? date('H:i:s', strtotime($data['jam_pulang'])) : '-' ?></td>
                    <td>
                        <?php if ($data['status_lembur'] === 'Ya'): ?>
                            <span class="lembur-ya">Ya</span>
                        <?php else: ?> - <?php endif; ?>
                    </td>
                    <td>
                        <?php if ($data['foto_masuk']): ?>
                            <a href="uploads/<?= htmlspecialchars($data['foto_masuk']) ?>" target="_blank">
                                <img src="uploads/<?= htmlspecialchars($data['foto_masuk']) ?>" alt="Foto Masuk" class="foto-absen">
                            </a>
                        <?php else: ?> - <?php endif; ?>
                    </td>
                    <td>
                        <?php if ($data['foto_pulang']): ?>
                            <a href="uploads/<?= htmlspecialchars($data['foto_pulang']) ?>" target="_blank">
                                <img src="uploads/<?= htmlspecialchars($data['foto_pulang']) ?>" alt="Foto Pulang" class="foto-absen">
                            </a>
                        <?php else: ?> - <?php endif; ?>
                    </td>
                </tr>
            <?php endwhile; ?>
        <?php else: ?>
            <tr>
                <td colspan="7" style="text-align: center;">Tidak ada data absensi untuk periode ini.</td>
            </tr>
        <?php endif; ?>
    </tbody>
</table>