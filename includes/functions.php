<?php
function e(?string $value): string
{
    return htmlspecialchars($value ?? '', ENT_QUOTES, 'UTF-8');
}

function url(string $path = ''): string
{
    return rtrim(BASE_URL, '/') . '/' . ltrim($path, '/');
}

function redirect(string $path): never
{
    header('Location: ' . url($path));
    exit;
}

function set_flash(string $type, string $message): void
{
    $_SESSION['flash'] = ['type' => $type, 'message' => $message];
}

function show_flash(): void
{
    if (!empty($_SESSION['flash'])) {
        $flash = $_SESSION['flash'];
        unset($_SESSION['flash']);
        echo '<div class="alert alert-' . e($flash['type']) . '">' . e($flash['message']) . '</div>';
    }
}

function is_logged_in(): bool
{
    return isset($_SESSION['user']);
}

function current_user(): ?array
{
    return $_SESSION['user'] ?? null;
}

function is_admin(): bool
{
    return isset($_SESSION['user']) && $_SESSION['user']['role'] === 'admin';
}

function require_login(): void
{
    if (!is_logged_in()) {
        set_flash('warning', 'Silakan login terlebih dahulu.');
        redirect('login.php');
    }
}

function require_admin(): void
{
    require_login();
    if (!is_admin()) {
        http_response_code(403);
        die('Akses ditolak. Halaman ini hanya untuk Admin.');
    }
}

function upload_file(array $file, string $targetSubdir, array $allowedExtensions, int $maxBytes = 3145728): ?string
{
    if (($file['error'] ?? UPLOAD_ERR_NO_FILE) === UPLOAD_ERR_NO_FILE) {
        return null;
    }

    if ($file['error'] !== UPLOAD_ERR_OK) {
        throw new RuntimeException('Upload gagal. Coba unggah ulang file.');
    }

    if ($file['size'] > $maxBytes) {
        throw new RuntimeException('Ukuran file terlalu besar. Maksimal ' . round($maxBytes / 1024 / 1024, 1) . ' MB.');
    }

    $originalName = $file['name'] ?? '';
    $extension = strtolower(pathinfo($originalName, PATHINFO_EXTENSION));
    if (!in_array($extension, $allowedExtensions, true)) {
        throw new RuntimeException('Format file tidak diizinkan. Format yang diizinkan: ' . implode(', ', $allowedExtensions));
    }

    $safeName = bin2hex(random_bytes(16)) . '.' . $extension;
    $targetDir = rtrim(UPLOAD_DIR, '/') . '/' . trim($targetSubdir, '/');
    if (!is_dir($targetDir)) {
        mkdir($targetDir, 0755, true);
    }

    $targetPath = $targetDir . '/' . $safeName;
    if (!move_uploaded_file($file['tmp_name'], $targetPath)) {
        throw new RuntimeException('File gagal disimpan.');
    }

    return 'uploads/' . trim($targetSubdir, '/') . '/' . $safeName;
}

function delete_uploaded_file(?string $relativePath): void
{
    if (!$relativePath) return;
    $fullPath = __DIR__ . '/../' . ltrim($relativePath, '/');
    $baseUpload = realpath(UPLOAD_DIR);
    $filePath = realpath($fullPath);
    if ($filePath && $baseUpload && str_starts_with($filePath, $baseUpload) && is_file($filePath)) {
        unlink($filePath);
    }
}
