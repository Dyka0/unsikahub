<?php
require_once __DIR__ . '/includes/auth.php';

if (is_logged_in()) {
    redirect('dashboard.php');
}

$errors = [];
$name = '';
$email = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirm = $_POST['confirm_password'] ?? '';

    if ($name === '') {
        $errors[] = 'Nama wajib diisi.';
    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = 'Format email tidak valid.';
    }

    if (strlen($password) < 6) {
        $errors[] = 'Password minimal 6 karakter.';
    }

    if ($password !== $confirm) {
        $errors[] = 'Konfirmasi password tidak sama.';
    }

    if (!$errors) {
        $check = $pdo->prepare('SELECT id FROM users WHERE email = ? LIMIT 1');
        $check->execute([$email]);

        if ($check->fetch()) {
            $errors[] = 'Email sudah terdaftar.';
        } else {
            try {
                $photoPath = 'assets/img/default-user.png';

                if (
                    isset($_FILES['photo']) &&
                    ($_FILES['photo']['error'] ?? UPLOAD_ERR_NO_FILE) !== UPLOAD_ERR_NO_FILE
                ) {
                    $photoPath = upload_file(
                        $_FILES['photo'],
                        'profile',
                        ['jpg', 'jpeg', 'png', 'webp'],
                        2097152
                    );
                }

                $hash = password_hash($password, PASSWORD_DEFAULT);

                $stmt = $pdo->prepare('
                    INSERT INTO users (name, email, password_hash, photo, role)
                    VALUES (?, ?, ?, ?, "user")
                ');

                $stmt->execute([
                    $name,
                    $email,
                    $hash,
                    $photoPath
                ]);

                set_flash('success', 'Registrasi berhasil. Silakan login.');
                redirect('login.php');
            } catch (RuntimeException $e) {
                $errors[] = $e->getMessage();
            }
        }
    }
}

$pageTitle = 'Register | UnsikaHub';
require_once __DIR__ . '/includes/header.php';
?>

<section class="form-page container">
    <div class="form-card">
        <div class="kicker">Buat akun</div>
        <h2>Register UnsikaHub</h2>

        <?php foreach ($errors as $error): ?>
            <div class="alert alert-danger"><?= e($error) ?></div>
        <?php endforeach; ?>

        <form method="post" enctype="multipart/form-data">
            <div class="input-group">
                <label for="name">Nama lengkap</label>
                <input
                    id="name"
                    type="text"
                    name="name"
                    value="<?= e($name) ?>"
                    required
                >
            </div>

            <div class="input-group">
                <label for="email">Email</label>
                <input
                    id="email"
                    type="email"
                    name="email"
                    value="<?= e($email) ?>"
                    required
                >
            </div>

            <div class="input-group">
                <label for="photo">
                    Foto pengguna <span class="help-text">(opsional)</span>
                </label>

                <input
                    id="photo"
                    type="file"
                    name="photo"
                    accept="image/jpeg,image/png,image/webp"
                >

                <div class="help-text">
                    Jika tidak diisi, sistem akan menggunakan foto profil default.
                    Format: JPG, PNG, WEBP. Maksimal 2 MB.
                </div>
            </div>

            <div class="form-row">
                <div class="input-group">
                    <label for="password">Password</label>
                    <input
                        id="password"
                        type="password"
                        name="password"
                        minlength="6"
                        required
                    >
                </div>

                <div class="input-group">
                    <label for="confirm_password">Konfirmasi password</label>
                    <input
                        id="confirm_password"
                        type="password"
                        name="confirm_password"
                        minlength="6"
                        required
                    >
                </div>
            </div>

            <button class="btn" type="submit">Daftar</button>

            <p>
                Sudah punya akun?
                <a href="<?= url('login.php') ?>">Login di sini</a>.
            </p>
        </form>
    </div>
</section>

<?php require_once __DIR__ . '/includes/footer.php'; ?>