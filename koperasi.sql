-- phpMyAdmin SQL Dump
-- version 5.0.4
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Waktu pembuatan: 21 Okt 2024 pada 17.53
-- Versi server: 10.4.16-MariaDB
-- Versi PHP: 7.3.24

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `koperasi`
--

-- --------------------------------------------------------

--
-- Struktur dari tabel `anggota`
--

CREATE TABLE `anggota` (
  `id_anggota` bigint(20) UNSIGNED NOT NULL,
  `nama` varchar(100) NOT NULL,
  `jenis_kelamin` varchar(10) NOT NULL,
  `tempat_lahir` varchar(100) NOT NULL,
  `tanggal_lahir` date NOT NULL,
  `alamat` varchar(255) DEFAULT NULL,
  `no_telp` varchar(15) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `nik` varchar(16) NOT NULL,
  `pekerjaan` varchar(100) DEFAULT NULL,
  `tanggal_bergabung` date DEFAULT NULL,
  `status` varchar(20) DEFAULT 'aktif',
  `uang_pendaftaran` decimal(10,2) DEFAULT 5000.00,
  `simpanan_pokok` decimal(10,2) DEFAULT 50000.00,
  `simpanan_wajib` decimal(10,2) DEFAULT 10000.00,
  `simpanan_taruta` decimal(10,2) DEFAULT 10000.00
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data untuk tabel `anggota`
--

INSERT INTO `anggota` (`id_anggota`, `nama`, `jenis_kelamin`, `tempat_lahir`, `tanggal_lahir`, `alamat`, `no_telp`, `email`, `nik`, `pekerjaan`, `tanggal_bergabung`, `status`, `uang_pendaftaran`, `simpanan_pokok`, `simpanan_wajib`, `simpanan_taruta`) VALUES
(1, 'Budi Santoso', 'Laki-laki', 'Surabaya', '1980-03-14', 'Jl. Kembang Jepun No. 21', '081234567890', 'budi.santoso@gmail.com', '1234567890123456', 'Karyawan Swasta', '2024-10-01', 'aktif', '5000.00', '50000.00', '10000.00', '10000.00'),
(2, 'Siti Aminah', 'Perempuan', 'Malang', '1992-12-25', 'Jl. Ijen No. 45', '081987654321', 'siti.aminah@gmail.com', '2345678901234567', 'Wiraswasta', '2024-10-05', 'aktif', '5000.00', '50000.00', '10000.00', '10000.00'),
(3, 'Agus Haryanto', 'Laki-laki', 'Yogyakarta', '1975-09-07', 'Jl. Malioboro No. 10', '081345678901', 'agus.haryanto@gmail.com', '3456789012345678', 'Pegawai Negeri Sipil', '2024-10-07', 'aktif', '5000.00', '50000.00', '10000.00', '10000.00'),
(4, 'Nurul Hidayati', 'Perempuan', 'Bandung', '1988-07-15', 'Jl. Dago No. 99', '081456789012', 'nurul.hidayati@gmail.com', '4567890123456789', 'Karyawan Swasta', '2024-10-09', 'aktif', '5000.00', '50000.00', '10000.00', '10000.00'),
(5, 'Teguh Prasetyo', 'Laki-laki', 'Semarang', '1990-11-20', 'Jl. Pandanaran No. 88', '081567890123', 'teguh.prasetyo@gmail.com', '5678901234567890', 'Wiraswasta', '2024-10-11', 'aktif', '5000.00', '50000.00', '10000.00', '10000.00');

-- --------------------------------------------------------

--
-- Struktur dari tabel `angsuran`
--

CREATE TABLE `angsuran` (
  `id_angsuran` bigint(20) UNSIGNED NOT NULL,
  `id_pinjaman` bigint(20) UNSIGNED NOT NULL,
  `tanggal_bayar` date NOT NULL,
  `jumlah_bayar` decimal(15,2) NOT NULL,
  `denda` decimal(15,2) DEFAULT 0.00
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data untuk tabel `angsuran`
--

INSERT INTO `angsuran` (`id_angsuran`, `id_pinjaman`, `tanggal_bayar`, `jumlah_bayar`, `denda`) VALUES
(1, 1, '2024-11-01', '100000.00', '0.00'),
(2, 2, '2024-11-05', '300000.00', '0.00'),
(3, 3, '2024-11-10', '75000.00', '5000.00'),
(4, 4, '2024-11-12', '100000.00', '10000.00'),
(5, 5, '2024-11-15', '50000.00', '0.00'),
(6, 1, '2024-10-21', '4900000.00', '0.00');

-- --------------------------------------------------------

--
-- Struktur dari tabel `pengguna`
--

CREATE TABLE `pengguna` (
  `id_user` bigint(20) UNSIGNED NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` varchar(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data untuk tabel `pengguna`
--

INSERT INTO `pengguna` (`id_user`, `username`, `password`, `role`) VALUES
(1, 'admin', '123456', 'admin');

-- --------------------------------------------------------

--
-- Struktur dari tabel `pinjaman`
--

CREATE TABLE `pinjaman` (
  `id_pinjaman` bigint(20) UNSIGNED NOT NULL,
  `id_anggota` bigint(20) UNSIGNED NOT NULL,
  `jenis_pinjaman` varchar(50) NOT NULL,
  `jumlah_pinjaman` decimal(15,2) NOT NULL,
  `tanggal_pinjaman` date NOT NULL,
  `status_pinjaman` varchar(20) DEFAULT 'belum lunas',
  `tanggal_pencairan` date DEFAULT NULL,
  `jatuh_tempo` date DEFAULT NULL,
  `tenor` int(11) NOT NULL,
  `angsuran` decimal(15,2) NOT NULL,
  `bunga` decimal(5,2) NOT NULL,
  `biaya_admin` decimal(5,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data untuk tabel `pinjaman`
--

INSERT INTO `pinjaman` (`id_pinjaman`, `id_anggota`, `jenis_pinjaman`, `jumlah_pinjaman`, `tanggal_pinjaman`, `status_pinjaman`, `tanggal_pencairan`, `jatuh_tempo`, `tenor`, `angsuran`, `bunga`, `biaya_admin`) VALUES
(1, 1, 'Mingguan', '5000000.00', '2024-10-12', 'lunas', '2024-10-13', '2025-10-12', 52, '100000.00', '1.50', '2.00'),
(2, 2, 'Bulanan', '10000000.00', '2024-10-14', 'belum lunas', '2024-10-15', '2027-10-14', 36, '300000.00', '2.00', '1.50'),
(3, 3, 'Mingguan', '3000000.00', '2024-10-16', 'belum lunas', '2024-10-17', '2025-10-16', 52, '75000.00', '1.75', '2.50'),
(4, 4, 'Bulanan', '2000000.00', '2024-10-18', 'belum lunas', '2024-10-19', '2025-10-18', 24, '100000.00', '1.25', '1.00'),
(5, 5, 'Mingguan', '1500000.00', '2024-10-20', 'belum lunas', '2024-10-21', '2025-10-20', 52, '50000.00', '1.00', '1.50');

-- --------------------------------------------------------

--
-- Struktur dari tabel `simpanan`
--

CREATE TABLE `simpanan` (
  `id_simpanan` bigint(20) UNSIGNED NOT NULL,
  `id_anggota` bigint(20) UNSIGNED NOT NULL,
  `jenis_simpanan` varchar(50) NOT NULL,
  `jumlah_simpanan` decimal(15,2) NOT NULL,
  `tanggal_simpanan` date NOT NULL,
  `sisa_saldo` decimal(15,2) DEFAULT 0.00
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data untuk tabel `simpanan`
--

INSERT INTO `simpanan` (`id_simpanan`, `id_anggota`, `jenis_simpanan`, `jumlah_simpanan`, `tanggal_simpanan`, `sisa_saldo`) VALUES
(1, 1, 'Bina Anggaran', '1000000.00', '2024-10-15', '500000.00'),
(2, 2, 'Taruta', '500000.00', '2024-10-18', '400000.00'),
(3, 3, 'Depokita', '2000000.00', '2024-10-20', '1000000.00'),
(4, 4, 'Intan', '1500000.00', '2024-10-21', '800000.00'),
(5, 5, 'Bina Anggaran', '750000.00', '2024-10-22', '500000.00');

-- --------------------------------------------------------

--
-- Struktur dari tabel `transaksi`
--

CREATE TABLE `transaksi` (
  `id_transaksi` bigint(20) UNSIGNED NOT NULL,
  `id_anggota` bigint(20) UNSIGNED NOT NULL,
  `tipe_transaksi` varchar(50) NOT NULL,
  `jumlah_transaksi` decimal(15,2) NOT NULL,
  `tanggal_transaksi` date NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data untuk tabel `transaksi`
--

INSERT INTO `transaksi` (`id_transaksi`, `id_anggota`, `tipe_transaksi`, `jumlah_transaksi`, `tanggal_transaksi`) VALUES
(1, 1, 'Setoran Simpanan', '200000.00', '2024-10-13'),
(2, 2, 'Tarikan Simpanan', '100000.00', '2024-10-14'),
(3, 3, 'Setoran Simpanan', '500000.00', '2024-10-15'),
(4, 4, 'Setoran Simpanan', '150000.00', '2024-10-16'),
(5, 5, 'Setoran Simpanan', '100000.00', '2024-10-17');

--
-- Indexes for dumped tables
--

--
-- Indeks untuk tabel `anggota`
--
ALTER TABLE `anggota`
  ADD PRIMARY KEY (`id_anggota`),
  ADD UNIQUE KEY `nik` (`nik`);

--
-- Indeks untuk tabel `angsuran`
--
ALTER TABLE `angsuran`
  ADD PRIMARY KEY (`id_angsuran`),
  ADD KEY `id_pinjaman` (`id_pinjaman`);

--
-- Indeks untuk tabel `pengguna`
--
ALTER TABLE `pengguna`
  ADD PRIMARY KEY (`id_user`);

--
-- Indeks untuk tabel `pinjaman`
--
ALTER TABLE `pinjaman`
  ADD PRIMARY KEY (`id_pinjaman`),
  ADD KEY `id_anggota` (`id_anggota`);

--
-- Indeks untuk tabel `simpanan`
--
ALTER TABLE `simpanan`
  ADD PRIMARY KEY (`id_simpanan`),
  ADD KEY `id_anggota` (`id_anggota`);

--
-- Indeks untuk tabel `transaksi`
--
ALTER TABLE `transaksi`
  ADD PRIMARY KEY (`id_transaksi`),
  ADD KEY `id_anggota` (`id_anggota`);

--
-- AUTO_INCREMENT untuk tabel yang dibuang
--

--
-- AUTO_INCREMENT untuk tabel `anggota`
--
ALTER TABLE `anggota`
  MODIFY `id_anggota` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT untuk tabel `angsuran`
--
ALTER TABLE `angsuran`
  MODIFY `id_angsuran` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT untuk tabel `pengguna`
--
ALTER TABLE `pengguna`
  MODIFY `id_user` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT untuk tabel `pinjaman`
--
ALTER TABLE `pinjaman`
  MODIFY `id_pinjaman` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT untuk tabel `simpanan`
--
ALTER TABLE `simpanan`
  MODIFY `id_simpanan` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT untuk tabel `transaksi`
--
ALTER TABLE `transaksi`
  MODIFY `id_transaksi` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- Ketidakleluasaan untuk tabel pelimpahan (Dumped Tables)
--

--
-- Ketidakleluasaan untuk tabel `angsuran`
--
ALTER TABLE `angsuran`
  ADD CONSTRAINT `angsuran_ibfk_1` FOREIGN KEY (`id_pinjaman`) REFERENCES `pinjaman` (`id_pinjaman`) ON DELETE CASCADE;

--
-- Ketidakleluasaan untuk tabel `pinjaman`
--
ALTER TABLE `pinjaman`
  ADD CONSTRAINT `pinjaman_ibfk_1` FOREIGN KEY (`id_anggota`) REFERENCES `anggota` (`id_anggota`) ON DELETE CASCADE;

--
-- Ketidakleluasaan untuk tabel `simpanan`
--
ALTER TABLE `simpanan`
  ADD CONSTRAINT `simpanan_ibfk_1` FOREIGN KEY (`id_anggota`) REFERENCES `anggota` (`id_anggota`) ON DELETE CASCADE;

--
-- Ketidakleluasaan untuk tabel `transaksi`
--
ALTER TABLE `transaksi`
  ADD CONSTRAINT `transaksi_ibfk_1` FOREIGN KEY (`id_anggota`) REFERENCES `anggota` (`id_anggota`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
