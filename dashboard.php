<?php
require_once __DIR__ . '/includes/auth.php';
require_login();
$user = current_user();

if (is_admin()) {
    $stats = [
        'users' => (int) $pdo->query('SELECT COUNT(*) FROM users')->fetchColumn(),
        'types' => (int) $pdo->query('SELECT COUNT(*) FROM portfolio_types')->fetchColumn(),
        'portfolios' => (int) $pdo->query('SELECT COUNT(*) FROM portfolios')->fetchColumn(),
    ];
    $stmt = $pdo->query('
        SELECT p.*, u.name AS user_name, t.name AS type_name
        FROM portfolios p
        JOIN users u ON u.id = p.user_id
        JOIN portfolio_types t ON t.id = p.type_id
        ORDER BY p.created_at DESC
        LIMIT 8
    ');
    $portfolios = $stmt->fetchAll();
} else {
    $stmt = $pdo->prepare('
        SELECT p.*, t.name AS type_name
        FROM portfolios p
        JOIN portfolio_types t ON t.id = p.type_id
        WHERE p.user_id = ?
        ORDER BY p.created_at DESC
    ');
    $stmt->execute([$user['id']]);
    $portfolios = $stmt->fetchAll();
    $stats = ['my_portfolios' => count($portfolios)];
}

$pageTitle = 'Dashboard | UnsikaHub';
require_once __DIR__ . '/includes/header.php';
?>
<section class="section container">
    <div class="section-head">
        <div>
            <div class="kicker">Dashboard <?= is_admin() ? 'Admin' : 'User' ?></div>
            <h2>Halo, <?= e($user['name']) ?>.</h2>
            <p>Role akun Anda: <span class="badge <?= e($user['role']) ?>"><?= e(strtoupper($user['role'])) ?></span>
            </p>
        </div>
        <a class="btn" href="<?= url('portfolio_create.php') ?>">Upload Portofolio</a>
    </div>

    <?php if (is_admin()): ?>
        <div class="stat-grid">
            <div class="stat-card"><strong><?= $stats['users'] ?></strong><span>Pengguna</span></div>
            <div class="stat-card"><strong><?= $stats['types'] ?></strong><span>Jenis Portofolio</span></div>
            <div class="stat-card"><strong><?= $stats['portfolios'] ?></strong><span>Total Karya</span></div>
            <div class="stat-card"><strong>2</strong><span>Role: Admin & User</span></div>
        </div>
        <div class="actions">
            <a class="btn secondary" href="<?= url('admin/users.php') ?>">Kelola Pengguna</a>
            <a class="btn secondary" href="<?= url('admin/types.php') ?>">Kelola Jenis Portofolio</a>
            <a class="btn secondary" href="<?= url('portfolios.php') ?>">Pantau Portofolio</a>
        </div>
    <?php else: ?>
        <div class="stat-grid">
            <a class="stat-card stat-link" href="#portfolio-saya">
                <strong><?= $stats['my_portfolios'] ?></strong>
                <span>Portofolio Saya</span>
                <small>Lihat karya yang sudah Anda unggah</small>
            </a>

            <a class="stat-card stat-link" href="<?= url('portfolio_create.php') ?>">
                <strong>Upload</strong>
                <small>Unggah portofolio, cover, dan bukti karya</small>
            </a>

            <a class="stat-card stat-link" href="<?= url('portfolios.php') ?>">
                <strong>Search</strong>
                <small>Temukan karya berdasarkan judul, jenis, atau nama</small>
            </a>

            <a class="stat-card stat-link" href="<?= url('portfolios.php') ?>">
                <strong>Explore</strong>
                <small>Lihat portofolio dari pengguna lain</small>
            </a>
        </div>
    <?php endif; ?>
</section>

<section class="section container" id="portfolio-saya">
    <div class="section-head">
        <div>
            <div class="kicker"><?= is_admin() ? 'Data terbaru' : 'Portofolio saya' ?></div>
            <h2><?= is_admin() ? 'Portofolio terbaru dari semua pengguna.' : 'Kelola karya yang sudah Anda unggah.' ?>
            </h2>
        </div>
    </div>
    <?php if ($portfolios): ?>
        <div class="table-wrap">
            <table>
                <thead>
                    <tr>
                        <th>Judul</th>
                        <th>Jenis</th><?php if (is_admin()): ?>
                            <th>Pemilik</th><?php endif; ?>
                        <th>Tanggal</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($portfolios as $item): ?>
                        <tr>
                            <td><?= e($item['title']) ?></td>
                            <td><span class="badge"><?= e($item['type_name']) ?></span></td>
                            <?php if (is_admin()): ?>
                                <td><?= e($item['user_name']) ?></td><?php endif; ?>
                            <td><?= e(date('d M Y', strtotime($item['created_at']))) ?></td>
                            <td>
                                <div class="action-row">
                                    <a class="btn small secondary"
                                        href="<?= url('portfolio_view.php?id=' . $item['id']) ?>">Detail</a>
                                    <?php if (!is_admin() || is_admin()): ?>
                                        <a class="btn small secondary"
                                            href="<?= url('portfolio_edit.php?id=' . $item['id']) ?>">Edit</a>
                                    <?php endif; ?>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php else: ?>
        <div class="empty">Belum ada portofolio. Klik tombol Upload Portofolio untuk menambahkan karya pertama.</div>
    <?php endif; ?>
</section>
<?php require_once __DIR__ . '/includes/footer.php'; ?>