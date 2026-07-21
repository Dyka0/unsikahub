<?php
require_once __DIR__ . '/includes/auth.php';

$q = trim($_GET['q'] ?? '');
$typeId = (int)($_GET['type_id'] ?? 0);

$types = $pdo->query('SELECT * FROM portfolio_types ORDER BY name ASC')->fetchAll();

$sql = '
    SELECT p.*, u.name AS user_name, u.photo AS user_photo, t.name AS type_name
    FROM portfolios p
    JOIN users u ON u.id = p.user_id
    JOIN portfolio_types t ON t.id = p.type_id
    WHERE 1=1
';
$params = [];

if ($q !== '') {
    $sql .= ' AND (p.title LIKE ? OR p.description LIKE ? OR u.name LIKE ? OR t.name LIKE ?)';
    $like = '%' . $q . '%';
    array_push($params, $like, $like, $like, $like);
}
if ($typeId > 0) {
    $sql .= ' AND p.type_id = ?';
    $params[] = $typeId;
}
$sql .= ' ORDER BY p.created_at DESC';

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$portfolios = $stmt->fetchAll();

$pageTitle = 'Explore Portofolio | UnsikaHub';
require_once __DIR__ . '/includes/header.php';
?>
<section class="section container">
    <div class="section-head">
        <div>
            <div class="kicker">Explore</div>
            <h2>Temukan portofolio mahasiswa.</h2>
            <p>Gunakan pencarian untuk menampilkan karya sesuai kata kunci, jenis portofolio, nama mahasiswa, atau deskripsi karya.</p>
        </div>
        <?php if (is_logged_in()): ?><a class="btn" href="<?= url('portfolio_create.php') ?>">Upload Karya</a><?php endif; ?>
    </div>
    <form class="search-panel" method="get">
        <input type="text" name="q" placeholder="Cari judul, deskripsi, jenis, atau nama mahasiswa..." value="<?= e($q) ?>">
        <select name="type_id">
            <option value="0">Semua jenis</option>
            <?php foreach ($types as $type): ?>
                <option value="<?= (int)$type['id'] ?>" <?= $typeId === (int)$type['id'] ? 'selected' : '' ?>><?= e($type['name']) ?></option>
            <?php endforeach; ?>
        </select>
        <button class="btn" type="submit">Cari</button>
    </form>

    <?php if ($portfolios): ?>
        <div class="portfolio-grid">
            <?php foreach ($portfolios as $item): ?>
                <article class="card portfolio-card">
                    <?php if ($item['cover_image']): ?>
                        <img class="portfolio-cover" src="<?= url($item['cover_image']) ?>" alt="Cover <?= e($item['title']) ?>">
                    <?php else: ?>
                        <div class="portfolio-cover"></div>
                    <?php endif; ?>
                    <div class="portfolio-body">
                        <span class="badge"><?= e($item['type_name']) ?></span>
                        <h3><a href="<?= url('portfolio_view.php?id=' . $item['id']) ?>"><?= e($item['title']) ?></a></h3>
                        <p><?= e(mb_strimwidth($item['description'], 0, 120, '...')) ?></p>
                        <div class="owner">
                            <img src="<?= url($item['user_photo']) ?>" alt="Foto <?= e($item['user_name']) ?>">
                            <span><?= e($item['user_name']) ?></span>
                        </div>
                    </div>
                </article>
            <?php endforeach; ?>
        </div>
    <?php else: ?>
        <div class="empty">Tidak ada portofolio yang cocok dengan pencarian.</div>
    <?php endif; ?>
</section>
<?php require_once __DIR__ . '/includes/footer.php'; ?>
