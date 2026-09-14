# PPDB TK Harapan Bunda

Website Penerimaan Peserta Didik Baru Taman Kanak-kanak Harapan Bunda dibuat dengan PHP Native dan MySQL.

## Fitur

- Halaman landing PPDB
- Profil sekolah
- Program kelas
- Jadwal pendaftaran
- Rincian biaya
- Formulir pendaftaran online
- Validasi input server-side
- Penyimpanan data pendaftar ke database MySQL
- Tampilan responsif untuk desktop dan mobile
- Menu mobile dengan JavaScript

## Struktur Folder

```text
ppdb-tk-harapan-bunda/
├── index.php
├── profil.php
├── program.php
├── jadwal.php
├── biaya.php
├── daftar.php
├── database.sql
├── README.md
├── config/
│   └── database.php
├── includes/
│   ├── header.php
│   └── footer.php
└── assets/
    ├── css/
    │   └── style.css
    └── js/
        └── script.js
```

File `database.sql` dipakai untuk membuat database dan tabel MySQL.

## Setup Database MySQL di XAMPP

1. Buka XAMPP Control Panel.
2. Start **Apache** dan **MySQL**.
3. Buka browser:

```text
http://localhost/phpmyadmin
```

4. Klik menu **Import**.
5. Pilih file:

```text
C:\xampp\htdocs\ppdb-tk-harapan-bunda\database.sql
```

6. Klik **Go/Kirim**.
7. Database `ppdb_tk_harapan_bunda` dan tabel `pendaftar` dibuat otomatis.

## Konfigurasi Database

File koneksi ada di:

```text
config/database.php
```

Default XAMPP:

```php
$host = 'localhost';
$dbname = 'ppdb_tk_harapan_bunda';
$username = 'root';
$password = '';
```

Jika MySQL Anda memakai password, ubah nilai `$password`.

## Cara Menjalankan Website

1. Pastikan folder proyek ada di:

```text
C:\xampp\htdocs\ppdb-tk-harapan-bunda
```

2. Start **Apache** dan **MySQL** di XAMPP.
3. Buka browser:

```text
http://localhost/ppdb-tk-harapan-bunda
```

## Penyimpanan Data

Data pendaftar tersimpan ke tabel:

```text
pendaftar
```

Di database:

```text
ppdb_tk_harapan_bunda
```

Setiap pendaftar mendapat nomor pendaftaran format:

```text
PPDB-YYYYMMDD-XXXXXX
```

## Kustomisasi

Ubah informasi sekolah langsung di `index.php`, seperti:

- Kontak panitia
- Alamat sekolah
- Jadwal PPDB
- Rincian biaya
- Program kelas

Ubah warna dan tampilan di:

```text
assets/css/style.css
```

## Catatan

Website ini memakai PHP Native + MySQL. Cocok untuk demo, tugas sekolah/kuliah, atau sistem PPDB sederhana. Untuk produksi, tambahkan autentikasi admin, proteksi CSRF, validasi usia, upload berkas, dan backup database rutin.