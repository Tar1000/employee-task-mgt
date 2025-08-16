<?php
/**
 * General helper functions.
 */

declare(strict_types=1);

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

/**
 * Sanitize a string for safe output.
 */
function sanitize(string $value): string
{
    return htmlspecialchars(trim($value), ENT_QUOTES, 'UTF-8');
}

/**
 * Redirect to a given path within the application.
 */
function redirect(string $path): void
{
    header('Location: ' . BASE_URL . '/' . ltrim($path, '/'));
    exit;
}

/**
 * Flash messaging helper. Stores message when provided, retrieves and clears when not.
 */
function flash(string $key, ?string $message = null): ?string
{
    if ($message === null) {
        $msg = $_SESSION['flash'][$key] ?? null;
        if ($msg !== null) {
            unset($_SESSION['flash'][$key]);
        }
        return $msg;
    }

    $_SESSION['flash'][$key] = $message;
    return null;
}

/**
 * Determine if the current request is a POST.
 */
function is_post(): bool
{
    return ($_SERVER['REQUEST_METHOD'] ?? 'GET') === 'POST';
}

/**
 * Retrieve the currently authenticated user, if any.
 */
function current_user(): ?array
{
    return $_SESSION['user'] ?? null;
}
