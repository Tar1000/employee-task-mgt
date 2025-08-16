<?php
/**
 * User model providing CRUD operations using PDO prepared statements.
 */

declare(strict_types=1);

require_once __DIR__ . '/../config/db.php';

class User
{
    /**
     * Retrieve all users.
     */
    public static function all(): array
    {
        $pdo = Database::getConnection();
        $stmt = $pdo->query('SELECT id, name, email, role, status, created_at FROM users ORDER BY id DESC');
        return $stmt->fetchAll();
    }

    /**
     * Find a user by id.
     */
    public static function find(int $id): ?array
    {
        $pdo = Database::getConnection();
        $stmt = $pdo->prepare('SELECT id, name, email, role, status, created_at FROM users WHERE id = :id');
        $stmt->execute(['id' => $id]);
        $user = $stmt->fetch();
        return $user !== false ? $user : null;
    }

    /**
     * Find a user by email.
     */
    public static function findByEmail(string $email): ?array
    {
        $pdo = Database::getConnection();
        $stmt = $pdo->prepare('SELECT id, name, email, role, status, created_at FROM users WHERE email = :email');
        $stmt->execute(['email' => $email]);
        $user = $stmt->fetch();
        return $user !== false ? $user : null;
    }

    /**
     * Create a new user and return the inserted id.
     */
    public static function create(array $data): int
    {
        $pdo = Database::getConnection();
        $stmt = $pdo->prepare('INSERT INTO users (name, email, password, role, status) VALUES (:name, :email, :password, :role, :status)');
        $stmt->execute([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => $data['password'],
            'role' => $data['role'],
            'status' => $data['status'],
        ]);
        return (int)$pdo->lastInsertId();
    }

    /**
     * Update an existing user.
     */
    public static function update(int $id, array $data): bool
    {
        $pdo = Database::getConnection();
        $fields = ['name = :name', 'email = :email', 'role = :role', 'status = :status'];
        $params = [
            'id' => $id,
            'name' => $data['name'],
            'email' => $data['email'],
            'role' => $data['role'],
            'status' => $data['status'],
        ];
        if (!empty($data['password'])) {
            $fields[] = 'password = :password';
            $params['password'] = $data['password'];
        }
        $sql = 'UPDATE users SET ' . implode(', ', $fields) . ' WHERE id = :id';
        $stmt = $pdo->prepare($sql);
        return $stmt->execute($params);
    }

    /**
     * Delete a user.
     */
    public static function delete(int $id): bool
    {
        $pdo = Database::getConnection();
        $stmt = $pdo->prepare('DELETE FROM users WHERE id = :id');
        return $stmt->execute(['id' => $id]);
    }
}
