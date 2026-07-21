<?php
require_once __DIR__ . '/../includes/auth.php';
require_admin();

$editType = null;
$errors = [];

if (isset($_GET['edit'])) {
    $stmt = $pdo->prepare('SELECT * FROM portfolio_types WHERE id = ?');
    $stmt->execute([(int)$_GET['edit']]);
    $editType = $stmt->fetch();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    $id = (int)($_POST['id'] ?? 0);
    $name = trim($_POST['name'] ?? '');
    $description = trim($_POST['description'] ?? '');

    if ($action === 'save') {
        if ($name === '') $errors[] = 'Nama jenis portofolio wajib diisi.';
        if (!$errors) {
            try {
                if ($id > 0) {
                    $stmt = $pdo->prepare('UPDATE portfolio_types SET name = ?, description = ? WHERE id = ?');
                    $stmt->execute([$name, $description, $id]);
                    set_flash('success', 'Jenis portofolio berhasil diperbarui.');
                } else {
                    $stmt = $pdo->prepare('INSERT INTO portfolio_types (name, description) VALUES (?, ?)');
                    $stmt->execute([$name, $description]);
                    set_flash('success', 'Jenis portofolio berhasil ditambahkan.');
                }
                redirect('admin/types.php');
            } catch (PDOException $e) {
                $errors[] = 'Nama jenis sudah ada atau data tidak valid.';
            }
        }
    }

    if ($action === 'delete') {
        $used = $pdo->prepare('SELECT COUNT(*) FROM portfolios WHERE type_id = ?');
        $used->execute([$id]);
        if ((int)$used->fetchColumn() > 0) {
            set_flash('warning', 'Jenis portofolio tidak dapat dihapus karena sudah dipakai oleh data portofolio.');
        } else {
            $delete = $pdo->prepare('DELETE FROM portfolio_types WHERE id = ?');
            $delete->execute([$id]);
            set_flash('success', 'Jenis portofolio berhasil dihapus.');
        }
        redirect('admin/types.php');
    }
}

$types = $pdo->query('SELECT * FROM portfolio_types ORDER BY name ASC')->fetchAll();
$pageTitle = 'Kelola Jenis Portofolio | UnsikaHub';
require_once __DIR__ . '/../includes/header.php';
?>
<section class="section container">
    <div class="section-head">
        <div>
            <div class="kicker">Admin</div>
            <h2>CRUD jenis portofolio.</h2>
            <p>Jenis yang dibuat di sini akan muncul sebagai pilihan ketika User mengunggah portofolio.</p>
        </div>
    </div>
    <?php foreach ($errors as $error): ?><div class="alert alert-danger"><?= e($error) ?></div><?php endforeach; ?>
    <div class="form-card wide" style="margin-bottom:22px">
        <form method="post">
            <input type="hidden" name="action" value="save">
            <input type="hidden" name="id" value="<?= (int)($editType['id'] ?? 0) ?>">
            <div class="form-row">
                <div class="input-group">
                    <label for="name">Nama jenis</label>
                    <input id="name" type="text" name="name" value="<?= e($editType['name'] ?? '') ?>" placeholder="Website / Desain / Fotografi / Bisnis" required>
                </div>
                <div class="input-group">
                    <label for="description">Deskripsi</label>
                    <input id="description" type="text" name="description" value="<?= e($editType['description'] ?? '') ?>" placeholder="Penjelasan singkat jenis portofolio">
                </div>
            </div>
            <button class="btn" type="submit"><?= $editType ? 'Simpan Perubahan' : 'Tambah Jenis' ?></button>
            <?php if ($editType): ?><a class="btn secondary" href="<?= url('admin/types.php') ?>">Batal Edit</a><?php endif; ?>
        </form>
    </div>
    <div class="table-wrap">
        <table>
            <thead><tr><th>Nama Jenis</th><th>Deskripsi</th><th>Dibuat</th><th>Aksi</th></tr></thead>
            <tbody>
            <?php foreach ($types as $type): ?>
                <tr>
                    <td><span class="badge"><?= e($type['name']) ?></span></td>
                    <td><?= e($type['description']) ?></td>
                    <td><?= e(date('d M Y', strtotime($type['created_at']))) ?></td>
                    <td>
                        <div class="action-row">
                            <a class="btn small secondary" href="<?= url('admin/types.php?edit=' . $type['id']) ?>">Edit</a>
                            <form method="post">
                                <input type="hidden" name="action" value="delete">
                                <input type="hidden" name="id" value="<?= (int)$type['id'] ?>">
                                <button class="btn small danger" type="submit" data-confirm="Hapus jenis portofolio ini?">Hapus</button>
                            </form>
                        </div>
                    </td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</section>
<?php require_once __DIR__ . '/../includes/footer.php'; ?>
