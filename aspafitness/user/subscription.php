<?php
// user/subscription.php
require_once __DIR__ . '/../config/app.php';
require_login('user');

$userId = (int)$_SESSION['user_id'];
$stmt = $conn->prepare('SELECT s.*, m.plan_name FROM subscriptions s JOIN membership_plans m ON s.plan_id = m.id WHERE s.user_id = ? ORDER BY s.start_date DESC LIMIT 1');
$stmt->bind_param('i', $userId);
$stmt->execute();
$sub = $stmt->get_result()->fetch_assoc();
?>
<?php include __DIR__ . '/../includes/header.php'; ?>
<h2>My Subscription</h2>
<?php if ($sub): ?>
    <p>Plan: <?= htmlspecialchars($sub['plan_name']) ?></p>
    <p>Start: <?= $sub['start_date'] ?></p>
    <p>End: <?= $sub['end_date'] ?></p>
    <p>Status: <?= $sub['status'] ?></p>
<?php else: ?>
    <p>You have no active subscription. <a href="<?= BASE_URL ?>/user/view_plans.php">Purchase a plan</a>.</p>
<?php endif; ?>
<?php include __DIR__ . '/../includes/footer.php'; ?>
