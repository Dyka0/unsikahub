<?php
require_once __DIR__ . '/includes/auth.php';
require_login();

$id = (int)($_GET['id'] ?? 0);
$stmt = $pdo->prepare('SELECT * FROM portfolios WHERE id = ?');
$stmt->execute([$id]);
$item = $stmt->fetch();
if (!$item) {
    http_response_code(404);
    die('Portofolio tidak ditemukan.');
}
if (!is_admin() && (int)$item['user_id'] !== (int)current_user()['id']) {
    http_response_code(403);
    die('Akses ditolak.');
}

$types = $pdo->query('SELECT * FROM portfolio_types ORDER BY name ASC')->fetchAll();
$errors = [];
$title = $item['title'];
$description = $item['description'];
$projectUrl = $item['project_url'];
$typeId = (int)$item['type_id'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = trim($_POST['title'] ?? '');
    $description = trim($_POST['description'] ?? '');
    $projectUrl = trim($_POST['project_url'] ?? '');
    $typeId = (int)($_POST['type_id'] ?? 0);

    if ($title === '') $errors[] = 'Judul portofolio wajib diisi.';
    if ($description === '') $errors[] = 'Deskripsi portofolio wajib diisi.';
    if ($typeId <= 0) $errors[] = 'Jenis portofolio wajib dipilih.';
    if ($projectUrl !== '' && !filter_var($projectUrl, FILTER_VALIDATE_URL)) $errors[] = 'URL proyek tidak valid.';

    if (!$errors) {
        try {
            $cover = $item['cover_image'];
            $proof = $item['proof_file'];
            $newCover = upload_file($_FILES['cover_image'], 'portfolio', ['jpg', 'jpeg', 'png', 'webp'], 3145728);
            if ($newCover) { delete_uploaded_file($cover); $cover = $newCover; }
            $newProof = upload_file($_FILES['proof_file'], 'portfolio', ['jpg', 'jpeg', 'png', 'webp', 'pdf', 'doc', 'docx', 'zip'], 8388608);
            if ($newProof) { delete_uploaded_file($proof); $proof = $newProof; }

            $update = $pdo->prepare('UPDATE portfolios SET type_id = ?, title = ?, description = ?, project_url = ?, cover_image = ?, proof_file = ? WHERE id = ?');
            $update->execute([$typeId, $title, $description, $projectUrl ?: null, $cover, $proof, $id]);
            set_flash('success', 'Portofolio berhasil diperbarui.');
            redirect('portfolio_view.php?id=' . $id);
        } catch (RuntimeException $e) {
            $errors[] = $e->getMessage();
        }
    }
}

$pageTitle = 'Edit Portofolio | UnsikaHub';
require_once __DIR__ . '/includes/header.php';
?>
<section class="form-page container">
    <div class="form-card wide">
        <div class="kicker">Edit karya</div>
        <h2>Perbarui data portofolio.</h2>
        <?php foreach ($errors as $error): ?><div class="alert alert-danger"><?= e($error) ?></div><?php endforeach; ?>
        <form method="post" enctype="multipart/form-data">
            <div class="form-row">
                <div class="input-group">
                    <label for="title">Judul portofolio</label>
                    <input id="title" type="text" name="title" value="<?= e($title) ?>" required>
                </div>
                <div class="input-group">
                    <label for="type_id">Jenis portofolio</label>
                    <select id="type_id" name="type_id" required>
                        <?php foreach ($types as $type): ?>
                            <option value="<?= (int)$type['id'] ?>" <?= $typeId === (int)$type['id'] ? 'selected' : '' ?>><?= e($type['name']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>
            <div class="input-group">
                <label for="description">Deskripsi karya</label>
                <textarea id="description" name="description" required><?= e($description) ?></textarea>
            </div>
            <div class="input-group">
                <label for="project_url">Link proyek atau demo <span class="help-text">(opsional)</span></label>
                <input id="project_url" type="url" name="project_url" value="<?= e($projectUrl) ?>">
            </div>
            <div class="form-row">
                <div class="input-group">
                    <label for="cover_image">Ganti cover portofolio</label>
                    <input id="cover_image" type="file" name="cover_image" accept="image/jpeg,image/png,image/webp">
                    <div class="help-text">Kosongkan jika tidak ingin mengganti cover.</div>
                </div>
                <div class="input-group">
                    <label for="proof_file">Ganti bukti portofolio</label>
                    <input id="proof_file" type="file" name="proof_file" accept=".jpg,.jpeg,.png,.webp,.pdf,.doc,.docx,.zip">
                    <div class="help-text">Kosongkan jika tidak ingin mengganti bukti.</div>
                </div>
            </div>
            <button class="btn" type="submit">Simpan Perubahan</button>
            <a class="btn secondary" href="<?= url('portfolio_view.php?id=' . $id) ?>">Batal</a>
        </form>
    </div>
</section>
<?php require_once __DIR__ . '/includes/footer.php'; ?>
