<?php
// user/dashboard.php
require_once __DIR__ . '/../config/app.php';
require_login('user');
?>
<?php include __DIR__ . '/../includes/header.php'; ?>
<h2>Welcome, <?= htmlspecialchars($_SESSION['name']) ?></h2>
<p>This is your user dashboard. Use the navigation links below:</p>
<ul>
    <li><a href="<?= BASE_URL ?>/user/view_plans.php">View Membership Plans</a></li>
    <li><a href="<?= BASE_URL ?>/user/subscription.php">My Subscription</a></li>
    <li><a href="<?= BASE_URL ?>/user/workouts.php">Workout Plans</a></li>
</ul>
<?php include __DIR__ . '/../includes/footer.php'; ?>
