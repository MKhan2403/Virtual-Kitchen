<?php
require_once __DIR__ . '/inc/header.php';
if (!isLoggedIn()) {
    header('Location: /login.php');
    exit;
}

$pdo = db();
$rid = intval($_GET['rid'] ?? 0);
if ($rid <= 0) {
    header('Location: /index.php');
    exit;
}
$stmt = $pdo->prepare('SELECT * FROM recipes WHERE rid = :rid');
$stmt->execute(['rid' => $rid]);
$recipe = $stmt->fetch();
if (!$recipe) {
    echo '<div class="card"><p>Recipe not found.</p></div>'; require_once __DIR__ . '/inc/footer.php'; exit;
}
if ($recipe['uid'] != $_SESSION['user_id']) {
    echo '<div class="card"><p>Not authorized to edit this recipe.</p></div>'; require_once __DIR__ . '/inc/footer.php'; exit;
}

$errors = [];
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $token = $_POST['csrf'] ?? '';
    if (!csrf_check($token)) $errors[] = 'Invalid CSRF token.';

    $name = trim($_POST['name'] ?? '');
    $description = trim($_POST['description'] ?? '');
    $type = $_POST['type'] ?? 'others';
    $cookingtime = intval($_POST['cookingtime'] ?? 0);
    $ingredients = trim($_POST['ingredients'] ?? '');
    $instructions = trim($_POST['instructions'] ?? '');

    if ($name === '' || strlen($name) > 150) $errors[] = 'Name is required (max 150).';
    if ($description === '') $errors[] = 'Description is required.';
    if ($cookingtime <= 0 || $cookingtime > 1000) $errors[] = 'Provide a valid cooking time in minutes.';
    if ($ingredients === '') $errors[] = 'Ingredients are required.';
    if ($instructions === '') $errors[] = 'Instructions are required.';

    $imageFilename = handle_image_upload($_FILES['image'] ?? ['error'=>UPLOAD_ERR_NO_FILE]);

    if (!$errors) {
        $sql = 'UPDATE recipes SET name=:name, description=:desc, type=:type, cookingtime=:ct, ingredients=:ing, instructions=:ins';
        if ($imageFilename) $sql .= ', image=:img';
        $sql .= ' WHERE rid=:rid AND uid=:uid';
        $params = [
            'name'=>$name,'desc'=>$description,'type'=>$type,'ct'=>$cookingtime,'ing'=>$ingredients,'ins'=>$instructions,'rid'=>$rid,'uid'=>$_SESSION['user_id']
        ];
        if ($imageFilename) $params['img'] = $imageFilename;
        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
        header('Location: /view.php?rid='.$rid);
        exit;
    }
}
?>
<div class="card">
  <h2>Edit Recipe</h2>
  <?php if ($errors) foreach ($errors as $err) echo '<p class="small">'.e($err).'</p>'; ?>
  <form method="post" action="edit_recipe.php?rid=<?= $rid ?>" enctype="multipart/form-data">
    <input type="hidden" name="csrf" value="<?= e(csrf_token()) ?>">
    <div class="form-group">
      <label>Recipe name</label>
      <input type="text" name="name" required value="<?= e($_POST['name'] ?? $recipe['name']) ?>">
    </div>
    <div class="form-group">
      <label>Description</label>
      <textarea name="description" required><?= e($_POST['description'] ?? $recipe['description']) ?></textarea>
    </div>
    <div class="form-group">
      <label>Type</label>
      <select name="type">
        <?php foreach (['Asian','Chinese','Indian','Italian','French','Mexican','others'] as $t): ?>
          <option value="<?= e($t) ?>" <?= (($t === ($_POST['type'] ?? $recipe['type'])) ? 'selected' : '') ?>><?= e($t) ?></option>
        <?php endforeach; ?>
      </select>
    </div>
    <div class="form-group">
      <label>Cooking time (minutes)</label>
      <input type="number" name="cookingtime" min="1" max="1000" required value="<?= e($_POST['cookingtime'] ?? $recipe['cookingtime']) ?>">
    </div>
    <div class="form-group">
      <label>Ingredients (one per line)</label>
      <textarea name="ingredients" required><?= e($_POST['ingredients'] ?? $recipe['ingredients']) ?></textarea>
    </div>
    <div class="form-group">
      <label>Instructions</label>
      <textarea name="instructions" required><?= e($_POST['instructions'] ?? $recipe['instructions']) ?></textarea>
    </div>
    <div class="form-group">
      <label>Replace Image (optional)</label>
      <input type="file" name="image" accept="image/*">
    </div>
    <button class="btn" type="submit">Save changes</button>
  </form>
</div>

<?php require_once __DIR__ . '/inc/footer.php'; ?>
