<?php
/**
 * Authentication middleware.
 */

declare(strict_types=1);

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

/**
 * Ensure the user is logged in, otherwise redirect to login page.
 */
function require_login(): void
{
    if (empty($_SESSION['user'])) {
        header('Location: ' . BASE_URL . '/login.php');
        exit;
    }
}
