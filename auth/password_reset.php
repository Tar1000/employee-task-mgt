<?php
require_once __DIR__ . '/../controllers/AuthController.php';

AuthController::resetPassword();
$csrf = generate_csrf_token();
$resetToken = sanitize($_GET['token'] ?? ($_POST['token'] ?? ''));
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>Set New Password</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="container mt-5" style="max-width: 400px;">
    <h2 class="mb-4">Reset Password</h2>
    <?php if ($msg = flash('error')): ?>
        <div class="alert alert-danger"><?= $msg ?></div>
    <?php elseif ($msg = flash('success')): ?>
        <div class="alert alert-success"><?= $msg ?></div>
    <?php endif; ?>
    <form method="post" action="">
        <input type="hidden" name="csrf_token" value="<?= $csrf ?>">
        <input type="hidden" name="token" value="<?= $resetToken ?>">
        <div class="mb-3">
            <label for="password" class="form-label">New Password</label>
            <input type="password" class="form-control" id="password" name="password" required>
        </div>
        <div class="mb-3">
            <label for="confirm_password" class="form-label">Confirm Password</label>
            <input type="password" class="form-control" id="confirm_password" name="confirm_password" required>
        </div>
        <button type="submit" class="btn btn-primary w-100">Reset Password</button>
    </form>
</div>
</body>
</html>
