-- phpMyAdmin SQL Dump
-- version 5.2.2
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Waktu pembuatan: 04 Okt 2025 pada 04.35
-- Versi server: 8.0.30
-- Versi PHP: 8.3.22

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `db_absen`
--

-- --------------------------------------------------------

--
-- Struktur dari tabel `absensi`
--

CREATE TABLE `absensi` (
  `id_absensi` int NOT NULL,
  `user_id` int NOT NULL,
  `tanggal_absen` date NOT NULL,
  `jam_masuk` time DEFAULT NULL,
  `jam_pulang` time DEFAULT NULL,
  `foto_masuk` varchar(255) DEFAULT NULL,
  `foto_pulang` varchar(255) DEFAULT NULL,
  `status` varchar(20) NOT NULL,
  `status_lembur` enum('Ya','Tidak') NOT NULL DEFAULT 'Tidak'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data untuk tabel `absensi`
--

INSERT INTO `absensi` (`id_absensi`, `user_id`, `tanggal_absen`, `jam_masuk`, `jam_pulang`, `foto_masuk`, `foto_pulang`, `status`, `status_lembur`) VALUES
(4, 2, '2025-10-04', '10:53:14', '10:53:25', 'masuk_68e09a2ad2cc6_1759549994.jpg', 'pulang_68e09a359df2a_1759550005.jpg', 'Terlambat', 'Ya'),
(5, 2, '2025-10-05', NULL, NULL, NULL, NULL, 'Sakit', 'Tidak'),
(6, 2, '2025-10-06', NULL, NULL, NULL, NULL, 'Sakit', 'Tidak'),
(7, 2, '2025-10-07', NULL, NULL, NULL, NULL, 'Sakit', 'Tidak');

-- --------------------------------------------------------

--
-- Struktur dari tabel `pengajuan_izin`
--

CREATE TABLE `pengajuan_izin` (
  `id_pengajuan` int NOT NULL,
  `user_id` int NOT NULL,
  `jenis_izin` enum('Sakit','Izin','Cuti') NOT NULL,
  `tanggal_mulai` date NOT NULL,
  `tanggal_selesai` date NOT NULL,
  `keterangan` text NOT NULL,
  `file_pendukung` varchar(255) DEFAULT NULL,
  `status_pengajuan` enum('Menunggu','Disetujui','Ditolak') NOT NULL DEFAULT 'Menunggu',
  `tanggal_pengajuan` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data untuk tabel `pengajuan_izin`
--

INSERT INTO `pengajuan_izin` (`id_pengajuan`, `user_id`, `jenis_izin`, `tanggal_mulai`, `tanggal_selesai`, `keterangan`, `file_pendukung`, `status_pengajuan`, `tanggal_pengajuan`) VALUES
(1, 2, 'Sakit', '2025-10-05', '2025-10-07', 'sakit', 'izin_68e09ebbb599d.jpg', 'Disetujui', '2025-10-04 04:12:43');

-- --------------------------------------------------------

--
-- Struktur dari tabel `users`
--

CREATE TABLE `users` (
  `id` int NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `nama_lengkap` varchar(100) NOT NULL,
  `jabatan` varchar(100) DEFAULT NULL,
  `role` enum('Admin','Karyawan') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data untuk tabel `users`
--

INSERT INTO `users` (`id`, `username`, `password`, `nama_lengkap`, `jabatan`, `role`) VALUES
(1, 'admin', '$2y$10$VQtzoCb4FXWmSjCk0/xam.O8lk7JXXGMfEz8v/xpHiHYFJLOF68My', 'Administrator', 'IT Support', 'Admin'),
(2, 'karyawan', '$2y$10$80ZlRDsWsAYiEiyg4nfPmeGE8p69eUc6kgglI.ZcydCEuJOCMdCd6', 'Budi Santoso', 'Staff Marketing', 'Karyawan'),
(3, 'nando', '$2y$10$80ZlRDsWsAYiEiyg4nfPmeGE8p69eUc6kgglI.ZcydCEuJOCMdCd6', 'Alex Smith', 'CEO', 'Karyawan'),
(4, 'andra', '$2y$10$80ZlRDsWsAYiEiyg4nfPmeGE8p69eUc6kgglI.ZcydCEuJOCMdCd6', 'Claretta Jane', 'Sekretaris', 'Karyawan'),
(5, 'farhan', '$2y$10$80ZlRDsWsAYiEiyg4nfPmeGE8p69eUc6kgglI.ZcydCEuJOCMdCd6', 'Jeny Jen', 'Asisten Manager', 'Karyawan'),
(6, 'raka', '$2y$10$D3ewMeyqqCyW9hix3jFk9.gaDeX5Sm4qJkj3ixsteHUHShoFmZ3Ia', 'raka', 'gacoan', 'Karyawan');

--
-- Indexes for dumped tables
--

--
-- Indeks untuk tabel `absensi`
--
ALTER TABLE `absensi`
  ADD PRIMARY KEY (`id_absensi`),
  ADD UNIQUE KEY `absensi_unik` (`user_id`,`tanggal_absen`);

--
-- Indeks untuk tabel `pengajuan_izin`
--
ALTER TABLE `pengajuan_izin`
  ADD PRIMARY KEY (`id_pengajuan`),
  ADD KEY `user_id` (`user_id`);

--
-- Indeks untuk tabel `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`);

--
-- AUTO_INCREMENT untuk tabel yang dibuang
--

--
-- AUTO_INCREMENT untuk tabel `absensi`
--
ALTER TABLE `absensi`
  MODIFY `id_absensi` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT untuk tabel `pengajuan_izin`
--
ALTER TABLE `pengajuan_izin`
  MODIFY `id_pengajuan` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT untuk tabel `users`
--
ALTER TABLE `users`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- Ketidakleluasaan untuk tabel pelimpahan (Dumped Tables)
--

--
-- Ketidakleluasaan untuk tabel `absensi`
--
ALTER TABLE `absensi`
  ADD CONSTRAINT `absensi_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Ketidakleluasaan untuk tabel `pengajuan_izin`
--
ALTER TABLE `pengajuan_izin`
  ADD CONSTRAINT `pengajuan_izin_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
