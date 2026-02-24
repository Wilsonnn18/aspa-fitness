<?php
// user/workouts.php
require_once __DIR__ . '/../config/app.php';
require_login('user');

$plans = [];
$res = $conn->query('SELECT * FROM workout_plans');
if ($res) {
    while ($row = $res->fetch_assoc()) {
        $plans[] = $row;
    }
}
?>
<?php include __DIR__ . '/../includes/header.php'; ?>
<h2>Workout Plans</h2>
<?php if (empty($plans)): ?>
    <p>No workout plans available.</p>
<?php else: ?>
    <?php foreach ($plans as $p): ?>
        <div class="card mb-2">
            <div class="card-body">
                <h5 class="card-title"><?= htmlspecialchars($p['title']) ?></h5>
                <p class="card-text"><?= nl2br(htmlspecialchars($p['description'])) ?></p>
                <p><strong>Level:</strong> <?= htmlspecialchars($p['level']) ?></p>
            </div>
        </div>
    <?php endforeach; ?>
<?php endif; ?>
<?php include __DIR__ . '/../includes/footer.php'; ?>
