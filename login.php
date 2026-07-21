<?php
require_once __DIR__ . '/includes/auth.php';
if (is_logged_in()) redirect('dashboard.php');

$errors = [];
$email = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    $stmt = $pdo->prepare('SELECT * FROM users WHERE email = ? LIMIT 1');
    $stmt->execute([$email]);
    $user = $stmt->fetch();

    if ($user && password_verify($password, $user['password_hash'])) {
        session_regenerate_id(true);
        $_SESSION['user'] = [
            'id' => (int)$user['id'],
            'name' => $user['name'],
            'email' => $user['email'],
            'photo' => $user['photo'],
            'role' => $user['role'],
        ];
        redirect('dashboard.php');
    }
    $errors[] = 'Email atau password salah.';
}

$pageTitle = 'Login | UnsikaHub';
require_once __DIR__ . '/includes/header.php';
?>
<section class="form-page container">
    <div class="form-card">
        <div class="kicker">Masuk akun</div>
        <h2>Login UnsikaHub</h2>
        <?php foreach ($errors as $error): ?><div class="alert alert-danger"><?= e($error) ?></div><?php endforeach; ?>
        <form method="post">
            <div class="input-group">
                <label for="email">Email</label>
                <input id="email" type="email" name="email" value="<?= e($email) ?>" required>
            </div>
            <div class="input-group">
                <label for="password">Password</label>
                <input id="password" type="password" name="password" required>
            </div>
            <button class="btn" type="submit">Login</button>
        </form>
    </div>
</section>
<?php require_once __DIR__ . '/includes/footer.php'; ?>
