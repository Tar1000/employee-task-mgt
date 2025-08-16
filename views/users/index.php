<?php
require_once __DIR__ . '/../../controllers/UserController.php';

if (is_post() && isset($_POST['delete_id'])) {
    UserController::destroy((int)$_POST['delete_id']);
}

$users = UserController::index();
$token = generate_csrf_token();
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>User Management</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="container mt-5">
    <h2 class="mb-4">Users</h2>
    <?php if ($msg = flash('success')): ?>
        <div class="alert alert-success"><?= $msg ?></div>
    <?php elseif ($msg = flash('error')): ?>
        <div class="alert alert-danger"><?= $msg ?></div>
    <?php endif; ?>
    <a href="create.php" class="btn btn-primary mb-3">Create User</a>
    <table class="table table-bordered">
        <thead>
        <tr>
            <th>ID</th>
            <th>Name</th>
            <th>Email</th>
            <th>Role</th>
            <th>Status</th>
            <th>Actions</th>
        </tr>
        </thead>
        <tbody>
        <?php foreach ($users as $user): ?>
            <tr>
                <td><?= $user['id'] ?></td>
                <td><?= htmlspecialchars($user['name']) ?></td>
                <td><?= htmlspecialchars($user['email']) ?></td>
                <td><?= htmlspecialchars($user['role']) ?></td>
                <td><?= htmlspecialchars($user['status']) ?></td>
                <td>
                    <a href="edit.php?id=<?= $user['id'] ?>" class="btn btn-sm btn-warning">Edit</a>
                    <form method="post" action="" style="display:inline-block">
                        <input type="hidden" name="csrf_token" value="<?= $token ?>">
                        <input type="hidden" name="delete_id" value="<?= $user['id'] ?>">
                        <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Delete this user?')">Delete</button>
                    </form>
                </td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
</div>
</body>
</html>
