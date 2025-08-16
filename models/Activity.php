<?php
/**
 * Activity model to log user actions.
 */

declare(strict_types=1);

require_once __DIR__ . '/../config/db.php';

class Activity
{
    /**
     * Log an activity.
     */
    public static function log(int $userId, ?int $taskId, string $action): int
    {
        $pdo = Database::getConnection();
        $stmt = $pdo->prepare('INSERT INTO activities (user_id, task_id, action) VALUES (:user_id, :task_id, :action)');
        $stmt->execute([
            'user_id' => $userId,
            'task_id' => $taskId,
            'action'  => $action,
        ]);
        return (int)$pdo->lastInsertId();
    }

    /**
     * Retrieve activity log for a task.
     */
    public static function forTask(int $taskId): array
    {
        $pdo = Database::getConnection();
        $sql = 'SELECT a.*, u.name FROM activities a JOIN users u ON a.user_id = u.id WHERE a.task_id = :id ORDER BY a.created_at DESC';
        $stmt = $pdo->prepare($sql);
        $stmt->execute(['id' => $taskId]);
        return $stmt->fetchAll();
    }
}
