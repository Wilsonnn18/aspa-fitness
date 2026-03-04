<?php
// config/app.php
require_once __DIR__ . '/db.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!defined('BASE_URL')) {
    $scriptDir = str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME'] ?? ''));
    $basePath = preg_replace('#/(admin|auth|user)$#', '', $scriptDir);
    if ($basePath === '/' || $basePath === '.' || $basePath === '\\') {
        $basePath = '';
    }

    define('BASE_URL', rtrim($basePath, '/'));
}

function redirect(string $path): void
{
    header('Location: ' . BASE_URL . $path);
    exit;
}

function is_logged_in(): bool
{
    return !empty($_SESSION['loggedin']) && !empty($_SESSION['user_id']);
}

function require_login(?string $role = null): void
{
    if (!is_logged_in()) {
        redirect('/auth/login.php');
    }

    if ($role !== null && ($_SESSION['role'] ?? null) !== $role) {
        redirect('/auth/login.php');
    }
}

function user_has_workout_access(mysqli $conn, int $userId): bool
{
    $paymentStmt = $conn->prepare('SELECT id FROM payments WHERE user_id = ? AND payment_status = ? LIMIT 1');
    $paymentStatus = 'completed';
    $paymentStmt->bind_param('is', $userId, $paymentStatus);
    $paymentStmt->execute();
    return $paymentStmt->get_result()->num_rows > 0;
}
