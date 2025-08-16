<?php
/**
 * Controller to handle task comments.
 */

declare(strict_types=1);

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/../models/Comment.php';
require_once __DIR__ . '/../models/Task.php';
require_once __DIR__ . '/../models/Activity.php';
require_once __DIR__ . '/../models/Notification.php';
require_once __DIR__ . '/../utils/helpers.php';
require_once __DIR__ . '/../middleware/csrf.php';
require_once __DIR__ . '/../middleware/auth.php';

class CommentController
{
    /**
     * Store a new comment and notify participants.
     */
    public static function store(int $taskId): void
    {
        require_login();
        $token = $_POST['csrf_token'] ?? '';
        if (!verify_csrf_token($token)) {
            http_response_code(400);
            flash('error', 'Invalid CSRF token.');
            return;
        }
        $content = sanitize($_POST['content'] ?? '');
        if ($content === '') {
            flash('error', 'Comment cannot be empty.');
            redirect('views/tasks/view.php?id=' . $taskId);
        }
        $user = current_user();
        Comment::create([
            'task_id' => $taskId,
            'user_id' => $user['id'],
            'content' => $content,
        ]);
        Activity::log($user['id'], $taskId, 'commented');
        $task = Task::find($taskId);
        if ($task) {
            $message = 'New comment on task "' . $task['title'] . '"';
            if (!empty($task['assignee_id']) && (int)$task['assignee_id'] !== $user['id']) {
                Notification::create((int)$task['assignee_id'], $message);
            }
            if ((int)$task['creator_id'] !== $user['id'] && (int)$task['creator_id'] !== (int)$task['assignee_id']) {
                Notification::create((int)$task['creator_id'], $message);
            }
        }
        flash('success', 'Comment added.');
        redirect('views/tasks/view.php?id=' . $taskId);
    }
}
