<?php
require_once __DIR__ . '/../../controllers/TaskController.php';
require_once __DIR__ . '/../../controllers/CommentController.php';

$id = (int)($_GET['id'] ?? 0);
if (is_post()) {
    if (isset($_POST['content'])) {
        CommentController::store($id);
    } elseif (isset($_FILES['attachment'])) {
        TaskController::addAttachment($id);
    }
}

$data = TaskController::view($id);
$task = $data['task'];
$comments = $data['comments'];
$attachments = $data['attachments'];
$activities = $data['activities'];
$token = $_SESSION['csrf_token'] ?? '';
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>View Task</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<?php include __DIR__ . '/../partials/topbar.php'; ?>
<div class="container mt-5">
    <h2 class="mb-4">Task Details</h2>
    <?php if ($msg = flash('success')): ?>
        <div class="alert alert-success"><?= $msg ?></div>
    <?php elseif ($msg = flash('error')): ?>
        <div class="alert alert-danger"><?= $msg ?></div>
    <?php endif; ?>
    <div class="mb-4">
        <h4><?= htmlspecialchars($task['title'] ?? '') ?></h4>
        <p><?= nl2br(htmlspecialchars($task['description'] ?? '')) ?></p>
        <p><strong>Status:</strong> <?= htmlspecialchars($task['status'] ?? '') ?></p>
        <p><strong>Priority:</strong> <?= htmlspecialchars($task['priority'] ?? '') ?></p>
        <p><strong>Assignee:</strong> <?= htmlspecialchars($task['assignee_name'] ?? '') ?></p>
    </div>
    <div class="row">
        <div class="col-md-6">
            <h5>Comments</h5>
            <ul class="list-group mb-3">
                <?php foreach ($comments as $c): ?>
                    <li class="list-group-item"><strong><?= htmlspecialchars($c['name']) ?>:</strong> <?= nl2br(htmlspecialchars($c['content'])) ?> <small class="text-muted float-end"><?= $c['created_at'] ?></small></li>
                <?php endforeach; ?>
            </ul>
            <form method="post">
                <input type="hidden" name="csrf_token" value="<?= $token ?>">
                <div class="mb-3">
                    <textarea name="content" class="form-control" rows="3" placeholder="Add comment"></textarea>
                </div>
                <button class="btn btn-primary" type="submit">Comment</button>
            </form>
        </div>
        <div class="col-md-6">
            <h5>Attachments</h5>
            <ul class="list-group mb-3">
                <?php foreach ($attachments as $a): ?>
                    <li class="list-group-item"><a href="<?= $a['file_path'] ?>" target="_blank">Attachment <?= $a['id'] ?></a> <small class="text-muted">by <?= htmlspecialchars($a['name']) ?></small></li>
                <?php endforeach; ?>
            </ul>
            <form method="post" enctype="multipart/form-data">
                <input type="hidden" name="csrf_token" value="<?= $token ?>">
                <div class="mb-3">
                    <input type="file" name="attachment" class="form-control" required>
                </div>
                <button class="btn btn-secondary" type="submit">Upload</button>
            </form>
        </div>
    </div>
    <div class="mt-4">
        <h5>Activity</h5>
        <ul class="list-group">
            <?php foreach ($activities as $act): ?>
                <li class="list-group-item"><strong><?= htmlspecialchars($act['name']) ?></strong> <?= htmlspecialchars($act['action']) ?> <small class="text-muted float-end"><?= $act['created_at'] ?></small></li>
            <?php endforeach; ?>
        </ul>
    </div>
    <a href="index.php" class="btn btn-link mt-3">Back to list</a>
</div>
</body>
</html>
