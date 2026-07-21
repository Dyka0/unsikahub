<?php $user = current_user(); ?>
<!doctype html>
<html lang="id">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= e($pageTitle ?? 'UnsikaHub') ?></title>
    <link rel="stylesheet" href="<?= url('assets/css/style.css') ?>">
</head>

<body>
    <header class="site-header">
        <a class="brand" href="<?= url('index.php') ?>">
            <span class="brand-mark">
                <img src="<?= url('assets/img/logo-unsikahub.png') ?>" alt="Logo UnsikaHub">
            </span>
            <span>UnsikaHub</span>
        </a>
        <nav class="main-nav" id="mainNav">
            <a href="<?= url('index.php') ?>">Beranda</a>
            <a href="<?= url('portfolios.php') ?>">Explore</a>
            <?php if ($user): ?>
                <a href="<?= url('dashboard.php') ?>">Dashboard</a>
                <a href="<?= url('portfolio_create.php') ?>">Upload Karya</a>
                <?php if (is_admin()): ?>
                    <a href="<?= url('admin/users.php') ?>">Pengguna</a>
                    <a href="<?= url('admin/types.php') ?>">Jenis</a>
                <?php endif; ?>
                <a href="<?= url('logout.php') ?>">Logout</a>
            <?php else: ?>
                <a href="<?= url('login.php') ?>">Login</a>
                <a class="btn btn-small" href="<?= url('register.php') ?>">Register</a>
            <?php endif; ?>
        </nav>
        <?php if ($user): ?>
            <a class="user-chip" href="<?= url('dashboard.php') ?>">
                <img src="<?= url($user['photo'] ?: 'assets/img/default-user.png') ?>" alt="Foto pengguna">
                <span><?= e($user['name']) ?></span>
            </a>
        <?php endif; ?>
        <button class="nav-toggle" type="button" aria-label="Buka menu"
            onclick="document.getElementById('mainNav').classList.toggle('open')">☰</button>
    </header>
    <main>
        <?php show_flash(); ?>