<?php
require_once __DIR__ . '/../../models/Task.php';
require_once __DIR__ . '/../../utils/helpers.php';
require_once __DIR__ . '/../../middleware/auth.php';

require_login();
$user = current_user();

$totalTasks = Task::count();
$myTasks = Task::count(['assignee' => $user['id'] ?? 0]);
$overdueTasks = Task::countOverdue();
$doneTasks = Task::count(['status' => 'completed']);

$statusDist = Task::statusDistribution();
$priorityDist = Task::priorityDistribution();
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body>
<div class="container mt-5">
    <h2 class="mb-4">Dashboard</h2>
    <div class="row g-3">
        <div class="col-md-3">
            <div class="card text-bg-primary text-center">
                <div class="card-body">
                    <h5 class="card-title">Total Tasks</h5>
                    <p class="display-6"><?= $totalTasks ?></p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-bg-info text-center">
                <div class="card-body">
                    <h5 class="card-title">My Tasks</h5>
                    <p class="display-6"><?= $myTasks ?></p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-bg-danger text-center">
                <div class="card-body">
                    <h5 class="card-title">Overdue</h5>
                    <p class="display-6"><?= $overdueTasks ?></p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-bg-success text-center">
                <div class="card-body">
                    <h5 class="card-title">Done</h5>
                    <p class="display-6"><?= $doneTasks ?></p>
                </div>
            </div>
        </div>
    </div>

    <div class="row mt-5">
        <div class="col-md-6">
            <canvas id="statusChart"></canvas>
        </div>
        <div class="col-md-6">
            <canvas id="priorityChart"></canvas>
        </div>
    </div>
</div>
<script>
const statusLabels = <?= json_encode(array_keys($statusDist)) ?>;
const statusData = <?= json_encode(array_values($statusDist)) ?>;
const priorityLabels = <?= json_encode(array_keys($priorityDist)) ?>;
const priorityData = <?= json_encode(array_values($priorityDist)) ?>;

if (statusLabels.length) {
    new Chart(document.getElementById('statusChart'), {
        type: 'pie',
        data: {labels: statusLabels, datasets: [{data: statusData, backgroundColor: ['#0d6efd','#198754','#ffc107','#6c757d','#dc3545']}]} 
    });
}

if (priorityLabels.length) {
    new Chart(document.getElementById('priorityChart'), {
        type: 'pie',
        data: {labels: priorityLabels, datasets: [{data: priorityData, backgroundColor: ['#0d6efd','#ffc107','#dc3545']}]} 
    });
}
</script>
</body>
</html>
