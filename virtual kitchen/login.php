<?php
require_once __DIR__ . '/inc/header.php';
$pdo = db();
$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $token = $_POST['csrf'] ?? '';
    if (!csrf_check($token)) {
        $errors[] = 'Invalid CSRF token.';
    }
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';

    if (!$errors) {
        $stmt = $pdo->prepare('SELECT uid, password FROM users WHERE username = :u OR email = :u');
        $stmt->execute(['u' => $username]);
        $u = $stmt->fetch();
        if ($u && password_verify($password, $u['password'])) {
            // login success
            $_SESSION['user_id'] = (int)$u['uid'];
            session_regenerate_id(true);
            header('Location: /index.php');
            exit;
        } else {
            $errors[] = 'Invalid username/email or password.';
        }
    }
}
?>
<div class="card">
  <h2>Login</h2>
  <?php if ($errors): foreach ($errors as $err) echo '<p class="small">'.e($err).'</p>'; endforeach; ?>
  <form method="post" action="login.php">
    <input type="hidden" name="csrf" value="<?= e(csrf_token()) ?>">
    <div class="form-group">
      <label>Username or Email</label>
      <input type="text" name="username" required value="<?= e($_POST['username'] ?? '') ?>">
    </div>
    <div class="form-group">
      <label>Password</label>
      <input type="password" name="password" required>
    </div>
    <button class="btn" type="submit">Login</button>
  </form>
</div>

<?php require_once __DIR__ . '/inc/footer.php'; ?>
