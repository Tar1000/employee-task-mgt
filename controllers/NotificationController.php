<?php
/**
 * Controller for user notifications.
 */

declare(strict_types=1);

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/../models/Notification.php';
require_once __DIR__ . '/../utils/helpers.php';
require_once __DIR__ . '/../middleware/auth.php';
require_once __DIR__ . '/../middleware/csrf.php';

class NotificationController
{
    /**
     * List notifications for current user.
     */
    public static function index(): array
    {
        require_login();
        $user = current_user();
        return Notification::forUser($user['id']);
    }

    /**
     * Fetch unread notifications for current user.
     */
    public static function unread(): array
    {
        require_login();
        $user = current_user();
        return Notification::unreadForUser($user['id']);
    }

    /**
     * Mark a notification as read.
     */
    public static function mark(int $id): bool
    {
        require_login();
        $token = $_POST['csrf_token'] ?? '';
        if (!verify_csrf_token($token)) {
            http_response_code(400);
            flash('error', 'Invalid CSRF token.');
            return false;
        }
        return Notification::markAsRead($id);
    }
}
