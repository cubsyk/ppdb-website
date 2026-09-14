-- PPDB TK Harapan Bunda - Schema Lengkap
CREATE DATABASE IF NOT EXISTS ppdb_tk_harapan_bunda CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE ppdb_tk_harapan_bunda;

CREATE TABLE IF NOT EXISTS users (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    nama VARCHAR(120) NOT NULL,
    email VARCHAR(150) NOT NULL UNIQUE,
    password_hash VARCHAR(255) NOT NULL,
    role ENUM('orang_tua','admin') NOT NULL DEFAULT 'orang_tua',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS pendaftaran (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id INT UNSIGNED NOT NULL,
    nomor_pendaftaran VARCHAR(30) NOT NULL UNIQUE,
    status ENUM('draft','menunggu_pengajuan','menunggu_verifikasi','perlu_perbaikan','terverifikasi','diterima','tidak_diterima','daftar_tunggu','daftar_ulang','siswa_terdaftar') NOT NULL DEFAULT 'draft',
    program VARCHAR(80) NULL,
    catatan_admin TEXT NULL,
    hasil_seleksi TEXT NULL,
    tanggal_daftar DATETIME NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS calon_siswa (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    pendaftaran_id INT UNSIGNED NOT NULL UNIQUE,
    nama_lengkap VARCHAR(120) NOT NULL,
    nik VARCHAR(20) NOT NULL,
    nisn VARCHAR(20) NULL,
    tempat_lahir VARCHAR(100) NOT NULL,
    tanggal_lahir DATE NOT NULL,
    jenis_kelamin ENUM('Laki-laki','Perempuan') NOT NULL,
    agama VARCHAR(50) NULL,
    alamat TEXT NOT NULL,
    nomor_kk VARCHAR(20) NOT NULL,
    anak_ke TINYINT UNSIGNED NULL,
    jumlah_saudara TINYINT UNSIGNED NULL,
    FOREIGN KEY (pendaftaran_id) REFERENCES pendaftaran(id) ON DELETE CASCADE
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS orang_tua (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    pendaftaran_id INT UNSIGNED NOT NULL UNIQUE,
    nama_ayah VARCHAR(120) NOT NULL,
    nik_ayah VARCHAR(20) NOT NULL,
    pekerjaan_ayah VARCHAR(100) NOT NULL,
    pendidikan_ayah VARCHAR(80) NOT NULL,
    nama_ibu VARCHAR(120) NOT NULL,
    nik_ibu VARCHAR(20) NOT NULL,
    pekerjaan_ibu VARCHAR(100) NOT NULL,
    pendidikan_ibu VARCHAR(80) NOT NULL,
    no_hp VARCHAR(20) NOT NULL,
    email VARCHAR(150) NOT NULL,
    alamat TEXT NOT NULL,
    FOREIGN KEY (pendaftaran_id) REFERENCES pendaftaran(id) ON DELETE CASCADE
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS wali (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    pendaftaran_id INT UNSIGNED NOT NULL UNIQUE,
    nama_wali VARCHAR(120) NULL,
    nik_wali VARCHAR(20) NULL,
    hubungan VARCHAR(80) NULL,
    pekerjaan_wali VARCHAR(100) NULL,
    no_hp_wali VARCHAR(20) NULL,
    alamat_wali TEXT NULL,
    FOREIGN KEY (pendaftaran_id) REFERENCES pendaftaran(id) ON DELETE CASCADE
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS dokumen (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    pendaftaran_id INT UNSIGNED NOT NULL,
    jenis VARCHAR(80) NOT NULL,
    nama_file VARCHAR(255) NOT NULL,
    path_file VARCHAR(255) NOT NULL,
    status ENUM('menunggu','valid','tidak_valid') NOT NULL DEFAULT 'menunggu',
    alasan TEXT NULL,
    uploaded_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (pendaftaran_id) REFERENCES pendaftaran(id) ON DELETE CASCADE
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS verifikasi (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    pendaftaran_id INT UNSIGNED NOT NULL,
    admin_id INT UNSIGNED NOT NULL,
    aksi ENUM('setujui','perbaikan','tolak','hasil','diterima','tidak_diterima','tunggu') NOT NULL,
    catatan TEXT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (pendaftaran_id) REFERENCES pendaftaran(id) ON DELETE CASCADE,
    FOREIGN KEY (admin_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS pengumuman (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    admin_id INT UNSIGNED NOT NULL,
    judul VARCHAR(160) NOT NULL,
    isi TEXT NOT NULL,
    status ENUM('draft','publish') NOT NULL DEFAULT 'publish',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (admin_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS daftar_ulang (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    pendaftaran_id INT UNSIGNED NOT NULL UNIQUE,
    konfirmasi TINYINT(1) NOT NULL DEFAULT 1,
    catatan TEXT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (pendaftaran_id) REFERENCES pendaftaran(id) ON DELETE CASCADE
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS pendaftar (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    nomor_pendaftaran VARCHAR(30) NOT NULL UNIQUE,
    tanggal_daftar DATETIME NOT NULL,
    nama_anak VARCHAR(120) NOT NULL,
    jenis_kelamin ENUM('Laki-laki','Perempuan') NOT NULL,
    tempat_lahir VARCHAR(100) NOT NULL,
    tanggal_lahir DATE NOT NULL,
    agama VARCHAR(50) NOT NULL,
    alamat TEXT NOT NULL,
    nama_ayah VARCHAR(120) NOT NULL,
    pekerjaan_ayah VARCHAR(100) NOT NULL,
    nama_ibu VARCHAR(120) NOT NULL,
    pekerjaan_ibu VARCHAR(100) NOT NULL,
    no_hp VARCHAR(20) NOT NULL,
    email VARCHAR(120) NULL,
    program ENUM('Kelompok A','Kelompok B','Daycare Ceria') NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;


CREATE TABLE IF NOT EXISTS pembayaran (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    pendaftaran_id INT UNSIGNED NOT NULL UNIQUE,
    jenis ENUM('uang_pangkal','daftar_ulang') NOT NULL DEFAULT 'daftar_ulang',
    nominal DECIMAL(12,2) NOT NULL DEFAULT 0,
    bank_tujuan VARCHAR(80) NOT NULL DEFAULT 'BRI 1234-5678-9012 a/n TK Harapan Bunda',
    nama_pengirim VARCHAR(120) NOT NULL,
    bukti_file VARCHAR(255) NOT NULL,
    catatan TEXT NULL,
    status ENUM('menunggu','dikonfirmasi','ditolak') NOT NULL DEFAULT 'menunggu',
    catatan_admin TEXT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (pendaftaran_id) REFERENCES pendaftaran(id) ON DELETE CASCADE
) ENGINE=InnoDB;

INSERT IGNORE INTO users (id,nama,email,password_hash,role)
VALUES (1,'Administrator','admin@tkharapanbunda.sch.id',
    '$2y$10$zKa3JhS.gv7571V3gH761.KrjB8IGRNBTItXXN6WgabCqDfiVQsIW','admin');