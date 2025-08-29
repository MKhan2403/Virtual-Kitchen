<?php
declare(strict_types=1);

require_once __DIR__ . '/config.php';

function e(string $s): string {
    return htmlspecialchars($s, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}


function db(): PDO {
    global $pdo;
    return $pdo;
}


function isLoggedIn(): bool {
    return !empty($_SESSION['user_id']);
}

function currentUser(): ?array {
    if (!isLoggedIn()) return null;
    $pdo = db();
    $stmt = $pdo->prepare('SELECT uid, username, email FROM users WHERE uid = :uid');
    $stmt->execute(['uid' => $_SESSION['user_id']]);
    return $stmt->fetch() ?: null;
}

function csrf_token(): string {
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

function csrf_check(string $token): bool {
    if (empty($token) || empty($_SESSION['csrf_token'])) return false;
    return hash_equals($_SESSION['csrf_token'], $token);
}

function handle_image_upload(array $file): ?string {
    if ($file['error'] === UPLOAD_ERR_NO_FILE) return null;
    if ($file['error'] !== UPLOAD_ERR_OK) return null;

    $maxSize = 2 * 1024 * 1024; // 2MB
    if ($file['size'] > $maxSize) return null;

    $finfo = new finfo(FILEINFO_MIME_TYPE);
    $mime = $finfo->file($file['tmp_name']);
    $allowed = ['image/jpeg' => 'jpg', 'image/png' => 'png', 'image/gif' => 'gif'];
    if (!isset($allowed[$mime])) return null;

    $ext = $allowed[$mime];
    $base = bin2hex(random_bytes(8));
    $filename = $base . '.' . $ext;
    $destination = __DIR__ . '/../uploads/' . $filename;
    if (!move_uploaded_file($file['tmp_name'], $destination)) return null;

    return $filename;
}
