<?php
require_once __DIR__ . '/../includes/auth.php';
require_admin();

$current = current_user();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    $userId = (int) ($_POST['user_id'] ?? 0);

    if ($userId === (int) $current['id'] && in_array($action, ['delete', 'update_role'], true)) {
        set_flash('warning', 'Admin tidak dapat mengubah atau menghapus role akunnya sendiri dari halaman ini.');
        redirect('admin/users.php');
    }

    if ($action === 'update_role') {
        $role = $_POST['role'] ?? 'user';
        if (!in_array($role, ['admin', 'user'], true))
            $role = 'user';
        $stmt = $pdo->prepare('UPDATE users SET role = ? WHERE id = ?');
        $stmt->execute([$role, $userId]);
        set_flash('success', 'Role pengguna berhasil diperbarui.');
        redirect('admin/users.php');
    }

    if ($action === 'delete') {
        $stmt = $pdo->prepare('SELECT photo FROM users WHERE id = ?');
        $stmt->execute([$userId]);
        $target = $stmt->fetch();
        if ($target) {
            $portfolioFiles = $pdo->prepare('SELECT cover_image, proof_file FROM portfolios WHERE user_id = ?');
            $portfolioFiles->execute([$userId]);
            foreach ($portfolioFiles->fetchAll() as $fileRow) {
                delete_uploaded_file($fileRow['cover_image']);
                delete_uploaded_file($fileRow['proof_file']);
            }

            $delete = $pdo->prepare('DELETE FROM users WHERE id = ?');
            $delete->execute([$userId]);
            if (!in_array($target['photo'], ['uploads/profile/default-admin.svg', 'assets/img/default-user.png'], true)) {
                delete_uploaded_file($target['photo']);
            }
            set_flash('success', 'Pengguna berhasil dihapus.');
        }
        redirect('admin/users.php');
    }
}

$keyword = trim($_GET['q'] ?? '');
if ($keyword !== '') {
    $stmt = $pdo->prepare('SELECT * FROM users WHERE name LIKE ? OR email LIKE ? ORDER BY created_at DESC');
    $like = '%' . $keyword . '%';
    $stmt->execute([$like, $like]);
} else {
    $stmt = $pdo->query('SELECT * FROM users ORDER BY created_at DESC');
}
$users = $stmt->fetchAll();

$pageTitle = 'Kelola Pengguna | UnsikaHub';
require_once __DIR__ . '/../includes/header.php';
?>
<section class="section container">
    <div class="section-head">
        <div>
            <div class="kicker">Admin</div>
            <h2>Kelola data pengguna dan role.</h2>
            <p>Halaman ini hanya dapat diakses Admin. User biasa tidak memiliki hak untuk mengelola pengguna.</p>
        </div>
    </div>
    <form class="search-panel" method="get">
        <input type="text" name="q" placeholder="Cari nama atau email pengguna..." value="<?= e($keyword) ?>">
        <button class="btn" type="submit">Cari</button>
        <a class="btn secondary" href="<?= url('admin/users.php') ?>">Reset</a>
    </form>
    <div class="table-wrap">
        <table>
            <thead>
                <tr>
                    <th>Foto</th>
                    <th>Nama</th>
                    <th>Email</th>
                    <th>Role</th>
                    <th>Terdaftar</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($users as $item): ?>
                    <tr>
                        <td><img class="avatar" src="<?= url($item['photo']) ?>" alt="Foto <?= e($item['name']) ?>"></td>
                        <td><?= e($item['name']) ?></td>
                        <td><?= e($item['email']) ?></td>
                        <td><span class="badge <?= e($item['role']) ?>"><?= e(strtoupper($item['role'])) ?></span></td>
                        <td><?= e(date('d M Y', strtotime($item['created_at']))) ?></td>
                        <td>
                            <?php if ((int) $item['id'] !== (int) $current['id']): ?>
                                <form class="inline-form" method="post">
                                    <input type="hidden" name="action" value="update_role">
                                    <input type="hidden" name="user_id" value="<?= (int) $item['id'] ?>">
                                    <select name="role">
                                        <option value="user" <?= $item['role'] === 'user' ? 'selected' : '' ?>>User</option>
                                        <option value="admin" <?= $item['role'] === 'admin' ? 'selected' : '' ?>>Admin</option>
                                    </select>
                                    <button class="btn small secondary" type="submit">Ubah</button>
                                </form>
                                <form class="inline-form" method="post" style="margin-top:8px">
                                    <input type="hidden" name="action" value="delete">
                                    <input type="hidden" name="user_id" value="<?= (int) $item['id'] ?>">
                                    <button class="btn small danger" type="submit"
                                        data-confirm="Hapus pengguna ini? Semua portofolionya juga akan terhapus.">Hapus</button>
                                </form>
                            <?php else: ?>
                                <span class="help-text">Akun aktif</span>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</section>
<?php require_once __DIR__ . '/../includes/footer.php'; ?>