<?php
/**
 * Comment model with CRUD operations.
 */

declare(strict_types=1);

require_once __DIR__ . '/../config/db.php';

class Comment
{
    /**
     * Retrieve comments for a task.
     */
    public static function forTask(int $taskId): array
    {
        $pdo = Database::getConnection();
        $sql = 'SELECT c.*, u.name FROM comments c JOIN users u ON c.user_id = u.id WHERE c.task_id = :id ORDER BY c.created_at ASC';
        $stmt = $pdo->prepare($sql);
        $stmt->execute(['id' => $taskId]);
        return $stmt->fetchAll();
    }

    /**
     * Create a new comment.
     */
    public static function create(array $data): int
    {
        $pdo = Database::getConnection();
        $stmt = $pdo->prepare('INSERT INTO comments (task_id, user_id, content) VALUES (:task_id, :user_id, :content)');
        $stmt->execute([
            'task_id' => $data['task_id'],
            'user_id' => $data['user_id'],
            'content' => $data['content'],
        ]);
        return (int)$pdo->lastInsertId();
    }

    /**
     * Delete a comment.
     */
    public static function delete(int $id): bool
    {
        $pdo = Database::getConnection();
        $stmt = $pdo->prepare('DELETE FROM comments WHERE id = :id');
        return $stmt->execute(['id' => $id]);
    }
}
