<?php
require_once __DIR__ . '/../../controllers/TaskController.php';
require_once __DIR__ . '/../../models/User.php';

if (is_post() && isset($_POST['delete_id'])) {
    TaskController::destroy((int)$_POST['delete_id']);
}

$data = TaskController::index();
$tasks = $data['tasks'];
$pagination = $data['pagination'];
$filters = $data['filters'];
$users = User::all();
$token = generate_csrf_token();
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>Tasks</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<?php include __DIR__ . '/../partials/topbar.php'; ?>
<div class="container mt-5">
    <h2 class="mb-4">Tasks</h2>
    <?php if ($msg = flash('success')): ?>
        <div class="alert alert-success"><?= $msg ?></div>
    <?php elseif ($msg = flash('error')): ?>
        <div class="alert alert-danger"><?= $msg ?></div>
    <?php endif; ?>
    <a href="create.php" class="btn btn-primary mb-3">Create Task</a>
    <form method="get" class="row g-2 mb-3">
        <div class="col">
            <select name="status" class="form-select">
                <option value="">Status</option>
                <?php foreach (['pending','in_progress','completed','on_hold','cancelled'] as $status): ?>
                    <option value="<?= $status ?>"<?= $filters['status'] === $status ? ' selected' : '' ?>><?= $status ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="col">
            <select name="priority" class="form-select">
                <option value="">Priority</option>
                <?php foreach (['low','normal','high'] as $priority): ?>
                    <option value="<?= $priority ?>"<?= $filters['priority'] === $priority ? ' selected' : '' ?>><?= $priority ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="col">
            <select name="assignee" class="form-select">
                <option value="">Assignee</option>
                <?php foreach ($users as $u): ?>
                    <option value="<?= $u['id'] ?>"<?= (int)$filters['assignee'] === (int)$u['id'] ? ' selected' : '' ?>><?= htmlspecialchars($u['name']) ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="col">
            <input type="date" name="start_date" class="form-control" value="<?= htmlspecialchars($filters['start_date']) ?>" placeholder="Start">
        </div>
        <div class="col">
            <input type="date" name="end_date" class="form-control" value="<?= htmlspecialchars($filters['end_date']) ?>" placeholder="End">
        </div>
        <div class="col">
            <input type="text" name="keyword" class="form-control" value="<?= htmlspecialchars($filters['keyword']) ?>" placeholder="Keyword">
        </div>
        <div class="col">
            <button class="btn btn-secondary" type="submit">Filter</button>
        </div>
    </form>
    <table class="table table-bordered">
        <thead>
        <tr>
            <th>ID</th>
            <th>Title</th>
            <th>Status</th>
            <th>Priority</th>
            <th>Assignee</th>
            <th>Actions</th>
        </tr>
        </thead>
        <tbody>
        <?php foreach ($tasks as $task): ?>
            <tr>
                <td><?= $task['id'] ?></td>
                <td><?= htmlspecialchars($task['title']) ?></td>
                <td><?= htmlspecialchars($task['status']) ?></td>
                <td><?= htmlspecialchars($task['priority']) ?></td>
                <td><?= htmlspecialchars($task['assignee_name'] ?? '') ?></td>
                <td>
                    <a href="view.php?id=<?= $task['id'] ?>" class="btn btn-sm btn-info">View</a>
                    <a href="edit.php?id=<?= $task['id'] ?>" class="btn btn-sm btn-warning">Edit</a>
                    <form method="post" action="" style="display:inline-block">
                        <input type="hidden" name="csrf_token" value="<?= $token ?>">
                        <input type="hidden" name="delete_id" value="<?= $task['id'] ?>">
                        <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Delete this task?')">Delete</button>
                    </form>
                </td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
    <?php if ($pagination['total_pages'] > 1): ?>
        <nav>
            <ul class="pagination">
                <?php for ($i = 1; $i <= $pagination['total_pages']; $i++): ?>
                    <li class="page-item<?= $i === $pagination['current_page'] ? ' active' : '' ?>">
                        <a class="page-link" href="?<?= http_build_query(array_merge($filters, ['page' => $i])) ?>"><?= $i ?></a>
                    </li>
                <?php endfor; ?>
            </ul>
        </nav>
    <?php endif; ?>
</div>
</body>
</html>
