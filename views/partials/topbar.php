<?php
require_once __DIR__ . '/../../controllers/NotificationController.php';
require_once __DIR__ . '/../../utils/helpers.php';

if (is_post() && isset($_POST['read_notification_id'])) {
    NotificationController::mark((int)$_POST['read_notification_id']);
}

$notifications = NotificationController::unread();
$token = generate_csrf_token();
?>
<nav class="navbar navbar-expand-lg navbar-light bg-light mb-4">
    <div class="container-fluid">
        <a class="navbar-brand" href="/">Task Manager</a>
        <div class="collapse navbar-collapse">
            <ul class="navbar-nav ms-auto">
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" id="notificationsDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                        Notifications (<?= count($notifications) ?>)
                    </a>
                    <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="notificationsDropdown">
                        <?php if (empty($notifications)): ?>
                            <li class="dropdown-item text-muted">No new notifications</li>
                        <?php else: ?>
                            <?php foreach ($notifications as $n): ?>
                                <li>
                                    <form method="post" class="dropdown-item d-flex justify-content-between align-items-start">
                                        <span class="me-2"><?= htmlspecialchars($n['message']) ?></span>
                                        <input type="hidden" name="csrf_token" value="<?= $token ?>">
                                        <input type="hidden" name="read_notification_id" value="<?= $n['id'] ?>">
                                        <button type="submit" class="btn btn-sm btn-link">Mark</button>
                                    </form>
                                </li>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </ul>
                </li>
            </ul>
        </div>
    </div>
</nav>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
