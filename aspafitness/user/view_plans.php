<?php
// user/view_plans.php - Display membership plans with pricing
require_once __DIR__ . '/../config/app.php';
require_login('user');

$plans = [];
$sql = 'SELECT id, plan_name, duration, price, description FROM membership_plans ORDER BY duration ASC';
$res = $conn->query($sql);
if ($res) {
    while ($row = $res->fetch_assoc()) {
        $plans[] = $row;
    }
}

// Check user's latest completed plan purchase
$currentSubscription = null;
$userId = (int)$_SESSION['user_id'];
$stmt = $conn->prepare('SELECT p.plan_id, p.payment_date, m.plan_name, m.duration FROM payments p JOIN membership_plans m ON p.plan_id = m.id WHERE p.user_id = ? AND p.payment_status = "completed" ORDER BY p.payment_date DESC LIMIT 1');
$stmt->bind_param('i', $userId);
$stmt->execute();
$result = $stmt->get_result();
if ($result->num_rows > 0) {
    $currentSubscription = $result->fetch_assoc();
    $currentSubscription['end_date'] = date('Y-m-d', strtotime($currentSubscription['payment_date'] . ' +' . (int)$currentSubscription['duration'] . ' days'));
}
?>
<?php include __DIR__ . '/../includes/header.php'; ?>

<section class="page-section">
    <div class="page-header">
        <div>
            <h2>Membership Plans</h2>
            <p class="page-header-sub">Choose the plan that fits your fitness goals</p>
        </div>
    </div>

    <?php if ($currentSubscription): ?>
        <div class="alert alert-info mb-4">
            <strong>Latest Plan:</strong> <?= htmlspecialchars($currentSubscription['plan_name']) ?>
            &mdash; Expires <?= date('M d, Y', strtotime($currentSubscription['end_date'])) ?>
        </div>
    <?php endif; ?>

    <?php if (empty($plans)): ?>
        <p class="text-muted">No membership plans are currently available.</p>
    <?php else: ?>
        <div class="row">
            <?php foreach ($plans as $plan): ?>
                <?php
                    $isCurrent = $currentSubscription && $currentSubscription['plan_id'] == $plan['id'];
                    $months = ceil($plan['duration'] / 30);
                    if ($plan['duration'] >= 365)     $period = '/ Year';
                    elseif ($plan['duration'] >= 90)  $period = '/ ' . $months . ' Months';
                    else                               $period = '/ Month';
                ?>
                <div class="col-md-4 mb-4">
                    <div class="membership-card <?= $isCurrent ? 'is-current' : '' ?>">
                        <div class="card-body text-center">
                            <?php if ($isCurrent): ?>
                                <div class="mb-2">
                                    <span class="status-pill active">Current Plan</span>
                                </div>
                            <?php endif; ?>

                            <h5 class="card-title mb-1"><?= htmlspecialchars($plan['plan_name']) ?></h5>

                            <div class="my-3">
                                <span class="membership-price">Rs <?= number_format($plan['price'], 0) ?></span>
                                <p class="membership-period"><?= $period ?></p>
                            </div>

                            <p class="membership-duration"><?= $plan['duration'] ?> days access</p>

                            <p style="color:var(--muted);font-size:0.88rem;min-height:48px;">
                                <?= htmlspecialchars($plan['description']) ?>
                            </p>

                            <ul class="membership-features text-left mt-3 mb-4">
                                <li>Access to all gym equipment</li>
                                <li>Workout programs</li>
                                <?php if ($plan['duration'] >= 30): ?>
                                    <li>Nutrition guides</li>
                                <?php endif; ?>
                            </ul>

                            <?php if ($isCurrent): ?>
                                <button class="btn btn-secondary btn-block" disabled>Current Plan</button>
                            <?php else: ?>
                                <a href="<?= BASE_URL ?>/user/checkout.php?id=<?= $plan['id'] ?>" class="btn btn-primary btn-block">
                                    Choose Plan
                                </a>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</section>

<?php include __DIR__ . '/../includes/footer.php'; ?>
