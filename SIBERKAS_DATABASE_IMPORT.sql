-- ================================================================
-- SIBERKAS - DATABASE SIAP IMPORT (MySQL / MariaDB)
-- ================================================================
-- PENTING:
-- 1. Import file ini HANYA ke database baru/kosong karena ada DROP TABLE.
-- 2. Pada shared hosting, buat database terlebih dahulu dari cPanel lalu
--    pilih database itu di phpMyAdmin sebelum melakukan import.
-- 3. Jika nama database hosting bukan "siberkas", hapus/ubah dua baris
--    CREATE DATABASE dan USE di bawah ini.
-- 4. Jangan menyimpan password demo untuk penggunaan produksi.
-- ================================================================

CREATE DATABASE IF NOT EXISTS `siberkas` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE `siberkas`;
SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

DROP TABLE IF EXISTS `detail_jurnal`;
DROP TABLE IF EXISTS `kas_kecil`;
DROP TABLE IF EXISTS `jurnal_umum`;
DROP TABLE IF EXISTS `akun_akuntansi`;
DROP TABLE IF EXISTS `sessions`;
DROP TABLE IF EXISTS `transaksi_administrasi`;
DROP TABLE IF EXISTS `arsip_digital`;
DROP TABLE IF EXISTS `tracking_status`;
DROP TABLE IF EXISTS `berkas`;
DROP TABLE IF EXISTS `nasabah`;
DROP TABLE IF EXISTS `personal_access_tokens`;
DROP TABLE IF EXISTS `failed_jobs`;
DROP TABLE IF EXISTS `password_resets`;
DROP TABLE IF EXISTS `users`;
DROP TABLE IF EXISTS `migrations`;

CREATE TABLE `migrations` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `migration` varchar(255) NOT NULL,
  `batch` int NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `users` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `role` enum('admin','cs','akuntan') NOT NULL DEFAULT 'cs',
  `status` enum('aktif','nonaktif') NOT NULL DEFAULT 'aktif',
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `password` varchar(255) NOT NULL,
  `remember_token` varchar(100) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `users_email_unique` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `password_resets` (
  `email` varchar(255) NOT NULL,
  `token` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  KEY `password_resets_email_index` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `failed_jobs` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `uuid` varchar(255) NOT NULL,
  `connection` text NOT NULL,
  `queue` text NOT NULL,
  `payload` longtext NOT NULL,
  `exception` longtext NOT NULL,
  `failed_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `failed_jobs_uuid_unique` (`uuid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `personal_access_tokens` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `tokenable_type` varchar(255) NOT NULL,
  `tokenable_id` bigint unsigned NOT NULL,
  `name` varchar(255) NOT NULL,
  `token` varchar(64) NOT NULL,
  `abilities` text DEFAULT NULL,
  `last_used_at` timestamp NULL DEFAULT NULL,
  `expires_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `personal_access_tokens_token_unique` (`token`),
  KEY `personal_access_tokens_tokenable_type_tokenable_id_index` (`tokenable_type`,`tokenable_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `nasabah` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `nama_nasabah` varchar(255) NOT NULL,
  `nik` varchar(255) NOT NULL,
  `alamat` text NOT NULL,
  `no_telepon` varchar(255) NOT NULL,
  `created_by` bigint unsigned NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `nasabah_nik_unique` (`nik`),
  KEY `nasabah_created_by_foreign` (`created_by`),
  CONSTRAINT `nasabah_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `berkas` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `id_nasabah` bigint unsigned NOT NULL,
  `user_id` bigint unsigned NOT NULL,
  `jenis_layanan` varchar(255) NOT NULL,
  `tanggal_masuk` date NOT NULL,
  `estimasi_selesai` date DEFAULT NULL,
  `status_berkas` enum('Diterima','Diproses','Selesai') NOT NULL DEFAULT 'Diterima',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `berkas_id_nasabah_foreign` (`id_nasabah`),
  KEY `berkas_user_id_foreign` (`user_id`),
  CONSTRAINT `berkas_id_nasabah_foreign` FOREIGN KEY (`id_nasabah`) REFERENCES `nasabah` (`id`) ON DELETE CASCADE,
  CONSTRAINT `berkas_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `tracking_status` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `berkas_id` bigint unsigned NOT NULL,
  `user_id` bigint unsigned NOT NULL,
  `tanggal_update` datetime NOT NULL,
  `status` varchar(255) NOT NULL,
  `keterangan` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `tracking_status_berkas_id_foreign` (`berkas_id`),
  KEY `tracking_status_user_id_foreign` (`user_id`),
  CONSTRAINT `tracking_status_berkas_id_foreign` FOREIGN KEY (`berkas_id`) REFERENCES `berkas` (`id`) ON DELETE CASCADE,
  CONSTRAINT `tracking_status_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `arsip_digital` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `berkas_id` bigint unsigned NOT NULL,
  `nama_file` varchar(255) NOT NULL,
  `jenis_dokumen` varchar(255) NOT NULL,
  `path_file` varchar(255) NOT NULL,
  `tanggal_upload` date NOT NULL,
  `status_arsip` enum('Aktif','Arsip') NOT NULL DEFAULT 'Aktif',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `arsip_digital_berkas_id_foreign` (`berkas_id`),
  CONSTRAINT `arsip_digital_berkas_id_foreign` FOREIGN KEY (`berkas_id`) REFERENCES `berkas` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `transaksi_administrasi` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `id_berkas` bigint unsigned NOT NULL,
  `tanggal_transaksi` date NOT NULL,
  `jenis_transaksi` varchar(255) NOT NULL,
  `arah_transaksi` varchar(255) NOT NULL DEFAULT 'Pemasukan',
  `kategori` varchar(255) NOT NULL DEFAULT 'Lainnya',
  `nominal` decimal(12,2) NOT NULL,
  `status_transaksi` varchar(255) NOT NULL DEFAULT 'Belum Dibayar',
  `metode_pembayaran` varchar(255) NOT NULL DEFAULT 'Tunai',
  `nomor_referensi` varchar(255) DEFAULT NULL,
  `bukti_pembayaran` varchar(255) DEFAULT NULL,
  `diperiksa_oleh` bigint unsigned DEFAULT NULL,
  `tanggal_verifikasi` datetime DEFAULT NULL,
  `catatan_verifikasi` text DEFAULT NULL,
  `keterangan` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `transaksi_administrasi_id_berkas_foreign` (`id_berkas`),
  KEY `transaksi_administrasi_diperiksa_oleh_foreign` (`diperiksa_oleh`),
  CONSTRAINT `transaksi_administrasi_id_berkas_foreign` FOREIGN KEY (`id_berkas`) REFERENCES `berkas` (`id`) ON DELETE CASCADE,
  CONSTRAINT `transaksi_administrasi_diperiksa_oleh_foreign` FOREIGN KEY (`diperiksa_oleh`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `kas_kecil` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `tanggal` date NOT NULL,
  `jenis` enum('Masuk','Keluar') NOT NULL,
  `kategori` varchar(100) NOT NULL,
  `keterangan` varchar(255) NOT NULL,
  `nominal` decimal(15,2) NOT NULL,
  `nomor_bukti` varchar(100) DEFAULT NULL,
  `created_by` bigint unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `kas_kecil_tanggal_index` (`tanggal`),
  KEY `kas_kecil_jenis_index` (`jenis`),
  KEY `kas_kecil_created_by_foreign` (`created_by`),
  CONSTRAINT `kas_kecil_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `akun_akuntansi` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `kode_akun` varchar(20) NOT NULL,
  `nama_akun` varchar(255) NOT NULL,
  `kelompok` varchar(255) NOT NULL,
  `saldo_normal` varchar(255) NOT NULL DEFAULT 'Debit',
  `status` varchar(255) NOT NULL DEFAULT 'aktif',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `akun_akuntansi_kode_akun_unique` (`kode_akun`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `jurnal_umum` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `transaksi_id` bigint unsigned NOT NULL,
  `user_id` bigint unsigned DEFAULT NULL,
  `nomor_jurnal` varchar(40) NOT NULL,
  `tanggal_jurnal` date NOT NULL,
  `keterangan` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `jurnal_umum_transaksi_id_unique` (`transaksi_id`),
  UNIQUE KEY `jurnal_umum_nomor_jurnal_unique` (`nomor_jurnal`),
  KEY `jurnal_umum_user_id_foreign` (`user_id`),
  CONSTRAINT `jurnal_umum_transaksi_id_foreign` FOREIGN KEY (`transaksi_id`) REFERENCES `transaksi_administrasi` (`id`) ON DELETE CASCADE,
  CONSTRAINT `jurnal_umum_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `detail_jurnal` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `jurnal_id` bigint unsigned NOT NULL,
  `akun_id` bigint unsigned NOT NULL,
  `debit` decimal(15,2) NOT NULL DEFAULT 0.00,
  `kredit` decimal(15,2) NOT NULL DEFAULT 0.00,
  `keterangan` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `detail_jurnal_jurnal_id_foreign` (`jurnal_id`),
  KEY `detail_jurnal_akun_id_foreign` (`akun_id`),
  CONSTRAINT `detail_jurnal_jurnal_id_foreign` FOREIGN KEY (`jurnal_id`) REFERENCES `jurnal_umum` (`id`) ON DELETE CASCADE,
  CONSTRAINT `detail_jurnal_akun_id_foreign` FOREIGN KEY (`akun_id`) REFERENCES `akun_akuntansi` (`id`) ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `sessions` (
  `id` varchar(255) NOT NULL,
  `user_id` bigint unsigned DEFAULT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `user_agent` text DEFAULT NULL,
  `payload` text NOT NULL,
  `last_activity` int NOT NULL,
  PRIMARY KEY (`id`),
  KEY `sessions_user_id_index` (`user_id`),
  KEY `sessions_last_activity_index` (`last_activity`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `users` (`id`, `name`, `email`, `role`, `status`, `password`, `created_at`, `updated_at`) VALUES
(1, 'Administrator', 'admin@gmail.com', 'admin', 'aktif', '$2y$12$4ZlxtR4QX7.7LGgZCOzluusVmAUBM2FC/lsT/RBxYc2jKuACH7iES', NOW(), NOW()),
(2, 'Customer Service', 'cs@gmail.com', 'cs', 'aktif', '$2y$12$B/GH7O/NVLkV7lQcHwg1peDgVtVRjD0lI1gMT6U8NvRh/ybCxZgqS', NOW(), NOW()),
(3, 'Petugas Akuntansi', 'akuntan@gmail.com', 'akuntan', 'aktif', '$2y$12$th0gzP6/MhIsF81JQ2J8juigQUKcVsQlFG2YSOf2WMCK1Mpx3s2he', NOW(), NOW());

INSERT INTO `akun_akuntansi` (`kode_akun`, `nama_akun`, `kelompok`, `saldo_normal`, `status`, `created_at`, `updated_at`) VALUES
('111', 'Kas', 'Aset', 'Debit', 'aktif', NOW(), NOW()),
('112', 'Bank', 'Aset', 'Debit', 'aktif', NOW(), NOW()),
('113', 'Piutang Administrasi', 'Aset', 'Debit', 'aktif', NOW(), NOW()),
('114', 'Kas Kecil', 'Aset', 'Debit', 'aktif', NOW(), NOW()),
('411', 'Pendapatan Administrasi', 'Pendapatan', 'Kredit', 'aktif', NOW(), NOW()),
('412', 'Pendapatan Layanan', 'Pendapatan', 'Kredit', 'aktif', NOW(), NOW()),
('511', 'Beban Operasional', 'Beban', 'Debit', 'aktif', NOW(), NOW()),
('512', 'Beban ATK dan Cetak', 'Beban', 'Debit', 'aktif', NOW(), NOW()),
('513', 'Beban Transportasi', 'Beban', 'Debit', 'aktif', NOW(), NOW());

INSERT INTO `migrations` (`migration`, `batch`) VALUES
('2014_10_12_000000_create_users_table', 1),
('2014_10_12_100000_create_password_resets_table', 1),
('2019_08_19_000000_create_failed_jobs_table', 1),
('2019_12_14_000001_create_personal_access_tokens_table', 1),
('2026_02_09_112738_add_role_status_to_users_table', 1),
('2026_02_09_112755_create_nasabah_table', 1),
('2026_02_09_112810_create_berkas_table', 1),
('2026_02_09_112826_create_tracking_status_table', 1),
('2026_02_09_112848_create_arsip_digital_table', 1),
('2026_02_09_112909_create_transaksi_administrasi_table', 1),
('2026_02_10_144932_add_user_id_to_nasabahs_table', 1),
('2026_02_11_000057_add_id_user_to_berkas_table', 1),
('2026_02_11_000903_add_id_berkas_to_transaksi_administrasi_table', 1),
('2026_02_11_001852_fix_berkas_remove_id_user', 1),
('2026_02_11_001924_fix_transaksi_remove_berkas_id', 1),
('2026_02_11_010322_add_id_berkas_to_tracking_status_table', 1),
('2026_07_03_080000_add_ta_fields_to_transaksi_administrasi_table', 1),
('2026_07_03_090000_add_akuntan_role_to_users_table', 1),
('2026_07_03_090100_add_accounting_fields_to_transaksi_administrasi_table', 1),
('2026_07_03_090200_create_akun_akuntansi_table', 1),
('2026_07_03_090300_create_jurnal_umum_table', 1),
('2026_07_03_090400_create_detail_jurnal_table', 1),
('2026_07_03_100000_remove_legacy_id_berkas_from_tracking_status_table', 1),
('2026_07_03_100100_create_sessions_table', 1),
('2026_07_23_120000_create_kas_kecil_table', 1),
('2026_07_23_121000_backfill_initial_tracking_for_berkas', 1),
('2026_07_23_122000_add_kas_kecil_account', 1);

SET FOREIGN_KEY_CHECKS = 1;
-- Setelah import: ubah password tiga akun demo dari menu Manajemen User sebelum sistem dipakai sungguhan.
