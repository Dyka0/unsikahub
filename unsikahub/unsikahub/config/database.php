<?php
// Ubah konfigurasi ini sesuai database lokal Anda.
define('DB_HOST', 'localhost');
define('DB_NAME', 'unsikahub');
define('DB_USER', 'root');
define('DB_PASS', '');

// Jika folder project berbeda, sesuaikan BASE_URL.
// Contoh XAMPP: http://localhost/unsikahub => BASE_URL '/unsikahub'
define('BASE_URL', '/unsikahub');
define('UPLOAD_DIR', __DIR__ . '/../uploads');

try {
    $pdo = new PDO(
        'mysql:host=' . DB_HOST . ';dbname=' . DB_NAME . ';charset=utf8mb4',
        DB_USER,
        DB_PASS,
        [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false,
        ]
    );
} catch (PDOException $e) {
    die('Koneksi database gagal: ' . htmlspecialchars($e->getMessage(), ENT_QUOTES, 'UTF-8'));
}
