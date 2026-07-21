<?php
require_once __DIR__ . '/includes/auth.php';
require_login();

$types = $pdo->query('SELECT * FROM portfolio_types ORDER BY name ASC')->fetchAll();
if (!$types) {
    set_flash('warning', 'Jenis portofolio belum tersedia. Admin harus membuat jenis portofolio terlebih dahulu.');
    redirect('dashboard.php');
}

$errors = [];
$title = $description = $projectUrl = '';
$typeId = 0;

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
            $cover = upload_file($_FILES['cover_image'], 'portfolio', ['jpg', 'jpeg', 'png', 'webp'], 3145728);
            $proof = upload_file($_FILES['proof_file'], 'portfolio', ['jpg', 'jpeg', 'png', 'webp', 'pdf', 'doc', 'docx', 'zip'], 8388608);
            $stmt = $pdo->prepare('INSERT INTO portfolios (user_id, type_id, title, description, project_url, cover_image, proof_file) VALUES (?, ?, ?, ?, ?, ?, ?)');
            $stmt->execute([current_user()['id'], $typeId, $title, $description, $projectUrl ?: null, $cover, $proof]);
            set_flash('success', 'Portofolio berhasil diunggah.');
            redirect('dashboard.php');
        } catch (RuntimeException $e) {
            $errors[] = $e->getMessage();
        }
    }
}

$pageTitle = 'Upload Portofolio | UnsikaHub';
require_once __DIR__ . '/includes/header.php';
?>
<section class="form-page container">
    <div class="form-card wide">
        <div class="kicker">Upload karya</div>
        <h2>Tambah portofolio mahasiswa.</h2>
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
                        <option value="">Pilih jenis</option>
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
                <input id="project_url" type="url" name="project_url" value="<?= e($projectUrl) ?>" placeholder="https://contoh.com/karya">
            </div>
            <div class="form-row">
                <div class="input-group">
                    <label for="cover_image">Cover portofolio</label>
                    <input id="cover_image" type="file" name="cover_image" accept="image/jpeg,image/png,image/webp">
                    <div class="help-text">Opsional. JPG, PNG, WEBP. Maksimal 3 MB.</div>
                </div>
                <div class="input-group">
                    <label for="proof_file">Bukti portofolio</label>
                    <input id="proof_file" type="file" name="proof_file" accept=".jpg,.jpeg,.png,.webp,.pdf,.doc,.docx,.zip">
                    <div class="help-text">Opsional. Gambar, PDF, DOC/DOCX, ZIP. Maksimal 8 MB.</div>
                </div>
            </div>
            <button class="btn" type="submit">Simpan Portofolio</button>
            <a class="btn secondary" href="<?= url('dashboard.php') ?>">Batal</a>
        </form>
    </div>
</section>
<?php require_once __DIR__ . '/includes/footer.php'; ?>
