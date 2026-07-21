# UnsikaHub

UnsikaHub adalah aplikasi portofolio mahasiswa berbasis **HTML + CSS + PHP + MySQL**. Desainnya terinspirasi dari pola website portofolio modern seperti Behance, Dribbble, Awwwards, dan Framer, tetapi tidak menyalin aset, merek, atau layout spesifik dari platform tersebut.

## Tech Stack

- PHP native (tanpa framework)
- MySQL / MariaDB
- HTML, CSS, JavaScript (vanilla)
- PDO untuk akses database

## Fitur

1. Login, logout, dan register.
2. Register wajib mengunggah foto pengguna.
3. Role terpisah: `admin` dan `user`.
4. Admin dapat mengelola data pengguna.
5. Admin dapat mengubah role pengguna.
6. Password tersimpan dalam bentuk hash menggunakan `password_hash()` dan diverifikasi dengan `password_verify()`.
7. CRUD jenis portofolio: Website, Desain, Fotografi, Bisnis, dan lainnya.
8. CRUD portofolio mahasiswa sesuai jenis portofolio.
9. Upload cover dan bukti portofolio.
10. Pencarian berdasarkan kata kunci, jenis, nama mahasiswa, judul, dan deskripsi.

## Struktur Folder

```text
unsikahub/
├── admin/
│   ├── types.php
│   └── users.php
├── assets/
│   ├── css/style.css
│   └── js/app.js
│   └── img/
├── config/database.php
├── database/unsikahub.sql
├── includes/
│   ├── auth.php
│   ├── footer.php
│   ├── functions.php
│   └── header.php
├── uploads/
│   ├── portfolio/
│   └── profile/
├── dashboard.php
├── index.php
├── login.php
├── logout.php
├── portfolio_create.php
├── portfolio_delete.php
├── portfolio_edit.php
├── portfolio_view.php
├── portfolios.php
└── register.php
```

## Instalasi & Menjalankan Secara Lokal

Prasyarat: PHP 8+, MySQL/MariaDB, dan web server (bisa pakai XAMPP/Laragon).

1. Clone repository ini ke folder root web server, misal `htdocs/unsikahub` (XAMPP).
   ```bash
   git clone https://github.com/<username>/unsikahub.git
   ```
2. Buat database baru bernama `unsikahub`, lalu import skema dari `database/unsikahub.sql`.
3. Sesuaikan kredensial database di `config/database.php` (`DB_HOST`, `DB_NAME`, `DB_USER`, `DB_PASS`) dan `BASE_URL` sesuai folder project Anda.
4. Pastikan folder `uploads/portfolio` dan `uploads/profile` dapat ditulis oleh web server (writable).
5. Akses project melalui browser, misal `http://localhost/unsikahub`.

## Akun Admin Awal

```text
Email    : admin@unsikahub.test
Password : admin12345
```

> Akun ini berasal dari data awal (seed) di `database/unsikahub.sql`. Segera ganti password setelah login pertama, terutama jika project di-deploy ke server publik.

Setelah login, admin dapat masuk ke menu **Pengguna** untuk mengubah role user dan menu **Jenis** untuk mengelola jenis portofolio.

## Catatan Penting

- Password tidak disimpan dalam bentuk teks biasa.
- Query database menggunakan PDO prepared statement.
- User hanya dapat mengedit dan menghapus portofolio miliknya sendiri.
- Admin dapat mengelola pengguna, mengubah role, mengelola jenis portofolio, dan memantau portofolio.
- Upload file dibatasi berdasarkan ekstensi dan ukuran file.
- Folder `uploads` diberi `.htaccess` untuk menolak eksekusi file PHP.

## Lisensi

Project ini menggunakan lisensi [MIT](LICENSE).