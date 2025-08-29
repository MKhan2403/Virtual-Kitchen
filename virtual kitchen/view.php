<?php
require_once __DIR__ . '/inc/header.php';
$rid = intval($_GET['rid'] ?? 0);
if ($rid <= 0) {
    header('Location: /index.php');
    exit;
}
$pdo = db();
$stmt = $pdo->prepare('SELECT r.*, u.username FROM recipes r JOIN users u ON r.uid = u.uid WHERE r.rid = :rid');
$stmt->execute(['rid' => $rid]);
$r = $stmt->fetch();
if (!$r) {
    echo '<div class="card"><p>Recipe not found.</p></div>';
    require_once __DIR__ . '/inc/footer.php';
    exit;
}
?>
<div class="card">
  <h2><?= e($r['name']) ?></h2>
  <div class="small"><?= e($r['type']) ?> • <?= intval($r['cookingtime']) ?> minutes • by <?= e($r['username']) ?></div>
  <?php if ($r['image'] && file_exists(__DIR__ . '/uploads/'.$r['image'])): ?>
    <img src="/uploads/<?= e($r['image']) ?>" alt="<?= e($r['name']) ?>" style="width:100%;max-height:360px;object-fit:cover;margin-top:12px;border-radius:8px">
  <?php endif; ?>
  <h3>Description</h3>
  <p><?= nl2br(e($r['description'])) ?></p>

  <h3>Ingredients</h3>
  <p><?= nl2br(e($r['ingredients'])) ?></p>

  <h3>Instructions</h3>
  <p><?= nl2br(e($r['instructions'])) ?></p>

  <?php
  $user = currentUser();
  if ($user && $user['uid'] == $r['uid'] || ($user && $user['uid'] == $r['uid'])): // owner check
  ?>
    <div style="margin-top:12px;">
      <a class="btn" href="/edit_recipe.php?rid=<?= intval($r['rid']) ?>">Edit this recipe</a>
    </div>
  <?php endif; ?>
</div>

<?php require_once __DIR__ . '/inc/footer.php'; ?>
