-- ============================================================
-- PKL CENTER - DATABASE UPDATE (REVISI LENGKAP)
-- Jalankan file ini setelah import pkl_center (4).sql yang lama
-- ============================================================

-- 1. Tambah kolom is_verified, otp_code, otp_expired ke tabel users
ALTER TABLE `users`
  ADD COLUMN `is_verified` tinyint(1) NOT NULL DEFAULT 0 AFTER `role`,
  ADD COLUMN `otp_code` varchar(6) DEFAULT NULL AFTER `is_verified`,
  ADD COLUMN `otp_expired` datetime DEFAULT NULL AFTER `otp_code`;

-- Tandai semua user lama (admin & yang sudah ada) sebagai verified
UPDATE `users` SET `is_verified` = 1;

-- 2. Tambah kolom syarat_pkl ke tabel jurusan
ALTER TABLE `jurusan`
  ADD COLUMN `syarat_pkl` text DEFAULT NULL AFTER `deskripsi`;

-- Isi syarat PKL default per jurusan
UPDATE `jurusan` SET `syarat_pkl` = 'Memahami dasar mekanika, elektronika, dan kontrol. Menguasai software AutoCAD atau sejenisnya. Siap bekerja di lingkungan industri.' WHERE id = 1;
UPDATE `jurusan` SET `syarat_pkl` = 'Memahami rangkaian elektronik dasar dan industri. Terbiasa dengan alat ukur elektronika. Siap bekerja di lingkungan pabrik.' WHERE id = 2;
UPDATE `jurusan` SET `syarat_pkl` = 'Menguasai dasar PLC dan wiring panel. Memahami sensor dan aktuator industri. Siap bekerja di lingkungan otomasi.' WHERE id = 3;
UPDATE `jurusan` SET `syarat_pkl` = 'Memahami sistem komunikasi kabel dan nirkabel. Terbiasa dengan perangkat antena dan radio. Menguasai dasar teknik sinyal.' WHERE id = 4;
UPDATE `jurusan` SET `syarat_pkl` = 'Memahami alat ukur industri dan kalibrasi. Menguasai dasar sistem kontrol proses. Siap bekerja di industri berskala besar.' WHERE id = 5;
UPDATE `jurusan` SET `syarat_pkl` = 'Memahami sistem refrigerasi dan tata udara. Mampu membaca diagram wiring AC. Siap bekerja di bidang instalasi HVAC.' WHERE id = 6;
UPDATE `jurusan` SET `syarat_pkl` = 'Menguasai minimal satu bahasa pemrograman. Memahami dasar basis data SQL. Siap mengerjakan proyek pengembangan perangkat lunak.' WHERE id = 7;
UPDATE `jurusan` SET `syarat_pkl` = 'Memahami jaringan komputer (LAN/WAN). Menguasai dasar administrasi server Linux. Memahami keamanan jaringan dasar.' WHERE id = 8;
UPDATE `jurusan` SET `syarat_pkl` = 'Mampu mengoperasikan kamera video dan peralatan audio. Menguasai dasar editing video. Siap bekerja di lingkungan produksi kreatif.' WHERE id = 9;

-- 3. Buat tabel user_activity
CREATE TABLE IF NOT EXISTS `user_activity` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `last_activity` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_user` (`user_id`),
  CONSTRAINT `fk_activity_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 4. Buat tabel pesan (chat siswa ke admin)
CREATE TABLE IF NOT EXISTS `pesan` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `isi_pesan` text NOT NULL,
  `balasan_admin` text DEFAULT NULL,
  `status` enum('belum','dibaca') NOT NULL DEFAULT 'belum',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `replied_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_pesan_user` (`user_id`),
  CONSTRAINT `fk_pesan_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
