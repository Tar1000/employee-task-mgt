<?php
/**
 * Controller handling CRUD operations for tasks.
 */

declare(strict_types=1);

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/../models/Task.php';
require_once __DIR__ . '/../models/Comment.php';
require_once __DIR__ . '/../models/Attachment.php';
require_once __DIR__ . '/../models/Activity.php';
require_once __DIR__ . '/../models/Notification.php';
require_once __DIR__ . '/../utils/helpers.php';
require_once __DIR__ . '/../utils/pagination.php';
require_once __DIR__ . '/../utils/upload.php';
require_once __DIR__ . '/../middleware/csrf.php';
require_once __DIR__ . '/../middleware/auth.php';

class TaskController
{
    /**
     * Display a listing of tasks with filters and pagination.
     */
    public static function index(): array
    {
        require_login();
        $filters = [
            'status'    => sanitize($_GET['status'] ?? ''),
            'priority'  => sanitize($_GET['priority'] ?? ''),
            'assignee'  => isset($_GET['assignee']) ? (int)$_GET['assignee'] : 0,
            'keyword'   => sanitize($_GET['keyword'] ?? ''),
            'start_date'=> sanitize($_GET['start_date'] ?? ''),
            'end_date'  => sanitize($_GET['end_date'] ?? ''),
        ];
        $page = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;
        $total = Task::count($filters);
        $pagination = paginate($total, 10, $page);
        $tasks = Task::filter($filters, $pagination['limit'], $pagination['offset']);
        return ['tasks' => $tasks, 'pagination' => $pagination, 'filters' => $filters];
    }

    /**
     * Show create form or handle submission.
     */
    public static function create(): void
    {
        require_login();
        if (is_post()) {
            self::store();
        } else {
            generate_csrf_token();
        }
    }

    /**
     * Store a newly created task.
     */
    public static function store(): void
    {
        $token = $_POST['csrf_token'] ?? '';
        if (!verify_csrf_token($token)) {
            http_response_code(400);
            flash('error', 'Invalid CSRF token.');
            return;
        }
        $title = sanitize($_POST['title'] ?? '');
        $description = sanitize($_POST['description'] ?? '');
        $status = sanitize($_POST['status'] ?? 'pending');
        $priority = sanitize($_POST['priority'] ?? 'normal');
        $assignee = isset($_POST['assignee_id']) ? (int)$_POST['assignee_id'] : null;
        $user = current_user();
        if ($title === '') {
            flash('error', 'Title is required.');
            return;
        }
        $taskId = Task::create([
            'title'       => $title,
            'description' => $description,
            'status'      => $status,
            'priority'    => $priority,
            'creator_id'  => $user['id'],
            'assignee_id' => $assignee,
        ]);
        Activity::log($user['id'], $taskId, 'created');
        if ($assignee !== null) {
            Activity::log($user['id'], $taskId, 'assigned');
            Notification::create($assignee, 'You have been assigned to task "' . $title . '"');
        }
        flash('success', 'Task created successfully.');
        redirect('views/tasks/index.php');
    }

    /**
     * Show edit form or handle update.
     */
    public static function edit(int $id): ?array
    {
        require_login();
        if (is_post()) {
            self::update($id);
        } else {
            generate_csrf_token();
        }
        return Task::find($id);
    }

    /**
     * Update a task.
     */
    public static function update(int $id): void
    {
        $token = $_POST['csrf_token'] ?? '';
        if (!verify_csrf_token($token)) {
            http_response_code(400);
            flash('error', 'Invalid CSRF token.');
            return;
        }
        $existing = Task::find($id);
        if (!$existing) {
            flash('error', 'Task not found.');
            return;
        }
        $title = sanitize($_POST['title'] ?? '');
        $description = sanitize($_POST['description'] ?? '');
        $status = sanitize($_POST['status'] ?? $existing['status']);
        $priority = sanitize($_POST['priority'] ?? $existing['priority']);
        $assignee = isset($_POST['assignee_id']) ? (int)$_POST['assignee_id'] : null;
        if ($title === '') {
            flash('error', 'Title is required.');
            return;
        }
        Task::update($id, [
            'title'       => $title,
            'description' => $description,
            'status'      => $status,
            'priority'    => $priority,
            'assignee_id' => $assignee,
        ]);
        $userId = current_user()['id'];
        Activity::log($userId, $id, 'updated');
        if ($existing['status'] !== $status) {
            Activity::log($userId, $id, 'status_changed');
        }
        if ((int)($existing['assignee_id'] ?? 0) !== (int)$assignee) {
            Activity::log($userId, $id, 'assigned');
            if ($assignee !== null) {
                Notification::create($assignee, 'You have been assigned to task "' . $title . '"');
            }
        }
        flash('success', 'Task updated successfully.');
        redirect('views/tasks/index.php');
    }

    /**
     * Delete a task.
     */
    public static function destroy(int $id): void
    {
        require_login();
        $token = $_POST['csrf_token'] ?? '';
        if (!verify_csrf_token($token)) {
            http_response_code(400);
            flash('error', 'Invalid CSRF token.');
            return;
        }
        Task::delete($id);
        flash('success', 'Task deleted successfully.');
        redirect('views/tasks/index.php');
    }

    /**
     * Display task details with relations.
     */
    public static function view(int $id): array
    {
        require_login();
        $task = Task::find($id);
        $comments = Comment::forTask($id);
        $attachments = Attachment::forTask($id);
        $activities = Activity::forTask($id);
        generate_csrf_token();
        return compact('task', 'comments', 'attachments', 'activities');
    }

    /**
     * Handle attachment upload for a task.
     */
    public static function addAttachment(int $id): void
    {
        $token = $_POST['csrf_token'] ?? '';
        if (!verify_csrf_token($token)) {
            http_response_code(400);
            flash('error', 'Invalid CSRF token.');
            return;
        }
        $file = $_FILES['attachment'] ?? null;
        $path = $file ? handle_upload($file) : null;
        if ($path === null) {
            flash('error', 'Invalid file upload.');
            redirect('views/tasks/view.php?id=' . $id);
        }
        $user = current_user();
        Attachment::create([
            'task_id'    => $id,
            'file_path'  => $path,
            'uploaded_by'=> $user['id'],
        ]);
        Activity::log($user['id'], $id, 'attachment_added');
        flash('success', 'Attachment uploaded.');
        redirect('views/tasks/view.php?id=' . $id);
    }
}
