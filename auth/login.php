<?php
require_once __DIR__ . '/../controllers/AuthController.php';

AuthController::login();
$token = generate_csrf_token();
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>Login</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="container mt-5" style="max-width: 400px;">
    <h2 class="mb-4">Login</h2>
    <?php if ($msg = flash('error')): ?>
        <div class="alert alert-danger"><?= $msg ?></div>
    <?php elseif ($msg = flash('success')): ?>
        <div class="alert alert-success"><?= $msg ?></div>
    <?php endif; ?>
    <form method="post" action="">
        <input type="hidden" name="csrf_token" value="<?= $token ?>">
        <div class="mb-3">
            <label for="email" class="form-label">Email</label>
            <input type="email" class="form-control" id="email" name="email" required>
        </div>
        <div class="mb-3">
            <label for="password" class="form-label">Password</label>
            <input type="password" class="form-control" id="password" name="password" required>
        </div>
        <button type="submit" class="btn btn-primary w-100">Login</button>
    </form>
    <div class="mt-3">
        <a href="password_reset_request.php">Forgot password?</a>
    </div>
</div>
</body>
</html>
