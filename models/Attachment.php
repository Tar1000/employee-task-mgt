<?php
/**
 * Attachment model handling file metadata.
 */

declare(strict_types=1);

require_once __DIR__ . '/../config/db.php';

class Attachment
{
    /**
     * Retrieve attachments for a task.
     */
    public static function forTask(int $taskId): array
    {
        $pdo = Database::getConnection();
        $sql = 'SELECT a.*, u.name FROM attachments a JOIN users u ON a.uploaded_by = u.id WHERE a.task_id = :id ORDER BY a.created_at DESC';
        $stmt = $pdo->prepare($sql);
        $stmt->execute(['id' => $taskId]);
        return $stmt->fetchAll();
    }

    /**
     * Create an attachment record.
     */
    public static function create(array $data): int
    {
        $pdo = Database::getConnection();
        $stmt = $pdo->prepare('INSERT INTO attachments (task_id, file_path, uploaded_by) VALUES (:task_id, :file_path, :uploaded_by)');
        $stmt->execute([
            'task_id'    => $data['task_id'],
            'file_path'  => $data['file_path'],
            'uploaded_by'=> $data['uploaded_by'],
        ]);
        return (int)$pdo->lastInsertId();
    }

    /**
     * Delete an attachment.
     */
    public static function delete(int $id): bool
    {
        $pdo = Database::getConnection();
        $stmt = $pdo->prepare('DELETE FROM attachments WHERE id = :id');
        return $stmt->execute(['id' => $id]);
    }
}
