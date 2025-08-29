<?php
require_once __DIR__ . '/functions.php';
$user = currentUser();
?>
header("Content-Security-Policy: default-src 'self'; img-src 'self' https: data:; style-src 'self' 'unsafe-inline';");
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <title>Virtual Kitchen</title>
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <link rel="stylesheet" href="/css/styles.css">
</head>
<body>
  <header class="site-header">
    <div class="container header-inner">
      <h1 class="site-title"><a href="/index.php">Virtual Kitchen</a></h1>
      <nav class="site-nav">
        <a href="/index.php">Home</a>
        <?php if ($user): ?>
          <a href="/add_recipe.php">Add recipe</a>
          <span class="muted">Hello, <?= e($user['username']) ?></span>
          <a href="/logout.php">Logout</a>
        <?php else: ?>
          <a href="/register.php">Register</a>
          <a href="/login.php">Login</a>
        <?php endif; ?>
      </nav>
    </div>
  </header>
  <main class="container">
