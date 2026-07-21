<?php
require_once __DIR__ . '/includes/auth.php';
require_login();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    redirect('dashboard.php');
}

$id = (int)($_POST['id'] ?? 0);
$stmt = $pdo->prepare('SELECT * FROM portfolios WHERE id = ?');
$stmt->execute([$id]);
$item = $stmt->fetch();
if (!$item) {
    set_flash('warning', 'Portofolio tidak ditemukan.');
    redirect('dashboard.php');
}
if (!is_admin() && (int)$item['user_id'] !== (int)current_user()['id']) {
    http_response_code(403);
    die('Akses ditolak.');
}

delete_uploaded_file($item['cover_image']);
delete_uploaded_file($item['proof_file']);
$delete = $pdo->prepare('DELETE FROM portfolios WHERE id = ?');
$delete->execute([$id]);
set_flash('success', 'Portofolio berhasil dihapus.');
redirect('dashboard.php');
