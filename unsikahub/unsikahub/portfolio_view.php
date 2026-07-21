<?php
require_once __DIR__ . '/includes/auth.php';
$id = (int)($_GET['id'] ?? 0);
$stmt = $pdo->prepare('
    SELECT p.*, u.name AS user_name, u.photo AS user_photo, u.email AS user_email, t.name AS type_name
    FROM portfolios p
    JOIN users u ON u.id = p.user_id
    JOIN portfolio_types t ON t.id = p.type_id
    WHERE p.id = ?
');
$stmt->execute([$id]);
$item = $stmt->fetch();
if (!$item) {
    http_response_code(404);
    die('Portofolio tidak ditemukan.');
}

$canEdit = is_logged_in() && (is_admin() || (int)current_user()['id'] === (int)$item['user_id']);
$pageTitle = e($item['title']) . ' | UnsikaHub';
require_once __DIR__ . '/includes/header.php';
?>
<section class="container detail-layout">
    <div>
        <?php if ($item['cover_image']): ?>
            <img class="detail-cover" src="<?= url($item['cover_image']) ?>" alt="Cover <?= e($item['title']) ?>">
        <?php else: ?>
            <div class="detail-cover"></div>
        <?php endif; ?>
    </div>
    <article class="card">
        <span class="badge"><?= e($item['type_name']) ?></span>
        <h1 style="font-size:clamp(2rem,5vw,4rem)"><?= e($item['title']) ?></h1>
        <div class="owner">
            <img src="<?= url($item['user_photo']) ?>" alt="Foto <?= e($item['user_name']) ?>">
            <span><?= e($item['user_name']) ?> · <?= e(date('d M Y', strtotime($item['created_at']))) ?></span>
        </div>
        <p><?= nl2br(e($item['description'])) ?></p>
        <div class="action-row">
            <?php if ($item['project_url']): ?><a class="btn" href="<?= e($item['project_url']) ?>" target="_blank" rel="noopener">Buka Proyek</a><?php endif; ?>
            <?php if ($item['proof_file']): ?><a class="btn secondary" href="<?= url($item['proof_file']) ?>" target="_blank" rel="noopener">Lihat Bukti</a><?php endif; ?>
            <?php if ($canEdit): ?>
                <a class="btn secondary" href="<?= url('portfolio_edit.php?id=' . $item['id']) ?>">Edit</a>
                <form method="post" action="<?= url('portfolio_delete.php') ?>">
                    <input type="hidden" name="id" value="<?= (int)$item['id'] ?>">
                    <button class="btn danger" type="submit" data-confirm="Hapus portofolio ini?">Hapus</button>
                </form>
            <?php endif; ?>
        </div>
    </article>
</section>
<?php require_once __DIR__ . '/includes/footer.php'; ?>
