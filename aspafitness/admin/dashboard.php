<?php
// admin/dashboard.php
require_once __DIR__ . '/../config/app.php';
require_login('admin');
?>
<?php include __DIR__ . '/../includes/header.php'; ?>
<h2>Admin Dashboard</h2>
<ul>
    <li><a href="<?= BASE_URL ?>/admin/manage_users.php">Manage Users</a></li>
    <li><a href="<?= BASE_URL ?>/admin/manage_plans.php">Manage Membership Plans</a></li>
    <li><a href="<?= BASE_URL ?>/admin/manage_workouts.php">Manage Workout Plans</a></li>
    <li><a href="<?= BASE_URL ?>/admin/payments.php">View Payments</a></li>
</ul>
<?php include __DIR__ . '/../includes/footer.php'; ?>
