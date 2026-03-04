<?php
// user/subscription.php
require_once __DIR__ . '/../config/app.php';
require_login('user');

$userId = (int)$_SESSION['user_id'];
$stmt = $conn->prepare('SELECT p.payment_date, p.payment_status, m.plan_name, m.duration FROM payments p JOIN membership_plans m ON p.plan_id = m.id WHERE p.user_id = ? AND p.payment_status = "completed" ORDER BY p.payment_date DESC LIMIT 1');
$stmt->bind_param('i', $userId);
$stmt->execute();
$membership = $stmt->get_result()->fetch_assoc();
?>
<?php include __DIR__ . '/../includes/header.php'; ?>

<section class="page-section">
    <div class="page-header">
        <div>
            <h2>My Membership</h2>
            <p class="page-header-sub">Your latest plan purchase details</p>
        </div>
        <a href="<?= BASE_URL ?>/user/view_plans.php" class="btn btn-primary">View Plans</a>
    </div>

    <?php if ($membership): ?>
        <div class="subscription-card">
            <div class="subscription-plan-name"><?= htmlspecialchars($membership['plan_name']) ?></div>
            <div class="mb-3 mt-1">
                <span class="status-pill active">Active</span>
            </div>

            <div class="subscription-meta-row">
                <strong>Purchased:</strong>
                <span><?= date('M d, Y', strtotime($membership['payment_date'])) ?></span>
            </div>
            <div class="subscription-meta-row">
                <strong>Duration:</strong>
                <span><?= (int)$membership['duration'] ?> days</span>
            </div>
            <?php $endDate = date('Y-m-d', strtotime($membership['payment_date'] . ' +' . (int)$membership['duration'] . ' days')); ?>
            <div class="subscription-meta-row">
                <strong>Access Until:</strong>
                <span><?= date('M d, Y', strtotime($endDate)) ?></span>
            </div>

            <div class="mt-3">
                <a href="<?= BASE_URL ?>/user/view_plans.php" class="btn btn-primary">Upgrade Plan</a>
            </div>
        </div>
    <?php else: ?>
        <div class="alert alert-warning" role="alert">
            You have no plan purchase yet. <a href="<?= BASE_URL ?>/user/view_plans.php">Browse our plans</a> to get started.
        </div>
    <?php endif; ?>
</section>

<?php include __DIR__ . '/../includes/footer.php'; ?>
