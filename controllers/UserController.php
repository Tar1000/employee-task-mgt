<?php
/**
 * Controller handling CRUD operations for users.
 */

declare(strict_types=1);

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/../models/User.php';
require_once __DIR__ . '/../utils/helpers.php';
require_once __DIR__ . '/../middleware/csrf.php';
require_once __DIR__ . '/../middleware/admin.php';

class UserController
{
    /**
     * Display a listing of the users.
     */
    public static function index(): array
    {
        require_admin();
        return User::all();
    }

    /**
     * Show the form for creating a new user or handle its submission.
     */
    public static function create(): void
    {
        require_admin();

        if (is_post()) {
            self::store();
        } else {
            generate_csrf_token();
        }
    }

    /**
     * Store a newly created user in storage.
     */
    public static function store(): void
    {
        $token = $_POST['csrf_token'] ?? '';
        if (!verify_csrf_token($token)) {
            http_response_code(400);
            flash('error', 'Invalid CSRF token.');
            return;
        }

        $name = sanitize($_POST['name'] ?? '');
        $email = sanitize($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';
        $role = sanitize($_POST['role'] ?? '');
        $status = sanitize($_POST['status'] ?? '');

        if ($name === '' || $email === '' || $password === '') {
            flash('error', 'Name, email and password are required.');
            return;
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            flash('error', 'Invalid email.');
            return;
        }

        if (User::findByEmail($email)) {
            flash('error', 'Email already exists.');
            return;
        }

        $validRoles = ['admin', 'employee'];
        if (!in_array($role, $validRoles, true)) {
            flash('error', 'Invalid role specified.');
            return;
        }

        $validStatus = ['active', 'inactive'];
        if (!in_array($status, $validStatus, true)) {
            flash('error', 'Invalid status specified.');
            return;
        }

        $hash = password_hash($password, PASSWORD_DEFAULT);

        User::create([
            'name' => $name,
            'email' => $email,
            'password' => $hash,
            'role' => $role,
            'status' => $status,
        ]);

        flash('success', 'User created successfully.');
        redirect('views/users/index.php');
    }

    /**
     * Show the form for editing the specified user or handle update.
     */
    public static function edit(int $id): ?array
    {
        require_admin();

        if (is_post()) {
            self::update($id);
        } else {
            generate_csrf_token();
        }

        return User::find($id);
    }

    /**
     * Update the specified user in storage.
     */
    public static function update(int $id): void
    {
        $token = $_POST['csrf_token'] ?? '';
        if (!verify_csrf_token($token)) {
            http_response_code(400);
            flash('error', 'Invalid CSRF token.');
            return;
        }

        $name = sanitize($_POST['name'] ?? '');
        $email = sanitize($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';
        $role = sanitize($_POST['role'] ?? '');
        $status = sanitize($_POST['status'] ?? '');

        if ($name === '' || $email === '') {
            flash('error', 'Name and email are required.');
            return;
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            flash('error', 'Invalid email.');
            return;
        }

        $existing = User::findByEmail($email);
        if ($existing && (int)$existing['id'] !== $id) {
            flash('error', 'Email already exists.');
            return;
        }

        $validRoles = ['admin', 'employee'];
        if (!in_array($role, $validRoles, true)) {
            flash('error', 'Invalid role specified.');
            return;
        }

        $validStatus = ['active', 'inactive'];
        if (!in_array($status, $validStatus, true)) {
            flash('error', 'Invalid status specified.');
            return;
        }

        $hash = $password !== '' ? password_hash($password, PASSWORD_DEFAULT) : null;

        User::update($id, [
            'name' => $name,
            'email' => $email,
            'password' => $hash,
            'role' => $role,
            'status' => $status,
        ]);

        flash('success', 'User updated successfully.');
        redirect('views/users/index.php');
    }

    /**
     * Remove the specified user from storage.
     */
    public static function destroy(int $id): void
    {
        require_admin();

        $token = $_POST['csrf_token'] ?? '';
        if (!verify_csrf_token($token)) {
            http_response_code(400);
            flash('error', 'Invalid CSRF token.');
            return;
        }

        User::delete($id);
        flash('success', 'User deleted successfully.');
        redirect('views/users/index.php');
    }
}
