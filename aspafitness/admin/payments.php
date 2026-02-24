<?php
// admin/payments.php
require_once __DIR__ . '/../config/app.php';
require_login('admin');

$payments = [];
$sql = 'SELECT p.*, u.name AS user_name, m.plan_name FROM payments p JOIN users u ON p.user_id = u.id JOIN membership_plans m ON p.plan_id = m.id ORDER BY payment_date DESC';
$res = $conn->query($sql);
if ($res) {
    while ($row = $res->fetch_assoc()) {
        $payments[] = $row;
    }
}
?>
<?php include __DIR__ . '/../includes/header.php'; ?>
<h2>Payment Records</h2>
<?php if (empty($payments)): ?>
    <p>No payments found.</p>
<?php else: ?>
    <table class="table table-bordered">
        <thead><tr><th>ID</th><th>User</th><th>Plan</th><th>Amount</th><th>Date</th><th>Status</th><th>Txn ID</th></tr></thead>
        <tbody>
        <?php foreach ($payments as $p): ?>
            <tr>
                <td><?= $p['id'] ?></td>
                <td><?= htmlspecialchars($p['user_name']) ?></td>
                <td><?= htmlspecialchars($p['plan_name']) ?></td>
                <td><?= number_format($p['amount'], 2) ?></td>
                <td><?= $p['payment_date'] ?></td>
                <td><?= $p['payment_status'] ?></td>
                <td><?= htmlspecialchars($p['transaction_id']) ?></td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
<?php endif; ?>
<?php include __DIR__ . '/../includes/footer.php'; ?>
