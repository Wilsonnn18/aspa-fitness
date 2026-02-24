<?php
// user/view_plans.php
require_once __DIR__ . '/../config/app.php';
require_login('user');

$plans = [];
$res = $conn->query('SELECT * FROM membership_plans');
if ($res) {
    while ($row = $res->fetch_assoc()) {
        $plans[] = $row;
    }
}
?>
<?php include __DIR__ . '/../includes/header.php'; ?>
<h2>Membership Plans</h2>
<?php if (empty($plans)): ?>
    <p>No plans available.</p>
<?php else: ?>
    <table class="table table-bordered">
        <thead><tr><th>Name</th><th>Duration (days)</th><th>Price</th><th></th></tr></thead>
        <tbody>
        <?php foreach ($plans as $p): ?>
            <tr>
                <td><?= htmlspecialchars($p['plan_name']) ?></td>
                <td><?= $p['duration'] ?></td>
                <td><?= number_format($p['price'], 2) ?></td>
                <td><a class="btn btn-sm btn-primary" href="<?= BASE_URL ?>/user/purchase_plan.php?id=<?= $p['id'] ?>">Purchase</a></td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
<?php endif; ?>
<?php include __DIR__ . '/../includes/footer.php'; ?>
