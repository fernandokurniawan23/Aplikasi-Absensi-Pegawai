<?php
// content_beranda.php

$user_role = $_SESSION['user_role'];
$user_id = $_SESSION['user_id'];
?>

<style>
    .dashboard-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
        gap: 1.5rem;
    }
    .stat-card {
        background-color: #f8f9fa;
        border: 1px solid #dee2e6;
        border-radius: 8px;
        padding: 1.5rem;
        text-align: center;
    }
    .stat-card h3 {
        margin-top: 0;
        font-size: 1.2rem;
        color: #495057;
    }
    .stat-card .stat-number {
        font-size: 2.5rem;
        font-weight: bold;
        color: #007bff;
    }
    .recent-activity {
        margin-top: 2rem;
    }
    .recent-activity table {
        width: 100%;
        border-collapse: collapse;
    }
    .recent-activity th, .recent-activity td {
        border: 1px solid #ddd;
        padding: 8px;
    }
    .recent-activity th {
        background-color: #f2f2f2;
    }
    .chart-container {
        background: #fff;
        padding: 1.5rem;
        border-radius: 8px;
        border: 1px solid #dee2e6;
        margin-top: 2rem;
    }
</style>

<?php if ($user_role === 'Admin'): ?>
    <?php
        // --- Query untuk Statistik Angka ---
        $tanggal_hari_ini = date('Y-m-d');
        $total_karyawan = mysqli_fetch_assoc(mysqli_query($koneksi, "SELECT COUNT(id) as total FROM users WHERE role = 'Karyawan'"))['total'];
        $query_today = "SELECT COUNT(id_absensi) as total_hadir, SUM(CASE WHEN status = 'Terlambat' THEN 1 ELSE 0 END) as total_terlambat FROM absensi WHERE tanggal_absen = '$tanggal_hari_ini'";
        $stats_today = mysqli_fetch_assoc(mysqli_query($koneksi, $query_today));
        $total_pengajuan = mysqli_fetch_assoc(mysqli_query($koneksi, "SELECT COUNT(id_pengajuan) as total FROM pengajuan_izin WHERE status_pengajuan = 'Menunggu'"))['total'];
        
        // --- Query Data untuk Chart ---
        $query_chart = "SELECT 
                            DATE_FORMAT(tanggal_absen, '%Y-%m-%d') as tanggal,
                            SUM(CASE WHEN status = 'Hadir' THEN 1 ELSE 0 END) as jumlah_hadir,
                            SUM(CASE WHEN status = 'Terlambat' THEN 1 ELSE 0 END) as jumlah_terlambat
                        FROM absensi
                        WHERE tanggal_absen BETWEEN CURDATE() - INTERVAL 6 DAY AND CURDATE()
                        GROUP BY tanggal
                        ORDER BY tanggal ASC";
        $result_chart = mysqli_query($koneksi, $query_chart);
        
        $chart_labels = [];
        $chart_data_hadir = [];
        $chart_data_terlambat = [];
        while($row = mysqli_fetch_assoc($result_chart)) {
            $chart_labels[] = date('d M', strtotime($row['tanggal']));
            $chart_data_hadir[] = $row['jumlah_hadir'];
            $chart_data_terlambat[] = $row['jumlah_terlambat'];
        }
        $chart_labels_json = json_encode($chart_labels);
        $chart_data_hadir_json = json_encode($chart_data_hadir);
        $chart_data_terlambat_json = json_encode($chart_data_terlambat);
    ?>
    
    <h1>Dashboard Admin</h1>
    <p>Ringkasan data sistem absensi hari ini, <?= date('d F Y') ?>.</p>
    
    <div class="dashboard-grid">
        <div class="stat-card">
            <h3>Total Karyawan</h3>
            <p class="stat-number"><?= $total_karyawan ?></p>
        </div>
        <div class="stat-card">
            <h3>Hadir Hari Ini</h3>
            <p class="stat-number"><?= $stats_today['total_hadir'] ?? 0 ?></p>
        </div>
        <div class="stat-card">
            <h3>Terlambat Hari Ini</h3>
            <p class="stat-number"><?= $stats_today['total_terlambat'] ?? 0 ?></p>
        </div>
        <div class="stat-card">
            <h3>Pengajuan Izin Menunggu</h3>
            <p class="stat-number"><?= $total_pengajuan ?></p>
        </div>
    </div>

    <div class="chart-container">
        <h3>Statistik Kehadiran 7 Hari Terakhir</h3>
        <canvas id="myChart"></canvas>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <script>
        const ctx = document.getElementById('myChart');

        new Chart(ctx, {
            type: 'bar',
            data: {
                labels: <?= $chart_labels_json ?>,
                datasets: [
                    {
                        label: 'Hadir Tepat Waktu',
                        data: <?= $chart_data_hadir_json ?>,
                        backgroundColor: 'rgba(54, 162, 235, 0.5)',
                        borderColor: 'rgba(54, 162, 235, 1)',
                        borderWidth: 1
                    },
                    {
                        label: 'Terlambat',
                        data: <?= $chart_data_terlambat_json ?>,
                        backgroundColor: 'rgba(255, 159, 64, 0.5)',
                        borderColor: 'rgba(255, 159, 64, 1)',
                        borderWidth: 1
                    }
                ]
            },
            options: {
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            stepSize: 1
                        }
                    }
                }
            }
        });
    </script>

<?php else: ?>
    <?php
        $bulan_ini = date('m');
        $tahun_ini = date('Y');

        $query_karyawan = "SELECT
                                COUNT(id_absensi) as total_hadir,
                                SUM(CASE WHEN status = 'Terlambat' THEN 1 ELSE 0 END) as total_terlambat,
                                SUM(CASE WHEN status_lembur = 'Ya' THEN 1 ELSE 0 END) as total_lembur
                           FROM absensi WHERE user_id = ? AND MONTH(tanggal_absen) = ? AND YEAR(tanggal_absen) = ?";
        $stmt = mysqli_prepare($koneksi, $query_karyawan);
        mysqli_stmt_bind_param($stmt, "iss", $user_id, $bulan_ini, $tahun_ini);
        mysqli_stmt_execute($stmt);
        $stats_karyawan = mysqli_stmt_get_result($stmt)->fetch_assoc();

        $riwayat_terakhir = mysqli_query($koneksi, "SELECT * FROM absensi WHERE user_id = $user_id ORDER BY tanggal_absen DESC LIMIT 5");
    ?>

    <h1>Dashboard Karyawan</h1>
    <p>Ringkasan performa Anda untuk bulan <?= date('F Y') ?>.</p>

    <div class="dashboard-grid">
        <div class="stat-card">
            <h3>Total Kehadiran</h3>
            <p class="stat-number"><?= $stats_karyawan['total_hadir'] ?? 0 ?></p>
        </div>
        <div class="stat-card">
            <h3>Total Terlambat</h3>
            <p class="stat-number"><?= $stats_karyawan['total_terlambat'] ?? 0 ?></p>
        </div>
        <div class="stat-card">
            <h3>Total Hari Lembur</h3>
            <p class="stat-number"><?= $stats_karyawan['total_lembur'] ?? 0 ?></p>
        </div>
    </div>

    <div class="recent-activity">
        <h3>5 Aktivitas Absensi Terakhir</h3>
        <table>
            <thead>
                <tr>
                    <th>Tanggal</th>
                    <th>Jam Masuk</th>
                    <th>Jam Pulang</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                <?php if (mysqli_num_rows($riwayat_terakhir) > 0): ?>
                    <?php while ($data = mysqli_fetch_assoc($riwayat_terakhir)): ?>
                        <tr>
                            <td><?= date('d M Y', strtotime($data['tanggal_absen'])) ?></td>
                            <td><?= $data['jam_masuk'] ? date('H:i', strtotime($data['jam_masuk'])) : '-' ?></td>
                            <td><?= $data['jam_pulang'] ? date('H:i', strtotime($data['jam_pulang'])) : '-' ?></td>
                            <td><?= htmlspecialchars($data['status']) ?></td>
                        </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr><td colspan="4">Belum ada riwayat absensi.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
<?php endif; ?>