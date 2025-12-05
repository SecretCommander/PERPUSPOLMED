-- phpMyAdmin SQL Dump
-- version 5.2.2
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: Dec 05, 2025 at 06:47 AM
-- Server version: 5.7.15-log
-- PHP Version: 8.3.16

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `perpustakaan`
--

-- --------------------------------------------------------

--
-- Table structure for table `berita`
--

CREATE TABLE `berita` (
  `berita_id` int(11) NOT NULL,
  `petugas_id` int(7) DEFAULT NULL,
  `judul_berita` varchar(250) NOT NULL,
  `gambar_berita` varchar(150) DEFAULT NULL,
  `isi_berita` text NOT NULL,
  `tanggal_publish` datetime DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `berita`
--

INSERT INTO `berita` (`berita_id`, `petugas_id`, `judul_berita`, `gambar_berita`, `isi_berita`, `tanggal_publish`) VALUES
(1, 1, 'Buku Baru Masuk', 'random.png', 'Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum.', '2025-11-30 01:17:50'),
(2, 2, 'Akreditasi Perpus', 'random.png', 'Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum.', '2025-11-30 01:18:40'),
(3, 4, 'Pengadaan Komputer Baru', 'komputer.png', 'Lorem ipsum dolor sit amet, consectetur adipiscing elit.', '2025-11-30 02:10:20'),
(4, 7, 'Workshop Literasi Digital', 'workshop.png', 'Lorem ipsum dolor sit amet, consectetur adipiscing elit.', '2025-11-30 02:12:05'),
(5, 2, 'Koleksi Majalah Baru', 'majalah.png', 'Lorem ipsum dolor sit amet, consectetur adipiscing elit.', '2025-11-30 02:14:11'),
(6, 9, 'Pelatihan Petugas Perpustakaan', 'pelatihan.png', 'Lorem ipsum dolor sit amet, consectetur adipiscing elit.', '2025-11-30 02:15:50'),
(7, 1, 'Layanan Perpus Ditutup Sementara', 'tutup.png', 'Lorem ipsum dolor sit amet, consectetur adipiscing elit.', '2025-11-30 02:17:33'),
(8, 6, 'Kunjungan Sekolah ke Perpustakaan', 'kunjungan.png', 'Lorem ipsum dolor sit amet, consectetur adipiscing elit.', '2025-11-30 02:19:10'),
(9, 10, 'Update Sistem Informasi Perpustakaan', 'sistem.png', 'Lorem ipsum dolor sit amet, consectetur adipiscing elit.', '2025-11-30 02:20:25'),
(10, 3, 'Perpustakaan Tambah Jam Operasional', 'jam_operasional.png', 'Lorem ipsum dolor sit amet, consectetur adipiscing elit.', '2025-11-30 02:22:44');

-- --------------------------------------------------------

--
-- Table structure for table `buku`
--

CREATE TABLE `buku` (
  `buku_id` int(11) NOT NULL,
  `judul` varchar(150) NOT NULL,
  `sampul` varchar(150) DEFAULT NULL,
  `pengarang` varchar(150) NOT NULL,
  `tahun_terbit` year(4) DEFAULT NULL,
  `kategori` set('FIKSI','NON FIKSI','PENDIDIKAN') DEFAULT NULL,
  `jumlah_eksemplar` int(3) NOT NULL,
  `deskripsi_buku` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `buku`
--

INSERT INTO `buku` (`buku_id`, `judul`, `sampul`, `pengarang`, `tahun_terbit`, `kategori`, `jumlah_eksemplar`, `deskripsi_buku`) VALUES
(1, 'Laskar Pelangi', NULL, 'Andrea Hirata', '2005', 'FIKSI', 15, 'Novel tentang perjuangan anak-anak di Belitung'),
(2, 'Bumi Manusia', NULL, 'Pramoedya Ananta Toer', '1980', 'FIKSI', 12, 'Tetralogi Buru bagian pertama tentang Minke'),
(3, 'Sapiens', NULL, 'Yuval Noah Harari', '2011', 'NON FIKSI', 8, 'Sejarah singkat umat manusia'),
(4, 'Filosofi Tehe', NULL, 'Dee Lestari', '2006', 'FIKSI', 20, 'Novel tentang filosofi hidup dan kopi'),
(5, 'Algoritma dan Pemrograman', NULL, 'Rinaldi Munir', '2018', 'PENDIDIKAN', 25, 'Buku teks tentang dasar-dasar pemrograman'),
(6, 'Perahu Kertas', NULL, 'Dee Lestari', '2009', 'FIKSI', 18, 'Novel romantis tentang Kugy dan Keenan'),
(7, 'Homo Deus', NULL, 'Yuval Noah Harari', '2015', 'NON FIKSI', 10, 'Sejarah masa depan umat manusia'),
(8, 'Matematika Dasar', 'buku/6931ef4039558.png', 'Prof. Dr. Yusuf Hartono', '2020', '', 30, 'Buku matematika untuk mahasiswa'),
(9, 'Negeri 5 Menara', NULL, 'Ahmad Fuadi', '2009', 'FIKSI', 14, 'Novel tentang kehidupan santri di Ponpes'),
(10, 'Sejarah Indonesia Modern', NULL, 'M.C. Ricklefs', '2008', 'NON FIKSI', 16, 'Sejarah Indonesia dari masa kolonial hingga reformasi');

-- --------------------------------------------------------

--
-- Table structure for table `jurusan`
--

CREATE TABLE `jurusan` (
  `jurusan_id` int(5) NOT NULL,
  `nama_jurusan` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `jurusan`
--

INSERT INTO `jurusan` (`jurusan_id`, `nama_jurusan`) VALUES
(9, 'Administrasi Bisnis'),
(6, 'Akuntansi'),
(1, 'Komputer dan Informatika'),
(5, 'Manajemen'),
(3, 'Mesin'),
(8, 'Perhotelan'),
(2, 'Sipil'),
(4, 'Tata Niaga'),
(7, 'Teknik Elektro'),
(10, 'Teknik Kimia');

-- --------------------------------------------------------

--
-- Table structure for table `komentar`
--

CREATE TABLE `komentar` (
  `komentar_id` int(11) NOT NULL,
  `nim` varchar(11) NOT NULL,
  `buku_id` int(11) NOT NULL,
  `isi_komentar` text NOT NULL,
  `tgl_komentar` datetime DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `komentar`
--

INSERT INTO `komentar` (`komentar_id`, `nim`, `buku_id`, `isi_komentar`, `tgl_komentar`) VALUES
(1, '2405181054', 1, 'Buku ini sangat membantu untuk belajar pemrograman dasar. Penjelasannya mudah dipahami.', '2025-11-20 08:30:00'),
(2, '2405181064', 3, 'Apakah buku ini ada stok tambahannya? Saya cari di rak tidak ada.', '2025-11-22 10:15:00'),
(3, '2405181111', 5, 'Materi database di bab 3 sangat lengkap. Recommended buat semester 3!', '2025-11-25 09:00:00'),
(4, '2405181070', 2, 'Sayang sekali cover bukunya sudah agak rusak, tapi isinya masih terbaca jelas.', '2025-11-28 14:20:00'),
(5, '2405181071', 7, 'Referensi terbaik untuk tugas akhir saya. Terima kasih perpus!', '2025-11-30 11:00:00'),
(6, '2405181072', 1, 'Contoh kodingannya ada yang error, tapi logikanya benar.', '2025-12-01 13:45:00'),
(7, '2405181073', 4, 'Buku ini wajib dibaca anak akuntansi semester awal.', '2025-11-15 16:30:00'),
(8, '2405181074', 6, 'Bahasa yang digunakan terlalu teknis, agak sulit bagi pemula.', '2025-11-18 09:10:00'),
(9, '2405181075', 8, 'Sangat inspiratif! Membuka wawasan tentang manajemen bisnis modern.', '2025-12-02 08:00:00'),
(10, '2405181076', 9, 'Mohon diperbarui edisinya, sepertinya ini terbitan lama.', '2025-12-03 10:25:00');

-- --------------------------------------------------------

--
-- Table structure for table `kunjungan`
--

CREATE TABLE `kunjungan` (
  `id_kunjungan` int(11) NOT NULL,
  `nim` varchar(11) DEFAULT NULL,
  `tgl_dan_waktu_kunjungan` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `tgl_dan_waktu_keluar` datetime DEFAULT NULL,
  `lama_kunjungan` time DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `kunjungan`
--

INSERT INTO `kunjungan` (`id_kunjungan`, `nim`, `tgl_dan_waktu_kunjungan`, `tgl_dan_waktu_keluar`, `lama_kunjungan`) VALUES
(1, '2405181054', '2025-11-20 08:30:00', '2025-11-20 10:30:00', '02:00:00'),
(2, '2405181054', '2025-11-21 09:00:00', '2025-11-21 13:00:00', '04:00:00'),
(3, '2405181054', '2025-11-22 10:15:00', '2025-11-22 13:15:00', '03:00:00'),
(4, '2405181070', '2025-11-25 13:00:00', '2025-11-25 15:00:00', '02:00:00'),
(5, '2405181070', '2025-11-26 14:20:00', '2025-11-26 18:20:00', '04:00:00'),
(6, '2405181070', '2025-11-27 11:45:00', '2025-11-27 14:45:00', '03:00:00'),
(7, '2405181064', '2025-11-18 08:00:00', '2025-11-18 12:00:00', '04:00:00'),
(8, '2405181111', '2025-11-29 15:30:00', '2025-11-29 17:30:00', '02:00:00'),
(9, '2405181072', '2025-11-30 09:00:00', '2025-11-30 12:00:00', '03:00:00'),
(10, '2405181071', '2025-12-01 07:45:00', '2025-12-01 10:45:00', '03:00:00'),
(11, '2405181070', '2025-11-25 13:00:00', '2025-11-25 15:00:00', '02:00:00'),
(12, '2405181064', '2025-12-03 23:54:33', '2025-12-03 23:57:07', '00:02:34'),
(13, '2405181071', '2025-12-03 23:58:47', '2025-12-04 00:09:43', '00:10:56');

-- --------------------------------------------------------

--
-- Table structure for table `like_peminjaman`
--

CREATE TABLE `like_peminjaman` (
  `like_id` int(11) NOT NULL,
  `peminjaman_id` int(11) NOT NULL,
  `nim` varchar(11) NOT NULL,
  `tanggal_like` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `like_peminjaman`
--

INSERT INTO `like_peminjaman` (`like_id`, `peminjaman_id`, `nim`, `tanggal_like`) VALUES
(1, 1, '2405181054', '2025-11-23 10:00:00'),
(2, 1, '2405181064', '2025-11-23 12:30:00'),
(3, 2, '2405181070', '2025-11-02 09:15:00'),
(4, 3, '2405181071', '2025-12-01 14:00:00'),
(5, 4, '2405181072', '2025-11-13 16:45:00'),
(6, 5, '2405181073', '2025-12-02 08:20:00'),
(7, 6, '2405181111', '2025-11-26 12:10:00'),
(8, 7, '2405181074', '2025-12-02 10:05:00'),
(9, 8, '2405181075', '2025-11-21 13:50:00'),
(10, 9, '2405181076', '2025-12-03 15:30:00'),
(15, 9, '2405181064', '2025-12-02 16:47:47');

-- --------------------------------------------------------

--
-- Table structure for table `peminjaman`
--

CREATE TABLE `peminjaman` (
  `peminjaman_id` int(11) NOT NULL,
  `nim` varchar(11) NOT NULL,
  `buku_id` int(11) NOT NULL,
  `tanggal_pinjam` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `tanggal_kembali` datetime DEFAULT NULL,
  `status` enum('dipinjam','kembali') DEFAULT 'dipinjam'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `peminjaman`
--

INSERT INTO `peminjaman` (`peminjaman_id`, `nim`, `buku_id`, `tanggal_pinjam`, `tanggal_kembali`, `status`) VALUES
(1, '2405181054', 5, '2025-11-22 00:46:56', '2025-12-01 00:46:56', 'kembali'),
(2, '2405181054', 1, '2025-11-01 00:46:56', '2025-11-08 00:46:56', 'kembali'),
(3, '2405181111', 10, '2025-12-01 00:49:57', NULL, 'dipinjam'),
(4, '2405181111', 3, '2025-11-12 00:46:56', '2025-11-13 00:46:56', 'kembali'),
(5, '2405181064', 9, '2025-12-01 00:49:57', NULL, 'dipinjam'),
(6, '2405181070', 2, '2025-11-25 10:00:00', '2025-11-28 14:30:00', 'kembali'),
(7, '2405181064', 7, '2025-12-02 09:15:00', NULL, 'dipinjam'),
(8, '2405181072', 4, '2025-11-20 08:00:00', '2025-11-27 16:00:00', 'kembali'),
(9, '2405181111', 8, '2025-12-03 11:45:00', NULL, 'dipinjam'),
(10, '2405181054', 6, '2025-11-15 13:20:00', '2025-11-22 09:00:00', 'kembali'),
(11, '2405181064', 5, '2025-12-04 04:12:38', '2025-12-04 04:12:50', 'kembali');

-- --------------------------------------------------------

--
-- Table structure for table `pengguna`
--

CREATE TABLE `pengguna` (
  `nim` varchar(11) NOT NULL,
  `nama` varchar(150) NOT NULL,
  `password` varchar(150) NOT NULL,
  `foto_profile` varchar(150) DEFAULT NULL,
  `kontak` varchar(15) NOT NULL,
  `riwayat_kunjungan` float DEFAULT NULL,
  `prodi_id` int(5) DEFAULT NULL,
  `jurusan_id` int(5) DEFAULT NULL,
  `poin_aktivitas` int(5) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `pengguna`
--

INSERT INTO `pengguna` (`nim`, `nama`, `password`, `foto_profile`, `kontak`, `riwayat_kunjungan`, `prodi_id`, `jurusan_id`, `poin_aktivitas`) VALUES
('2405181054', 'Daffi Armansyah', '2405181054', 'daffi.png', '08123456789', 0, 2, 1, 0),
('2405181064', 'Farrel Ardan Awaly', '2405181064', 'profile/6931de1ab97eb.png', '081263647330', 5, 1, 1, 1),
('2405181070', 'Siti Aminah', '2405181070', 'siti.png', '081298765432', 0, 8, 1, 0),
('2405181071', 'Budi Santoso', '2405181071', 'budi.png', '081345678901', 2, 3, 2, 1),
('2405181072', 'Kevin Wijaya', '2405181072', 'kevin.png', '081234561234', 1, 6, 3, 0),
('2405181073', 'Dian Pratiwi', '2405181073', 'dian.png', '085212345678', 4, 5, 4, 10),
('2405181074', 'Rizky Pratama', '2405181074', 'rizky.png', '081987654321', 0, 1, 1, 0),
('2405181075', 'Melati Putri', '2405181075', 'melati.png', '082156789012', 3, 9, 5, 2),
('2405181076', 'Ahmad Fauzi', '2405181076', 'ahmad.png', '081223344556', 0, 2, 1, 0),
('2405181111', 'Yabes Raja Imam Manalu', '2405181111', 'yabes.png', '081231311234', 50, 3, 2, 0),
('2405555555', 'Brian', '2405555555', NULL, '081200001111', NULL, 2, 1, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `petugas`
--

CREATE TABLE `petugas` (
  `petugas_id` int(7) NOT NULL,
  `nama` varchar(150) NOT NULL,
  `username` varchar(100) NOT NULL,
  `password` varchar(150) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `petugas`
--

INSERT INTO `petugas` (`petugas_id`, `nama`, `username`, `password`) VALUES
(1, 'admin', 'admin', 'admin'),
(2, 'root', 'root', 'root'),
(3, 'Farrel', 'Farrel', 'Farrel'),
(4, 'Yabes', 'Yabes', 'yabes'),
(5, 'daffi', 'daffi', 'daffi'),
(6, 'jkw1', 'jkw1', 'jkw1'),
(7, 'admin2', 'admin2', 'admin2'),
(8, 'user', 'user', 'user'),
(9, 'petugas', 'petugas', 'petugas'),
(10, 'ayam', 'ayam', 'ayam');

-- --------------------------------------------------------

--
-- Table structure for table `program_studi`
--

CREATE TABLE `program_studi` (
  `prodi_id` int(5) NOT NULL,
  `jurusan_id` int(5) NOT NULL,
  `nama_prodi` varchar(150) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `program_studi`
--

INSERT INTO `program_studi` (`prodi_id`, `jurusan_id`, `nama_prodi`) VALUES
(1, 1, 'Teknologi Rekayasa Perangkat Lunak'),
(2, 1, 'Teknologi Rekayasa Multimedia Grafis'),
(3, 2, 'Teknik Sipil'),
(4, 5, 'SDM'),
(5, 4, 'Akuntansi'),
(6, 3, 'Teknik Mesin Otomotif'),
(7, 2, 'Teknik Konstruksi Bangunan'),
(8, 1, 'Sistem Informasi'),
(9, 5, 'Manajemen Bisnis'),
(10, 4, 'Administrasi Perkantoran');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `berita`
--
ALTER TABLE `berita`
  ADD PRIMARY KEY (`berita_id`),
  ADD KEY `fk_berita_petugas` (`petugas_id`);

--
-- Indexes for table `buku`
--
ALTER TABLE `buku`
  ADD PRIMARY KEY (`buku_id`);

--
-- Indexes for table `jurusan`
--
ALTER TABLE `jurusan`
  ADD PRIMARY KEY (`jurusan_id`),
  ADD UNIQUE KEY `nama_jurusan` (`nama_jurusan`);

--
-- Indexes for table `komentar`
--
ALTER TABLE `komentar`
  ADD PRIMARY KEY (`komentar_id`),
  ADD KEY `fk_komen_nim` (`nim`),
  ADD KEY `fk_komen_buku` (`buku_id`);

--
-- Indexes for table `kunjungan`
--
ALTER TABLE `kunjungan`
  ADD PRIMARY KEY (`id_kunjungan`),
  ADD KEY `fk_kunjungan_nim` (`nim`);

--
-- Indexes for table `like_peminjaman`
--
ALTER TABLE `like_peminjaman`
  ADD PRIMARY KEY (`like_id`),
  ADD KEY `fk_like_pinjam` (`peminjaman_id`),
  ADD KEY `fk_like_nim` (`nim`);

--
-- Indexes for table `peminjaman`
--
ALTER TABLE `peminjaman`
  ADD PRIMARY KEY (`peminjaman_id`),
  ADD KEY `fk_pinjam_nim` (`nim`),
  ADD KEY `fk_pinjam_buku` (`buku_id`);

--
-- Indexes for table `pengguna`
--
ALTER TABLE `pengguna`
  ADD PRIMARY KEY (`nim`),
  ADD KEY `fk_pengguna_prodi` (`prodi_id`),
  ADD KEY `fk_pengguna_jurusan` (`jurusan_id`);

--
-- Indexes for table `petugas`
--
ALTER TABLE `petugas`
  ADD PRIMARY KEY (`petugas_id`),
  ADD UNIQUE KEY `username` (`username`);

--
-- Indexes for table `program_studi`
--
ALTER TABLE `program_studi`
  ADD PRIMARY KEY (`prodi_id`),
  ADD KEY `fk_prodi_jurusan` (`jurusan_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `berita`
--
ALTER TABLE `berita`
  MODIFY `berita_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `buku`
--
ALTER TABLE `buku`
  MODIFY `buku_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `jurusan`
--
ALTER TABLE `jurusan`
  MODIFY `jurusan_id` int(5) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `komentar`
--
ALTER TABLE `komentar`
  MODIFY `komentar_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `kunjungan`
--
ALTER TABLE `kunjungan`
  MODIFY `id_kunjungan` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT for table `like_peminjaman`
--
ALTER TABLE `like_peminjaman`
  MODIFY `like_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT for table `peminjaman`
--
ALTER TABLE `peminjaman`
  MODIFY `peminjaman_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `petugas`
--
ALTER TABLE `petugas`
  MODIFY `petugas_id` int(7) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `program_studi`
--
ALTER TABLE `program_studi`
  MODIFY `prodi_id` int(5) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `berita`
--
ALTER TABLE `berita`
  ADD CONSTRAINT `fk_berita_petugas` FOREIGN KEY (`petugas_id`) REFERENCES `petugas` (`petugas_id`) ON DELETE SET NULL;

--
-- Constraints for table `komentar`
--
ALTER TABLE `komentar`
  ADD CONSTRAINT `fk_komen_buku` FOREIGN KEY (`buku_id`) REFERENCES `buku` (`buku_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_komen_nim` FOREIGN KEY (`nim`) REFERENCES `pengguna` (`nim`) ON DELETE CASCADE;

--
-- Constraints for table `kunjungan`
--
ALTER TABLE `kunjungan`
  ADD CONSTRAINT `fk_kunjungan_nim` FOREIGN KEY (`nim`) REFERENCES `pengguna` (`nim`) ON DELETE CASCADE;

--
-- Constraints for table `like_peminjaman`
--
ALTER TABLE `like_peminjaman`
  ADD CONSTRAINT `fk_like_nim` FOREIGN KEY (`nim`) REFERENCES `pengguna` (`nim`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_like_pinjam` FOREIGN KEY (`peminjaman_id`) REFERENCES `peminjaman` (`peminjaman_id`) ON DELETE CASCADE;

--
-- Constraints for table `peminjaman`
--
ALTER TABLE `peminjaman`
  ADD CONSTRAINT `fk_pinjam_buku` FOREIGN KEY (`buku_id`) REFERENCES `buku` (`buku_id`),
  ADD CONSTRAINT `fk_pinjam_nim` FOREIGN KEY (`nim`) REFERENCES `pengguna` (`nim`) ON DELETE CASCADE;

--
-- Constraints for table `pengguna`
--
ALTER TABLE `pengguna`
  ADD CONSTRAINT `fk_pengguna_jurusan` FOREIGN KEY (`jurusan_id`) REFERENCES `jurusan` (`jurusan_id`) ON DELETE SET NULL,
  ADD CONSTRAINT `fk_pengguna_prodi` FOREIGN KEY (`prodi_id`) REFERENCES `program_studi` (`prodi_id`) ON DELETE SET NULL;

--
-- Constraints for table `program_studi`
--
ALTER TABLE `program_studi`
  ADD CONSTRAINT `fk_prodi_jurusan` FOREIGN KEY (`jurusan_id`) REFERENCES `jurusan` (`jurusan_id`) ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
