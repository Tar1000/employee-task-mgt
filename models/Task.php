<?php
/**
 * Task model providing CRUD operations and filtering support.
 */

declare(strict_types=1);

require_once __DIR__ . '/../config/db.php';

class Task
{
    /**
     * Retrieve tasks with optional filters and pagination.
     *
     * @param array $filters [status, priority, assignee, keyword, start_date, end_date]
     * @param int   $limit   Number of records.
     * @param int   $offset  Offset for pagination.
     */
    public static function filter(array $filters = [], int $limit = 10, int $offset = 0): array
    {
        $pdo = Database::getConnection();
        $sql = 'SELECT t.*, u.name AS assignee_name FROM tasks t LEFT JOIN users u ON t.assignee_id = u.id';
        $conditions = [];
        $params = [];

        if (!empty($filters['status'])) {
            $conditions[] = 't.status = :status';
            $params['status'] = $filters['status'];
        }
        if (!empty($filters['priority'])) {
            $conditions[] = 't.priority = :priority';
            $params['priority'] = $filters['priority'];
        }
        if (!empty($filters['assignee'])) {
            $conditions[] = 't.assignee_id = :assignee';
            $params['assignee'] = $filters['assignee'];
        }
        if (!empty($filters['keyword'])) {
            $conditions[] = '(t.title LIKE :kw OR t.description LIKE :kw)';
            $params['kw'] = '%' . $filters['keyword'] . '%';
        }
        if (!empty($filters['start_date'])) {
            $conditions[] = 't.created_at >= :start_date';
            $params['start_date'] = $filters['start_date'];
        }
        if (!empty($filters['end_date'])) {
            $conditions[] = 't.created_at <= :end_date';
            $params['end_date'] = $filters['end_date'];
        }

        if ($conditions) {
            $sql .= ' WHERE ' . implode(' AND ', $conditions);
        }
        $sql .= ' ORDER BY t.created_at DESC LIMIT :limit OFFSET :offset';

        $stmt = $pdo->prepare($sql);
        foreach ($params as $key => $value) {
            $stmt->bindValue(':' . $key, $value);
        }
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll();
    }

    /**
     * Count tasks matching filters.
     */
    public static function count(array $filters = []): int
    {
        $pdo = Database::getConnection();
        $sql = 'SELECT COUNT(*) FROM tasks t';
        $conditions = [];
        $params = [];

        if (!empty($filters['status'])) {
            $conditions[] = 't.status = :status';
            $params['status'] = $filters['status'];
        }
        if (!empty($filters['priority'])) {
            $conditions[] = 't.priority = :priority';
            $params['priority'] = $filters['priority'];
        }
        if (!empty($filters['assignee'])) {
            $conditions[] = 't.assignee_id = :assignee';
            $params['assignee'] = $filters['assignee'];
        }
        if (!empty($filters['keyword'])) {
            $conditions[] = '(t.title LIKE :kw OR t.description LIKE :kw)';
            $params['kw'] = '%' . $filters['keyword'] . '%';
        }
        if (!empty($filters['start_date'])) {
            $conditions[] = 't.created_at >= :start_date';
            $params['start_date'] = $filters['start_date'];
        }
        if (!empty($filters['end_date'])) {
            $conditions[] = 't.created_at <= :end_date';
            $params['end_date'] = $filters['end_date'];
        }

        if ($conditions) {
            $sql .= ' WHERE ' . implode(' AND ', $conditions);
        }

        $stmt = $pdo->prepare($sql);
        foreach ($params as $key => $value) {
            $stmt->bindValue(':' . $key, $value);
        }
        $stmt->execute();

        return (int)$stmt->fetchColumn();
    }

    /**
     * Find a task by id.
     */
    public static function find(int $id): ?array
    {
        $pdo = Database::getConnection();
        $sql = 'SELECT t.*, u.name AS assignee_name FROM tasks t LEFT JOIN users u ON t.assignee_id = u.id WHERE t.id = :id';
        $stmt = $pdo->prepare($sql);
        $stmt->execute(['id' => $id]);
        $task = $stmt->fetch();
        return $task !== false ? $task : null;
    }

    /**
     * Create a new task.
     */
    public static function create(array $data): int
    {
        $pdo = Database::getConnection();
        $stmt = $pdo->prepare('INSERT INTO tasks (title, description, status, priority, creator_id, assignee_id) VALUES (:title, :description, :status, :priority, :creator_id, :assignee_id)');
        $stmt->execute([
            'title'       => $data['title'],
            'description' => $data['description'],
            'status'      => $data['status'],
            'priority'    => $data['priority'],
            'creator_id'  => $data['creator_id'],
            'assignee_id' => $data['assignee_id'],
        ]);
        return (int)$pdo->lastInsertId();
    }

    /**
     * Update an existing task.
     */
    public static function update(int $id, array $data): bool
    {
        $pdo = Database::getConnection();
        $stmt = $pdo->prepare('UPDATE tasks SET title = :title, description = :description, status = :status, priority = :priority, assignee_id = :assignee_id WHERE id = :id');
        return $stmt->execute([
            'id'          => $id,
            'title'       => $data['title'],
            'description' => $data['description'],
            'status'      => $data['status'],
            'priority'    => $data['priority'],
            'assignee_id' => $data['assignee_id'],
        ]);
    }

    /**
     * Delete a task.
     */
    public static function delete(int $id): bool
    {
        $pdo = Database::getConnection();
        $stmt = $pdo->prepare('DELETE FROM tasks WHERE id = :id');
        return $stmt->execute(['id' => $id]);
    }

    /**
     * Count overdue tasks where due_date is in the past and status is not completed.
     * If the tasks table lacks a due_date column, returns 0 to avoid SQL errors.
     */
    public static function countOverdue(?int $assignee = null): int
    {
        $pdo = Database::getConnection();
        $col = $pdo->prepare("SHOW COLUMNS FROM tasks LIKE 'due_date'");
        $col->execute();
        if ($col->fetch() === false) {
            return 0;
        }

        $sql = "SELECT COUNT(*) FROM tasks WHERE status != 'completed' AND due_date < CURDATE()";
        $params = [];
        if ($assignee !== null) {
            $sql .= " AND assignee_id = :assignee";
            $params['assignee'] = $assignee;
        }
        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
        return (int)$stmt->fetchColumn();
    }

    /**
     * Return an associative array of status => count.
     */
    public static function statusDistribution(): array
    {
        $pdo = Database::getConnection();
        $stmt = $pdo->query("SELECT status, COUNT(*) AS count FROM tasks GROUP BY status");
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $data = [];
        foreach ($rows as $row) {
            $data[$row['status']] = (int)$row['count'];
        }
        return $data;
    }

    /**
     * Return an associative array of priority => count.
     * If the tasks table lacks a priority column, an empty array is returned.
     */
    public static function priorityDistribution(): array
    {
        $pdo = Database::getConnection();
        $col = $pdo->prepare("SHOW COLUMNS FROM tasks LIKE 'priority'");
        $col->execute();
        if ($col->fetch() === false) {
            return [];
        }
        $stmt = $pdo->query("SELECT priority, COUNT(*) AS count FROM tasks GROUP BY priority");
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $data = [];
        foreach ($rows as $row) {
            $data[$row['priority']] = (int)$row['count'];
        }
        return $data;
    }
}
