<?php
declare(strict_types=1);

session_start();
session_set_cookie_params([
    'httponly' => true,
    'secure' => isset($_SERVER['HTTPS']), // only over HTTPS
    'samesite' => 'Strict'
]);

$DB_HOST = '127.0.0.1';
$DB_NAME = 'vkitchen';
$DB_USER = 'root'; //input your username
$DB_PASS = ''; // input your DB password

$options = [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES => false,
];

try {
    $pdo = new PDO(
        "mysql:host={$DB_HOST};dbname={$DB_NAME};charset=utf8mb4",
        $DB_USER,
        $DB_PASS,
        $options
    );
} catch (PDOException $e) {
    die('Database connection failed. Please check configuration.');
}
