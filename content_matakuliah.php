<?php
// content_matakuliah.php
// Halaman daftar mata kuliah yang tersedia untuk presensi

// Catatan: Data ini seharusnya diambil dari database,
// namun kita menggunakan data statis sebagai contoh implementasi.

$data_matakuliah = [
    ['kode' => '007PW2', 'nama' => 'Pemrograman Web 2', 'kode_kelas' => '007TPLP019', 'jam' => '07.10 - 08.50', 'dosen' => 'Ir. Siti Aminah'],
    ['kode' => '007JN', 'nama' => 'Jaringan Nirkabel', 'kode_kelas' => '007TPLP019', 'jam' => '08.50 - 10.30', 'dosen' => 'Dr. Budi Santoso'],
    ['kode' => '007EP', 'nama' => 'Etika Profesi', 'kode_kelas' => '007TPLP019', 'jam' => '10.30 - 12.00', 'dosen' => 'Prof. Chandra'],
    ['kode' => '007MP', 'nama' => 'Manajemen Proyek', 'kode_kelas' => '007TPLP019', 'jam' => '13.00 - 14.40', 'dosen' => 'Dr. Budi Santoso'],
    // Tambahkan data lain sesuai screenshot Anda
    ['kode' => '007TNQA', 'nama' => 'Testing dan QA', 'kode_kelas' => '007TPLP019', 'jam' => '14.40 - 16.10', 'dosen' => 'Dr. Budi Santoso'], 
];

$title = "PRESENSI MATA KULIAH";
$subtitle = "Daftar Mata Kuliah";
?>

<div class="presensi-mahasiswa-header">
    <h2><?= $title ?></h2>
    <p><?= $subtitle ?></p>
</div>

<div class="presensi-container">
    <table class="data-table">
        <thead>
            <tr>
                <th>KODE MK</th>
                <th>MATA KULIAH</th>
                <th>KODE KELAS</th>
                <th>JAM MATA KULIAH</th>
                <th>Presensi</th> </tr>
        </thead>
        <tbody>
            <?php foreach ($data_matakuliah as $mk): ?>
            <tr>
                <td><?= $mk['kode'] ?></td>
                <td><?= $mk['nama'] ?></td>
                <td><?= $mk['kode_kelas'] ?></td>
                <td><?= $mk['jam'] ?></td>
                <td style="text-align: center;"> <a href="index.php?page=presensi&mk=<?= $mk['kode'] ?>" class="btn-primary btn-small">Presensi</a>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<style>
.btn-small {
    padding: 6px 10px;
    font-size: 0.8em;
    font-weight: normal;
    text-decoration: none; /* Penting karena menggunakan tag <a> */
    display: inline-block;
}
</style>