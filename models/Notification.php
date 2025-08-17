<?php
/**
 * Notification model to handle in-app alerts.
 */

declare(strict_types=1);

require_once __DIR__ . '/../config/db.php';

class Notification
{
    /**
     * Create a notification.
     */
    public static function create(int $userId, string $message): int
    {
        $pdo = Database::getConnection();
        $stmt = $pdo->prepare('INSERT INTO notifications (user_id, message) VALUES (:user_id, :message)');
        $stmt->execute([
            'user_id' => $userId,
            'message' => $message,
        ]);
        return (int)$pdo->lastInsertId();
    }

    /**
     * Retrieve notifications for a user.
     */
    public static function forUser(int $userId): array
    {
        $pdo = Database::getConnection();
        $sql = 'SELECT * FROM notifications WHERE user_id = :id ORDER BY created_at DESC';
        $stmt = $pdo->prepare($sql);
        $stmt->execute(['id' => $userId]);
        return $stmt->fetchAll();
    }

    /**
     * Retrieve unread notifications for a user.
     */
    public static function unreadForUser(int $userId): array
    {
        $pdo = Database::getConnection();
        $sql = 'SELECT * FROM notifications WHERE user_id = :id AND is_read = 0 ORDER BY created_at DESC';
        $stmt = $pdo->prepare($sql);
        $stmt->execute(['id' => $userId]);
        return $stmt->fetchAll();
    }

    /**
     * Mark a notification as read.
     */
    public static function markAsRead(int $id): bool
    {
        $pdo = Database::getConnection();
        $stmt = $pdo->prepare('UPDATE notifications SET is_read = 1 WHERE id = :id');
        return $stmt->execute(['id' => $id]);
    }

    /**
     * Delete a notification.
     */
    public static function delete(int $id): bool
    {
        $pdo = Database::getConnection();
        $stmt = $pdo->prepare('DELETE FROM notifications WHERE id = :id');
        return $stmt->execute(['id' => $id]);
    }
}
