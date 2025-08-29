<?php
require_once __DIR__ . '/inc/header.php';

$errors = [];
$pdo = db();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $token = $_POST['csrf'] ?? '';
    if (!csrf_check($token)) {
        $errors[] = 'Invalid CSRF token.';
    }

    $username = trim($_POST['username'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $password2 = $_POST['password2'] ?? '';

    if (!preg_match('/^[A-Za-z0-9_]{3,30}$/', $username)) $errors[] = 'Username must be 3-30 characters, letters, numbers or underscore.';
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = 'Email address is invalid.';
    if (strlen($password) < 8) $errors[] = 'Password must be at least 8 characters.';
    if ($password !== $password2) $errors[] = 'Passwords do not match.';

    if (!$errors) {
        $stmt = $pdo->prepare('SELECT uid FROM users WHERE username = :username OR email = :email');
        $stmt->execute(['username' => $username, 'email' => $email]);
        if ($stmt->fetch()) {
            $errors[] = 'Username or email already taken.';
        } else {
            $hash = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $pdo->prepare('INSERT INTO users (username, password, email) VALUES (:u, :p, :e)');
            $stmt->execute(['u' => $username, 'p' => $hash, 'e' => $email]);
            $_SESSION['user_id'] = (int)$pdo->lastInsertId();
            session_regenerate_id(true);
            header('Location: /index.php');
            exit;
        }
    }
}
?>
<div class="card">
  <h2>Register</h2>
  <?php if ($errors): ?>
    <div class="card" style="background:#ffeef0;border:1px solid #ffd1dc;">
      <?php foreach ($errors as $err) echo '<p class="small">'.e($err).'</p>'; ?>
    </div>
  <?php endif; ?>
  <form method="post" action="register.php">
    <input type="hidden" name="csrf" value="<?= e(csrf_token()) ?>">
    <div class="form-group">
      <label>Username</label>
      <input type="text" name="username" required value="<?= e($_POST['username'] ?? '') ?>">
    </div>
    <div class="form-group">
      <label>Email</label>
      <input type="email" name="email" required value="<?= e($_POST['email'] ?? '') ?>">
    </div>
    <div class="form-group">
      <label>Password</label>
      <input type="password" name="password" required>
    </div>
    <div class="form-group">
      <label>Confirm password</label>
      <input type="password" name="password2" required>
    </div>
    <button class="btn" type="submit">Register</button>
  </form>
</div>

<?php require_once __DIR__ . '/inc/footer.php'; ?>
