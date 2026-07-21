</main>

<footer class="site-footer">
    <div class="footer-shell">
        <div class="footer-brand-card">
            <a class="footer-brand" href="<?= url('index.php') ?>">
                <span class="footer-brand-mark">
                    <img src="<?= url('assets/img/logo-unsikahub.png') ?>" alt="Logo UnsikaHub">
                </span>
                <span>UnsikaHub</span>
            </a>

            <p>
                Platform portofolio mahasiswa untuk menampilkan karya, bukti proyek,
                dan identitas kreatif dalam satu ruang digital yang rapi.
            </p>

            <div class="footer-pill-row">
                <span class="footer-pill">Portfolio</span>
                <span class="footer-pill">Creative Work</span>
                <span class="footer-pill">Student Hub</span>
            </div>
        </div>

        <div class="footer-column">
            <h3>Navigasi</h3>
            <a href="<?= url('index.php') ?>">Beranda</a>
            <a href="<?= url('portfolios.php') ?>">Explore Karya</a>

            <?php if (is_logged_in()): ?>
                <a href="<?= url('dashboard.php') ?>">Dashboard</a>
                <a href="<?= url('portfolio_create.php') ?>">Upload Karya</a>
            <?php else: ?>
                <a href="<?= url('login.php') ?>">Login</a>
                <a href="<?= url('register.php') ?>">Register</a>
            <?php endif; ?>
        </div>

        <div class="footer-column">
            <h3>Kategori</h3>
            <a href="<?= url('portfolios.php?type_id=1') ?>">Website</a>
            <a href="<?= url('portfolios.php?type_id=2') ?>">Desain</a>
            <a href="<?= url('portfolios.php?type_id=3') ?>">Fotografi</a>
            <a href="<?= url('portfolios.php?type_id=4') ?>">Bisnis</a>
        </div>

        <div class="footer-column footer-info">
            <h3>Platform</h3>
            <p>
                Kelola karya mahasiswa berdasarkan jenis portofolio, lengkap dengan
                cover, deskripsi, link proyek, dan bukti karya.
            </p>

            <?php if (is_logged_in() && is_admin()): ?>
                <a class="footer-admin-link" href="<?= url('admin/users.php') ?>">Kelola Pengguna</a>
                <a class="footer-admin-link" href="<?= url('admin/types.php') ?>">Kelola Jenis</a>
            <?php endif; ?>
        </div>
    </div>

    <div class="footer-bottom">
        <span>&copy; <?= date('Y') ?> UnsikaHub. Semua hak cipta dilindungi.</span>
        <span>Designed for student portfolio management.</span>
    </div>
</footer>

<script src="<?= url('assets/js/app.js') ?>"></script>
</body>

</html>