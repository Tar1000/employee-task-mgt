<?php
/**
 * Authentication controller handling login, logout and password resets.
 */

declare(strict_types=1);

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../utils/helpers.php';
require_once __DIR__ . '/../middleware/csrf.php';

class AuthController
{
    /**
     * Handle user login.
     */
    public static function login(): void
    {
        if (is_post()) {
            $token = $_POST['csrf_token'] ?? '';
            if (!verify_csrf_token($token)) {
                http_response_code(400);
                flash('error', 'Invalid CSRF token.');
                return;
            }

            $email = sanitize($_POST['email'] ?? '');
            $password = $_POST['password'] ?? '';

            if ($email === '' || $password === '') {
                flash('error', 'Email and password are required.');
                return;
            }

            $pdo = Database::getConnection();
            $stmt = $pdo->prepare('SELECT id, name, email, password, role FROM users WHERE email = :email');
            $stmt->execute(['email' => $email]);
            $user = $stmt->fetch();

            if (!$user || !password_verify($password, $user['password'])) {
                flash('error', 'Invalid credentials.');
                return;
            }

            unset($user['password']);
            $_SESSION['user'] = $user;
            flash('success', 'Logged in successfully.');
            redirect('index.php');
        } else {
            generate_csrf_token();
        }
    }

    /**
     * Log out the current user.
     */
    public static function logout(): void
    {
        $_SESSION = [];
        if (ini_get('session.use_cookies')) {
            $params = session_get_cookie_params();
            setcookie(session_name(), '', time() - 42000, $params['path'], $params['domain'], $params['secure'], $params['httponly']);
        }
        session_destroy();
        flash('success', 'Logged out successfully.');
        redirect('auth/login.php');
    }

    /**
     * Handle password reset request.
     */
    public static function requestPasswordReset(): void
    {
        if (is_post()) {
            $token = $_POST['csrf_token'] ?? '';
            if (!verify_csrf_token($token)) {
                http_response_code(400);
                flash('error', 'Invalid CSRF token.');
                return;
            }

            $email = sanitize($_POST['email'] ?? '');
            if ($email === '') {
                flash('error', 'Email is required.');
                return;
            }

            $pdo = Database::getConnection();
            $stmt = $pdo->prepare('SELECT id FROM users WHERE email = :email');
            $stmt->execute(['email' => $email]);
            $user = $stmt->fetch();

            if ($user) {
                $resetToken = bin2hex(random_bytes(32));
                $expires = date('Y-m-d H:i:s', time() + 3600);
                $stmt = $pdo->prepare('INSERT INTO password_resets (user_id, token, expires_at) VALUES (:uid, :token, :expires)');
                $stmt->execute([
                    'uid' => $user['id'],
                    'token' => $resetToken,
                    'expires' => $expires,
                ]);
                // In real app, email would be sent here.
            }

            flash('success', 'If that email exists, a reset link has been sent.');
        } else {
            generate_csrf_token();
        }
    }

    /**
     * Reset password using a valid token.
     */
    public static function resetPassword(): void
    {
        if (is_post()) {
            $csrf = $_POST['csrf_token'] ?? '';
            if (!verify_csrf_token($csrf)) {
                http_response_code(400);
                flash('error', 'Invalid CSRF token.');
                return;
            }

            $resetToken = sanitize($_POST['token'] ?? '');
            $password = $_POST['password'] ?? '';
            $confirm = $_POST['confirm_password'] ?? '';

            if ($password === '' || $confirm === '') {
                flash('error', 'All fields are required.');
                return;
            }
            if ($password !== $confirm) {
                flash('error', 'Passwords do not match.');
                return;
            }

            $pdo = Database::getConnection();
            $stmt = $pdo->prepare('SELECT user_id, expires_at FROM password_resets WHERE token = :token');
            $stmt->execute(['token' => $resetToken]);
            $row = $stmt->fetch();

            if (!$row || strtotime($row['expires_at']) < time()) {
                flash('error', 'Invalid or expired reset token.');
                return;
            }

            $hash = password_hash($password, PASSWORD_DEFAULT);

            $pdo->beginTransaction();
            try {
                $pdo->prepare('UPDATE users SET password = :password WHERE id = :id')
                    ->execute(['password' => $hash, 'id' => $row['user_id']]);
                $pdo->prepare('DELETE FROM password_resets WHERE token = :token')
                    ->execute(['token' => $resetToken]);
                $pdo->commit();
                flash('success', 'Password updated. Please log in.');
                redirect('auth/login.php');
            } catch (Throwable $e) {
                $pdo->rollBack();
                flash('error', 'Could not reset password.');
            }
        } else {
            generate_csrf_token();
        }
    }
}
