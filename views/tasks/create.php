<?php
require_once __DIR__ . '/../../controllers/TaskController.php';
require_once __DIR__ . '/../../models/User.php';

TaskController::create();
$users = User::all();
$token = generate_csrf_token();
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>Create Task</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="container mt-5">
    <h2 class="mb-4">Create Task</h2>
    <?php if ($msg = flash('error')): ?>
        <div class="alert alert-danger"><?= $msg ?></div>
    <?php endif; ?>
    <form method="post">
        <input type="hidden" name="csrf_token" value="<?= $token ?>">
        <div class="mb-3">
            <label class="form-label">Title</label>
            <input type="text" name="title" class="form-control" required>
        </div>
        <div class="mb-3">
            <label class="form-label">Description</label>
            <textarea name="description" class="form-control" rows="4"></textarea>
        </div>
        <div class="mb-3">
            <label class="form-label">Status</label>
            <select name="status" class="form-select">
                <?php foreach (['pending','in_progress','completed','on_hold','cancelled'] as $s): ?>
                    <option value="<?= $s ?>"><?= $s ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="mb-3">
            <label class="form-label">Priority</label>
            <select name="priority" class="form-select">
                <?php foreach (['low','normal','high'] as $p): ?>
                    <option value="<?= $p ?>"><?= $p ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="mb-3">
            <label class="form-label">Assignee</label>
            <select name="assignee_id" class="form-select">
                <option value="">-- Select --</option>
                <?php foreach ($users as $u): ?>
                    <option value="<?= $u['id'] ?>"><?= htmlspecialchars($u['name']) ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <button type="submit" class="btn btn-primary">Create</button>
        <a href="index.php" class="btn btn-secondary">Cancel</a>
    </form>
</div>
</body>
</html>
