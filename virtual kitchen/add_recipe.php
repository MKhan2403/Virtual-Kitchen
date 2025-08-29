<?php
require_once __DIR__ . '/inc/header.php';
if (!isLoggedIn()) {
    header('Location: /login.php');
    exit;
}
$pdo = db();
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
        $stmt = $pdo->prepare('INSERT INTO recipes (name, description, type, cookingtime, ingredients, instructions, image, uid) VALUES (:name,:desc,:type,:ct,:ing,:ins,:img,:uid)');
        $stmt->execute([
            'name' => $name,
            'desc' => $description,
            'type' => $type,
            'ct' => $cookingtime,
            'ing' => $ingredients,
            'ins' => $instructions,
            'img' => $imageFilename,
            'uid' => $_SESSION['user_id']
        ]);
        header('Location: /index.php');
        exit;
    }
}
?>
<div class="card">
  <h2>Add Recipe</h2>
  <?php if ($errors) foreach ($errors as $err) echo '<p class="small">'.e($err).'</p>'; ?>
  <form method="post" action="add_recipe.php" enctype="multipart/form-data">
    <input type="hidden" name="csrf" value="<?= e(csrf_token()) ?>">
    <div class="form-group">
      <label>Recipe name</label>
      <input type="text" name="name" required value="<?= e($_POST['name'] ?? '') ?>">
    </div>
    <div class="form-group">
      <label>Description</label>
      <textarea name="description" required><?= e($_POST['description'] ?? '') ?></textarea>
    </div>
    <div class="form-group">
      <label>Type</label>
      <select name="type">
        <option value="Asian">Asian</option>
        <option value="Chinese">Chinese</option>
        <option value="Indian">Indian</option>
        <option value="Italian">Italian</option>
        <option value="French">French</option>
        <option value="Mexican">Mexican</option>
        <option value="others">Others</option>
      </select>
    </div>
    <div class="form-group">
      <label>Cooking time (minutes)</label>
      <input type="number" name="cookingtime" min="1" max="1000" required value="<?= e($_POST['cookingtime'] ?? '') ?>">
    </div>
    <div class="form-group">
      <label>Ingredients (one per line)</label>
      <textarea name="ingredients" required><?= e($_POST['ingredients'] ?? '') ?></textarea>
    </div>
    <div class="form-group">
      <label>Instructions</label>
      <textarea name="instructions" required><?= e($_POST['instructions'] ?? '') ?></textarea>
    </div>
    <div class="form-group">
      <label>Image (optional, jpg/png/gif, max 2MB)</label>
      <input type="file" name="image" accept="image/*">
    </div>
    <button class="btn" type="submit">Add Recipe</button>
  </form>
</div>

<?php require_once __DIR__ . '/inc/footer.php'; ?>
