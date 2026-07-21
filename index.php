<?php
require_once __DIR__ . '/includes/auth.php';
$pageTitle = 'UnsikaHub | Portal Portofolio Mahasiswa';

$typeStmt = $pdo->query('SELECT * FROM portfolio_types ORDER BY name ASC');
$types = $typeStmt->fetchAll();

$portfolioStmt = $pdo->query('
    SELECT p.*, u.name AS user_name, u.photo AS user_photo, t.name AS type_name
    FROM portfolios p
    JOIN users u ON u.id = p.user_id
    JOIN portfolio_types t ON t.id = p.type_id
    ORDER BY p.created_at DESC
    LIMIT 6
');
$latestPortfolios = $portfolioStmt->fetchAll();

require_once __DIR__ . '/includes/header.php';
?>
<section class="hero container">
    <div class="hero-grid">
        <div>
            <div class="kicker">Pusat Portofolio Mahasiswa Unsika</div>
            <h1>Temukan, unggah, dan kurasi karya mahasiswa Unsika.</h1>
            <p class="lead">UnsikaHub adalah aplikasi web portofolio mahasiswa yang menggabungkan tampilan eksplorasi karya, pengelompokan jenis portofolio, pencarian berbasis kata kunci, dan manajemen akun berbasis role.</p>
            <div class="actions">
                <a class="btn" href="<?= url('portfolios.php') ?>">Jelajahi Karya</a>
                <?php if (!is_logged_in()): ?>
                    <a class="btn secondary" href="<?= url('register.php') ?>">Mulai Upload</a>
                <?php else: ?>
                    <a class="btn secondary" href="<?= url('portfolio_create.php') ?>">Upload Karya</a>
                <?php endif; ?>
            </div>
        </div>
        <div class="showcase" aria-label="Preview kategori portofolio">
            <div class="showcase-card"><div class="showcase-thumb"></div><h3>Website</h3><span>UI, landing page, aplikasi web</span></div>
            <div class="showcase-card"><div class="showcase-thumb"></div><h3>Desain</h3><span>Branding, poster, grafis</span></div>
            <div class="showcase-card"><div class="showcase-thumb"></div><h3>Fotografi</h3><span>Dokumentasi visual kreatif</span></div>
            <div class="showcase-card"><div class="showcase-thumb"></div><h3>Bisnis</h3><span>Pitch deck dan ide usaha</span></div>
        </div>
    </div>
</section>

<section class="section container">
    <div class="section-head">
        <div>
            <div class="kicker">Keunggulan UnsikaHub</div>
            <h2>Ruang digital untuk menampilkan karya dan potensi terbaik.</h2>
            <p>
                UnsikaHub membantu pengguna membangun portofolio yang rapi, mudah ditemukan,
                dan siap dibagikan sebagai identitas karya di dunia digital.
            </p>
        </div>
    </div>

    <div class="grid">
        <article class="card">
            <h3>Tampilkan Karya Terbaik</h3>
            <p>
                Unggah karya pilihan dalam satu halaman portofolio yang lebih rapi,
                profesional, dan mudah dilihat oleh orang lain.
            </p>
        </article>

        <article class="card">
            <h3>Bangun Identitas Digital</h3>
            <p>
                Setiap pengguna dapat memperlihatkan kemampuan, minat, dan pengalaman
                melalui karya yang sudah pernah dibuat.
            </p>
        </article>

        <article class="card">
            <h3>Jelajahi Berbagai Kategori</h3>
            <p>
                Temukan portofolio dari berbagai bidang seperti website, desain,
                fotografi, bisnis, dan kategori karya lainnya.
            </p>
        </article>

        <article class="card">
            <h3>Mudah Dicari dan Dibagikan</h3>
            <p>
                Portofolio dapat ditemukan melalui halaman explore, sehingga karya
                lebih mudah dilihat oleh pengguna lain.
            </p>
        </article>

        <article class="card">
            <h3>Simpan Bukti Karya</h3>
            <p>
                Tambahkan cover, deskripsi, link proyek, dan file pendukung agar
                portofolio terlihat lebih lengkap dan meyakinkan.
            </p>
        </article>

        <article class="card">
            <h3>Cocok untuk Semua Pengguna</h3>
            <p>
                UnsikaHub dapat digunakan untuk menampilkan karya pribadi, tugas,
                proyek kreatif, maupun portofolio yang ingin dipersiapkan untuk kebutuhan masa depan.
            </p>
        </article>
    </div>
</section>

<section class="section container">
    <div class="section-head">
        <div>
            <div class="kicker">Kategori</div>
            <h2>Jenis portofolio yang tersedia.</h2>
        </div>
        <a class="btn secondary small" href="<?= url('portfolios.php') ?>">Lihat Semua</a>
    </div>
    <div class="grid">
        <?php foreach ($types as $type): ?>
            <article class="card">
                <span class="badge"><?= e($type['name']) ?></span>
                <p><?= e($type['description']) ?></p>
            </article>
        <?php endforeach; ?>
    </div>
</section>

<section class="section container">
    <div class="section-head">
        <div>
            <div class="kicker">Karya terbaru</div>
            <h2>Portofolio yang baru diunggah.</h2>
        </div>
    </div>
    <?php if ($latestPortfolios): ?>
        <div class="portfolio-grid">
            <?php foreach ($latestPortfolios as $item): ?>
                <article class="card portfolio-card">
                    <?php if ($item['cover_image']): ?>
                        <img class="portfolio-cover" src="<?= url($item['cover_image']) ?>" alt="Cover <?= e($item['title']) ?>">
                    <?php else: ?>
                        <div class="portfolio-cover"></div>
                    <?php endif; ?>
                    <div class="portfolio-body">
                        <span class="badge"><?= e($item['type_name']) ?></span>
                        <h3><a href="<?= url('portfolio_view.php?id=' . $item['id']) ?>"><?= e($item['title']) ?></a></h3>
                        <p><?= e(mb_strimwidth($item['description'], 0, 115, '...')) ?></p>
                        <div class="owner">
                            <img src="<?= url($item['user_photo']) ?>" alt="Foto <?= e($item['user_name']) ?>">
                            <span><?= e($item['user_name']) ?></span>
                        </div>
                    </div>
                </article>
            <?php endforeach; ?>
        </div>
    <?php else: ?>
        <div class="empty">Belum ada portofolio. Jadilah pengguna pertama yang mengunggah karya.</div>
    <?php endif; ?>
</section>
<?php require_once __DIR__ . '/includes/footer.php'; ?>
