<?php
require_once __DIR__ . '/inc/header.php';

$pdo = db();

$q = trim($_GET['q'] ?? '');
$type = $_GET['type'] ?? '';

$sql = 'SELECT r.rid, r.name, r.description, r.type, r.image, u.username
        FROM recipes r JOIN users u ON r.uid = u.uid';
$conditions = [];
$params = [];

if ($q !== '') {
    $conditions[] = '(r.name LIKE :q OR r.description LIKE :q OR r.ingredients LIKE :q)';
    $params['q'] = '%' . $q . '%';
}
if ($type !== '') {
    $conditions[] = 'r.type = :type';
    $params['type'] = $type;
}
if ($conditions) {
    $sql .= ' WHERE ' . implode(' AND ', $conditions);
}
$sql .= ' ORDER BY r.created_at DESC LIMIT 100';

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$recipes = $stmt->fetchAll();
?>

<div class="card">
  <form class="search-bar" method="get" action="index.php">
    <input type="text" name="q" placeholder="Search by name, ingredient or description..." value="<?= e($q) ?>">
    <select name="type">
      <option value="">All types</option>
      <option value="Asian" <?= $type === 'Asian' ? 'selected' : '' ?>>Asian</option>
      <option value="Chinese" <?= $type === 'Chinese' ? 'selected' : '' ?>>Chinese</option>
      <option value="Indian" <?= $type === 'Indian' ? 'selected' : '' ?>>Indian</option>
      <option value="Italian" <?= $type === 'Italian' ? 'selected' : '' ?>>Italian</option>
      <option value="French" <?= $type === 'French' ? 'selected' : '' ?>>French</option>
      <option value="Mexican" <?= $type === 'Mexican' ? 'selected' : '' ?>>Mexican</option>
      <option value="others" <?= $type === 'others' ? 'selected' : '' ?>>Others</option>
    </select>
    <button class="btn" type="submit">Search</button>
  </form>

  <h2>Recipes</h2>
  <?php if (empty($recipes)): ?>
    <p class="small">No recipes match that search. Try browsing all recipes.</p>
  <?php else: ?>
    <div class="grid">
      <?php foreach ($recipes as $r): ?>
        <div class="card recipe-card">
          <?php if ($r['image'] && file_exists(__DIR__ . '/uploads/' . $r['image'])): ?>
            <img src="/uploads/<?= e($r['image']) ?>" alt="<?= e($r['name']) ?>">
          <?php else: ?>
            <img src="https://via.placeholder.com/600x300?text=Recipe" alt="placeholder">
          <?php endif; ?>
          <h3><?= e($r['name']) ?></h3>
          <div class="recipe-meta"><?= e($r['type']) ?> • by <?= e($r['username']) ?></div>
          <p><?= e(strlen($r['description']) > 140 ? substr($r['description'],0,137).'...' : $r['description']) ?></p>
          <div style="margin-top:auto;"><a class="btn" href="/view.php?rid=<?= intval($r['rid']) ?>">View Recipe</a></div>
        </div>
      <?php endforeach; ?>
    </div>
  <?php endif; ?>
</div>

<?php require_once __DIR__ . '/inc/footer.php'; ?>
